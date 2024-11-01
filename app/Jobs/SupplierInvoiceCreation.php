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
use App\Models\ItemCategoryTypeMaster;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\SupplierMaster;
use App\Models\SystemGlCodeScenario;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\Tax;
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

class SupplierInvoiceCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DocumentSystemMappingTrait;
    public $record;
    public $input;
    public $timeout = 500;
    public $db;
    public $apiExternalKey;
    public $apiExternalUrl;
    public $authorization;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $db, $apiExternalKey, $apiExternalUrl, $authorization)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->input = $input;
        $this->db = $db;
        $this->apiExternalKey = $apiExternalKey;
        $this->apiExternalUrl = $apiExternalUrl;
        $this->authorization = $authorization;
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
        $returnData = [];
        try {
            /*** insert a try catch */
            $systemUser = UserTypeService::getSystemEmployee();
            $input = $this->input;
            if (!empty($input[0])) {
                $compId = $input['company_id'];
                $company = Company::where('companySystemID', $compId)->first();
                if (empty($company)) {
                    $responseData[] = [
                        "success" => false,
                        "message" => "Validation Failed",
                        "code" => 402,
                        "errors" => [
                            'fieldErrors' => [
                                'field' => '',
                                'message' => 'Company not found'
                            ],
                        ]
                    ];
                }
                $invoiceNo = 1;
                foreach ($input[0] as $value) {
                    $validationError = $headerDataError = [];
                    $invDetails = [];
                    $invMaster = $value;
                    if (!empty($invMaster['documentType'])) {
                        if (in_array($invMaster['documentType'], [1,2])) {
                            if($invMaster['documentType'] == 2) {
                                $invMaster['documentType'] = 3;
                            }
                            if (empty($invMaster['supplierInvoiceNo'])) {
                                $validationError = [
                                    'field' => 'supplierInvoiceNo',
                                    'message' => 'Supplier Invoice No field is required'
                                ];
                            }

                            if (empty($invMaster['comments'])) {
                                $validationError[] = [
                                    'field' => 'comments',
                                    'message' => 'Narration field is required'
                                ];
                            }

                            if (empty($invMaster['supplierInvoiceDate'])) {
                                $validationError[] = [
                                    'field' => 'supplierInvoiceDate',
                                    'message' => 'Supplier Invoice Date field is required'
                                ];
                            }

                            if (empty($invMaster['bookingDate'])) {
                                $validationError[] = [
                                    'field' => 'bookingDate',
                                    'message' => 'Document Date field is required'
                                ];
                            } else {
                                $bookingDate = Carbon::parse($invMaster['bookingDate']);
                                $currentDate = Carbon::now()->startOfDay();
                                if ($bookingDate->gt($currentDate)) {
                                    $headerDataError[] = [
                                        'field' => '',
                                        'message' => 'The booking date must be today or before.'
                                    ];
                                } else {
                                    $financeYear = CompanyFinanceYear::where('companySystemID', $compId)->where('isActive', -1)->where('bigginingDate', "<=", $invMaster['bookingDate'])->where('endingDate', ">=", $invMaster['bookingDate'])->first();
                                    if (empty($financeYear)) {
                                        $headerDataError[] = [
                                            'field' => '',
                                            'message' => 'Finance Year not found'
                                        ];
                                    } else {
                                        $invMaster['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
                                    }

                                    $financePeriod = CompanyFinancePeriod::where('companySystemID', $compId)->where('departmentSystemID', 1)->where('dateFrom', "<=", $invMaster['bookingDate'])->where('dateTo', ">=", $invMaster['bookingDate'])->where('isActive', -1)->first();
                                    if (empty($financePeriod)) {
                                        $headerDataError[] = [
                                            'field' => '',
                                            'message' => 'Finance Period not found'
                                        ];
                                    } else {
                                        $invMaster['companyFinancePeriodID'] = $financePeriod['companyFinancePeriodID'];
                                        $invMaster['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
                                        $invMaster['FYPeriodDateTo'] = $financePeriod['dateTo'];
                                    }
                                }
                            }

                            if($invMaster['documentType'] == 3) {
                                if(empty($invMaster['segment'])) {
                                    $validationError[] = [
                                        'field' => 'segment',
                                        'message' => 'Segment field is required'
                                    ];
                                } else {
                                    $segment = SegmentMaster::where('ServiceLineCode',$invMaster['segment'])
                                        ->where('isActive', 1)
                                        ->where('isDeleted', 0)
                                        ->where('companySystemID', $compId)
                                        ->first();
                                    if(!$segment){
                                        $headerDataError[] = [
                                            'field' => 'segment',
                                            'message' => 'Segment not found'
                                        ];
                                    } else {
                                        $invMaster['serviceLineSystemID'] = $segment['serviceLineSystemID'];
                                    }
                                }

                                if (empty($invMaster['warehouse'])) {
                                    $validationError[] = [
                                        'field' => 'warehouse',
                                        'message' => 'Warehouse field is required'
                                    ];
                                } else {
                                    $warehouse = WarehouseMaster::where('wareHouseCode', $invMaster['warehouse'])
                                        ->where('isActive', 1)
                                        ->where('companySystemID', $compId)
                                        ->first();

                                    if(!$warehouse){
                                        $headerDataError[] = [
                                            'field' => 'warehouse',
                                            'message' => 'Warehouse not found'
                                        ];
                                    } else {
                                        $invMaster['wareHouseSystemCode'] = $warehouse['wareHouseSystemCode'];
                                    }
                                }
                            }

                            $invMaster['supplierID'] = null;
                            if (empty($invMaster['supplier'])) {
                                $validationError[] = [
                                    'field' => 'supplier',
                                    'message' => 'supplier field is required'
                                ];
                                $invMaster['supplierID'] = null;
                            } else {
                                $supplierExist = SupplierAssigned::where(function($query) use ($invMaster) {
                                        $query->where('primarySupplierCode', $invMaster['supplier'])
                                             ->orWhere('registrationNumber', $invMaster['supplier']);
                                    })
                                    ->where('companySystemID', $compId)
                                    ->where('isActive', 1)
                                    ->where('isAssigned', -1)
                                    ->first();

                                if(empty($supplierExist)) {
                                    $headerDataError[] = [
                                        'field' => 'supplier',
                                        'message' => 'supplier not found'
                                    ];
                                } else {
                                    $invMaster['supplierID'] = $supplierExist['supplierCodeSytem'];
                                    $supplier = SupplierMaster::where('supplierCodeSystem', $supplierExist['supplierCodeSytem'])->first();
                                    $invMaster['whtApplicableYN'] = $supplier['whtApplicableYN'];
                                    if(isset($supplier['whtType'])) {
                                        $invMaster['whtType'] = $supplier['whtType'];
                                    } else {
                                        $invMaster['whtType'] = 0;
                                    }

                                    if(isset($invMaster['bookingDate'])) {
                                        $validatorResult = \Helper::checkBlockSuppliers($invMaster['bookingDate'],$invMaster['supplierID']);
                                        if (!$validatorResult['success']) {
                                            $headerDataError[] = [
                                                'field' => 'supplier',
                                                'message' => 'The selected supplier has been blocked'
                                            ];
                                        }
                                    }

                                    if (empty($invMaster['currency'])) {
                                        $validationError[] = [
                                            'field' => 'currency',
                                            'message' => 'Currency field is required'
                                        ];
                                    } else {
                                        $currency = SupplierCurrency::join('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                                            ->where('currencymaster.CurrencyCode', $invMaster['currency'])
                                            ->where('supplierCodeSystem', $supplierExist['supplierCodeSytem'])
                                            ->where('isAssigned', -1)
                                            ->first();

                                        if(!$currency){
                                            $headerDataError[] = [
                                                'field' => 'currency',
                                                'message' => 'Currency is invalid'
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
                                            $headerDataError[] = [
                                                'field' => 'supplierInvoiceNo',
                                                'message' => 'Entered supplier invoice number was already used (' . $invMaster['supplierInvoiceNo'] . '). Please check again'
                                            ];
                                        }
                                    }

                                    if(!empty($invMaster['retentionPercentage'])) {
                                        if(!is_numeric($invMaster['retentionPercentage']) || $invMaster['retentionPercentage'] < 0 || $invMaster['retentionPercentage'] > 100) {
                                            $headerDataError[] = [
                                                'field' => 'retentionPercentage',
                                                'message' => 'Retention % should be a numeric value and between 0 - 100'
                                            ];
                                        }

                                        if(!empty($invMaster['bookingDate'])) {
                                            $creditPeriod = SupplierMaster::where('supplierCodeSystem', $invMaster['supplierID'])->value('creditPeriod');
                                            $invMaster['retentionDueDate'] = Carbon::parse($invMaster['bookingDate'])->addDays(($creditPeriod ?? 0));
                                        }
                                    }

                                    if (!empty($invMaster['retentionAmount']) && !is_numeric($invMaster['retentionAmount'])) {
                                        $headerDataError[] = [
                                            'field' => 'retentionAmount',
                                            'message' => 'Retention amount should be a numeric value'
                                        ];
                                    }

                                    if(!empty($invMaster['bookingDate']) && (!empty($invMaster['retentionPercentage']) || !empty($invMaster['retentionAmount']))) {
                                        $creditPeriod = SupplierMaster::where('supplierCodeSystem', $invMaster['supplierID'])->value('creditPeriod');
                                        $invMaster['retentionDueDate'] = Carbon::parse($invMaster['bookingDate'])->addDays(($creditPeriod ?? 0));
                                    }
                                }
                            }

                            if (empty($invMaster['details'])) {
                                $headerDataError[] = [
                                    'field' => '',
                                    'message' => 'Supplier invoice details not found'
                                ];
                            } else {
                                /** Supplier direct invoice */
                                $whtTotal = 0;
                                $isVATEligible = TaxService::checkCompanyVATEligible($compId);
                                $detailIndex = 1;
                                $detailsError = [];
                                foreach ($invMaster['details'] as $detail) {
                                    $detailsDataError = [];

                                    if((!empty($detail['VATPercentage']) || !empty($detail['VATAmount'])) && !empty($detail['whtAmount'])) {
                                        $detailsDataError[] = [
                                            'field' => 'whtAmount',
                                            'message' => 'Cannot allocate WHT amount and VAT amount for same detail'
                                        ];
                                    }

                                    if (!empty($detail['whtAmount'])) {
                                        if ($supplierExist && $invMaster['whtApplicableYN'] == 0) {
                                            $detailsDataError[] = [
                                                'field' => 'whtAmount',
                                                'message' => 'Cannot allocate a WHT amount as the supplier is not applicable for WHT'
                                            ];
                                            $detail['whtAmount'] = 0;
                                        }

                                        if($detail['whtAmount'] < 0) {
                                            $detailsDataError[] = [
                                                'field' => 'whtAmount',
                                                'message' => 'whtAmount should be a positive value'
                                            ];
                                        }
                                        $invMaster['whtApplicable'] =  1;
                                    }

                                    if((!empty($detail['VATPercentage']) || !empty($detail['VATAmount'])) && !$isVATEligible) {
                                        $detailsDataError[] = [
                                            'field' => 'VATPercentage',
                                            'message' => 'Company is not vat registered'
                                        ];
                                    }

                                    if ($invMaster['documentType'] == 1) {
                                        if(empty($detail['glCode'])) {
                                            $validationError[] = [
                                                'field' => 'glCode',
                                                'message' => 'GlCode field is required'
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
                                                $detailsDataError[] = [
                                                    'field' => 'glCode',
                                                    'message' => 'GlCode not found'
                                                ];
                                            }
                                        }
                                        if(empty($detail['segment'])) {
                                            $validationError[] = [
                                                'field' => 'segment',
                                                'message' => 'Segment field is required'
                                            ];
                                        } else {
                                            $detSegment = SegmentMaster::where('ServiceLineCode',$detail['segment'])
                                                ->where('isActive', 1)
                                                ->where('isDeleted', 0)
                                                ->where('companySystemID', $compId)
                                                ->first();
                                            if(!$detSegment){
                                                $detailsDataError[] = [
                                                    'field' => 'segment',
                                                    'message' => 'Segment not found'
                                                ];
                                            }
                                        }

                                        if (!empty($detail['amount'])) {
                                            if (!is_numeric($detail['amount']) || $detail['amount'] <= 0) {
                                                $detailsDataError[] = [
                                                    'field' => 'amount',
                                                    'message' => 'Amount field should be numeric and greater than zero'
                                                ];
                                            }
                                        } else {
                                            $validationError[] = [
                                                'field' => 'amount',
                                                'message' => 'Amount field is required'
                                            ];
                                        }

                                        if ($isVATEligible) {
                                            $defaultVAT = TaxService::getDefaultVAT($compId, $invMaster['supplierID']);
                                            if($defaultVAT['vatMasterCategoryID'] == null) {
                                                $taxDetails = TaxVatCategories::whereHas('tax', function ($q) use ($compId) {
                                                    $q->where('companySystemID', $compId)
                                                        ->where('isActive', 1)
                                                        ->where('taxCategory', 2);
                                                })
                                                    ->whereHas('main', function ($q) {
                                                        $q->where('isActive', 1);
                                                    })
                                                    ->where('isActive', 1)
                                                    ->where('subCatgeoryType', 1)
                                                    ->first();

                                                if (!empty($taxDetails)) {
                                                    $defaultVAT['vatSubCategoryID'] = $taxDetails->taxVatSubCategoriesAutoID;
                                                    $defaultVAT['vatMasterCategoryID'] = $taxDetails->mainCategory;
                                                } else {
                                                    $headerDataError[] = [
                                                        'field' => 'VATPercentage',
                                                        'message' => 'VAT Category not found'
                                                    ];
                                                }
                                            }
                                            $docAmount = ($detail['amount'] ?? 0);
                                            if(!empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATPercentage']) || $detail['VATPercentage'] > 100 || $detail['VATPercentage'] < 0) {
                                                    $detailsDataError[] = [
                                                        'field' => 'VATPercentage',
                                                        'message' => 'VAT % should be a numeric value and between 0 - 100'
                                                    ];
                                                } else {
                                                    $vatAmount = ($docAmount / 100) * $detail['VATPercentage'];

                                                    if(!empty($detail['VATAmount']) && $detail['VATAmount'] != $vatAmount) {
                                                        $detailsDataError[] = [
                                                            'field' => 'VATPercentage',
                                                            'message' => 'VAT % and VAT Amount is not matching'
                                                        ];
                                                    }
                                                    $detail['VATAmount'] = $vatAmount;
                                                }
                                            }

                                            if(!empty($detail['VATAmount']) && empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATAmount']) || ($detail['VATAmount'] > $docAmount) || ($detail['VATAmount'] < 0)) {
                                                    $detailsDataError[] = [
                                                        'field' => 'VATAmount',
                                                        'message' => 'VAT amount should be a positive numeric value and cannot be greater than invoice amount'
                                                    ];
                                                } else {
                                                    $detail['VATPercentage'] = ($detail['VATAmount'] * 100) / $docAmount;
                                                }
                                            }
                                        }

                                        if (empty($validationError) && empty($headerDataError) && empty($detailsDataError)) {
                                            /*** insert records for direct invoice details */
                                            $whtTotal = $whtTotal + ($detail['whtAmount'] ?? 0);
                                            $invDetails[] = [
                                                'companySystemID' => $compId,
                                                'serviceLineSystemID' => $detSegment['serviceLineSystemID'],
                                                'serviceLineCode' => $detSegment['ServiceLineCode'],
                                                'chartOfAccountSystemID' => $chartOfAccountAssign['chartOfAccountSystemID'],
                                                'glCode' => $chartOfAccountAssign['AccountCode'],
                                                'glCodeDes' => $chartOfAccountAssign['AccountDescription'],
                                                'comments' => $detail['comments'] ?? null,
                                                'DIAmountCurrency' => $invMaster['supplierTransactionCurrencyID'],
                                                'DIAmountCurrencyER' => 1,
                                                'DIAmount' => $detail['amount'],
                                                'netAmount' => $detail['amount'],
                                                'whtApplicable' => isset($invMaster['whtApplicable']) ? $invMaster['whtApplicable'] : 0,
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
                                            if (!is_numeric($detail['qty']) || $detail['qty'] < 1) {
                                                $detailsDataError[] = [
                                                    'field' => 'qty',
                                                    'message' => 'Quantity field should be numeric and greater than zero'
                                                ];
                                            }
                                        } else {
                                            $validationError[] = [
                                                'field' => 'qty',
                                                'message' => 'Quantity field is required'
                                            ];
                                        }

                                        if (!empty($detail['unitCost'])) {
                                            if (!is_numeric($detail['unitCost']) || $detail['unitCost'] < 0) {
                                                $detailsDataError[] = [
                                                    'field' => 'unitCost',
                                                    'message' => 'Unit cost field should be numeric and a positive value'
                                                ];
                                            }
                                        } else {
                                            $validationError[] = [
                                                'field' => 'unitCost',
                                                'message' => 'Unit cost field is required'
                                            ];
                                        }

                                        if(!empty($detail['item'])) {
                                            $itemAssign = ItemAssigned::with(['item_master'])->where('itemCodeSystem', $detail['item'])
                                                ->where('companySystemID', $compId)
                                                ->where('isActive', 1)
                                                ->where('isAssigned', -1)
                                                ->whereHas('item_category_type', function ($query) {
                                                    $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems());
                                                })
                                                ->when((isset($input['fixedAsset']) && $input['fixedAsset'] == 0), function($query) {
                                                    $query->whereIn('financeCategoryMaster', [1,2,4]);
                                                })
                                                ->first();

                                            if(!$itemAssign) {
                                                $detailsDataError[] = [
                                                    'field' => 'item',
                                                    'message' => 'Item not found'
                                                ];
                                            } else {
                                                if(!empty($invDetails)) {
                                                    $names = collect($invDetails)->pluck('itemCode');
                                                    if ($names->contains($detail['item'])) {
                                                        $detailsDataError[] = [
                                                            'field' => 'item',
                                                            'message' => 'Item is already added to the details'
                                                        ];
                                                    }
                                                }
                                            }
                                        } else {
                                            $validationError[] = [
                                                'field' => 'item',
                                                'message' => 'Item field is required'
                                            ];
                                        }

                                        if (!empty($detail['discountPercentage'])) {
                                            if ($detail['discountPercentage'] > 100 || $detail['discountPercentage'] < 0) {
                                                $detailsDataError[] = [
                                                    'field' => 'discountPercentage',
                                                    'message' => 'Discount % should be a numeric value and between 0 - 100'
                                                ];
                                            } else {
                                                $discountAmount = (($detail['unitCost'] ?? 0) / 100) * $detail['discountPercentage'];
                                                if(!empty($detail['discountAmount']) && $detail['discountAmount'] != $discountAmount) {
                                                    $detailsDataError[] = [
                                                        'field' => 'discountAmount',
                                                        'message' => 'Discount % and Discount Amount is not matching'
                                                    ];
                                                }
                                                $detail['discountAmount'] = $discountAmount;
                                            }
                                        }

                                        if(!empty($detail['discountAmount']) && empty($detail['discountPercentage'])) {
                                            if(!is_numeric($detail['discountAmount']) || ($detail['discountAmount'] > ($detail['unitCost'] ?? 0)) || ($detail['discountAmount'] < 0)) {
                                                $detailsDataError[] = [
                                                    'field' => 'discountAmount',
                                                    'message' => 'Discount amount should be a positive numeric value and cannot be greater than unit cost'
                                                ];
                                            } else {
                                                $detail['discountPercentage'] = ($detail['discountAmount'] * 100) / ($detail['unitCost'] ?? 1);
                                            }
                                        }

                                        if ($isVATEligible) {
                                            $defaultVAT = TaxService::getVATDetailsByItem($compId, $detail['item'], $invMaster['supplierID']);
                                            if($defaultVAT['vatMasterCategoryID'] == null) {
                                                $taxDetails = TaxVatCategories::whereHas('tax', function ($q) use ($compId) {
                                                    $q->where('companySystemID', $compId)
                                                        ->where('isActive', 1)
                                                        ->where('taxCategory', 2);
                                                })
                                                    ->whereHas('main', function ($q) {
                                                        $q->where('isActive', 1);
                                                    })
                                                    ->where('isActive', 1)
                                                    ->where('subCatgeoryType', 1)
                                                    ->first();

                                                if (!empty($taxDetails)) {
                                                    $defaultVAT['vatSubCategoryID'] = $taxDetails->taxVatSubCategoriesAutoID;
                                                    $defaultVAT['vatMasterCategoryID'] = $taxDetails->mainCategory;
                                                    $defaultVAT['applicableOn'] = $taxDetails->applicableOn;
                                                } else {
                                                    $headerDataError[] = [
                                                        'field' => 'VATPercentage',
                                                        'message' => 'VAT Category not found'
                                                    ];
                                                }
                                            }
                                            $docAmount = $detail['unitCost'] ?? 0;
                                            if($defaultVAT['applicableOn'] != 1) {
                                                $docAmount = $docAmount - ($detail['discountAmount'] ?? 0);
                                            }

                                            if(!empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATPercentage']) || $detail['VATPercentage'] > 100 || $detail['VATPercentage'] < 0) {
                                                    $detailsDataError[] = [
                                                        'field' => 'VATPercentage',
                                                        'message' => 'VAT % should be a numeric value and between 0 - 100'
                                                    ];
                                                } else {
                                                    $vatAmount = ($docAmount / 100) * $detail['VATPercentage'];

                                                    if(!empty($detail['VATAmount']) && $detail['VATAmount'] != $vatAmount) {
                                                        $detailsDataError[] = [
                                                            'field' => 'VATPercentage',
                                                            'message' => 'VAT % and VAT Amount is not matching'
                                                        ];
                                                    }
                                                    $detail['VATAmount'] = $vatAmount;
                                                }
                                            }

                                            if(!empty($detail['VATAmount']) && empty($detail['VATPercentage'])) {
                                                if(!is_numeric($detail['VATAmount']) || ($detail['VATAmount'] > $docAmount)) {
                                                    $detailsDataError[] = [
                                                        'field' => 'VATAmount',
                                                        'message' => 'VAT amount should be a numeric value and cannot be greater than invoice amount'
                                                    ];
                                                } else {
                                                    $detail['VATPercentage'] = ($detail['VATAmount'] * 100) / $docAmount;
                                                }
                                            }
                                        }

                                        if (empty($validationError) && empty($headerDataError) && empty($detailsDataError)) {
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
                                                'trackingType' => isset($itemAssign['trackingType']) ? $itemAssign['trackingType'] : null,
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
                                                'whtApplicable' => isset($invMaster['whtApplicable']) ? $invMaster['whtApplicable'] : 0,
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

                                    if(!empty($detailsDataError)) {
                                        $detailsError[] = [
                                            'index' => $detailIndex,
                                            'error' => $detailsDataError
                                        ];
                                    }
                                    $detailIndex++;
                                }
                            }

                            if(isset($whtTotal)) {
                                $invMaster['whtAmount'] = $whtTotal;
                            }
                            $invMaster['companySystemID'] = $compId;
                            $invMaster['createdUserID'] = $systemUser->empID;
                            $invMaster['createdUserSystemID'] = $systemUser->employeeSystemID;
                            $invMaster['createdPcID'] = getenv('COMPUTERNAME');
                            $invMaster['whtApplicable'] = isset($invMaster['whtApplicable']) ? $invMaster['whtApplicable'] : 0;
                            unset($invMaster['details']);
                            unset($invMaster['currency']);
                            unset($invMaster['supplier']);
                        } else {
                            $headerDataError[] = [
                                'field' => 'documentType',
                                'message' => 'Document Type format is invalid'
                            ];
                        }
                    } else {
                        $validationError[] = [
                            'field' => 'documentType',
                            'message' => 'Document Type field is required'
                        ];
                    }

                    if(empty($headerDataError) && empty($validationError) && empty($detailsError))
                    {
                        DB::beginTransaction();
                        $createSupplierInvoice = self::createSupplierInvoice($invMaster, $invDetails);

                        if(!$createSupplierInvoice['status']) {
                            $errors =
                                ['identifier' =>
                                    [
                                        'unique-key' => isset($invMaster['supplierInvoiceNo']) ? $invMaster['supplierInvoiceNo']: "",
                                        'index' => $invoiceNo
                                    ],
                                    'fieldErrors' => [],
                                    'headerData' => $createSupplierInvoice['error'],
                                    'detailData' => []
                                ];

                            $responseData[] = [
                                "success" => false,
                                "message" => "Validation Failed",
                                "code" => 402,
                                "errors" => $errors
                            ];
                        } else {
                            $responseData[] = [
                                "success" => true,
                                "message" => "Invoice created Successfully!",
                                "code" => 200,
                                "data" => [
                                    'reference' => isset($invMaster['supplierInvoiceNo']) ? $invMaster['supplierInvoiceNo']: "",
                                    'index' => $invoiceNo,
                                    'invoiceNumber' => $createSupplierInvoice['invoiceCode'] ?? ''
                                ]
                            ];
                        }
                    } else {
                        if(empty($headerDataError)) {
                            $headerDataError = [
                                'status' => true,
                                'errors' => []
                            ];
                        } else {
                            $headerDataError = [
                                'status' => false,
                                'errors' => $headerDataError
                            ];
                        }

                        if(empty($detailsError)) {
                            $detailsError = [
                                'status' => true,
                                'errors' => []
                            ];
                        } else {
                            $detailsError = [
                                'status' => false,
                                'errors' => $detailsError
                            ];
                        }

                        $errors =
                            ['identifier' =>
                                [
                                    'unique-key' => isset($invMaster['supplierInvoiceNo']) ? $invMaster['supplierInvoiceNo']: "",
                                    'index' => $invoiceNo
                                ],
                                'fieldErrors' => $validationError,
                                'headerData' => $headerDataError,
                                'detailData' => $detailsError
                            ];

                        $responseData[] = [
                            "success" => false,
                            "message" => "Validation Failed",
                            "code" => 402,
                            "errors" => $errors
                        ];
                    }
                    $invoiceNo++;
                }
            } else {
                $responseData[] = [
                    "success" => false,
                    "message" => "Validation Failed",
                    "code" => 402,
                    "errors" => [
                        'fieldErrors' => [
                            'field' => '',
                            'message' => 'No supplier invoice data found'
                        ],
                    ]
                ];
            }

            Log::error($responseData);
            $apiExternalKey = $this->apiExternalKey;
            $apiExternalUrl = $this->apiExternalUrl;
            if($apiExternalKey != null && $apiExternalUrl != null) {
                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$apiExternalKey
                ];
                $res = $client->request('POST', $apiExternalUrl . '/supplier_invoice_create_log', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $responseData
                    ]
                ]);
                $json = $res->getBody();
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

            $bookInvSuppMaster = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->first();
            if(!empty($invMaster['retentionPercentage']) && !empty($invMaster['retentionAmount'])) {
                $retentionAmount = ($bookInvSuppMaster->bookingAmountTrans / 100) * $invMaster['retentionPercentage'];
                if($retentionAmount != $invMaster['retentionAmount']) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'error' => [
                            'field' => 'retentionPercentage',
                            'message' => 'Retention % and retention amount is not matching'
                        ]
                    ];
                }
            } else
                if(!empty($invMaster['retentionPercentage']) && empty($invMaster['retentionAmount'])) {
                    $retentionAmount = ($bookInvSuppMaster->bookingAmountTrans / 100) * $invMaster['retentionPercentage'];
                    BookInvSuppMaster::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->update(['retentionAmount' => $retentionAmount]);
            } else
                if(empty($invMaster['retentionPercentage']) && !empty($invMaster['retentionAmount'])) {
                    if($invMaster['retentionAmount'] > $bookInvSuppMaster->bookingAmountTrans) {
                        DB::rollBack();
                        return [
                            'status' => false,
                            'error' => [
                                'field' => 'retentionAmount',
                                'message' => 'Retention amount cannot be greater than invoice amount'
                            ]
                        ];
                    }
                    $retentionPercentage = ($invMaster['retentionAmount'] * 100) / $bookInvSuppMaster->bookingAmountTrans;
                    BookInvSuppMaster::where('bookingSuppMasInvAutoID', $returnData['bookingSuppMasInvAutoID'])->update(['retentionPercentage' => $retentionPercentage]);
            }

            /*** update details before confirmation*/
            $invoiceUpdate = self::updateInvoiceDetails($returnData);
            if(!$invoiceUpdate['status']) {
                DB::rollBack();
                return [
                    'status' => false,
                    'error' => $invoiceUpdate["error"]
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
                        'error' => 'Invoice created successfully!',
                        'invoiceCode' => $returnData['bookingInvCode']
                    ];
                }
                else {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'error' => $approveDocument['message']
                    ];
                }
            }
        } else {
            DB::rollBack();
            return [
                'status' => false,
                'error' => $returnData['message']
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
