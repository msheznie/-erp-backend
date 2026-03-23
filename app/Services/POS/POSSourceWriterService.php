<?php

namespace App\Services\POS;

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
use App\Models\POSSOURCEMenuSalesTaxes;
use App\Models\POSSOURCEMenuSalesOutletTaxes;

class POSSourceWriterService
{
    /**
     * Orchestrate writes for one shift payload to all relevant pos_source_* tables.
     */
    public static function write(array $data, int $logId): void
    {
        $posType   = $data['shift']['posTypeID'];
        $companyId = $data['shift']['companyID'];

        self::writeShift($data['shift'], $posType, $logId);
        self::writeTaxes($data['taxes'] ?? [], $posType, $companyId, $logId);
        self::writePayments($data['payments'] ?? [], $posType, $companyId, $logId);

        if ($posType === 1) {
            self::writeCustomers($data['customers'] ?? [], $posType, $companyId, $logId);
            self::writeInvoices($data['invoices'] ?? [], $posType, $companyId, $logId);
            self::writeSalesReturns($data['salesReturns'] ?? [], $posType, $companyId, $logId);
        }

        if ($posType === 2) {
            self::writeMenuSales($data['menuSales'] ?? [], $posType, $companyId, $logId);
        }
    }

    // -------------------------------------------------------------------------
    // Shift
    // -------------------------------------------------------------------------

    private static function writeShift(array $shift, int $posType, int $logId): void
    {
        POSSOURCEShiftDetails::updateOrInsert(
            [
                'shiftID'   => $shift['shiftID'],
                'pos_type'  => $posType,
                'companyID' => $shift['companyID'],
            ],
            array_merge($shift, [
                'pos_type'           => $posType,
                'transaction_log_id' => $logId,
            ])
        );
    }

    // -------------------------------------------------------------------------
    // Reference tables (shared by both POS types)
    // -------------------------------------------------------------------------

    private static function writeTaxes(array $taxes, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($taxes, 500) as $chunk) {
            $rows = array_map(fn($t) => array_merge($t, [
                'pos_type'           => $posType,
                'companyID'          => $t['companyID'] ?? $companyId,
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
                    'companyID'          => $payment['companyID'] ?? $companyId,
                    'transaction_log_id' => $logId,
                ]);
                foreach ($paymentDetails as $detail) {
                    $details[] = array_merge($detail, [
                        'paymentConfigMasterID' => $payment['autoID'],
                        'pos_type'              => $posType,
                        'transaction_log_id'    => $logId,
                    ]);
                }
            }

            if ($masters) {
                POSSourcePaymentGlConfig::upsert($masters, ['autoID', 'pos_type', 'companyID']);
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
                'companyID'          => $c['companyID'] ?? $companyId,
                'transaction_log_id' => $logId,
            ]), $chunk);

            POSSOURCECustomerMaster::upsert($rows, ['customerSystemCode', 'companyID', 'pos_type']);
        }
    }

    // -------------------------------------------------------------------------
    // GPOS — Invoices
    // -------------------------------------------------------------------------

    private static function writeInvoices(array $invoices, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($invoices, 500) as $chunk) {
            $headers  = [];
            $details  = [];
            $payments = [];

            foreach ($chunk as $inv) {
                $lines           = $inv['details'] ?? [];
                $invoicePayments = $inv['payments'] ?? [];
                unset($inv['details'], $inv['payments']);

                $headers[] = array_merge($inv, [
                    'pos_type'           => $posType,
                    'companyID'          => $inv['companyID'] ?? $companyId,
                    'transaction_log_id' => $logId,
                ]);

                foreach ($lines as $line) {
                    $details[] = array_merge($line, [
                        'invoiceID'          => $inv['invoiceID'],
                        'pos_type'           => $posType,
                        'companyID'          => $inv['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }

                foreach ($invoicePayments as $pay) {
                    $payments[] = array_merge($pay, [
                        'invoiceID'          => $inv['invoiceID'],
                        'pos_type'           => $posType,
                        'transaction_log_id' => $logId,
                    ]);
                }
            }

            if ($headers) {
                POSInvoiceSource::upsert($headers, ['invoiceID', 'pos_type', 'companyID']);
            }

            // Detail & payment rows are replaced per invoice on each sync
            $invoiceIds = array_column($headers, 'invoiceID');
            POSInvoiceSourceDetail::whereIn('invoiceID', $invoiceIds)
                ->where('pos_type', $posType)
                ->delete();
            if ($details) {
                foreach (array_chunk($details, 500) as $chunk) {
                    POSInvoiceSourceDetail::insert($chunk);
                }
            }

            POSSourceInvoicePayment::whereIn('invoiceID', $invoiceIds)
                ->where('pos_type', $posType)
                ->delete();
            if ($payments) {
                foreach (array_chunk($payments, 500) as $chunk) {
                    POSSourceInvoicePayment::insert($chunk);
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // GPOS — Sales Returns
    // -------------------------------------------------------------------------

    private static function writeSalesReturns(array $returns, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($returns, 500) as $chunk) {
            $headers = [];
            $details = [];

            foreach ($chunk as $ret) {
                $lines = $ret['details'] ?? [];
                unset($ret['details']);

                $headers[] = array_merge($ret, [
                    'pos_type'           => $posType,
                    'companyID'          => $ret['companyID'] ?? $companyId,
                    'transaction_log_id' => $logId,
                ]);

                foreach ($lines as $line) {
                    $details[] = array_merge($line, [
                        'salesReturnID'      => $ret['salesReturnID'],
                        'pos_type'           => $posType,
                        'companyID'          => $ret['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
            }

            if ($headers) {
                POSSourceSalesReturn::upsert($headers, ['salesReturnID', 'pos_type', 'companyID']);
            }

            $returnIds = array_column($headers, 'salesReturnID');
            POSSourceSalesReturnDetails::whereIn('salesReturnID', $returnIds)
                ->where('pos_type', $posType)
                ->delete();
            if ($details) {
                foreach (array_chunk($details, 500) as $chunk) {
                    POSSourceSalesReturnDetails::insert($chunk);
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // RPOS — Menu Sales
    // -------------------------------------------------------------------------

    private static function writeMenuSales(array $menuSales, int $posType, int $companyId, int $logId): void
    {
        foreach (array_chunk($menuSales, 500) as $chunk) {
            $masters        = [];
            $items          = [];
            $itemDetails    = [];
            $payments       = [];
            $taxes          = [];
            $outletTaxes    = [];
            $serviceCharges = [];

            foreach ($chunk as $sale) {
                $saleItems      = $sale['items'] ?? [];
                $salePayments   = $sale['payments'] ?? [];
                $saleTaxes      = $sale['taxes'] ?? [];
                $saleOutletTaxes = $sale['outletTaxes'] ?? [];
                $saleServiceCharges = $sale['serviceCharges'] ?? [];
                unset($sale['items'], $sale['payments'], $sale['taxes'], $sale['outletTaxes'], $sale['serviceCharges']);

                $masters[] = array_merge($sale, [
                    'pos_type'           => $posType,
                    'companyID'          => $sale['companyID'] ?? $companyId,
                    'transaction_log_id' => $logId,
                ]);

                foreach ($saleItems as $item) {
                    $subDetails = $item['details'] ?? [];
                    unset($item['details']);
                    $items[] = array_merge($item, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                    foreach ($subDetails as $sd) {
                        $itemDetails[] = array_merge($sd, [
                            'menuSalesID'        => $sale['menuSalesID'],
                            'menuSalesItemID'    => $item['menuSalesItemID'],
                            'pos_type'           => $posType,
                            'companyID'          => $sale['companyID'] ?? $companyId,
                            'transaction_log_id' => $logId,
                        ]);
                    }
                }

                foreach ($salePayments as $pay) {
                    $payments[] = array_merge($pay, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'transaction_log_id' => $logId,
                    ]);
                }
                foreach ($saleTaxes as $tax) {
                    $taxes[] = array_merge($tax, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
                foreach ($saleOutletTaxes as $ot) {
                    $outletTaxes[] = array_merge($ot, [
                        'menuSalesID'        => $sale['menuSalesID'],
                        'pos_type'           => $posType,
                        'companyID'          => $sale['companyID'] ?? $companyId,
                        'transaction_log_id' => $logId,
                    ]);
                }
                foreach ($saleServiceCharges as $sc) {
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
                POSSourceMenuSalesMaster::upsert($masters, ['menuSalesID', 'companyID']);
            }

            // Replace children for these menuSalesIDs on every sync (idempotent)
            POSSourceMenuSalesItem::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($items) {
                foreach (array_chunk($items, 500) as $ch) {
                    POSSourceMenuSalesItem::insert($ch);
                }
            }

            POSSourceMenueSalesItemDetail::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($itemDetails) {
                foreach (array_chunk($itemDetails, 500) as $ch) {
                    POSSourceMenueSalesItemDetail::insert($ch);
                }
            }

            POSSourceMenuSalesPayment::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($payments) {
                foreach (array_chunk($payments, 500) as $ch) {
                    POSSourceMenuSalesPayment::insert($ch);
                }
            }

            POSSOURCEMenuSalesTaxes::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($taxes) {
                foreach (array_chunk($taxes, 500) as $ch) {
                    POSSOURCEMenuSalesTaxes::insert($ch);
                }
            }

            POSSOURCEMenuSalesOutletTaxes::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($outletTaxes) {
                foreach (array_chunk($outletTaxes, 500) as $ch) {
                    POSSOURCEMenuSalesOutletTaxes::insert($ch);
                }
            }

            POSSourceMenuSalesServiceCharge::whereIn('menuSalesID', $menuSalesIds)->delete();
            if ($serviceCharges) {
                foreach (array_chunk($serviceCharges, 500) as $ch) {
                    POSSourceMenuSalesServiceCharge::insert($ch);
                }
            }
        }
    }
}
