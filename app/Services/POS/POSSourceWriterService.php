<?php

namespace App\Services\POS;

use App\Models\Company;
use App\Models\POSInvoiceSource;
use App\Models\POSInvoiceSourceDetail;
use App\Models\POSSOURCECustomerMaster;
use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Models\POSSOURCEShiftDetails;
use App\Models\POSSOURCETaxMaster;
use App\Models\POSSourceInvoicePayment;
use App\Models\POSSourceMenuSalesItem;
use App\Models\POSSourceMenueSalesItemDetail;
use App\Models\POSSourceMenuSalesMaster;
use App\Models\POSSourceMenuSalesPayment;
use App\Models\POSSourceMenuSalesServiceCharge;
use App\Models\POSSourcePaymentGlConfig;
use App\Models\POSSourceSalesReturn;
use App\Models\POSSourceSalesReturnDetails;
use App\Models\PosSourceMenuCategory;
use App\Models\PosSourceMenuMaster;
use App\Models\POSSOURCEMenuSalesTaxes;
use App\Models\POSSOURCEMenuSalesOutletTaxes;
use App\Models\POSSOURCETaxLedger;
use App\Models\SourceCustomerTypeMaster;

class POSSourceWriterService
{
  
    public static function write(array $data, int $logId): void
    {
        $posType = self::resolvePosType($data);
        $companyId = $data['company_id'] ;

        self::writeShift($data['shift'], $posType, $logId,$companyId);
        //self::writeTaxLedger($data['tax_ledger'] ?? [], $posType, $companyId, $logId);


        if ($posType === 1) {
            self::writeCustomers($data['customer_details'] ?? [], $posType, $companyId, $logId);
            self::writeInvoices($data['invoices'] ?? [], $posType, $companyId, $logId);
            self::writeSalesReturns($data['return'] ?? [], $posType, $companyId, $logId);
            self::writeTaxes($data['tax_details'] ?? [], $posType, $companyId, $logId);
            self::writePayments($data['payment_config'] ?? [], $posType, $companyId, $logId);
        }

        if ($posType === 2) {
            self::writeMenuSales($data['menuSales'] ?? [], $posType, $companyId, $logId);
            self::writeMenuSalesCustomerTypes($data['menuSales_customerType'] ?? [], $companyId, $logId);
            self::writeMenuSalesMenuMasters($data['menuSales_menuMaster'] ?? [], $companyId, $logId);
            self::writeMenuSalesMenuCategories($data['menuSales_menuCategory'] ?? [], $companyId, $logId);
        }
    }

    private static function resolvePosType(array $data): int
    {
        $type = strtoupper((string) ($data['type'] ?? ''));
        if ($type === 'GPOS') {
            return 1;
        }
        if ($type === 'RPOS') {
            return 2;
        }

        // Fallback when client sends numeric posType in shift payload.
        $shiftPosType = (int) ($data['shift']['posType'] ?? 0);
        if (in_array($shiftPosType, [1, 2], true)) {
            return $shiftPosType;
        }

        // Safe default for legacy POS sync payloads.
        return 1;
    }

    // -------------------------------------------------------------------------
    // Shift
    // -------------------------------------------------------------------------

    private static function writeShift(array $shift, int $posType, int $logId, int $companyId): void
    {
        $shiftID = (int) ($shift['shiftID'] ?? 0);
        if ($shiftID <= 0) {
            return;
        }

        $idStore = (int) ($shift['id_store'] ?? ($posType - 1));
        if ($idStore < 0) {
            $idStore = 0;
        }

        $existingIdStore = POSSOURCEShiftDetails::query()
            ->where('shiftID', $shiftID)
            ->where('pos_type', $posType)
            ->where('companyID', $companyId)
            ->value('id_store');

        if ($existingIdStore !== null) {
            $idStore = (int) $existingIdStore;
        }

        $shift['companyID'] = $companyId;
        $shift['pos_type'] = $posType;
        $shift['posType'] = $posType; 
        $shift['id_store'] = $idStore;

        $shift = self::enrichShiftWithCompanyMaster($shift, $companyId);

        POSSOURCEShiftDetails::updateOrInsert(
            [
                'shiftID'   => $shiftID,
                'pos_type'  => $posType,
                'companyID' => $companyId,
            ],
            array_merge($shift, [
                'shiftID'            => $shiftID,
                'pos_type'           => $posType,
                'companyID'          => $companyId,
                'posType'            => $posType,
                'id_store'           => $idStore,
                'transaction_log_id' => $logId,
            ])
        );
    }

    /**
     * Fill company/currency fields from companymaster + currencymaster so payload can omit them.
     */
    private static function enrichShiftWithCompanyMaster(array $shift, int $companyId): array
    {
        $company = Company::query()
            ->with(['localcurrency:currencyID,CurrencyCode,DecimalPlaces,ExchangeRate', 'reportingcurrency:currencyID,CurrencyCode,DecimalPlaces,ExchangeRate'])
            ->find($companyId);

        if (! $company) {
            return $shift;
        }

        $local = $company->localcurrency;
        $reporting = $company->reportingcurrency;

        $shift['companyID'] = (int) $companyId;
        $shift['companyCode'] = $company->CompanyID ?? ($company->companyShortCode ?? ($shift['companyCode'] ?? null));

        $shift['companyLocalCurrencyID'] = $company->localCurrencyID ?? ($shift['companyLocalCurrencyID'] ?? null);
        $shift['companyLocalCurrency'] = $local->CurrencyCode ?? ($shift['companyLocalCurrency'] ?? null);
        $shift['companyLocalExchangeRate'] = $local->ExchangeRate ?? ($shift['companyLocalExchangeRate'] ?? 1);
        $shift['companyLocalCurrencyDecimalPlaces'] = $local->DecimalPlaces ?? ($shift['companyLocalCurrencyDecimalPlaces'] ?? 2);

        $shift['companyReportingCurrencyID'] = $company->reportingCurrency ?? ($shift['companyReportingCurrencyID'] ?? null);
        $shift['companyReportingCurrency'] = $reporting->CurrencyCode ?? ($shift['companyReportingCurrency'] ?? null);
        $shift['companyReportingExchangeRate'] = $reporting->ExchangeRate ?? ($shift['companyReportingExchangeRate'] ?? 1);
        $shift['companyReportingCurrencyDecimalPlaces'] = $reporting->DecimalPlaces ?? ($shift['companyReportingCurrencyDecimalPlaces'] ?? 2);

        return $shift;
    }

    // -------------------------------------------------------------------------
    // Reference tables (shared by both POS types)
    // -------------------------------------------------------------------------

    private static function writeTaxes(array $taxes, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($taxes, 500) as $chunk) {
            $rows = array_map(fn($t) => array_merge($t, [
                'pos_type'           => $posType,
                'companyID'          => $companyId,
                'transaction_log_id' => $logId,
            ]), $chunk);

            POSSOURCETaxMaster::upsert($rows, ['taxShortCode', 'companyID', 'pos_type']);
        }
    }

    private static function writePayments(array $payments, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($payments, 500) as $chunk) {
            $masters = [];
            $details = [];

            foreach ($chunk as $payment) {
                $paymentDetails = $payment['details'] ?? [];
                unset($payment['details']);
                $masters[] = array_merge($payment, [
                    'pos_type'           => $posType,
                    'companyID'          => $companyId,
                    'transaction_log_id' => $logId
                ]);
                foreach ($paymentDetails as $detail) {
                    $details[] = array_merge($detail, [
                        'paymentConfigMasterID' => $payment['autoID'],
                        'pos_type'              => $posType,
                        'transaction_log_id'    => $logId
                    ]);
                }
            }

            if ($masters) {
                POSSourcePaymentGlConfig::upsert($masters, ['autoID', 'pos_type']);
            }
            if ($details) {
                POSSOURCEPaymentGlConfigDetail::upsert($details, ['ID', 'paymentConfigMasterID', 'pos_type']);
            }
        }
    }

    // -------------------------------------------------------------------------
    // GPOS — Customers
    // -------------------------------------------------------------------------

    private static function writeCustomers(array $customers, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($customers, 500) as $chunk) {
            $rows = array_map(fn($c) => array_merge($c, [
                'pos_type'           => $posType,
                'companyID'          => $companyId,
                'transaction_log_id' => $logId,
                'createdDateTime' => $c['createdDateTime'] ?? now()
            ]), $chunk);

            POSSOURCECustomerMaster::withoutTimestamps(function () use ($rows) {
                POSSOURCECustomerMaster::upsert($rows, ['customerSystemCode', 'companyID', 'pos_type']);
            });
        }
    }

    // -------------------------------------------------------------------------
    // GPOS — Invoices
    // -------------------------------------------------------------------------

    private static function writeInvoices(array $invoices, int $posType, int $companyId, int $logId): void
    {
        foreach ($invoices as $inv) {
            $lines           = $inv['details'] ?? [];
            $invoicePayments = $inv['payments'] ?? [];
            unset($inv['details'], $inv['payments']);

            $header = array_merge($inv, [
                'pos_type'           => $posType,
                'companyID'          => $companyId,
                'transaction_log_id' => $logId,
                'createdDateTime' => $inv['createdDateTime'] ?? now()
            ]);
            // Legacy PK column is no longer AUTO_INCREMENT; set business ID manually.
            $invoiceId = (int) ($inv['invoiceID'] ?? 0);
            if ($invoiceId <= 0) {
                continue;
            }

            // Same (invoiceID, pos_type, companyID) may arrive on re-sync; update instead of failing on PRIMARY.
            POSInvoiceSource::withoutTimestamps(function () use ($header, $invoiceId, $posType) {
                POSInvoiceSource::updateOrInsert(
                    [
                        'invoiceID' => $invoiceId,
                        'pos_type'  => $posType,
                        'companyID' => $companyId,
                    ],
                    $header
                );
            });

            $companyID = $header['companyID'];

            POSInvoiceSourceDetail::where('invoiceID', $invoiceId)
                ->where('pos_type', $posType)
                ->where('companyID', $companyID)
                ->delete();

            $details = [];
            foreach ($lines as $line) {
                unset($line['invoiceID']);
                $details[] = array_merge($line, [
                    'invoiceID'          => $invoiceId,
                    'pos_type'           => $posType,
                    'companyID'          => $companyID,
                    'transaction_log_id' => $logId,
                    'createdDateTime' => $inv['createdDateTime'] ?? now()
                ]);
            }
            if ($details) {
                foreach (array_chunk($details, 500) as $detailChunk) {
                    POSInvoiceSourceDetail::withoutTimestamps(function () use ($detailChunk) {
                        POSInvoiceSourceDetail::insert($detailChunk);
                    });
                }
            }

            POSSourceInvoicePayment::where('invoiceID', $invoiceId)
                ->where('pos_type', $posType)
                ->delete();

            $payments = [];
            foreach ($invoicePayments as $pay) {
                unset($pay['invoiceID']);
                $payments[] = array_merge($pay, [
                    'invoiceID'          => $invoiceId,
                    'pos_type'           => $posType,
                    'transaction_log_id' => $logId,
                    'createdDateTime' => $inv['createdDateTime'] ?? now()
                ]);
            }
            if ($payments) {
                foreach (array_chunk($payments, 500) as $paymentChunk) {
                    POSSourceInvoicePayment::withoutTimestamps(function () use ($paymentChunk) {
                        POSSourceInvoicePayment::insert($paymentChunk);
                    });
                }
            }
        }
    }

    /**
     * Generate next numeric business id for legacy PK columns.
     */
    private static function nextBusinessId(string $modelClass, string $column): int
    {
        $max = $modelClass::query()->max($column);
        return ((int) $max) + 1;
    }

    // -------------------------------------------------------------------------
    // GPOS — Sales Returns
    // -------------------------------------------------------------------------

    private static function writeSalesReturns(array $returns, int $posType, int $companyId, int $logId): void
    {
        $returns = self::enrichShiftWithCompanyMaster($returns,$companyId);

        foreach ($returns as $ret) {
            $lines = $ret['details'] ?? [];
            $lines = self::enrichShiftWithCompanyMaster($lines,$companyId);
            unset($ret['details']);

            $header = array_merge($ret, [
                'pos_type'           => $posType,
                'companyID'          => $companyId,
                'transaction_log_id' => $logId,
                'createdDateTime'    => $ret['createdDateTime'] ?? now(),
            ]);
            $salesReturnId = (int) ($ret['salesReturnID'] ?? 0);
            if ($salesReturnId <= 0) {
                continue;
            }
            $header['salesReturnID'] = $salesReturnId;

            // If already exists, update. If not, insert. No duplicate error.
            POSSourceSalesReturn::withoutTimestamps(function () use ($header, $salesReturnId, $posType) {
                POSSourceSalesReturn::updateOrInsert(
                    [
                        'salesReturnID' => $salesReturnId,
                        'pos_type'      => $posType,
                        'companyID'     => $companyId,
                    ],
                    $header
                );
            });

            POSSourceSalesReturnDetails::where('salesReturnID', $salesReturnId)
                ->where('pos_type', $posType)
                ->delete();

            $details = [];
            foreach ($lines as $line) {
                $details[] = array_merge($line, [
                    'salesReturnID'       => $salesReturnId,
                    'pos_type'            => $posType,
                    'companyID'           => $companyId,
                    'transaction_log_id'  => $logId,
                    'createdDateTime'     => $ret['createdDateTime'] ?? now(),
                ]);
            }

            if ($details) {
                foreach (array_chunk($details, 500) as $detailChunk) {
                    POSSourceSalesReturnDetails::withoutTimestamps(function () use ($detailChunk) {
                        POSSourceSalesReturnDetails::upsert($detailChunk, ['salesReturnDetailID']);
                    });
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // RPOS — Menu Sales
    // -------------------------------------------------------------------------

    private static function writeMenuSales(array $menuSales, int $posType, int $companyId, int $logId): void
    {
        // Payload may send a single menuSales object instead of an array of objects.
        // In that case, JSON decodes to an associative array and `foreach ($chunk as $sale)`
        // will iterate scalar values (e.g. menuSalesID, totals), causing unset() on non-array.
        if ($menuSales) {
            $keys = array_keys($menuSales);
            $isList = $keys === range(0, count($menuSales) - 1);
            if (! $isList) {
                $menuSales = [$menuSales];
            }
        }

      //  $menuSales = self::enrichShiftWithCompanyMaster($menuSales, $companyId);
        foreach (array_chunk($menuSales, 500) as $chunk) {
            $masters        = [];
            $items          = [];
            $itemDetails    = [];
            $payments       = [];
            $taxes          = [];
            $outletTaxes    = [];
            $serviceCharges = [];

            foreach ($chunk as $sale) {
                if (! is_array($sale)) {
                    continue;
                }
                $saleItems      = $sale['menuSales_items'] ?? [];
               // $saleItems = self::enrichShiftWithCompanyMaster($saleItems,$companyId);
                $saleTaxes      = $sale['menuSales_tax_details'] ?? [];
               // $saleTaxes = self::enrichShiftWithCompanyMaster($saleTaxes,$companyId);
                $saleOutletTaxes = $sale['menuSales_outletTax_details'] ?? [];
                $saleServiceCharges = $sale['menuSales_serviceCharge'] ?? [];
                unset($sale['menuSales_items'], $sale['menuSales_tax_details'], $sale['menuSales_outletTax_details'], $sale['menuSales_serviceCharge']);

                $masters[] = array_merge($sale, [
                    'pos_type'           => $posType,
                    'companyID'          => $companyId,
                    'transaction_log_id' => $logId,
                ]);

                foreach ($saleItems as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $subDetails = $item['menuSales_itemdetails'] ?? [];
                    unset($item['menuSales_itemdetails']);
                    $items[] = array_merge($item, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                    foreach ($subDetails as $sd) {
                        if (! is_array($sd)) {
                            continue;
                        }
                        $itemDetails[] = array_merge($sd, [
                            'menuSalesID'        => $sale['menuSalesID'],
                            'menuSalesItemID'    => $item['menuSalesItemID'],
                            'pos_type'           => $posType,
                            'companyID'          => $sale['companyID'] ?? $companyId,
                            'transaction_log_id' => $logId,
                        ]);
                    }
                }

                // foreach ($salePayments as $pay) {
                //     $payments[] = array_merge($pay, [
                //         'menuSalesID'        => $sale['menuSalesID'],
                //         'pos_type'           => $posType,
                //         'transaction_log_id' => $logId,
                //     ]);
                // }
                foreach ($saleTaxes as $tax) {
                    if (! is_array($tax)) {
                        continue;
                    }
                    $taxes[] = array_merge($tax, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
                foreach ($saleOutletTaxes as $ot) {
                    if (! is_array($ot)) {
                        continue;
                    }
                    $outletTaxes[] = array_merge($ot, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
                foreach ($saleServiceCharges as $sc) {
                    if (! is_array($sc)) {
                        continue;
                    }
                    $serviceCharges[] = array_merge($sc, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
            }

            $menuSalesIds = array_column($masters, 'menuSalesID');

            if ($masters) {
                // Avoid auto-injecting created_at/updated_at for legacy POS source tables.
                POSSourceMenuSalesMaster::withoutTimestamps(function () use ($masters) {
                    POSSourceMenuSalesMaster::upsert($masters, ['menuSalesID', 'companyID']);
                });
            }

           // Replace children for these menuSalesIDs on every sync (idempotent)
            POSSourceMenuSalesItem::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($items) {
                foreach (array_chunk($items, 500) as $ch) {
                    POSSourceMenuSalesItem::withoutTimestamps(function () use ($ch) {
                        POSSourceMenuSalesItem::upsert($ch, ['menuSalesItemID', 'companyID']);
                    });
                }
            }

            POSSourceMenueSalesItemDetail::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($itemDetails) {
                foreach (array_chunk($itemDetails, 500) as $ch) {
                    POSSourceMenueSalesItemDetail::withoutTimestamps(function () use ($ch) {
                        POSSourceMenueSalesItemDetail::upsert($ch, ['menuSalesItemDetailID', 'companyID']);
                    });
                }
            }

            // POSSourceMenuSalesPayment::whereIn('menuSalesID', $menuSalesIds)->delete();
            // if ($payments) {
            //     foreach (array_chunk($payments, 500) as $ch) {
            //         POSSourceMenuSalesPayment::insert($ch);
            //     }
            // }

            POSSOURCEMenuSalesTaxes::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($taxes) {
                foreach (array_chunk($taxes, 500) as $ch) {
                    POSSOURCEMenuSalesTaxes::withoutTimestamps(function () use ($ch) {
                        POSSOURCEMenuSalesTaxes::upsert($ch, ['menuSalesTaxID', 'companyID']);
                    });
                }
            }

            POSSOURCEMenuSalesOutletTaxes::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($outletTaxes) {
                foreach (array_chunk($outletTaxes, 500) as $ch) {
                    POSSOURCEMenuSalesOutletTaxes::withoutTimestamps(function () use ($ch) {
                        POSSOURCEMenuSalesOutletTaxes::upsert($ch, ['menuSalesOutletTaxID', 'companyID']);
                    });
                }
            }

            POSSourceMenuSalesServiceCharge::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($serviceCharges) {
                foreach (array_chunk($serviceCharges, 500) as $ch) {
                    POSSourceMenuSalesServiceCharge::withoutTimestamps(function () use ($ch) {
                        POSSourceMenuSalesServiceCharge::upsert($ch, ['menuSalesServiceChargeID', 'companyID']);
                    });
                }
            }
        }
    }

    private static function writeMenuSalesCustomerTypes(array $customerTypes, int $companyId, int $logId): void
    {
        foreach (array_chunk($customerTypes, 500) as $chunk) {
            $rows = array_map(fn($row) => array_merge($row, [
                'company_id'         => $companyId,
                'transaction_log_id' => $logId,
            ]), $chunk);

            SourceCustomerTypeMaster::withoutTimestamps(function () use ($rows) {
                SourceCustomerTypeMaster::upsert($rows, ['customerTypeID', 'company_id']);
            });
        }
    }

    private static function writeMenuSalesMenuMasters(array $menuMasters, int $companyId, int $logId): void
    {
        foreach (array_chunk($menuMasters, 500) as $chunk) {
            $rows = array_map(fn($row) => array_merge($row, [
                'companyID'          => $row['companyID'] ?? $companyId,
                'transaction_log_id' => $logId,
            ]), $chunk);

            PosSourceMenuMaster::withoutTimestamps(function () use ($rows) {
                PosSourceMenuMaster::upsert($rows, ['menuMasterID', 'companyID']);
            });
        }
    }

    private static function writeMenuSalesMenuCategories(array $menuCategories, int $companyId, int $logId): void
    {
        foreach (array_chunk($menuCategories, 500) as $chunk) {
            $rows = array_map(fn($row) => array_merge($row, [
                'companyID'          => $row['companyID'] ?? $companyId,
                'transaction_log_id' => $logId,
            ]), $chunk);

            PosSourceMenuCategory::withoutTimestamps(function () use ($rows) {
                PosSourceMenuCategory::upsert($rows, ['menuCategoryID', 'companyID']);
            });
        }
    }

    private static function writeTaxLedger(array $taxLedger, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($taxLedger, 500) as $chunk) {
            $rows = array_map(fn($t) => array_merge($t, [
                'pos_type'           => $posType,
                'companyID'          => $t['companyID'] ?? $companyId,
                'transaction_log_id' => $logId,
                'createdDateTime' => $t['createdDateTime'] ?? now(),
            ]), $chunk);

            if ($rows) {
                POSSOURCETaxLedger::withoutTimestamps(function () use ($rows) {
                    POSSOURCETaxLedger::upsert($rows, ['taxLedgerAutoID', 'pos_type', 'companyID']);
                });
            }
        }
    }
}
