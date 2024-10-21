<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\Helper;
use App\helper\SupplierInvoice;
use App\helper\TaxService;
use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DirectInvoiceDetails;
use App\Models\FinanceItemCategorySub;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\SupplierMaster;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\Taxdetail;
use App\Models\TaxVatCategories;
use App\Models\WarehouseMaster;
use App\Services\API\SupplierInvoiceAPIService;
use App\Services\DocumentAutoApproveService;
use App\Services\UserTypeService;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\DocumentSystemMappingTrait;
use Carbon\Carbon;

class supplierInvoiceCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;
    public $record;
    public $input;
    public $timeout = 500;
    public $db;
    public $api_external_key;
    public $api_external_url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db, $api_external_key, $api_external_url)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->input = $input;
        $this->db = $db;
        $this->api_external_key = $api_external_key;
        $this->api_external_url = $api_external_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/supplier_invoice_creation.log');
        CommonJobService::db_switch($this->db);
        try {
            /*** insert a try catch */
            $systemUser = UserTypeService::getSystemEmployee();
            $input = $this->input;
            $invError = [];
            if (!empty($input[0])) {
                $compId = $input['company_id'];
                $company = Company::where('companySystemID', $compId)->first();
                if (empty($company)) {
                    $invError[] = [
                        'field' => '',
                        'error' => 'Company not found'
                    ];
                }
                $invoiceNo = 1;
                foreach ($input[0] as $value) {
                    $error = [];
                    $invDetails = [];
                    $invMaster = $value;
                    if (!empty($invMaster['documentType'])) {
                        if (in_array($invMaster['documentType'], [1,2])) {
                            if($invMaster['documentType'] == 2) {
                                $invMaster['documentType'] = 3;
                            }
                            if (empty($invMaster['supplierInvoiceNo'])) {
                                $error[] = [
                                    'field' => 'supplierInvoiceNo',
                                    'error' => 'Supplier Invoice No field is required'
                                ];
                            }

                            if (empty($invMaster['comments'])) {
                                $error[] = [
                                    'field' => 'comments',
                                    'error' => 'Narration field is required'
                                ];
                            }

                            if (empty($invMaster['supplierInvoiceDate'])) {
                                $error[] = [
                                    'field' => 'supplierInvoiceDate',
                                    'error' => 'Supplier Invoice Date field is required'
                                ];
                            }

                            if (empty($invMaster['bookingDate'])) {
                                $error[] = [
                                    'field' => 'bookingDate',
                                    'error' => 'Document Date field is required'
                                ];
                            } else {
                                $bookingDate = Carbon::parse($invMaster['bookingDate']);
                                $currentDate = Carbon::now()->startOfDay();
                                if ($bookingDate->gt($currentDate)) {
                                    $error[] = [
                                        'field' => '',
                                        'error' => 'The booking date must be today or before.'
                                    ];
                                }
                            }

                            $financeYear = CompanyFinanceYear::where('companySystemID', $compId)->where('isActive', -1)->where('bigginingDate', "<=", $invMaster['bookingDate'])->where('endingDate', ">=", $invMaster['bookingDate'])->first();
                            if (empty($financeYear)) {
                                $error[] = [
                                    'field' => '',
                                    'error' => 'Finance Year not found'
                                ];
                            } else {
                                $invMaster['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
                            }

                            $financePeriod = CompanyFinancePeriod::where('companySystemID', $compId)->where('departmentSystemID', 1)->where('dateFrom', "<=", $invMaster['bookingDate'])->where('dateTo', ">=", $invMaster['bookingDate'])->where('isActive', -1)->first();
                            if (empty($financePeriod)) {
                                $error[] = [
                                    'field' => '',
                                    'error' => 'Finance Period not found'
                                ];
                            } else {
                                $invMaster['companyFinancePeriodID'] = $financePeriod['companyFinancePeriodID'];
                                $invMaster['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
                                $invMaster['FYPeriodDateTo'] = $financePeriod['dateTo'];
                            }

                            if($invMaster['documentType'] == 3) {
                                if(empty($invMaster['segment'])) {
                                    $error[] = [
                                        'field' => 'segment',
                                        'error' => 'Segment field is required'
                                    ];
                                } else {
                                    $segment = SegmentMaster::where('ServiceLineCode',$invMaster['segment'])
                                        ->where('isActive', 1)
                                        ->where('isDeleted', 0)
                                        ->where('companySystemID', $compId)
                                        ->first();
                                    if(!$segment){
                                        $error[] = [
                                            'field' => 'segment',
                                            'error' => 'Segment not found'
                                        ];
                                    } else {
                                        $invMaster['serviceLineSystemID'] = $segment['serviceLineSystemID'];
                                    }
                                }

                                if (empty($invMaster['warehouse'])) {
                                    $error[] = [
                                        'field' => 'warehouse',
                                        'error' => 'Warehouse field is required'
                                    ];
                                } else {
                                    $warehouse = WarehouseMaster::where('wareHouseCode', $invMaster['warehouse'])
                                        ->where('isActive', 1)
                                        ->where('companySystemID', $compId)
                                        ->first();

                                    if(!$warehouse){
                                        $error[] = [
                                            'field' => 'warehouse',
                                            'error' => 'Warehouse not found'
                                        ];
                                    } else {
                                        $invMaster['wareHouseSystemCode'] = $warehouse['wareHouseSystemCode'];
                                    }
                                }
                            }

                            if (empty($invMaster['supplier'])) {
                                $error[] = [
                                    'field' => 'supplier',
                                    'error' => 'supplier field is required'
                                ];
                                $invMaster['supplierID'] = null;
                            } else {
                                $supplierExist = SupplierAssigned::where(function($query) use ($invMaster) {
                                        $query->where('primarySupplierCode', $invMaster['supplier'])
                                             ->orWhere('registrationNumber', $invMaster['supplier']);
                                    })
                                    ->where('companySystemID', $compId)
                                    ->where('isActive', 1)
                                    ->first();

                                if(empty($supplierExist)) {
                                    $error[] = [
                                        'field' => 'supplier',
                                        'error' => 'supplier not found'
                                    ];
                                } else {
                                    $invMaster['supplierID'] = $supplierExist['supplierCodeSytem'];
                                    $supplier = SupplierMaster::where('supplierCodeSystem', $supplierExist['supplierCodeSytem'])->first();
                                    $invMaster['whtApplicableYN'] = $supplier['whtApplicableYN'];

                                    $validatorResult = \Helper::checkBlockSuppliers($invMaster['bookingDate'],$invMaster['supplierID']);
                                    if (!$validatorResult['success']) {
                                        $error[] = [
                                            'field' => 'supplier',
                                            'error' => 'The selected supplier has been blocked'
                                        ];
                                    }

                                    if (empty($invMaster['currency'])) {
                                        $error[] = [
                                            'field' => 'currency',
                                            'error' => 'Currency field is required'
                                        ];
                                    } else {
                                        $currency = SupplierCurrency::join('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                                            ->where('currencymaster.CurrencyCode', $invMaster['currency'])
                                            ->where('supplierCodeSystem', $supplierExist['supplierCodeSytem'])
                                            ->where('isAssigned', -1)
                                            ->first();

                                        if(!$currency){
                                            $error[] = [
                                                'field' => 'currency',
                                                'error' => 'Currency is invalid'
                                            ];
                                        } else {
                                            $invMaster['supplierTransactionCurrencyID'] = $currency['currencyID'];
                                        }
                                    }

                                    if(!empty($invMaster['supplierInvoiceNo'])) {
                                        $alreadyAdded = BookInvSuppMaster::where('supplierInvoiceNo', $invMaster['supplierInvoiceNo'])
                                            ->where('supplierID', $invMaster['supplierID'])
                                            ->first();

                                        if ($alreadyAdded) {
                                            $error[] = [
                                                'field' => 'supplierInvoiceNo',
                                                'error' => 'Entered supplier invoice number was already used (' . $invMaster['supplierInvoiceNo'] . '). Please check again'
                                            ];
                                        }
                                    }

                                    if((!empty($invMaster['retentionPercentage']) || !empty($invMaster['retentionAmount'])) && !empty($invMaster['bookingDate'])) {
                                        if(!is_numeric($invMaster['retentionPercentage']) || $invMaster['retentionPercentage'] < 0 || $invMaster['retentionPercentage'] > 100) {
                                            $error[] = [
                                                'field' => 'retentionPercentage',
                                                'error' => 'Retention% should be a numeric value and between 0 - 100'
                                            ];
                                        }

                                        $creditPeriod = SupplierMaster::where('supplierCodeSystem', $invMaster['supplierID'])->value('creditPeriod');
                                        $invMaster['retentionDueDate'] = Carbon::parse($invMaster['bookingDate'])->addDays(($creditPeriod ?? 0));
                                    }
                                }
                            }

                            if (empty($invMaster['details'])) {
                                $error[] = [
                                    'field' => '',
                                    'error' => 'Supplier invoice details not found'
                                ];
                            } else {
                                /** Supplier direct invoice */
                                $whtTotal = 0;
                                $isVATEligible = TaxService::checkCompanyVATEligible($compId);
                                foreach ($invMaster['details'] as $detail) {
                                    if((!empty($detail['VATPercentage']) || !empty($detail['VATAmount'])) && !empty($detail['whtAmount'])) {
                                        $error[] = [
                                            'field' => 'whtAmount',
                                            'error' => 'Cannot allocate WHT amount and VAT amount for same detail'
                                        ];
                                    }

                                    if (!empty($detail['whtAmount'])) {
                                        if ($supplierExist && $invMaster['whtApplicableYN'] == 0) {
                                            $error[] = [
                                                'field' => 'whtAmount',
                                                'error' => 'Cannot allocate a WHT amount as the supplier is not applicable for WHT'
                                            ];
                                            $detail['whtAmount'] = 0;
                                        }
                                        $invMaster['whtApplicable'] =  1;
                                    }

                                    if((!empty($detail['VATPercentage']) || !empty($detail['VATAmount'])) && !$isVATEligible) {
                                        $error[] = [
                                            'field' => 'VATPercentage',
                                            'error' => 'Company is not vat registered'
                                        ];
                                    }

                                    if ($invMaster['documentType'] == 1) {
                                        if(empty($detail['glCode'])) {
                                            $error[] = [
                                                'field' => 'glCode',
                                                'error' => 'GlCode field is required'
                                            ];
                                        } else {
                                            $chartOfAccountAssign = ChartOfAccountsAssigned::where('companySystemID',$compId)
                                                ->where('AccountCode',$detail['glCode'])
                                                ->where('controllAccountYN', 0)
                                                ->where('isAssigned', -1)
                                                ->where('isActive', 1)
                                                ->where('isBank', 0)
                                                ->first();
                                            if(!$chartOfAccountAssign){
                                                $error[] = [
                                                    'field' => 'glCode',
                                                    'error' => 'GlCode not found'
                                                ];
                                            }
                                        }
                                        if(empty($detail['segment'])) {
                                            $error[] = [
                                                'field' => 'segment',
                                                'error' => 'Segment field is required'
                                            ];
                                        } else {
                                            $detSegment = SegmentMaster::where('ServiceLineCode',$detail['segment'])
                                                ->where('isActive', 1)
                                                ->where('isDeleted', 0)
                                                ->where('companySystemID', $compId)
                                                ->first();
                                            if(!$detSegment){
                                                $error[] = [
                                                    'field' => 'segment',
                                                    'error' => 'Segment not found'
                                                ];
                                            }
                                        }

                                        if (!empty($detail['amount'])) {
                                            if (!is_numeric($detail['amount']) || $detail['amount'] <= 0) {
                                                $error[] = [
                                                    'field' => 'amount',
                                                    'error' => 'Amount field should be numeric and greater than zero'
                                                ];
                                            }
                                        } else {
                                            $error[] = [
                                                'field' => 'amount',
                                                'error' => 'Amount field is required'
                                            ];
                                        }

                                        if ($isVATEligible) {
                                            $defaultVAT = TaxService::getDefaultVAT($compId, $invMaster['supplierID']);
                                            $docAmount = ($detail['amount'] ?? 0);
                                            if(!empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATPercentage']) || $detail['VATPercentage'] > 100 || $detail['VATPercentage'] < 0) {
                                                    $error[] = [
                                                        'field' => 'VATPercentage',
                                                        'error' => 'VAT% should be a numeric value and between 0 - 100'
                                                    ];
                                                } else {
                                                    $vatAmount = ($docAmount / 100) * $detail['VATPercentage'];

                                                    if(!empty($detail['VATAmount']) && $detail['VATAmount'] != $vatAmount) {
                                                        $error[] = [
                                                            'field' => 'VATPercentage',
                                                            'error' => 'VAT% and VAT Amount is not matching'
                                                        ];
                                                    }
                                                    $detail['VATAmount'] = $vatAmount;
                                                }
                                            }

                                            if(!empty($detail['VATAmount']) && empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATAmount']) || ($detail['VATAmount'] > $docAmount)) {
                                                    $error[] = [
                                                        'field' => 'VATAmount',
                                                        'error' => 'VAT amount should be a numeric value and cannot be greater than invoice amount'
                                                    ];
                                                } else {
                                                    $detail['VATPercentage'] = ($detail['VATAmount'] * 100) / $docAmount;
                                                }
                                            }
                                        }

                                        if (empty($error)) {
                                            /*** insert records for direct invoice details */
                                            $whtTotal = $whtTotal + ($detail['whtAmount'] ?? 0);
                                            $invDetails[] = [
                                                'companySystemID' => $compId,
                                                'serviceLineSystemID' => $detSegment['serviceLineSystemID'],
                                                'serviceLineCode' => $detSegment['serviceLineMasterCode'],
                                                'chartOfAccountSystemID' => $chartOfAccountAssign['chartOfAccountSystemID'],
                                                'glCode' => $chartOfAccountAssign['AccountCode'],
                                                'glCodeDes' => $chartOfAccountAssign['AccountDescription'],
                                                'comments' => $detail['comments'] ?? null,
                                                'DIAmountCurrency' => $invMaster['supplierTransactionCurrencyID'],
                                                'DIAmountCurrencyER' => 1,
                                                'DIAmount' => $detail['amount'],
                                                'netAmount' => $detail['amount'],
                                                'whtApplicable' => $invMaster['whtApplicable'],
                                                'whtAmount' => $detail['whtAmount'] ?? null,
                                                'vatSubCategoryID' => $defaultVAT['vatSubCategoryID'] ?? null,
                                                'vatMasterCategoryID' => $defaultVAT['vatMasterCategoryID'] ?? null,
                                                'VATPercentage' => $detail['VATPercentage'] ?? null,
                                                'VATAmount' => $detail['VATAmount'] ?? null,
                                            ];
                                        }
                                    } elseif ($invMaster['documentType'] == 3) {
                                    /** Supplier item invoice */
                                        if (!empty($detail['qty'])) {
                                            if (!is_numeric($detail['qty']) || $detail['qty'] <= 1) {
                                                $error[] = [
                                                    'field' => 'qty',
                                                    'error' => 'Quantity field should be numeric and greater than zero'
                                                ];
                                            }
                                        } else {
                                            $error[] = [
                                                'field' => 'qty',
                                                'error' => 'Quantity field is required'
                                            ];
                                        }

                                        if (!empty($detail['unitCost'])) {
                                            if (!is_numeric($detail['unitCost']) || $detail['unitCost'] < 0) {
                                                $error[] = [
                                                    'field' => 'unitCost',
                                                    'error' => 'Unit cost field should be numeric and a positive value'
                                                ];
                                            }
                                        } else {
                                            $error[] = [
                                                'field' => 'unitCost',
                                                'error' => 'Unit cost field is required'
                                            ];
                                        }

                                        if(!empty($detail['item'])) {
                                            $itemAssign = ItemAssigned::with(['item_master'])->where('itemCodeSystem', $detail['item'])
                                                ->where('companySystemID', $compId)
                                                ->where('isActive', 1)
                                                ->first();

                                            if(!$itemAssign) {
                                                $error[] = [
                                                    'field' => 'item',
                                                    'error' => 'Item not found'
                                                ];
                                            } else {
                                                if(!empty($invDetails)) {
                                                    $names = collect($invDetails)->pluck('itemCode');
                                                    if ($names->contains($detail['item'])) {
                                                        $error[] = [
                                                            'field' => 'item',
                                                            'error' => 'Item is already added to the details'
                                                        ];
                                                    }
                                                }
                                            }
                                        } else {
                                            $error[] = [
                                                'field' => 'item',
                                                'error' => 'Item field is required'
                                            ];
                                        }

                                        if (!empty($detail['discountPercentage'])) {
                                            if ($detail['discountPercentage'] > 100 || $detail['discountPercentage'] < 0) {
                                                $error[] = [
                                                    'field' => 'discountPercentage',
                                                    'error' => 'Discount% should be a numeric value and between 0 - 100'
                                                ];
                                            } else {
                                                $discountAmount = (($detail['unitCost'] ?? 0) / 100) * $detail['discountPercentage'];
                                                if(!empty($detail['discountAmount']) && $detail['discountAmount'] != $discountAmount) {
                                                    $error[] = [
                                                        'field' => 'discountAmount',
                                                        'error' => 'Discount% and Discount Amount is not matching'
                                                    ];
                                                }
                                                $detail['discountAmount'] = $discountAmount;
                                            }
                                        }

                                        if(!empty($detail['discountAmount']) && empty($detail['discountPercentage'])) {
                                            if(!is_numeric($detail['discountAmount']) || ($detail['discountAmount'] > ($detail['unitCost'] ?? 0))) {
                                                $error[] = [
                                                    'field' => 'discountAmount',
                                                    'error' => 'Discount amount should be a numeric value and cannot be greater than unit cost'
                                                ];
                                            } else {
                                                $detail['discountPercentage'] = ($detail['discountAmount'] * 100) / ($detail['unitCost'] ?? 1);
                                            }
                                        }

                                        if ($isVATEligible) {
                                            $defaultVAT = TaxService::getVATDetailsByItem($compId, $detail['item'], $invMaster['supplierID']);
                                            $docAmount = $detail['unitCost'] ?? 0;
                                            if($defaultVAT['applicableOn'] != 1) {
                                                $docAmount = $docAmount - ($detail['discountAmount'] ?? 0);
                                            }

                                            if(!empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATPercentage']) || $detail['VATPercentage'] > 100 || $detail['VATPercentage'] < 0) {
                                                    $error[] = [
                                                        'field' => 'VATPercentage',
                                                        'error' => 'VAT% should be a numeric value and between 0 - 100'
                                                    ];
                                                } else {
                                                    $vatAmount = ($docAmount / 100) * $detail['VATPercentage'];

                                                    if(!empty($detail['VATAmount']) && $detail['VATAmount'] != $vatAmount) {
                                                        $error[] = [
                                                            'field' => 'VATPercentage',
                                                            'error' => 'VAT% and VAT Amount is not matching'
                                                        ];
                                                    }
                                                    $detail['VATAmount'] = $vatAmount;
                                                }
                                            }

                                            if(!empty($detail['VATAmount']) && empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATAmount']) || ($detail['VATAmount'] > $docAmount)) {
                                                    $error[] = [
                                                        'field' => 'VATAmount',
                                                        'error' => 'VAT amount should be a numeric value and cannot be greater than invoice amount'
                                                    ];
                                                } else {
                                                    $detail['VATPercentage'] = ($detail['VATAmount'] * 100) / $docAmount;
                                                }
                                            }
                                        }

                                        if (empty($error)) {
                                            /*** insert records for item invoice details*/
                                            $whtTotal = $whtTotal + ($detail['whtAmount'] ?? 0);
                                            $financeCategorySub = FinanceItemCategorySub::find($itemAssign->financeCategorySub);
                                            $invDetails[] = [
                                                'companySystemID' => $compId,
                                                'itemCode' => $detail['item'],
                                                'itemPrimaryCode' => $itemAssign['itemPrimaryCode'],
                                                'itemDescription' => $itemAssign['itemDescription'],
                                                'itemFinanceCategoryID' => $itemAssign['financeCategoryMaster'],
                                                'itemFinanceCategorySubID' => $itemAssign['financeCategorySub'],
                                                'financeGLcodebBSSystemID' => $financeCategorySub->financeGLcodebBSSystemID,
                                                'financeGLcodePLSystemID' => $financeCategorySub->financeGLcodePLSystemID,
                                                'includePLForGRVYN' => $financeCategorySub->includePLForGRVYN,
                                                'supplierPartNumber' => $itemAssign['secondaryItemCode'],
                                                'unitOfMeasure' => $itemAssign['itemUnitOfMeasure'],
                                                'trackingType' => (isset($itemAssign['trackingType'])) ? $itemAssign['trackingType'] : null,
                                                'noQty' => $detail['qty'],
                                                'unitCost' => $detail['unitCost'],
                                                'netAmount' => ($detail['unitCost'] - ($detail['discountAmount'] ?? 0)) * $detail['qty'],
                                                'discountPercentage' => $detail['discountPercentage'] ?? null,
                                                'discountAmount' => $detail['discountAmount'] ?? null,
                                                'comment' => $detail['comments'] ?? null,

                                                'supplierDefaultCurrencyID' => $invMaster['supplierTransactionCurrencyID'],
                                                'supplierDefaultER' => 1,
                                                'supplierItemCurrencyID' => $invMaster['supplierTransactionCurrencyID'],
                                                'foreignToLocalER' => 1,
                                                'whtApplicable' => $invMaster['whtApplicable'] ?? 0,
                                                'whtAmount' => $detail['whtAmount'] ?? null,
                                                'vatSubCategoryID' => $defaultVAT['vatSubCategoryID'] ?? null,
                                                'vatMasterCategoryID' => $defaultVAT['vatMasterCategoryID'] ?? null,
                                                'VATPercentage' => $detail['VATPercentage'] ?? null,
                                                'VATAmount' => $detail['VATAmount'] ?? 0,
                                                'VATApplicableOn' => $defaultVAT['applicableOn'] ?? null,
                                                'exempt_vat_portion' => 0,
                                                'createdUserID' => $systemUser->empID
                                            ];
                                        }
                                    }
                                }
                            }

                            if(isset($whtTotal)) {
                                $invMaster['whtAmount'] = $whtTotal;
                            }
                            $invMaster['companySystemID'] = $compId;
                            $invMaster['createdUserID'] = $systemUser->empID;
                            $invMaster['createdUserSystemID'] = $systemUser->employeeSystemID;
                            $invMaster['createdPcID'] = getenv('COMPUTERNAME');
                            unset($invMaster['details']);
                            unset($invMaster['currency']);
                            unset($invMaster['supplier']);
                        } else {
                            $error[] = [
                                'field' => 'documentType',
                                'error' => 'Document Type format is invalid'
                            ];
                        }
                    } else {
                        $error[] = [
                            'field' => 'documentType',
                            'error' => 'Document Type field is required'
                        ];
                    }

                    if (empty($error)) {
                        DB::beginTransaction();
                        $crateSupplierInvoice = self::createSupplierInvoice($invMaster, $invDetails);

                        if(!$crateSupplierInvoice['status']) {
                            $invError[] = [
                                'index' => $invoiceNo,
                                'error' => $crateSupplierInvoice['error']
                            ];
                        }
                    } else {
                        $invError[] = [
                            'index' => $invoiceNo,
                            'error' => $error
                        ];
                    }
                    $invoiceNo++;
                }
            } else {
                $invError[] = [
                    'field' => '',
                    'error' => 'No supplier invoice data found'
                ];
            }

            if(!empty($invError)) {
                Log::error('Error Log');
                Log::error($invError);
            } else {
                Log::info('Invoice created Successfully!');
            }


            $api_external_key = $this->api_external_key;
            $api_external_url = $this->api_external_url;
            if (!empty($invError)) {
                if($api_external_key != null && $api_external_url != null) {

                    $client = new Client();
                    $headers = [
                        'content-type' => 'application/json',
                        'Authorization' => 'ERP '.$api_external_key
                    ];
                    $res = $client->request('POST', $api_external_url . '/supplier_invoice_create_log', [
                        'headers' => $headers,
                        'json' => [
                            'data' => $invError
                        ]
                    ]);
                    $json = $res->getBody();
                }
            }

        } catch (\Exception $exception) {
            Log::error('Error');
            Log::error($exception->getMessage());
            Log::error('File: ' . $exception->getFile() . ' at line ' . $exception->getLine());
        }
    }

    function createSupplierInvoice($invMaster, $invDetails)
    {
        $returnData = SupplierInvoiceAPIService::storeBookingInvoice($invMaster);
        if($returnData['status'] == 'success') {
            $returnData = $returnData['data'];
            $compId = $returnData['companySystemID'];
            if($invDetails){
                if($returnData['documentType'] == 1) {
                    foreach ($invDetails as $det) {
                        $det['directInvoiceAutoID'] = $returnData['bookingSuppMasInvAutoID'];
                        $det['companyID'] = $returnData['companyID'];
                        $det['localCurrency'] = $returnData['localCurrencyID'];
                        $det['localCurrencyER'] = $returnData['localCurrencyER'];
                        $det['localAmount' ] = \Helper::roundValue($det['DIAmount'] / $returnData['localCurrencyER']);
                        $det['netAmountLocal'] = \Helper::roundValue( $det['netAmount']/ $returnData['localCurrencyER']);
                        $det['comRptCurrency'] = $returnData['companyReportingCurrencyID'];
                        $det['comRptCurrencyER'] = $returnData['companyReportingER'];
                        $det['comRptAmount'] = \Helper::roundValue($det['DIAmount'] / $returnData['companyReportingER']);
                        $det['netAmountRpt'] = \Helper::roundValue($det['netAmount'] / $returnData['companyReportingER']);
                        if ($returnData['FYBiggin']) {
                            $finYearExp = explode('-', $returnData['FYBiggin']);
                            $det['budgetYear'] = $finYearExp[0];
                        } else {
                            $det['budgetYear'] = CompanyFinanceYear::budgetYearByDate(now(), $compId);
                        }
                        if($det['VATAmount'] > 0) {
                            $det['VATAmountLocal'] = \Helper::roundValue($det['VATAmount'] / $returnData['localCurrencyER']);
                            $det['VATAmountRpt'] = \Helper::roundValue($det['VATAmount'] / $returnData['companyReportingER']);
                        }
                        DirectInvoiceDetails::create($det);
                    }
                    SupplierInvoice::updateMaster($returnData['bookingSuppMasInvAutoID']);
                } else {
                    $bookingAmountTrans = $bookingAmountLocal = $bookingAmountRpt = 0;
                    foreach ($invDetails as $det) {
                        $costPerUnitSupTransCur = $det['unitCost'] - ($det['discountAmount'] ?? 0);
                        if(TaxService::checkPOVATEligible($returnData['supplierVATEligible'], $returnData['vatRegisteredYN'])){
                            $checkVATCategory = TaxVatCategories::with(['type'])->find($det['vatSubCategoryID']);
                            if ($checkVATCategory) {
                                if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 1 && $det['exempt_vat_portion'] > 0 && $det['VATAmount'] > 0) {
                                    $exemptVAT = $det['VATAmount'] * ($det['exempt_vat_portion'] / 100);

                                    $costPerUnitSupTransCur = $costPerUnitSupTransCur + $exemptVAT;
                                } else if (isset($checkVATCategory->type->id) && $checkVATCategory->type->id == 3) {
                                    $costPerUnitSupTransCur = $costPerUnitSupTransCur + $det['VATAmount'];
                                }
                            }
                        }
                        $det['bookingSuppMasInvAutoID'] = $returnData['bookingSuppMasInvAutoID'];
                        $det['companyReportingCurrencyID'] = $returnData['companyReportingCurrencyID'];
                        $det['companyReportingER'] = $returnData['companyReportingER'];
                        $det['localCurrencyID'] = $returnData['localCurrencyID'];
                        $det['localCurrencyER'] = $returnData['localCurrencyER'];

                        $det['costPerUnitSupTransCur'] = $costPerUnitSupTransCur;
                        $currencyConversion = \Helper::currencyConversion($compId, $returnData['supplierTransactionCurrencyID'], $returnData['supplierTransactionCurrencyID'], $det['costPerUnitSupTransCur']);
                        $det['costPerUnitLocalCur'] = \Helper::roundValue($currencyConversion['localAmount']);
                        $det['costPerUnitComRptCur'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                        $currencyConversionDefault = \Helper::currencyConversion($compId, $returnData['supplierTransactionCurrencyID'], $det['supplierDefaultCurrencyID'], $det['costPerUnitSupTransCur']);
                        $det['costPerUnitSupDefaultCur'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);

                        if($det['VATAmount'] > 0) {
                            $det['VATAmountLocal'] = \Helper::roundValue($det['VATAmount'] / $returnData['localCurrencyER']);
                            $det['VATAmountRpt'] = \Helper::roundValue($det['VATAmount'] / $returnData['companyReportingER']);
                        }

                        $det['createdPcID'] = getenv('COMPUTERNAME');
                        SupplierInvoiceDirectItem::create($det);

                        $bookingAmountTrans += ($det['netAmount'] + ($det['VATAmount'] * $det['noQty']));
                        $booking = \Helper::currencyConversion($compId, $returnData['supplierTransactionCurrencyID'], $returnData['supplierTransactionCurrencyID'], $bookingAmountTrans);
                        $bookingAmountLocal += $booking['localAmount'];
                        $bookingAmountRpt += $booking['reportingAmount'];
                    }
                    $updateMaster['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
                    $updateMaster['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
                    $updateMaster['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);
                    BookInvSuppMaster::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->update($updateMaster);
                }
            }

            /*** update details before confirmation*/
            $invoiceUpdate = self::updateInvoiceDetails($returnData);
            if(!$invoiceUpdate['status']) {
                DB::rollBack();
                return [
                    'status' => false,
                    'error' => $invoiceUpdate["message"]
                ];
            }

            $params = array(
                'autoID' => $returnData['bookingSuppMasInvAutoID'],
                'company' => $compId,
                'document' => $returnData['documentSystemID'],
                'segment' => '',
                'category' => '',
                'amount' => '',
                'isAutoCreateDocument' => 1
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                DB::rollBack();
                return [
                    'status' => false,
                    'error' => $confirm["message"]
                ];
            } else {
                $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($returnData['documentSystemID'],$returnData['bookingSuppMasInvAutoID']);
                $autoApproveParams['db'] = $this->db;
                $autoApproveParams['supplierPrimaryCode'] = $returnData['supplierID'];
                $approveDocument = Helper::approveDocument($autoApproveParams);
                if ($approveDocument["success"]) {
                    $invId[] = $returnData['bookingSuppMasInvAutoID'];
                    $this->storeToDocumentSystemMapping(11,$invId,$this->authorization);
                    DB::commit();
                    return [
                        'status' => true,
                        'error' => 'Invoice created successfully!'
                    ];
                }
                else {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'error' => 'An error occurred in supplier invoice creation'
                    ];
                }
            }
        } else {
            DB::rollBack();
            return [
                'status' => false,
                'error' => 'An error occurred in supplier invoice creation'
            ];
        }
    }

    private function updateInvoiceDetails($returnData)
    {
        $updateRecord = [];
        $companyID = $returnData['companySystemID'] ?? null;
        $returnData['retentionPercentage'] = $returnData['retentionPercentage'] ?? 0;
        if($returnData['retentionPercentage'] > 0) {
            $slug = "retention-control-account";
            $isConfigured = SystemGlCodeScenario::where('slug',$slug)->first();
            $isDetailConfigured = ($isConfigured) ? SystemGlCodeScenarioDetail::where('systemGLScenarioID', $isConfigured->id)->where('companySystemID', $companyID)->first() : null;
            if($isConfigured && $isDetailConfigured) {
                if ($isConfigured->isActive != 1 || $isDetailConfigured->chartOfAccountSystemID == null || $isDetailConfigured->chartOfAccountSystemID == 0) {
                    return [
                        'status' => false,
                        'error' => 'Chart of account is not configured for retention control account'
                    ];
                }
                $isChartOfAccountConfigured = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $isDetailConfigured->chartOfAccountSystemID)->where('companySystemID', $isDetailConfigured->companySystemID)->first();
                if($isChartOfAccountConfigured){
                    if ($isChartOfAccountConfigured->isActive != 1 || $isChartOfAccountConfigured->chartOfAccountSystemID == null || $isChartOfAccountConfigured->isAssigned != -1 || $isChartOfAccountConfigured->chartOfAccountSystemID == 0 || $isChartOfAccountConfigured->companySystemID == 0 || $isChartOfAccountConfigured->companySystemID == null) {
                        return [
                            'status' => false,
                            'error' => 'Chart of account is not configured for retention control account'
                        ];
                    }
                }
                else{
                    return [
                        'status' => false,
                        'error' => 'Chart of account is not configured for retention control account'
                    ];
                }
            }
            else{
                return [
                    'status' => false,
                    'error' => 'Chart of account is not configured for retention control account'
                ];
            }
        }
        $directAmountTrans = DirectInvoiceDetails::where('directInvoiceAutoID', $returnData['bookingSuppMasInvAutoID'])
            ->sum('DIAmount');

        $directAmountLocal = DirectInvoiceDetails::where('directInvoiceAutoID', $returnData['bookingSuppMasInvAutoID'])
            ->sum('localAmount');

        $directAmountReport = DirectInvoiceDetails::where('directInvoiceAutoID', $returnData['bookingSuppMasInvAutoID'])
            ->sum('comRptAmount');

        $detailTaxSumTrans = Taxdetail::where('documentSystemCode', $returnData['bookingSuppMasInvAutoID'])
            ->where('documentSystemID', 11)
            ->sum('amount');

        $detailTaxSumLocal = Taxdetail::where('documentSystemCode', $returnData['bookingSuppMasInvAutoID'])
            ->where('documentSystemID', 11)
            ->sum('localAmount');

        $detailTaxSumReport = Taxdetail::where('documentSystemCode', $returnData['bookingSuppMasInvAutoID'])
            ->where('documentSystemID', 11)
            ->sum('rptAmount');

        if($detailTaxSumTrans > 0 ){
            if(empty(TaxService::getInputVATGLAccount($companyID))){
                return [
                    'status' => false,
                    'error' => 'Input VAT GL Account not configured'
                ];
            }
            $inputVATGL = TaxService::getInputVATGLAccount($companyID);
            $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatGLAccountAutoID, $companyID);
            if (!$checkAssignedStatus) {
                return [
                    'status' => false,
                    'error' => 'Input VAT GL Account not assigned to company'
                ];
            }
        }

        if($returnData['documentType'] == 1)
        {
            $bookingAmountTrans = $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $directAmountReport + $detailTaxSumReport;

            $updateRecord['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $updateRecord['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $updateRecord['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);

            /*** retention tax computation */
            if($returnData['retentionPercentage'] > 0) {
                $vatTrans = TaxService::processDirectSupplierInvoiceVAT($returnData['bookingSuppMasInvAutoID'], $returnData['documentSystemID']);
                $updateRecord['retentionVatAmount'] = $vatTrans['masterVATTrans'] *  $returnData['retentionPercentage'] / 100;
            }

            if($returnData['whtApplicable'] == 1) {
                $directNetAmount = DirectInvoiceDetails::where('directInvoiceAutoID', $returnData['bookingSuppMasInvAutoID'])
                    ->sum('netAmount');
                /** wht percentage computation */
                $updateRecord['whtPercentage'] = ($returnData['whtAmount'] / $directNetAmount) * 100;
            }

        } elseif ($returnData['documentType'] == 3) {
            $grvAmountTransaction = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])
                ->sum('netAmount');
            $grvAmountLocal = SupplierInvoiceDirectItem::selectRaw('SUM(VATAmount * noQty) as VATAmount')->where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])
                ->first();

            $totatlDirectItemTrans = $grvAmountTransaction + ($grvAmountLocal->VATAmount ?? 0);

            $currencyConversionDire = \Helper::currencyConversion($companyID, $returnData['supplierTransactionCurrencyID'], $returnData['supplierTransactionCurrencyID'], $totatlDirectItemTrans);
            $bookingAmountTrans = $totatlDirectItemTrans + $directAmountTrans + $detailTaxSumTrans;
            $bookingAmountLocal = $currencyConversionDire['localAmount'] + $directAmountLocal + $detailTaxSumLocal;
            $bookingAmountRpt = $currencyConversionDire['reportingAmount'] + $directAmountReport + $detailTaxSumReport;

            $updateRecord['bookingAmountTrans'] = \Helper::roundValue($bookingAmountTrans);
            $updateRecord['bookingAmountLocal'] = \Helper::roundValue($bookingAmountLocal);
            $updateRecord['bookingAmountRpt'] = \Helper::roundValue($bookingAmountRpt);

            /*** retention tax computation */
            if($returnData['retentionPercentage'] > 0) {
                $vatTrans = TaxService::processSupplierInvoiceItemsVAT($returnData['bookingSuppMasInvAutoID']);
                $updateRecord['retentionVatAmount'] = $vatTrans['masterVATTrans'] *  $returnData['retentionPercentage'] / 100;
            }

            if($returnData['whtApplicable'] == 1) {
                /** wht percentage computation */
                $updateRecord['whtPercentage'] = ($returnData['whtAmount'] / $grvAmountTransaction) * 100;
            }

            $vatTotal = SupplierInvoiceDirectItem::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->sum('VATAmount');
            if($vatTotal > 0){
                if (empty(TaxService::getInputVATTransferGLAccount($companyID))){
                    return [
                        'status' => false,
                        'error' => 'Input VAT Transfer GL Account not configured'
                    ];
                }

                $inputVATGL = TaxService::getInputVATTransferGLAccount($companyID);
                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $companyID);

                if (!$checkAssignedStatus) {
                    return [
                        'status' => false,
                        'error' => 'Input VAT Transfer GL Account not assigned to company'
                    ];
                }
            }
        }

        if(!empty($updateRecord)) {
            BookInvSuppMaster::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->update($updateRecord);
        }
        return [
            'status' => true,
            'error' => 'Invoice details updated'
        ];
    }
}
