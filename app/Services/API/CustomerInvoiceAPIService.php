<?php

namespace App\Services\API;

use App\helper\Helper;
use App\helper\inventory;
use App\helper\ItemTracking;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CustomerAssigned;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\DeliveryOrder;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\PurchaseReturn;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SegmentAssigned;
use App\Models\SegmentMaster;
use App\Models\StockTransfer;
use App\Models\Taxdetail;
use App\Models\TaxVatCategories;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\WarehouseMaster;
use App\Services\ChartOfAccountValidationService;
use App\Services\DocumentAutoApproveService;
use App\Services\UserTypeService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class CustomerInvoiceAPIService extends AppBaseController
{
    private static function validateMasterData($request): array {

        $errorData = $fieldErrors = [];

        // Validate Invoice Type
        if (isset($request['invoice_type'])) {
            if(is_int($request['invoice_type'])) {
                if (in_array($request['invoice_type'], [1,2])) {
                    $invoiceType = ($request['invoice_type'] == 1) ? 0 : 2;

                    if($invoiceType == 2) {
                        // Validate Sales Type
                        if (isset($request['salesType'])) {
                            if(!in_array($request['salesType'], [1,2,3])) {
                                $errorData[] = [
                                    'field' => "salesType",
                                    'message' => [trans('custom.invalid_sales_type')]
                                ];
                            }
                        } else {
                            $errorData[] = [
                                'field' => "salesType",
                                'message' => [trans('custom.sales_type_mandatory')]
                            ];
                        }

                        // Validate Segment Code
                        if (isset($request['segment_code'])) {
                            $segment = SegmentMaster::withoutGlobalScope('final_level')
                                ->where('ServiceLineCode',$request['segment_code'])
                                ->where('isActive', 1)
                                ->where('isDeleted', 0)
                                ->first();

                            if(!$segment){
                                $errorData[] = [
                                    'field' => "segment_code",
                                    'message' => [trans('custom.segment_not_found')]
                                ];
                            } else {
                                if($segment->approved_yn == 0) {
                                    $errorData[] = [
                                        'field' => "segment",
                                        'message' => [trans('custom.segment_not_approved')]
                                    ];
                                } else {
                                    $segmentAssigned = SegmentAssigned::where('serviceLineSystemID',$segment->serviceLineSystemID)
                                        ->where('companySystemID', $segment->companySystemID)
                                        ->where('isAssigned', 1)
                                        ->first();

                                    if(!$segmentAssigned){
                                        $errorData[] = [
                                            'field' => "segment",
                                            'message' => [trans('custom.segment_not_assigned')]
                                        ];
                                    }
                                }
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "segment_code",
                                'message' => [trans('custom.segment_code_required')]
                            ];
                        }

                        // Validate Warehouse
                        if (isset($request['warehouse_code'])) {
                            $warehouse = WarehouseMaster::where('wareHouseCode',$request['warehouse_code'])
                                ->where("companySystemID", $request['company_id'])
                                ->where('isActive', 1)
                                ->first();
                            if(!$warehouse){
                                $errorData[] = [
                                    'field' => "warehouse_code",
                                    'message' => [trans('custom.warehouse_not_found')]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "warehouse_code",
                                'message' => [trans('custom.warehouse_code_required')]
                            ];
                        }
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "invoice_type",
                        'message' => [trans('custom.invoice_type_invalid')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "invoice_type",
                    'message' => [trans('custom.invoice_type_integer')]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "invoice_type",
                'message' => [trans('custom.invoice_type_required')]
            ];
        }

        // Validate Customer
        if (isset($request['customer_code'])) {
            $customer = CustomerAssigned::join('customermaster', 'customerassigned.customerCodeSystem', '=', 'customermaster.customerCodeSystem')
                ->where('customermaster.customer_registration_no', $request['customer_code'])
                ->orWhere('customerassigned.CutomerCode',$request['customer_code'])
                ->where('companySystemID', $request['company_id'])
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();

            if(!$customer){
                $errorData[] = [
                    'field' => "customer_code",
                    'message' => [trans('custom.invalid_customer_code')]
                ];
            }
            else {
                // Validate customer invoice no
                if (isset($request['customer_invoice_number'])) {
                    $verifyCompanyInvoiceNo = CustomerInvoiceDirect::select("bookingInvCode")->where('customerInvoiceNo', $request['customer_invoice_number'])->where('customerID', $customer->customerCodeSystem)->where('companySystemID', $request['company_id'])->first();
                    if ($verifyCompanyInvoiceNo) {
                        $fieldErrors = [
                            'field' => "customer_invoice_number",
                            'message' => [trans('custom.customer_invoice_used', ['bookingInvCode' => $verifyCompanyInvoiceNo->bookingInvCode])]
                        ];
                        $errorData[] = $fieldErrors;
                    }
                }
                else {
                    $fieldErrors = [
                        'field' => "customer_invoice_number",
                        'message' => [trans('custom.customer_invoice_number_required')]
                    ];
                    $errorData[] = $fieldErrors;
                }

                // Validate Currency
                if (isset($request['currency_code'])) {
                    $request['currency_code'] = strtoupper($request['currency_code']);
                    $currency = CustomerCurrency::join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')
                        ->where('currencymaster.CurrencyCode', $request['currency_code'])
                        ->where('customerCodeSystem', $customer->customerCodeSystem)
                        ->where('isAssigned', -1)
                        ->first();

                    if(!$currency){
                        $errorData[] = [
                            'field' => "currency_code",
                            'message' => [trans('custom.invalid_currency')]
                        ];
                    }
                    else {
                        // Validate Bank Code
                        if (isset($request['bank_code'])) {
                            $bank = BankAssign::where('isActive', 1)
                                ->where('isAssigned', -1)
                                ->where('companySystemID', $request['company_id'])
                                ->where('bankShortCode',$request['bank_code'])
                                ->first();

                            if(!$bank){
                                $errorData[] = [
                                    'field' => "bank_code",
                                    'message' => [trans('custom.bank_not_found')]
                                ];
                            }
                            else {
                                // Validate Bank Account Number
                                if (isset($request['account_number'])) {
                                    $bankAccount = BankAccount::where('companySystemID', $request['company_id'])
                                        ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                                        ->where('accountCurrencyID', $currency->currencyID)
                                        ->where('AccountNo', $request['account_number'])
                                        ->where('approvedYN', 1)
                                        ->where('isAccountActive', 1)
                                        ->first();

                                    if(!$bankAccount){
                                        $errorData[] = [
                                            'field' => "account_number",
                                            'message' => [trans('custom.bank_account_not_found')]
                                        ];
                                    }
                                }
                                else {
                                    $errorData[] = [
                                        'field' => "account_number",
                                        'message' => [trans('custom.account_number_required')]
                                    ];
                                }
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "bank_code",
                                'message' => [trans('custom.bank_code_required')]
                            ];
                        }
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "currency_code",
                        'message' => [trans('custom.currency_code_required')]
                    ];
                }

                // Validate Document Date
                if (isset($request['document_date'])) {
                    if(DateTime::createFromFormat('Y-m-d', $request['document_date'])) {
                        $documentDate = Carbon::parse($request['document_date']);

                        $invoiceDueDate = $documentDate->copy();
                        $invoiceDueDate->addDays($customer->creditDays);

                        // Validate Financial Year & Period
                        $financeYear = CompanyFinanceYear::where('companySystemID',$request['company_id'])
                            ->where('isDeleted',0)
                            ->where('bigginingDate','<=',$documentDate)
                            ->where('endingDate','>=',$documentDate)
                            ->first();

                        if($financeYear){
                            $financePeriod = CompanyFinancePeriod::where('companySystemID',$request['company_id'])
                                ->where('departmentSystemID',4)
                                ->where('companyFinanceYearID',$financeYear->companyFinanceYearID)
                                ->where('isActive',-1)
                                ->whereMonth('dateFrom',$documentDate->month)
                                ->whereMonth('dateTo',$documentDate->month)
                                ->first();
                            if(!$financePeriod){
                                $errorData[] = [
                                    'field' => "document_date",
                                    'message' => [trans('custom.finance_period_not_active')]
                                ];
                            }
                        }
                        else{
                            $errorData[] = [
                                'field' => "document_date",
                                'message' => [trans('custom.finance_year_not_found')]
                            ];
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "document_date",
                            'message' => [trans('custom.document_date_invalid')]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "document_date",
                        'message' => [trans('custom.document_date_required')]
                    ];
                }
            }
        }
        else {
            $errorData[] = [
                'field' => "customer_code",
                'message' => ["customer_code field is required"]
            ];
        }

        // Validate Comment
        if (!isset($request['comment'])) {
            $errorData[] = [
                'field' => "comment",
                'message' => [trans('custom.comment_required')]
            ];
        }

        $details = $request['details'] ?? null;

        if (isset($details)) {
            if (is_array($details)) {
                $detailsCollection = collect($details);

                if(empty($detailsCollection->count())) {
                    $errorData[] = [
                        'field' => "details",
                        'message' => [trans('custom.details_less_than_one')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "details",
                    'message' => [trans('custom.details_format_invalid')]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "details",
                'message' => [trans('custom.details_required')]
            ];
        }

        if (empty($errorData) && empty($fieldErrors)) {
            $returnDataset = [
                'status' => true,
                'data' => [
                    'bookingDate' => $documentDate->toDateString(),
                    'comments' => $request['comment'],
                    'salesType' => $request['salesType'] ?? null,
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                    'companyID' => $request['company_id'],
                    'custTransactionCurrencyID' => $currency->currencyID,
                    'customerID' => $customer->customerCodeSystem,
                    'date_of_supply' => Carbon::today()->toDateString(),
                    'invoiceDueDate' => $invoiceDueDate->toDateString(),
                    'isPerforma' => $invoiceType,
                    'bankID' => $bank->bankmasterAutoID,
                    'bankAccountID' => $bankAccount->bankAccountAutoID,
                    'customerInvoiceNo' => $request['customer_invoice_number'],
                    'isAutoCreateDocument' => true
                ]
            ];

            if($invoiceType == 2){
                $returnDataset['data']['wareHouseSystemCode'] = $warehouse->wareHouseSystemCode;
                $returnDataset['data']['serviceLineSystemID'] = $segment->serviceLineSystemID;
            }
        }
        else {
            $returnDataset = [
                'status' => false,
                'data' => $errorData,
                'fieldErrors' => $fieldErrors
            ];
        }

        return $returnDataset;
    }

    private static function validateDetailsData($masterData, $request): array {

        $errorData = [];

        if (isset($masterData['invoice_type'])) {
            if($masterData['invoice_type'] == 1) {
                // Validate GL Code
                if(isset($request['gl_code'])){
                    $chartOfAccountAssign = ChartOfAccountsAssigned::where('companySystemID',$masterData['company_id'])
                        ->where('AccountCode',$request['gl_code'])
                        ->where('controllAccountYN', 0)
                        ->where('isAssigned', -1)
                        ->where('isActive', 1)
                        ->where('isBank', 0)
                        ->first();
                    if(!$chartOfAccountAssign){
                        $errorData[] = [
                            'field' => "gl_code",
                            'message' => [trans('custom.gl_code_not_found')]
                        ];
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "gl_code",
                        'message' => [trans('custom.gl_code_required')]
                    ];
                }

                // Validate Segment Code
                if(isset($request['segment_code'])){
                    $segment = SegmentMaster::withoutGlobalScope('final_level')
                        ->where('ServiceLineCode',$request['segment_code'])
                        ->where('isActive', 1)
                        ->where('isDeleted', 0)
                        ->first();
                    if(!$segment){
                        $errorData[] = [
                            'field' => "segment_code",
                            'message' => [trans('custom.segment_not_found')]
                        ];
                    } else {
                        if($segment->approved_yn == 0) {
                            $errorData[] = [
                                'field' => "segment",
                                'message' => [trans('custom.segment_not_approved_detail')]
                            ];
                        } else {
                            $segmentAssigned = SegmentAssigned::where('serviceLineSystemID',$segment->serviceLineSystemID)
                                ->where('companySystemID', $masterData['company_id'])
                                ->where('isAssigned', 1)
                                ->first();

                            if(!$segmentAssigned){
                                $errorData[] = [
                                    'field' => "segment",
                                    'message' => [trans('custom.segment_not_assigned_detail')]
                                ];
                            }
                        }
                    }
                }
                else {
                    $errorData[] = [
                        'field' => "segment_code",
                        'message' => [trans('custom.segment_code_required_detail')]
                    ];
                }
            }

            if($masterData['invoice_type'] == 2) {
                // Validate salesType exists before using it
                if (!isset($masterData['salesType'])) {
                    $errorData[] = [
                        'field' => "salesType",
                        'message' => [trans('custom.sales_type_mandatory')]
                    ];
                } else {
                    // Validate user qty
                    if($masterData['salesType'] == 3){
                        if(isset($request['userQty'])){
                            if(gettype($request['userQty']) != 'string'){
                                if($request['userQty'] < 0){
                                    $errorData[] = [
                                        'field' => "userQty",
                                        'message' => [trans('custom.user_qty_positive')]
                                    ];
                                }
                            } else {
                                $errorData[] = [
                                    'field' => "userQty",
                                    'message' => [trans('custom.user_qty_positive')]
                                ];
                            }
                        } else {
                            $errorData[] = [
                                'field' => "userQty",
                                'message' => [trans('custom.user_qty_required')]
                            ];
                        }
                    }

                    // Validate Service Code
                    if(isset($request['service_code'])){
                        $serviceCode = ItemAssigned::where('itemPrimaryCode',$request['service_code'])
                            ->where('companySystemID', $masterData['company_id'])
                            ->where('isActive', 1)
                            ->where('isAssigned', -1)
                            ->whereIn('financeCategoryMaster', [1,2,4])
                            ->first();
                        if(!$serviceCode){
                            $errorData[] = [
                                'field' => "service_code",
                                'message' => [trans('custom.service_code_not_found')]
                            ];
                        } else {
                            if($masterData['salesType'] == 1 && $serviceCode->financeCategoryMaster == 2){
                                $errorData[] = [
                                    'field' => "service_code",
                                    'message' => [trans('custom.item_category_inventory')]
                                ];
                            } else if(($masterData['salesType'] == 2 || $masterData['salesType'] == 3) && $serviceCode->financeCategoryMaster != 2){
                                $errorData[] = [
                                    'field' => "service_code",
                                    'message' => [trans('custom.item_category_service')]
                                ];
                            }
                        }
                    }
                    else {
                        $errorData[] = [
                            'field' => "service_code",
                            'message' => [trans('custom.service_code_required')]
                        ];
                    }

                    // Validate Margin Percentage
                    if (isset($request['margin_percentage'])) {
                        if (gettype($request['margin_percentage']) != 'string') {
                            if ($request['margin_percentage'] < 0) {
                                $errorData[] = [
                                    'field' => "margin_percentage",
                                    'message' => [trans('custom.margin_percentage_min')]
                                ];
                            }
                        }
                        else {
                            $errorData[] = [
                                'field' => "margin_percentage",
                                'message' => [trans('custom.margin_percentage_numeric')]
                            ];
                        }
                    }
                }
            }
        }

        // Validate Unit Code
        if(isset($request['uom'])){
            $unit = Unit::where('is_active', 1)->where('UnitShortCode',$request['uom'])->first();

            if(!$unit){
                $errorData[] = [
                    'field' => "uom",
                    'message' => [trans('custom.unit_not_found')]
                ];
            }
        }
        else {
            $errorData[] = [
                'field' => "uom",
                'message' => [trans('custom.uom_required')]
            ];
        }

        // Validate Quantity
        if(isset($request['quantity'])) {
            if (is_int($request['quantity'])) {
                if ($request['quantity'] <= 0) {
                    $errorData[] = [
                        'field' => "quantity",
                        'message' => [trans('custom.quantity_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "quantity",
                    'message' => [trans('custom.quantity_integer')]
                ];
            }
        }
        else{
            $errorData[] = [
                'field' => "quantity",
                'message' => [trans('custom.quantity_required')]
            ];
        }

        // Validate Sales Price
        if(isset($request['sales_price'])) {
            if (gettype($request['sales_price']) != 'string') {
                if ($request['sales_price'] <= 0) {
                    $errorData[] = [
                        'field' => "sales_price",
                        'message' => [trans('custom.sales_price_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "sales_price",
                    'message' => [trans('custom.sales_price_numeric')]
                ];
            }
        }
        else{
            $errorData[] = [
                'field' => "sales_price",
                'message' => [trans('custom.sales_price_required')]
            ];
        }

        // Validate Discount Percentage
        if (isset($request['discount_percentage'])) {
            if (gettype($request['discount_percentage']) != 'string') {
                if ($request['discount_percentage'] < 0) {
                    $errorData[] = [
                        'field' => "discount_percentage",
                        'message' => [trans('custom.discount_percentage_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "discount_percentage",
                    'message' => [trans('custom.discount_percentage_numeric')]
                ];
            }
        }

        // Validate Discount Amount
        if (isset($request['discount_amount'])) {
            if (gettype($request['discount_amount']) != 'string') {
                if ($request['discount_amount'] < 0) {
                    $errorData[] = [
                        'field' => "discount_amount",
                        'message' => [trans('custom.discount_amount_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "discount_amount",
                    'message' => [trans('custom.discount_amount_numeric')]
                ];
            }
        }

        // Validate Vat Percentage
        if (isset($request['vat_percentage'])) {
            if (gettype($request['vat_percentage']) != 'string') {
                if ($request['vat_percentage'] < 0) {
                    $errorData[] = [
                        'field' => "vat_percentage",
                        'message' => [trans('custom.vat_percentage_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_percentage",
                    'message' => [trans('custom.vat_percentage_numeric')]
                ];
            }
        }

        // Validate Vat Amount
        if (isset($request['vat_amount'])) {
            if (gettype($request['vat_amount']) != 'string') {
                if ($request['vat_amount'] < 0) {
                    $errorData[] = [
                        'field' => "vat_amount",
                        'message' => [trans('custom.vat_amount_min')]
                    ];
                }
            }
            else {
                $errorData[] = [
                    'field' => "vat_amount",
                    'message' => [trans('custom.vat_amount_numeric')]
                ];
            }
        }

        if (empty($errorData)) {
            $returnData = [
                "status" => true,
                "data" => [
                    'companySystemID' => $masterData['company_id'],
                    'salesPrice' => $request['sales_price'],
                    'isAutoCreateDocument' => true,
                    'discountPercentage' => $request['discount_percentage'] ?? 0,
                    'VATPercentage' => $request['vat_percentage'] ?? 0,
                    'VATAmount' => $request['vat_amount'] ?? 0
                ]
            ];

            if (isset($masterData['invoice_type'])) {
                if($masterData['invoice_type'] == 1) {
                    $returnData['data']['glCode'] = $chartOfAccountAssign->chartOfAccountSystemID;
                    $returnData['data']['unitOfMeasure'] = $unit->UnitID;
                    $returnData['data']['serviceLineSystemID'] = $segment->serviceLineSystemID;
                    $returnData['data']['invoiceQty'] = $request['quantity'];
                    $returnData['data']['discountAmountLine'] = $request['discount_amount'] ?? 0;

                }
                elseif ($masterData['invoice_type'] == 2){
                    $returnData['data']['customerCatalogDetailID'] = 0;
                    $returnData['data']['customerCatalogMasterID'] = 0;
                    $returnData['data']['itemCode'] = $serviceCode->idItemAssigned;
                    $returnData['data']['itemUnitOfMeasure'] = $unit->UnitID;
                    $returnData['data']['qtyIssued'] = $request['quantity'];
                    $returnData['data']['discountAmount'] = $request['discount_amount'] ?? 0;
                    $returnData['data']['marginPercentage'] = $request['margin_percentage'] ?? 0;
                    $returnData['data']['userQty'] = $request['userQty'] ?? 0;
                }
            }
        }
        else{
            $returnData = [
                "status" => false,
                "data" => $errorData
            ];
        }

        return $returnData;
    }

    public static function customerInvoiceStore($input): array {

        $companyFinanceYearID = $input['companyFinanceYearID'];

        $company = Company::where('companySystemID', $input['companyID'])->first()->toArray();

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $companyFinanceYearID)->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        $myCurr = $input['custTransactionCurrencyID'];

        $companyCurrency = \Helper::companyCurrency($company['companySystemID']);
        $companyCurrencyConversion = \Helper::currencyConversion($company['companySystemID'], $myCurr, $myCurr, 0);
        /*exchange added*/
        $input['custTransactionCurrencyER'] = 1;
        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];

        if(!isset($input['isAutoCreateDocument'])){
            $bank = BankAssign::select('bankmasterAutoID')
                ->where('companySystemID', $input['companyID'])
                ->where('isDefault', -1)
                ->first();
            if ($bank) {
                $input['bankID'] = $bank->bankmasterAutoID;
                $bankAccount = BankAccount::where('companySystemID', $input['companyID'])
                    ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                    ->where('isDefault', 1)
                    ->where('accountCurrencyID', $myCurr)
                    ->first();
                if ($bankAccount) {
                    $input['bankAccountID'] = $bankAccount->bankAccountAutoID;
                }

            }
        }

        if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3 || $input['isPerforma'] == 4 || $input['isPerforma'] == 5)) {
            $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
            if (!$serviceLine) {
                return [
                    'status' => false,
                    'message' => trans('custom.please_select_segment')
                ];
            }
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
        }

        $lastSerial = CustomerInvoiceDirect::where('companySystemID', $input['companyID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $bookingInvCode = ($company['CompanyID'] . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
            ->where('companySystemID', $input['companyID'])
            ->first();
        if ($customerGLCodeUpdate) {
            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
        }

        $company = Company::where('companySystemID', $input['companyID'])->first();

        if ($company) {
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
        }

        $input['documentID'] = "INV";
        $input['documentSystemiD'] = 20;
        $input['bookingInvCode'] = $bookingInvCode;
        $input['serialNo'] = $lastSerialNumber;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $input['FYPeriodDateTo'] = $FYPeriodDateTo;

        $autoGeneratePolicy = \Helper::checkPolicy($input['companyID'], 103);

        if( $autoGeneratePolicy && (!isset($input['isAutoCreateDocument']) || (isset($input['isAutoCreateDocument']) &&!$input['isAutoCreateDocument'])))
        {
            $input['customerInvoiceNo'] = $bookingInvCode;
        }

        try{
            $input['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        }
        catch (\Exception $e){
            return [
                'status' => false,
                'message' => trans('custom.invalid_due_date_format')
            ];
        }
        $input['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $input['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
        $input['customerInvoiceDate'] = $input['bookingDate'];
        $input['companySystemID'] = $input['companyID'];
        $input['companyID'] = $company['CompanyID'];
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
        $input['documentType'] = 11;

        if(!isset($input['isAutoCreateDocument'])){
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        }
        else{
            $systemUser = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $systemUser->empID;
            $input['modifiedUser'] = $systemUser->empID;
            $input['createdUserSystemID'] = $systemUser->employeeSystemID;
            $input['modifiedUserSystemID'] = $systemUser->employeeSystemID;
        }

        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedPc'] = getenv('COMPUTERNAME');


        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($input['bookingDate'] > $curentDate) {
            return [
                'status' => true,
                'data' => "e",
                'message' => trans('custom.document_date_greater_than_current')
            ];
        }
        if (($input['bookingDate'] >= $FYPeriodDateFrom) && ($input['bookingDate'] <= $FYPeriodDateTo)) {
            $customerInvoiceDirects = CustomerInvoiceDirect::create($input);
            return [
                'status' => true,
                'data' => $customerInvoiceDirects->refresh()->toArray(),
                'message' => trans('custom.customer_invoice_saved')
            ];
        }
        else {
            return [
                'status' => true,
                'data' => "e",
                'message' => trans('custom.document_date_between_period')
            ];
        }
    }

    public static function customerInvoiceUpdate($id,$input){
        $customerInvoiceDirect = CustomerInvoiceDirect::find($id);

        if (empty($customerInvoiceDirect)) {
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.customer_invoice_not_found')
            ];
        }

        $isPerforma = $customerInvoiceDirect->isPerforma;
        $salesType = $customerInvoiceDirect->salesType;

        if ($isPerforma == 2 || $isPerforma == 3 || $isPerforma == 4|| $isPerforma == 5) {
            $detail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)->get();
        } else {
            $detail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->get();
        }

     
        $autoGeneratePolicy = \Helper::checkPolicy($input['companySystemID'], 103);
        if(  $autoGeneratePolicy && isset($input['invoiceAutoType']) && $input['invoiceAutoType'] == 1 && (!isset($input['isAutoCreateDocument']) || (isset($input['isAutoCreateDocument']) && !$input['isAutoCreateDocument'])))
        {
            $input['customerInvoiceNo'] = $input['bookingInvCode'];
        }

        if($isPerforma == 2) {
            $_glSelectionItems = CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$id)->get();

            if($_glSelectionItems) {
                foreach($_glSelectionItems as $_glSelectionItem) {
                    if(!isset($_glSelectionItem->serviceLineCode)) {
                        return [
                            'status' => false,
                            'code' => 500,
                            'message' => trans('custom.please_select_segment_gl')
                        ];
                    }
                    if(!isset($_glSelectionItem->invoiceAmount) || $_glSelectionItem->invoiceAmount == 0) {
                        return [
                            'status' => false,
                            'code' => 500,
                            'message' => trans('custom.amount_required_gl')
                        ];
                    }
                }

            }
        }

        if(isset($detail[0])) {
            $qo_master = QuotationMaster::find($detail[0]['quotationMasterID']);
            $details = CustomerInvoiceItemDetails::where('quotationMasterID',$detail[0]['quotationMasterID'])->get();

            if(isset($qo_master->detail)) {
                foreach ($qo_master->detail as $item) {
                    $item_details_count = CustomerInvoiceItemDetails::where('quotationMasterID', $detail[0]['quotationMasterID'])->where('itemCodeSystem', $item->itemAutoID)->sum('qtyIssued');
                    $qo_master_count = QuotationDetails::where('quotationMasterID', $detail[0]['quotationMasterID'])->where('itemAutoID', $item->itemAutoID)->sum('requestedQty');

                    if ($qo_master) {
                        if ($qo_master_count == $item_details_count) {
                            $qo_master->isInDOorCI = 2;
                            $qo_master->save();
                        } else {
                            $qo_master->isInDOorCI = 4;
                            $qo_master->save();
                        }
                    }
                }
            }


        }

        if ($isPerforma != 1) {
            if (isset($input['isPerforma']) && ($input['isPerforma'] == 2 || $input['isPerforma'] == 3|| $input['isPerforma'] == 4|| $input['isPerforma'] == 5)) {
                $wareHouse = isset($input['wareHouseSystemCode']) ? $input['wareHouseSystemCode'] : 0;

                if (!$wareHouse) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.please_select_warehouse')
                    ];
                }
                $_post['wareHouseSystemCode'] = $input['wareHouseSystemCode'];


                $serviceLine = isset($input['serviceLineSystemID']) ? $input['serviceLineSystemID'] : 0;
                if (!$serviceLine) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.please_select_segment')
                    ];
                }
                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                $_post['serviceLineSystemID'] = $input['serviceLineSystemID'];
                $_post['serviceLineCode'] = isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null;
            }

            if(isset($input['custTransactionCurrencyID'])){
                $_post['custTransactionCurrencyID'] = $input['custTransactionCurrencyID'];
            }
            else{
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.please_select_currency')
                ];
            }

            $_post['bankID'] = $input['bankID'];
            $_post['bankAccountID'] = $input['bankAccountID'];

            if ($_post['custTransactionCurrencyID'] != $customerInvoiceDirect->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.invoice_details_exist_currency')
                    ];
                } else {
                    $myCurr = $_post['custTransactionCurrencyID'];
                    //$companyCurrency = \Helper::companyCurrency($customerInvoiceDirect->companySystemID);
                    //$companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $_post['custTransactionCurrencyER'] = 1;
                    /* $_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                     $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                     $_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                     $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];*/
                    $_post['bankAccountID'] = NULL;

                }
            }

            /*if ($_post['bankID'] != $customerInvoiceDirect->bankID) {
                $_post['bankAccountID'] = NULL;
            }*/

        }

        if ($customerInvoiceDirect->customerCodeSystem != $input['customerID']) {
            $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
                ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                ->first();
            if ($customerGLCodeUpdate) {
                $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
            }
        }

        $_post['customerVATEligible'] = $input['customerVATEligible'];

        $input['departmentSystemID'] = 4;
        /*financial Year check*/
        if ($isPerforma == 0) {
            $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYearCheck["success"]) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => $companyFinanceYearCheck["message"]
                ];
            }
        }

        if ($isPerforma == 0) {
            /*financial Period check*/
            $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
            if (!$companyFinancePeriodCheck["success"]) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => $companyFinancePeriodCheck["message"]
                ];
            }
        }
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $FYPeriodDateFrom = $companyfinanceperiod->dateFrom;
        $FYPeriodDateTo = $companyfinanceperiod->dateTo;
        $_post['companyFinancePeriodID'] = $input['companyFinancePeriodID'];

        $_post['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $_post['FYEnd'] = $CompanyFinanceYear->endingDate;
        $_post['FYPeriodDateFrom'] = $FYPeriodDateFrom;
        $_post['FYPeriodDateTo'] = $FYPeriodDateTo;
        $_post['companyFinancePeriodID'] = $input['companyFinancePeriodID'];
        $_post['companyFinanceYearID'] = $input['companyFinanceYearID'];
        $_post['wanNO'] = $input['wanNO'];
        $_post['secondaryLogoCompanySystemID'] = $input['secondaryLogoCompanySystemID'] ?? null;
        $_post['servicePeriod'] = $input['servicePeriod'];
        $_post['comments'] = $input['comments'];
        $_post['customerID'] = $input['customerID'];
        $_post['rigNo'] = $input['rigNo'];
        $_post['PONumber'] = $input['PONumber'];
        $_post['customerGRVAutoID'] = $input['customerGRVAutoID'];
        $_post['isPerforma'] = $input['isPerforma'];
        if(isset($input['salesType'])){
            $_post['salesType'] = $input['salesType'];
        }

        if (isset($input['customerGRVAutoID']) && $input['customerGRVAutoID']) {
            $checkGrv = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $id)
                ->where('customerGRVAutoID', $input['customerGRVAutoID'])
                ->first();

            if (!empty($checkGrv)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'type' => array('type' => 'grvAssigned'),
                    'message' => trans('custom.selected_grv_already_assigned', ['bookingInvCode' => $checkGrv->bookingInvCode])
                ];
            }
        } else {
            $input['customerGRVAutoID'] = null;
        }


        if (isset($input['secondaryLogoCompanySystemID']) && $input['secondaryLogoCompanySystemID'] != $customerInvoiceDirect->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompID'] != '') {
                $company = Company::where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $_post['secondaryLogoCompID'] = $company->CompanyID;
                $_post['secondaryLogo'] = $company->logo_url;
            } else {
                $_post['secondaryLogoCompID'] = NULL;
                $_post['secondaryLogo'] = NULL;
            }

        } else {
            $_post['secondaryLogoCompID'] = NULL;
            $_post['secondaryLogo'] = NULL;
        }

        if ($input['customerInvoiceNo'] != $customerInvoiceDirect->customerInvoiceNo) {
            $_post['customerInvoiceNo'] = $input['customerInvoiceNo'];
        } else {
            $_post['customerInvoiceNo'] = $customerInvoiceDirect->customerInvoiceNo;
        }

        if ($_post['customerInvoiceNo'] != '') {
            /*checking customer invoice no already exist*/
            $verifyCompanyInvoiceNo = CustomerInvoiceDirect::select("bookingInvCode")->where('customerInvoiceNo', $_post['customerInvoiceNo'])->where('customerID', $input['customerID'])->where('companySystemID', $input['companySystemID'])->where('custInvoiceDirectAutoID', '<>', $id)->first();
            if ($verifyCompanyInvoiceNo) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Entered customer invoice number was already used ($verifyCompanyInvoiceNo->bookingInvCode). Please check again."
                ];
            }
        }


        if ($input['customerID'] != $customerInvoiceDirect->customerID) {
            if (count($detail) > 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => 'Invoice details exist. You cannot change the customer.'
                ];
            }
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            if ($customer->creditDays == 0 || $customer->creditDays == '') {
                return [
                    'status' => false,
                    'code' => 500,
                    'type' => array('type' => 'customer_credit_days'),
                    'message' => $customer->CustomerName . ' - Credit days not mentioned for this customer'
                ];
            }

            /*if customer change*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $_post['customerGLCode'] = $customer->custGLaccount;
            $_post['customerGLSystemID'] = $customer->custGLAccountSystemID;
            $currency = CustomerCurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
            if ($currency) {
                $_post['custTransactionCurrencyID'] = $currency->currencyID;
                $myCurr = $currency->currencyID;

                //$companyCurrency = \Helper::companyCurrency($currency->currencyID);
                $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $myCurr, $myCurr, 0);
                /*exchange added*/
                $_post['custTransactionCurrencyER'] = 1;
                $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                    ->where('companyPolicyCategoryID', 67)
                    ->where('isYesNO', 1)
                    ->first();
                $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

                if($policy == false || $input['isPerforma'] != 0) {
                    //$_post['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    //$_post['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
                $_post['bankID'] = null;
                $_post['bankAccountID'] = null;
                $bank = BankAssign::select('bankmasterAutoID')
                    ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                    ->where('isDefault', -1)
                    ->first();

                if ($bank) {
                    $_post['bankID'] = $bank->bankmasterAutoID;
                    $bankAccount = BankAccount::where('companySystemID', $customerInvoiceDirect->companySystemID)
                        ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                        ->where('isDefault', 1)
                        ->where('accountCurrencyID', $currency->currencyID)
                        ->first();

                    if ($bankAccount) {
                        $_post['bankAccountID'] = $bankAccount->bankAccountAutoID;
                    }
                }
            }
            /**/

        } else {
            $companyCurrencyConversion = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $input['custTransactionCurrencyID'], $input['custTransactionCurrencyID'], 0);
            /*exchange added*/
            $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyPolicyCategoryID', 67)
                ->where('isYesNO', 1)
                ->first();
            $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

            if($policy == false || $input['isPerforma'] != 0) {
                $_post['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $_post['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            }
        }


        if(isset($input['serviceStartDate']) && $input['serviceStartDate'] != ''){
            $_post['serviceStartDate'] = Carbon::parse($input['serviceStartDate'])->format('Y-m-d') . ' 00:00:00';
        }

        if(isset($input['serviceEndDate']) && $input['serviceEndDate'] != ''){
            $_post['serviceEndDate'] = Carbon::parse($input['serviceEndDate'])->format('Y-m-d') . ' 00:00:00';
        }

        if (isset($input['serviceStartDate']) && isset($input['serviceEndDate']) && $input['serviceStartDate'] != '' && $input['serviceEndDate'] != '') {
            if (($_post['serviceStartDate'] > $_post['serviceEndDate'])) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.service_start_date_greater')
                ];
            }
        }

        $_post['bookingDate'] = Carbon::parse($input['bookingDate'])->format('Y-m-d') . ' 00:00:00';
        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($_post['bookingDate'] > $curentDate) {
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.document_date_greater_than_current')
            ];
        }

        if ($input['invoiceDueDate'] != '') {
            $_post['invoiceDueDate'] = Carbon::parse($input['invoiceDueDate'])->format('Y-m-d') . ' 00:00:00';
        } else {
            $_post['invoiceDueDate'] = null;
        }

        if ($input['date_of_supply'] != '') {
            $_post['date_of_supply'] = Carbon::parse($input['date_of_supply'])->format('Y-m-d') . ' 00:00:00';
        } else {
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.date_of_supply_required')
            ];
        }

        /*validaation*/
        $_post['customerInvoiceDate'] = $customerInvoiceDirect->customerInvoiceDate;
        if ($input['customerInvoiceDate'] != '') {
            $_post['customerInvoiceDate'] = Carbon::parse($input['customerInvoiceDate'])->format('Y-m-d') . ' 00:00:00';
        } else {
            $_post['customerInvoiceDate'] = null;
        }


        if (($_post['bookingDate'] >= $_post['FYPeriodDateFrom']) && ($_post['bookingDate'] <= $_post['FYPeriodDateTo'])) {

        } else {
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.document_date_between_period_detail')
            ];
            $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
            // $_post['bookingDate'] = $curentDate;

        }

        if ($isPerforma == 2 || $isPerforma == 3|| $isPerforma == 4|| $isPerforma == 5) {
            if($salesType == 3){
                $detailAmount = CustomerInvoiceItemDetails::select(DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin * userQty),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal * userQty),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt * userQty),0) as bookingAmountRpt"))->where('custInvoiceDirectAutoID', $id)->first();
            }else{
                $detailAmount = CustomerInvoiceItemDetails::select(DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMargin),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt),0) as bookingAmountRpt"))->where('custInvoiceDirectAutoID', $id)->first();
            }
        } else {
            $detailAmount = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $id)->first();
        }


        $_post['bookingAmountTrans'] = \Helper::roundValue($detailAmount->bookingAmountTrans);
        $_post['bookingAmountLocal'] = \Helper::roundValue($detailAmount->bookingAmountLocal);
        $_post['bookingAmountRpt'] = \Helper::roundValue($detailAmount->bookingAmountRpt);

        if ($input['confirmedYN'] == 1) {
            if ($customerInvoiceDirect->confirmedYN == 0) {

                if (($_post['bookingDate'] >= $_post['FYPeriodDateFrom']) && ($_post['bookingDate'] <= $_post['FYPeriodDateTo'])) {

                } else {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.document_date_between_selected_period')
                    ];
                }

                if ($isPerforma == 0 || $isPerforma == 2) {
                    $object = new ChartOfAccountValidationService();
                    if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                        $empInfo = UserTypeService::getSystemEmployee();
                        $result = $object->checkChartOfAccountStatus($customerInvoiceDirect->documentSystemiD, $id,$customerInvoiceDirect->companySystemID,$empInfo->empID);
                    }
                    else{
                        $result = $object->checkChartOfAccountStatus($customerInvoiceDirect->documentSystemiD, $id,$customerInvoiceDirect->companySystemID);
                    }

                    if (isset($result) && !empty($result["accountCodes"])) {
                        return [
                            'status' => false,
                            'code' => 500,
                            'message' => $result["errorMsg"]
                        ];
                    }
                }

                $autoGeneratePolicy = Helper::checkPolicy($input['companySystemID'], 103);
                if( $autoGeneratePolicy && isset($input['isAutoInvoice']) && $input['isAutoInvoice'] && (!isset($input['isAutoCreateDocument']) || (isset($input['isAutoCreateDocument']) && !$input['isAutoCreateDocument'])))
                {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.are_you_sure_update_invoice'),
                        'type' => ['type' => 'autoInvoice'],
                    ];
                }

                /**/
                if ($isPerforma != 1) {


                    $messages = [

                        'custTransactionCurrencyID.required' => trans('custom.currency_required'),
                        'bankID.required' => trans('custom.bank_required'),
                        'bankAccountID.required' => trans('custom.bank_account_required'),

                        'customerInvoiceNo.required' => trans('custom.customer_invoice_no_required'),
                        'customerInvoiceDate.required' => trans('custom.customer_invoice_date_required'),
                        'PONumber.required' => trans('custom.po_number_required'),
                        'servicePeriod.required' => trans('custom.service_period_required'),
                        'serviceStartDate.required' => trans('custom.service_start_date_required'),
                        'serviceEndDate.required' => trans('custom.service_end_date_required'),
                        'bookingDate.required' => trans('custom.booking_date_required')

                    ];
                    $validator = \Validator::make($_post, [
                        'custTransactionCurrencyID' => 'required|numeric|min:1',
                        'bankID' => 'required|numeric|min:1',
                        'bankAccountID' => 'required|numeric|min:1',

                        'customerInvoiceNo' => 'required',
                        'customerInvoiceDate' => 'required',
                        // 'PONumber' => 'required',
                        // 'servicePeriod' => 'required',
                        // 'serviceStartDate' => 'required',
                        // 'serviceEndDate' => 'required',
                        'bookingDate' => 'required'
                    ], $messages);


                } else {

                    $messages = [
                        'custTransactionCurrencyID.required' => trans('custom.currency_required'),
                        'bankID.required' => trans('custom.bank_required'),
                        'bankAccountID.required' => trans('custom.bank_account_required'),

                        'customerInvoiceNo.required' => trans('custom.customer_invoice_no_required'),
                        'customerInvoiceDate.required' => trans('custom.customer_invoice_date_required'),
                        'PONumber.required' => trans('custom.po_number_required'),
                        'servicePeriod.required' => trans('custom.service_period_required'),
                        'serviceStartDate.required' => trans('custom.service_start_date_required'),
                        'serviceEndDate.required' => trans('custom.service_end_date_required'),
                        'bookingDate.required' => trans('custom.booking_date_required')

                    ];
                    $validator = \Validator::make($_post, [
                        'customerInvoiceNo' => 'required',
                        'customerInvoiceDate' => 'required',
                        'PONumber' => 'required',
                        'servicePeriod' => 'required',
                        'serviceStartDate' => 'required',
                        'serviceEndDate' => 'required',
                        'bookingDate' => 'required'
                    ], $messages);

                }
                if ($validator->fails()) {
                    return [
                        'status' => false,
                        'code' => 422,
                        'message' => $validator->messages()
                    ];
                }
                /**/
                /*                if ($isPerforma != 1) {

                                    $messages = [

                                        'custTransactionCurrencyID.required' => 'Currency is required.',
                                        'bankID.required' => 'Bank is required.',
                                        'bankAccountID.required' => 'Bank account is required.'

                                    ];
                                    $validator = \Validator::make($_post, [
                                        'custTransactionCurrencyID' => 'required|numeric|min:1',
                                        'bankID' => 'required|numeric|min:1',
                                        'bankAccountID' => 'required|numeric|min:1'
                                    ], $messages);

                                    if ($validator->fails()) {
                                        return $this->sendError($validator->messages(), 422);
                                    }


                                }*/

                if (count($detail) == 0) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.cannot_confirm_no_details')
                    ];
                }
                else {

                    if ($isPerforma == 2 || $isPerforma == 3|| $isPerforma == 4|| $isPerforma == 5) {   // item sales invoice || From Delivery Note|| From Sales Order|| From Quotation

                        $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($customerInvoiceDirect->documentSystemiD, $id);

                        if (!$trackingValidation['status']) {
                            return [
                                'status' => false,
                                'code' => 500,
                                'type' => ['type' => 'confirm'],
                                'message' => $trackingValidation["message"]
                            ];
                        }

                        $checkQuantity = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
                            ->where(function ($q) {
                                $q->where('qtyIssued', '<=', 0)
                                    ->orWhereNull('qtyIssued');
                            })
                            ->count();
                        if ($checkQuantity > 0) {
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => trans('custom.every_item_min_qty')
                            ];
                        }

                        if($salesType == 3){
                            $checkUserQuantity = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
                                ->where(function ($q) {
                                    $q->where('userQty', '<=', 0)
                                        ->orWhereNull('userQty');
                                })
                                ->count();
                            if($checkUserQuantity > 0){
                                return [
                                    'status' => false,
                                    'code' => 500,
                                    'message' => trans('custom.every_item_min_user_qty')
                                ];
                            }
                        }

                        $details = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)->get();

                        $financeCategories = $details->pluck('itemFinanceCategoryID')->toArray();

                        if (count(array_unique($financeCategories)) > 1) {
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => trans('custom.multiple_finance_category')
                            ];
                        }
                        foreach ($details as $item) {

//                            If the revenue account or cost account or BS account is null do not allow to confirm

                            if ((!($item->financeGLcodebBSSystemID > 0)) && $item->itemFinanceCategoryID != 2) {
                                return [
                                    'status' => false,
                                    'code' => 500,
                                    'message' => trans('custom.bs_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
                                ];
                            } elseif (!($item->financeGLcodePLSystemID > 0)) {
                                return [
                                    'status' => false,
                                    'code' => 500,
                                    'message' => trans('custom.cost_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
                                ];
                            } elseif (!($item->financeGLcodeRevenueSystemID > 0)) {
                                return [
                                    'status' => false,
                                    'code' => 500,
                                    'message' => trans('custom.revenue_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
                                ];
                            }

                            $updateItem = CustomerInvoiceItemDetails::find($item['customerItemDetailID']);
                            $data = array('companySystemID' => $customerInvoiceDirect->companySystemID,
                                'itemCodeSystem' => $updateItem->itemCodeSystem,
                                'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);
                            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                            $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                            $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                            $updateItem->issueCostLocal = $itemCurrentCostAndQty['wacValueLocal'];
                            $updateItem->issueCostRpt = $itemCurrentCostAndQty['wacValueReporting'];
                            $updateItem->issueCostLocalTotal = $itemCurrentCostAndQty['wacValueLocal'] * $updateItem->qtyIssuedDefaultMeasure;
                            $updateItem->issueCostRptTotal = $itemCurrentCostAndQty['wacValueReporting'] * $updateItem->qtyIssuedDefaultMeasure;

                            if ($isPerforma == 2 && $updateItem->itemFinanceCategoryID == 1) {
                                $companyCurrencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $customerInvoiceDirect->companyReportingCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $updateItem->issueCostRpt);
                                $updateItem->sellingCost = $companyCurrencyConversion['documentAmount'];
                            }

                            /*margin calculation*/
                            if ($updateItem->marginPercentage != 0 && $updateItem->marginPercentage != null) {
                                $updateItem->salesPrice = $updateItem->sellingCost + ($updateItem->sellingCost * $updateItem->marginPercentage / 100);
                            } else {
                                if ($isPerforma != 4 && $isPerforma != 5) {
                                    $updateItem->salesPrice = $updateItem->sellingCost;
                                }
                            }

                            $updateItem->sellingCostAfterMargin = ($updateItem->salesPrice - $updateItem->discountAmount < 0.00001) ? 0 : ($updateItem->salesPrice - $updateItem->discountAmount);

                            if ($updateItem->sellingCurrencyID != $updateItem->localCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $updateItem->sellingCurrencyID, $updateItem->localCurrencyID, $updateItem->sellingCostAfterMargin);
                                if (!empty($currencyConversion)) {
                                    $updateItem->sellingCostAfterMarginLocal = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $updateItem->sellingCostAfterMarginLocal = $updateItem->sellingCostAfterMargin;
                            }

                            if ($updateItem->sellingCurrencyID != $updateItem->reportingCurrencyID) {
                                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID, $updateItem->sellingCurrencyID, $updateItem->reportingCurrencyID, $updateItem->sellingCostAfterMargin);
                                if (!empty($currencyConversion)) {
                                    $updateItem->sellingCostAfterMarginRpt = $currencyConversion['documentAmount'];
                                }
                            } else {
                                $updateItem->sellingCostAfterMarginRpt = $updateItem->sellingCostAfterMargin;
                            }
                            if($salesType == 3){
                                $updateItem->sellingTotal = $updateItem->sellingCostAfterMargin * $updateItem->qtyIssuedDefaultMeasure * $updateItem->userQty;
                            }else{
                                $updateItem->sellingTotal = $updateItem->sellingCostAfterMargin * $updateItem->qtyIssuedDefaultMeasure;
                            }

                            /*round to 7 decimal*/

                            $updateItem->issueCostLocal = Helper::roundValue($updateItem->issueCostLocal);
                            $updateItem->issueCostRpt = Helper::roundValue($updateItem->issueCostRpt);
                            $updateItem->issueCostLocalTotal = Helper::roundValue($updateItem->issueCostLocalTotal);
                            $updateItem->issueCostRptTotal = Helper::roundValue($updateItem->issueCostRptTotal);
                            $updateItem->sellingCost = Helper::roundValue($updateItem->sellingCost);
                            $updateItem->sellingCostAfterMargin = Helper::roundValue($updateItem->sellingCostAfterMargin);
                            $updateItem->sellingCostAfterMarginLocal = Helper::roundValue($updateItem->sellingCostAfterMarginLocal);
                            $updateItem->sellingCostAfterMarginRpt = Helper::roundValue($updateItem->sellingCostAfterMarginRpt);
                            $updateItem->sellingTotal = Helper::roundValue($updateItem->sellingTotal);

                            $updateItem->save();

                            if ($isPerforma == 2 || $isPerforma == 4 || $isPerforma == 5) {// only item sales invoice. we won't get from delivery note type.

                                if($updateItem->itemFinanceCategoryID == 1){
                                    if ($updateItem->issueCostLocal == 0 || $updateItem->issueCostRpt == 0) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.item_must_not_zero_cost')
                                        ];
                                    }
                                    if ($updateItem->issueCostLocal < 0 || $updateItem->issueCostRpt < 0) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.item_must_not_negative_cost')
                                        ];
                                    }
                                    if ($updateItem->currentWareHouseStockQty <= 0) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.warehouse_stock_qty_zero', ['itemDescription' => $updateItem->itemDescription])
                                        ];
                                    }
                                    if ($updateItem->currentStockQty <= 0) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.stock_qty_zero', ['itemDescription' => $updateItem->itemDescription])
                                        ];
                                    }
                                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentStockQty) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.insufficient_stock_qty', ['itemDescription' => $updateItem->itemDescription])
                                        ];
                                    }

                                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentWareHouseStockQty) {
                                        return [
                                            'status' => false,
                                            'code' => 500,
                                            'message' => trans('custom.insufficient_warehouse_qty', ['itemDescription' => $updateItem->itemDescription])
                                        ];
                                    }
                                }else{
                                    if ($updateItem->sellingCostAfterMargin == 0) {
                                        // return $this->sendError('Item must not have zero selling cost', 500);
                                    }
                                }


                            }
                        }

                        // VAT configuration validation
                        $taxSum = Taxdetail::where('documentSystemCode', $id)
                            ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                            ->where('documentSystemID', $customerInvoiceDirect->documentSystemiD)
                            ->sum('amount');

                        if($taxSum  > 0 && empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => trans('custom.cannot_confirm_output_vat_gl')
                            ];
                        }

                        if($taxSum  > 0 && empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => trans('custom.cannot_confirm_output_vat_transfer_gl')
                            ];
                        }

                        $amount = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
                            ->sum('issueCostRptTotal');


                        $params = array(
                            'autoID' => $id,
                            'company' => $customerInvoiceDirect->companySystemID,
                            'document' => $customerInvoiceDirect->documentSystemiD,
                            'segment' => '',
                            'category' => '',
                            'amount' => $amount,
                            'isAutoCreateDocument' => isset($input['isAutoCreateDocument'])
                        );

                        $customerInvoiceDirect = CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$id)->first();
                        $customerInvoiceDirect->update($_post);
                        $confirm = Helper::confirmDocument($params);
                        if (!$confirm["success"]) {
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => $confirm["message"]
                            ];
                        } else {
                            return [
                                'status' => true,
                                'data' => $customerInvoiceDirect->refresh()->toArray(),
                                'message' => trans('custom.customer_invoice_confirmed'),
                                'detail' => $confirm['data'] ?? null
                            ];
                        }
                    }
                    else {
                        $detailValidation = CustomerInvoiceDirectDetail::selectRaw("glSystemID,IF ( serviceLineCode IS NULL OR serviceLineCode = '', null, 1 ) AS serviceLineCode,IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( unitOfMeasure IS NULL OR unitOfMeasure = '' OR unitOfMeasure = 0, null, 1 ) AS unitOfMeasure, IF ( invoiceQty IS NULL OR invoiceQty = '' OR invoiceQty = 0, null, 1 ) AS invoiceQty, IF ( contractID IS NULL OR contractID = '' OR contractID = 0, null, 1 ) AS contractID,
                    IF ( invoiceAmount IS NULL OR invoiceAmount = '' OR invoiceAmount = 0, null, 1 ) AS invoiceAmount,
                    IF ( unitCost IS NULL OR unitCost = '' OR unitCost = 0, null, 1 ) AS unitCost, IF ( salesPrice IS NULL OR salesPrice = '' OR salesPrice = 0, null, 1 ) AS salesPrice")->
                        where('custInvoiceDirectID', $id)
                            ->where(function ($query) {

                                $query->whereRaw('serviceLineSystemID IS NULL OR serviceLineSystemID =""')
                                    ->orwhereRaw('serviceLineCode IS NULL OR serviceLineCode =""')
                                    ->orwhereRaw('unitOfMeasure IS NULL OR unitOfMeasure =""')
                                    ->orwhereRaw('invoiceQty IS NULL OR invoiceQty =""')
                                    ->orwhereRaw('contractID IS NULL OR contractID =""')
                                    ->orwhereRaw('invoiceAmount IS NULL OR invoiceAmount =""')
                                    ->orwhereRaw('unitCost IS NULL OR unitCost =""');
                            });

                        if (!empty($detailValidation->get()->toArray())) {

                            /*
                             * check policy 15
                             *  Allow to confirm the Customer invoice with contract number
                             *  This policy should work only for Revenue GL
                             * */

                            $policyRGLCID = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                                ->where('companySystemID', $input['companySystemID'])
                                ->where('isYesNO', 1)
                                ->exists();

                            foreach ($detailValidation->get()->toArray() as $item) {

                                $validators = \Validator::make($item, [
                                    'serviceLineSystemID' => 'required|numeric|min:1',
                                    'serviceLineCode' => 'required|min:1',
                                    'unitOfMeasure' => 'required|numeric|min:1',
                                    'invoiceQty' => 'required|numeric|min:1',
                                    'salesPrice' => 'required|numeric|min:1',
                                ], [

                                    'serviceLineSystemID.required' => trans('custom.serviceline_system_id_required'),
                                    'serviceLineCode.required' => trans('custom.serviceline_code_required'),
                                    'unitOfMeasure.required' => trans('custom.unit_of_measure_required'),
                                    'invoiceQty.required' => trans('custom.invoice_qty_required'),
                                    'salesPrice.required' => trans('custom.sales_price_required'),
                                    'unitCost.required' => trans('custom.unit_cost_required')

                                ]);

                                if ($validators->fails()) {
                                    return [
                                        'status' => false,
                                        'code' => 422,
                                        'message' => $validators->messages()
                                    ];
                                }

                                if(!$policyRGLCID){

                                    $glSystemID = isset($item['glSystemID'])?$item['glSystemID']:0;
                                    $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')
                                        ->where('chartOfAccountSystemID', $glSystemID)
                                        ->where('controlAccountsSystemID',1)// Revenue
                                        ->exists();

                                    if($chartOfAccount){
                                        $contractValidator = \Validator::make($item, [
                                            'contractID' => 'required|numeric|min:1'
                                        ], [
                                            'contractID.required' => trans('custom.contract_no_required')
                                        ]);
                                        if ($contractValidator->fails()) {
                                            return [
                                                'status' => false,
                                                'code' => 422,
                                                'message' => $contractValidator->messages()
                                            ];
                                        }
                                    }

                                }

                            }

                        }
                        $groupby = CustomerInvoiceDirectDetail::select('serviceLineCode')->where('custInvoiceDirectID', $id)->groupBy('serviceLineCode')->get();
                        CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $id)->where('contractID', 0)->update(['contractID' => null]);
                        $groupbycontract = CustomerInvoiceDirectDetail::select('contractID')->where('custInvoiceDirectID', $id)->groupBy('contractID')->get();

                        if (count($groupby) != 0) {

                            if (count($groupbycontract) > 1) {
                                return [
                                    'status' => false,
                                    'code' => 500,
                                    'message' => trans('custom.you_cannot_continue_multiple_contract')
                                ];
                            } else {

                                // VAT configuration validation
                                $taxSum = Taxdetail::where('documentSystemCode', $id)
                                    ->where('companySystemID', $customerInvoiceDirect->companySystemID)
                                    ->where('documentSystemID', $customerInvoiceDirect->documentSystemiD)
                                    ->sum('amount');

                                if($taxSum  > 0 && empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))){
                                    return [
                                        'status' => false,
                                        'code' => 500,
                                        'message' => trans('custom.cannot_confirm_output_vat_gl')
                                    ];
                                }

                                $params = array(
                                    'autoID' => $id,
                                    'company' => $customerInvoiceDirect->companySystemID,
                                    'document' => $customerInvoiceDirect->documentSystemiD,
                                    'segment' => '',
                                    'category' => '',
                                    'amount' => '',
                                    'isAutoCreateDocument' => isset($input['isAutoCreateDocument'])
                                );

                                $customerInvoiceDirect = CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$id)->first();
                                $customerInvoiceDirect->update($_post);
                                $confirm = \Helper::confirmDocument($params);
                                if (!$confirm["success"]) {
                                    return [
                                        'status' => false,
                                        'code' => 500,
                                        'message' => $confirm["message"]
                                    ];
                                } else {
                                    return [
                                        'status' => true,
                                        'data' => $customerInvoiceDirect->refresh()->toArray(),
                                        'message' => trans('custom.customer_invoice_confirmed'),
                                        'detail' => $confirm['data'] ?? null
                                    ];
                                }
                            }
                        } else {
                            return [
                                'status' => false,
                                'code' => 500,
                                'message' => trans('custom.no_invoice_details_found')
                            ];
                        }

                    }
                }

            }
        }
        else {
            CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$id)->update($_post);
            return [
                'status' => true,
                'data' => $_post,
                'message' => trans('custom.invoice_updated_successfully')
            ];
        }
    }

    public static function customerInvoiceDirectDetailsStore($request): array {

        /* $amount = $request['amount'];
         $comments = $request['comments'];*/
        $companySystemID = $request['companySystemID'];
        /* $contractID = $request['contractID'];*/
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];
        $glCode = $request['glCode'];
        /* $qty = $request['qty'];*/
        /* $serviceLineSystemID = $request['serviceLineSystemID'];
         $unitCost = $request['unitCost'];
         $unitID = $request['unitID'];*/


        /*this*/


        /*get master*/
        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        $bookingInvCode = $master->bookingInvCode;
        /*selectedPerformaMaster*/


        $tax = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();
        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue !');
        }

        $myCurr = $master->custTransactionCurrencyID;
        /*currencyID*/

        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        /*$serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $serviceLineSystemID)->first();*/
        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();
        $totalAmount = 0; //$unitCost * $qty;

        $addToCusInvDetails['custInvoiceDirectID'] = $custInvoiceDirectAutoID;
        $addToCusInvDetails['companyID'] = $master->companyID;
        $addToCusInvDetails['companySystemID'] = $companySystemID;
        /*  $addToCusInvDetails['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;*/
        /*        $addToCusInvDetails['serviceLineCode'] = $serviceLine->ServiceLineCode;*/
        $addToCusInvDetails['customerID'] = $master->customerID;
        $addToCusInvDetails['glSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $addToCusInvDetails['glCode'] = $chartOfAccount->AccountCode;
        $addToCusInvDetails['glCodeDes'] = $chartOfAccount->AccountDescription;
        $addToCusInvDetails['accountType'] = $chartOfAccount->catogaryBLorPL;
        $addToCusInvDetails['comments'] = $master->comments;
        $addToCusInvDetails['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
        $addToCusInvDetails['invoiceAmountCurrencyER'] = 1;
        /* $addToCusInvDetails['unitOfMeasure'] = $unitID;
         $addToCusInvDetails['invoiceQty'] = $qty;
         $addToCusInvDetails['unitCost'] = $unitCost;*/
        $addToCusInvDetails['invoiceAmount'] = round($totalAmount, $decimal);

        $addToCusInvDetails['localCurrency'] = $master->localCurrencyID;
        $addToCusInvDetails['localCurrencyER'] = $master->localCurrencyER;

        $addToCusInvDetails['comRptCurrency'] = $master->companyReportingCurrencyID;
        $addToCusInvDetails['comRptCurrencyER'] = $master->companyReportingER;
        $addToCusInvDetails["comRptAmount"] = 0; // \Helper::roundValue($MyRptAmount);
        $addToCusInvDetails["localAmount"] = 0; // \Helper::roundValue($MyLocalAmount);
        if($master->isPerforma==0){
            if(isset($request['isAutoCreateDocument']) && $request['isAutoCreateDocument']){
                $addToCusInvDetails['unitOfMeasure'] = $request['unitOfMeasure'];
                $addToCusInvDetails['invoiceQty'] = $request['invoiceQty'];
            }
            else{
                $addToCusInvDetails['unitOfMeasure'] = 7;
                $addToCusInvDetails['invoiceQty'] = 1;
            }
        }

        $checkIsVatEligible = false;

        if(isset($request['isAutoCreateDocument']) && $request['isAutoCreateDocument']){
            if ($master->vatRegisteredYN && $master->isPerforma != 2){
                $checkIsVatEligible = true;
            }
            elseif (!$master->vatRegisteredYN){
                if(($request['VATAmount'] > 0) || ($request['VATPercentage'] > 0)){
                    return [
                        'status' => false,
                        'message' => trans('custom.company_not_registered_vat')
                    ];
                }
            }
        }
        elseif ($master->vatRegisteredYN && $master->isPerforma != 2) {
            $checkIsVatEligible = true;
        }

        if ($checkIsVatEligible){
            $vatDetails = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
            $addToCusInvDetails['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $addToCusInvDetails['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $addToCusInvDetails['VATPercentage'] = $vatDetails['percentage'];
        }

        DB::beginTransaction();

        try {
            $customerInvoiceDirectDetail = CustomerInvoiceDirectDetail::create($addToCusInvDetails);
            $details = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as bookingAmountTrans"), DB::raw("SUM(localAmount) as bookingAmountLocal"), DB::raw("SUM(comRptAmount) as bookingAmountRpt"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($details);


            DB::commit();

            if(isset($request['isAutoCreateDocument']) && $request['isAutoCreateDocument']){
                $inputData = $customerInvoiceDirectDetail->refresh()->toArray();

                $inputData['serviceLineSystemID'] = $request['serviceLineSystemID'];
                $inputData['salesPrice'] = $request['salesPrice'];
                $inputData['discountPercentage'] = $request['discountPercentage'];
                $inputData['discountAmountLine'] = $request['discountAmountLine'];
                $inputData['VATAmount'] = $request['VATAmount'];
                $inputData['VATPercentage'] = $request['VATPercentage'];
                $inputData['isAutoCreateDocument'] = true;

                $returnData = self::customerInvoiceDirectDetailsUpdate($inputData);

                if($returnData['status']){
                    return [
                        'status' => true,
                        'data' => $returnData['data'],
                        'message' => $returnData['message']
                    ];
                }
                else{
                    return [
                        'status' => false,
                        'message' => $returnData['message']
                    ];
                }
            }
            else{
                return [
                    'status' => true,
                    'data' => $customerInvoiceDirectDetail->refresh(),
                    'message' => trans('custom.successfully_created')
                ];
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return [
                'status' => false,
                'message' => trans('custom.error_occurred')
            ];
        }
    }

    public static function customerInvoiceDirectDetailsUpdate($input): array {
        $id = $input['custInvDirDetAutoID'];

        $detail = CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $id)->first();

        if (empty($detail)) {
            return [
                'status' => false,
                'message' => trans('custom.customer_invoice_direct_detail_not_found')
            ];
        }

        $master = CustomerInvoiceDirect::select('*')->where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->first();

        if (empty($master)) {
            return [
                'status' => false,
                'message' => trans('custom.customer_invoice_direct_not_found')
            ];
        }

        $tax = Taxdetail::where('documentSystemCode', $detail->custInvoiceDirectID)
            ->where('companySystemID', $master->companySystemID)
            ->where('documentSystemID', $master->documentSystemiD)
            ->first();

        if (!empty($tax)) {
            // return $this->sendError('Please delete tax details to continue');
        }


        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($master->documentSystemiD, $master->companySystemID, $id, $input, $master->customerID, $master->isPerforma);

        if (!$validateVATCategories['status']) {
            return [
                'status' => false,
                'type' => array('type' => 'vat'),
                'message' => $validateVATCategories['message']
            ];
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];
        }

        if ($input['contractID'] != $detail->contractID) {

            $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob', 'contractStatus')
                ->where('CompanyID', $detail->companyID)
                ->where('contractUID', $input['contractID'])
                ->first();

            $input['clientContractID'] = $contract->ContractNumber;

            if (!empty($contract)) {
                if($contract->contractStatus != 6){
                    if ($contract->paymentInDaysForJob <= 0) {
                        return [
                            'status' => false,
                            'message' => trans('custom.payment_period_not_updated_contract')
                        ];
                    }
                }
            } else {
                return [
                    'status' => false,
                    'message' => trans('custom.contract_not_exist')
                ];
            }
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            return [
                'status' => false,
                'type' => array('type' => 'discountPercentageError'),
                'message' => trans('custom.discount_percentage_cannot_be_greater')
            ];
        }

        if (isset($input["discountAmountLine"]) && isset($input['salesPrice']) && $input['discountAmountLine'] > $input['salesPrice']) {
            return [
                'status' => false,
                'type' => array('type' => 'discountAmountLineError'),
                'message' => trans('custom.discount_amount_cannot_be_greater')
            ];
        }

        $vatCategories = TaxVatCategories::where('taxVatSubCategoriesAutoID', $input["vatSubCategoryID"])
                                                ->where('mainCategory', $input["vatMasterCategoryID"])
                                                ->first();

        if (isset($input["VATPercentage"]) && $input["VATPercentage"] > 100) {
            return [
                'status' => false,
                'type' => array('type' => 'VATPercentageError'),
                'message' => trans('custom.vat_percentage_cannot_be_greater')
            ];
        }

        if ($vatCategories) {
            if($vatCategories->applicableOn == 1){
                if (isset($input["VATAmount"]) && isset($input['salesPrice']) && $input['VATAmount'] > $input['salesPrice']) {
                    return [
                        'status' => false,
                        'type' => array('type' => 'VATAmountError'),
                        'message' => trans('custom.vat_amount_cannot_be_greater_sales_price')
                    ];
                }
            }

            if($vatCategories->applicableOn == 2){
                if (isset($input["VATAmount"]) && isset($input['unitCost']) && $input['VATAmount'] > $input['unitCost']) {
                    return [
                        'status' => false,
                        'type' => array('type' => 'VATAmountError'),
                        'message' => trans('custom.vat_amount_cannot_be_greater_unit_price')
                    ];
                }
            }
        }


        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            $input['contractID'] = NULL;
            $input['clientContractID'] = NULL;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        $input['invoiceQty']= ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
        $input['salesPrice']= ($input['salesPrice'] != '' ? $input['salesPrice'] : 0);

        $input['salesPrice'] = floatval($input['salesPrice'] );

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(($input["discountAmountLine"] != 0) && ($input["discountPercentage"] != 0)){
                $checkDiscountAmount = $input['salesPrice'] * $input["discountPercentage"] / 100;
                $checkDiscountPercentage = ($input["discountAmountLine"] / $input['salesPrice']) * 100;

                if(($checkDiscountAmount != $input["discountAmountLine"]) && ($checkDiscountPercentage != $input["discountPercentage"])){
                    return [
                        'status' => false,
                        'message' => trans('custom.discount_percentage_amount_not_matching')
                    ];
                }
            }
            elseif ($input["discountAmountLine"] != 0){
                $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
            }
            elseif ($input["discountPercentage"] != 0){
                $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            }
            else{
                $input["discountPercentage"] = 0;
                $input["discountAmountLine"] = 0;
            }
        }
        else{
            if(isset($input['by']) && ($input['by'] == 'discountPercentage' || $input['by'] == 'discountAmountLine')){
                if ($input['by'] === 'discountPercentage') {
                    $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
                } else if ($input['by'] === 'discountAmountLine') {
                    if($input['salesPrice'] > 0){
                        $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                    } else {
                        $input["discountPercentage"] = 0;
                    }
                }
            } else {
                if ($input['discountPercentage'] != 0) {
                    $input["discountAmountLine"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
                } else if ($input['discountAmountLine'] != 0){
                    if($input['salesPrice'] > 0){
                        $input["discountPercentage"] = ($input["discountAmountLine"] / $input['salesPrice']) * 100;
                    } else {
                        $input["discountPercentage"] = 0;
                    }
                }
            }
        }

        $input['unitCost'] = $input['salesPrice'] - $input["discountAmountLine"];
        $unitCostForCalculation = ($vatCategories && $vatCategories->applicableOn == 1) ? $input['salesPrice'] : $input['unitCost'];
        if ($input['invoiceQty'] != $detail->invoiceQty || $input['unitCost'] != $detail->unitCost) {
            $myCurr = $master->custTransactionCurrencyID;               /*currencyID*/
            //$companyCurrency = \Helper::companyCurrency($myCurr);
            $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

            $input['invoiceAmountCurrency'] = $master->custTransactionCurrencyID;
            $input['invoiceAmountCurrencyER'] = 1;
            $totalAmount = ($input['unitCost'] != ''?$input['unitCost']:0) * ($input['invoiceQty'] != ''?$input['invoiceQty']:0);
            $input['invoiceAmount'] = round($totalAmount, $decimal);

            if($master->isPerforma == 2) {
                $totalAmount = $input['salesPrice'];
                $input['invoiceAmount'] = round($input['salesPrice'], $decimal);
            }

            /**/
            $MyRptAmount = 0;
            if ($master->custTransactionCurrencyID == $master->companyReportingCurrencyID) {
                $MyRptAmount = $totalAmount;
            } else {
                if ($master->companyReportingER > $master->custTransactionCurrencyER) {
                    if ($master->companyReportingER > 1) {
                        $MyRptAmount = ($totalAmount / $master->companyReportingER);
                    } else {
                        $MyRptAmount = ($totalAmount * $master->companyReportingER);
                    }
                } else {
                    if ($master->companyReportingER > 1) {
                        $MyRptAmount = ($totalAmount * $master->companyReportingER);
                    } else {
                        $MyRptAmount = ($totalAmount / $master->companyReportingER);
                    }
                }
            }
            $input["comRptAmount"] =   \Helper::roundValue($MyRptAmount);
            if ($master->custTransactionCurrencyID == $master->localCurrencyID) {
                $MyLocalAmount = $totalAmount;
            } else {
                if ($master->localCurrencyER > $master->custTransactionCurrencyER) {
                    if ($master->localCurrencyER > 1) {
                        $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                    } else {
                        $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                    }
                } else {
                    if ($master->localCurrencyER > 1) {
                        $MyLocalAmount = ($totalAmount * $master->localCurrencyER);
                    } else {
                        $MyLocalAmount = ($totalAmount / $master->localCurrencyER);
                    }
                }
            }
            $input["localAmount"] =  \Helper::roundValue($MyLocalAmount);
        }

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(($input["VATAmount"] != 0) && ($input["VATPercentage"] != 0)){
                $checkVatAmount = $unitCostForCalculation * $input["VATPercentage"] / 100;
                $checkVatPercentage = ($input["VATAmount"] / $unitCostForCalculation) * 100;

                if(($checkVatAmount != $input["VATAmount"]) && ($checkVatPercentage != $input["VATPercentage"])){
                    return [
                        'status' => false,
                        'message' => trans('custom.vat_percentage_amount_not_matching')
                    ];
                }
            }
            elseif ($input["VATAmount"] != 0){
                $input["VATPercentage"] = ($input["VATAmount"] / $unitCostForCalculation) * 100;
            }
            elseif ($input["VATPercentage"] != 0){
                $input["VATAmount"] = $unitCostForCalculation * $input["VATPercentage"] / 100;
            }
            else{
                $input["VATAmount"] = 0;
                $input["VATPercentage"] = 0;
            }

            if($input["VATPercentage"] > 100){
                return [
                    'status' => false,
                    'message' => trans('custom.vat_percentage_exceed_100')
                ];
            }

            if($input['VATAmount'] > $input['salesPrice']){
                return [
                    'status' => false,
                    'message' => 'Vat amount cannot be greater than sales price'
                ];
            }
        }
        else{
            if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount') && is_numeric($input['VATPercentage'])){
                if ($input['by'] === 'VATPercentage') {
                    $input["VATAmount"] = $unitCostForCalculation * $input["VATPercentage"] / 100;
                } else if ($input['by'] === 'VATAmount') {
                    if($unitCostForCalculation > 0){
                        $input["VATPercentage"] = ($input["VATAmount"] / $unitCostForCalculation) * 100;
                    } else {
                        $input["VATPercentage"] = 0;
                    }
                }
            } else {
                if ($input['VATPercentage'] != 0 && is_numeric($input['VATPercentage'])) {
                    $input["VATAmount"] = $unitCostForCalculation * $input["VATPercentage"] / 100;
                } else if ($input['VATAmount'] != 0){
                    if($unitCostForCalculation > 0){
                        $input["VATPercentage"] = ($input["VATAmount"] / $unitCostForCalculation) * 100;
                    } else {
                        $input["VATPercentage"] = 0;
                    }
                }
            }
        }

        $decimalPlaces = Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);
        $input["VATAmount"] = round($input["VATAmount"],$decimalPlaces);

        $currencyConversionVAT = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $input['VATAmount']);
        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;
        if($policy == true) {
            $input['VATAmountLocal'] = \Helper::roundValue($input["VATAmount"] / $master->localCurrencyER);
            $input['VATAmountRpt'] = \Helper::roundValue($input["VATAmount"] / $master->companyReportingER);
        }
        if($policy == false) {
            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }
        if (isset($input['by'])) {
            unset($input['by']);
        }

        if (isset($input['vatMasterCategoryAutoID'])) {
            unset($input['vatMasterCategoryAutoID']);
        }

        if (isset($input['itemPrimaryCode'])) {
            unset($input['itemPrimaryCode']);
        }

        if (isset($input['itemDescription'])) {
            unset($input['itemDescription']);
        }

        if (isset($input['subCategoryArray'])) {
            unset($input['subCategoryArray']);
        }

        if (isset($input['subCatgeoryType'])) {
            unset($input['subCatgeoryType']);
        }

        if (isset($input['exempt_vat_portion'])) {
            unset($input['exempt_vat_portion']);
        }

        if (isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']) {
            unset($input['isAutoCreateDocument']);
        }

        DB::beginTransaction();

        try {
            $x=CustomerInvoiceDirectDetail::where('custInvDirDetAutoID', $detail->custInvDirDetAutoID)->update($input);
            $allDetail = CustomerInvoiceDirectDetail::select(DB::raw("IFNULL(SUM(invoiceAmount),0) as bookingAmountTrans"), DB::raw("IFNULL(SUM(localAmount),0) as bookingAmountLocal"), DB::raw("IFNULL(SUM(comRptAmount),0) as bookingAmountRpt"))->where('custInvoiceDirectID', $detail->custInvoiceDirectID)->first()->toArray();

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $detail->custInvoiceDirectID)->update($allDetail);

            if($master->isPerforma != 2) {
                $resVat =  self::updateTotalVAT($master->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                    return [
                        'status' => false,
                        'message' => $resVat['message']
                    ];
                }
            }

            DB::commit();
            return [
                'status' => true,
                'data' => 's',
                'message' => trans('custom.successfully_created')
            ];
        } catch (\Exception $exception) {
            DB::rollback();
            return [
                'status' => false,
                'message' => $exception
            ];
        }
    }

    public static function customerInvoiceItemDetailsStore($input): array {

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $updateData = $input;
        }

        $companySystemID = $input['companySystemID'];

        if(isset($input['isInDOorCI'])) {
            unset($input['timesReferred']);
            $item = ItemAssigned::with(['item_master'])
                ->where('itemCodeSystem', $input['itemCode'])
                ->where('companySystemID', $companySystemID)
                ->first();

        }else {
            $item = ItemAssigned::with(['item_master'])
                ->where('idItemAssigned', $input['itemCode'])
                ->where('companySystemID', $companySystemID)
                ->first();

        }
        if (empty($item)) {
            return [
                'status' => false,
                'message' => trans('custom.item_not_found')
            ];
        }

        $customerInvoiceDirect = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

        if (empty($customerInvoiceDirect)) {
            return [
                'status' => false,
                'message' => trans('custom.customer_invoice_direct_not_found')
            ];
        }

        $is_pref = $customerInvoiceDirect->isPerforma;

        if(CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->where('itemFinanceCategoryID','!=',$item->financeCategoryMaster)->exists()){
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.different_finance_category_found')
            ];
        }

        /* TODO confirm approve check here*/

        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;

        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        $input['trackingType'] = isset($item->item_master->trackingType) ? $item->item_master->trackingType : null;
        $input['convertionMeasureVal'] = 1;

        if(!isset($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }
        else if(isset($updateData['isAutoCreateDocument']) && $updateData['isAutoCreateDocument']){
            $input['qtyIssuedDefaultMeasure'] = 0;
        }

        $input['comments'] = '';
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['localCurrencyID'] = $customerInvoiceDirect->localCurrencyID;
        $input['localCurrencyER'] = $customerInvoiceDirect->localCurrencyER;


        $data = array('companySystemID' => $companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);

        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];


        $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];

        if ($item->financeCategoryMaster == 1){
            if ($input['currentStockQty'] <= 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.stock_qty_zero_cannot_issue_item')
                ];
            }

            if ($input['currentWareHouseStockQty'] <= 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.warehouse_stock_qty_zero_cannot_issue_item')
                ];
            }

            if ($input['issueCostLocal'] == 0 || $input['issueCostRpt'] == 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.cost_zero_cannot_issue_item')
                ];
            }

            if ($input['issueCostLocal'] < 0 || $input['issueCostRpt'] < 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.cost_negative_cannot_issue_item')
                ];
            }
        }

        $input['issueCostLocalTotal'] =  $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];

        $input['reportingCurrencyID'] = $customerInvoiceDirect->companyReportingCurrencyID;
        $input['reportingCurrencyER'] = $customerInvoiceDirect->companyReportingER;

        $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];
        $input['marginPercentage'] = (isset($updateData['isAutoCreateDocument']) && $updateData['isAutoCreateDocument']) ? $input['marginPercentage'] : 0;

        $companyCurrencyConversion = Helper::currencyConversion($companySystemID,$customerInvoiceDirect->companyReportingCurrencyID,$customerInvoiceDirect->custTransactionCurrencyID,$input['issueCostRpt']);
        $input['sellingCurrencyID'] = $customerInvoiceDirect->custTransactionCurrencyID;
        $input['sellingCurrencyER'] = $customerInvoiceDirect->custTransactionCurrencyER;
        $input['sellingCost'] = ($companyCurrencyConversion['documentAmount'] != 0) ? $companyCurrencyConversion['documentAmount'] : 1.0;
        if((isset($input['customerCatalogDetailID']) && $input['customerCatalogDetailID']>0)){
            $catalogDetail = CustomerCatalogDetail::find($input['customerCatalogDetailID']);

            if(empty($catalogDetail)){
                return [
                    'status' => false,
                    'message' => trans('custom.customer_catalog_not_found')
                ];
            }

            if($customerInvoiceDirect->custTransactionCurrencyID != $catalogDetail->localCurrencyID){
                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID,$catalogDetail->localCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID,$catalogDetail->localPrice);
                if(!empty($currencyConversion)){
                    $catalogDetail->localPrice = $currencyConversion['documentAmount'];
                }
            }

            $input['sellingCostAfterMargin'] = $catalogDetail->localPrice;
            $input['marginPercentage'] = ($input['sellingCostAfterMargin'] - $input['sellingCost'])/$input['sellingCost']*100;
            $input['part_no'] = $catalogDetail->partNo;
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
            $input['part_no'] = $item->secondaryItemCode;
        }

        if(isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
//            $input['sellingCostAfterMarginLocal'] = ($input['issueCostLocal']) + ($input['issueCostLocal']*$input['marginPercentage']/100);
//            $input['sellingCostAfterMarginRpt'] = ($input['issueCostRpt']) + ($input['issueCostRpt']*$input['marginPercentage']/100);
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
//            $input['sellingCostAfterMarginLocal'] = $input['issueCostLocal'];
//            $input['sellingCostAfterMarginRpt'] = $input['issueCostRpt'];
        }

        $costs = self::updateCostBySellingCost($input,$customerInvoiceDirect);
        $input['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
        $input['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

        if($is_pref == 2 && $customerInvoiceDirect->salesType == 3){
            $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'] * $input['userQty'];
        }else{
            $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'];
        }

        /*round to 7 decimals*/
        $input['issueCostLocal'] = Helper::roundValue($input['issueCostLocal']);
        $input['issueCostLocalTotal'] = Helper::roundValue($input['issueCostLocalTotal']);
        $input['issueCostRpt'] = Helper::roundValue($input['issueCostRpt']);
        $input['issueCostRptTotal'] = Helper::roundValue($input['issueCostRptTotal']);
        $input['sellingCost'] = Helper::roundValue($input['sellingCost']);
        $input['sellingCostAfterMargin'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['salesPrice'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['sellingTotal'] = Helper::roundValue($input['sellingTotal']);
        $input['sellingCostAfterMarginLocal'] = Helper::roundValue($input['sellingCostAfterMarginLocal']);
        $input['sellingCostAfterMarginRpt'] = Helper::roundValue($input['sellingCostAfterMarginRpt']);

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();
        if (!empty($financeItemCategorySubAssigned)) {
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $input['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
            $input['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;

            $input['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
            $input['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
        } else {
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.finance_item_category_sub_assigned_not_found')
            ];
        }

        if((!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']) && $input['itemFinanceCategoryID'] != 2){
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.bs_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
            ];
        }elseif (!$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']){
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.cost_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
            ];
        }elseif (!$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']){
            return [
                'status' => false,
                'code' => 500,
                'message' => trans('custom.revenue_account_cannot_be_null', ['itemCode' => $item->itemPrimaryCode, 'itemDescription' => $item->itemDescription])
            ];
        }

        /*if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']
            || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']
            || !$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']) {
            return $this->sendError("Account code not updated.", 500);
        }*/


        if ($input['itemFinanceCategoryID'] == 1 || $input['itemFinanceCategoryID'] == 2 || $input['itemFinanceCategoryID'] == 4) {
            $alreadyAdded = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->where('itemCodeSystem',$item->itemCodeSystem)->first();

            if ($alreadyAdded) {
                if(($input['itemFinanceCategoryID'] != 2 )&& ($input['itemFinanceCategoryID'] != 4 ))
                {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.selected_item_already_added')
                    ];
                }

            }

        }

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if($item->financeCategoryMaster == 1){
            $checkWhether = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $customerInvoiceDirect->custInvoiceDirectAutoID)
                ->where('companySystemID', $companySystemID)
                ->select([
                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.bookingInvCode',
                    'erp_custinvoicedirect.wareHouseSystemCode',
                    'erp_custinvoicedirect.approved'
                ])
                ->groupBy(
                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.companySystemID',
                    'erp_custinvoicedirect.bookingInvCode',
                    'erp_custinvoicedirect.wareHouseSystemCode',
                    'erp_custinvoicedirect.approved'
                )
                ->whereHas('issue_item_details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->where('canceledYN', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhether)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.customer_invoice_pending_approval', ['bookingInvCode' => $checkWhether->bookingInvCode])
                ];
            }


            $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $companySystemID)
//            ->where('wareHouseFrom', $customerInvoiceDirect->wareHouseSystemCode)
                ->select([
                    'erp_itemissuemaster.itemIssueAutoID',
                    'erp_itemissuemaster.companySystemID',
                    'erp_itemissuemaster.wareHouseFromCode',
                    'erp_itemissuemaster.itemIssueCode',
                    'erp_itemissuemaster.approved'
                ])
                ->groupBy(
                    'erp_itemissuemaster.itemIssueAutoID',
                    'erp_itemissuemaster.companySystemID',
                    'erp_itemissuemaster.wareHouseFromCode',
                    'erp_itemissuemaster.itemIssueCode',
                    'erp_itemissuemaster.approved'
                )
                ->whereHas('details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherItemIssueMaster)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.material_issue_pending_approval', ['itemIssueCode' => $checkWhetherItemIssueMaster->itemIssueCode])
                ];
            }

            $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
//            ->where('locationFrom', $customerInvoiceDirect->wareHouseSystemCode)
                ->select([
                    'erp_stocktransfer.stockTransferAutoID',
                    'erp_stocktransfer.companySystemID',
                    'erp_stocktransfer.locationFrom',
                    'erp_stocktransfer.stockTransferCode',
                    'erp_stocktransfer.approved'
                ])
                ->groupBy(
                    'erp_stocktransfer.stockTransferAutoID',
                    'erp_stocktransfer.companySystemID',
                    'erp_stocktransfer.locationFrom',
                    'erp_stocktransfer.stockTransferCode',
                    'erp_stocktransfer.approved'
                )
                ->whereHas('details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherStockTransfer)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.stock_transfer_pending_approval', ['stockTransferCode' => $checkWhetherStockTransfer->stockTransferCode])
                ];
            }

            // check in delivery order
            $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
                ->select([
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.deliveryOrderCode'
                ])
                ->groupBy(
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.companySystemID'
                )
                ->whereHas('detail', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approvedYN', 0)
                ->first();

            if (!empty($checkWhetherDeliveryOrder)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.delivery_order_pending_approval', ['deliveryOrderCode' => $checkWhetherDeliveryOrder->deliveryOrderCode])
                ];
            }

            /*Check in purchase return*/
            $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
                ->select([
                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                    'erp_purchasereturnmaster.companySystemID',
                    'erp_purchasereturnmaster.purchaseReturnLocation',
                    'erp_purchasereturnmaster.purchaseReturnCode',
                ])
                ->groupBy(
                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                    'erp_purchasereturnmaster.companySystemID',
                    'erp_purchasereturnmaster.purchaseReturnLocation'
                )
                ->whereHas('details', function ($query) use ($input) {
                    $query->where('itemCode', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherPR)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.purchase_return_pending_approval', ['purchaseReturnCode' => $checkWhetherPR->purchaseReturnCode])
                ];
            }
        }

        $checkIsVatEligible = false;

        if(isset($updateData['isAutoCreateDocument']) && $updateData['isAutoCreateDocument']){
            if ($customerInvoiceDirect->vatRegisteredYN){
                $checkIsVatEligible = true;
            }
            else {
                if(($updateData['VATAmount'] > 0) || ($updateData['VATPercentage'] > 0)){
                    return [
                        'status' => false,
                        'message' => trans('custom.company_not_registered_vat')
                    ];
                }
            }
        }
        elseif ($customerInvoiceDirect->vatRegisteredYN) {
            $checkIsVatEligible = true;
        }

        if ($checkIsVatEligible){
            $vatDetails = TaxService::getVATDetailsByItem($customerInvoiceDirect->companySystemID, $input['itemCodeSystem'], $customerInvoiceDirect->customerID,0);
            $input['VATPercentage'] = $vatDetails['percentage'];
            $input['VATApplicableOn'] = $vatDetails['applicableOn'];
            $input['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $input['VATAmount'] = 0;
            if (isset($input['sellingCostAfterMargin']) && $input['sellingCostAfterMargin'] > 0) {
                $input['VATAmount'] = (($input['sellingCostAfterMargin'] / 100) * $vatDetails['percentage']);
            }
            $currencyConversionVAT = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $customerInvoiceDirect->custTransactionCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $input['VATAmount']);

            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }

        $customerInvoiceItemDetails = CustomerInvoiceItemDetails::create($input);

        if(isset($updateData['isAutoCreateDocument']) && $updateData['isAutoCreateDocument']){
            $inputData = $customerInvoiceItemDetails->refresh()->toArray();

            $inputData['unitOfMeasureIssued'] = $updateData['itemUnitOfMeasure'];
            $inputData['qtyIssued'] = $updateData['qtyIssued'];
            $inputData['salesPrice'] = $updateData['salesPrice'];
            $inputData['marginPercentage'] = $updateData['marginPercentage'];
            $inputData['discountPercentage'] = $updateData['discountPercentage'];
            $inputData['discountAmount'] = $updateData['discountAmount'];
            $inputData['VATAmount'] = $updateData['VATAmount'];
            $inputData['VATPercentage'] = $updateData['VATPercentage'];
            $inputData['isAutoCreateDocument'] = true;

            $returnData = self::customerInvoiceItemDetailsUpdate($inputData);

            if($returnData['status']){
                return [
                    'status' => true,
                    'data' => $returnData['data'],
                    'message' => $returnData['message']
                ];
            }
            else{
                return [
                    'status' => false,
                    'message' => $returnData['message']
                ];
            }
        }
        else{
            return [
                'status' => true,
                'data' => $customerInvoiceItemDetails->toArray(),
                'message' => trans('custom.customer_invoice_item_details_saved_successfully')
            ];
        }
    }

    public static function customerInvoiceItemDetailsUpdate($input): array {
        $qtyError = array('type' => 'qty');
        $message = trans('custom.item_updated_successfully');

        $id = $input['customerItemDetailID'];

        $customerInvoiceItemDetails = CustomerInvoiceItemDetails::find($id);

        if (empty($customerInvoiceItemDetails)) {
            return [
                'status' => false,
                'message' => trans('custom.customer_invoice_item_details_not_found')
            ];
        }

        $customerDirectInvoice = CustomerInvoiceDirect::find($customerInvoiceItemDetails->custInvoiceDirectAutoID);

        if (empty($customerDirectInvoice)) {
            return [
                'status' => false,
                'message' => trans('custom.customer_invoice_details_not_found')
            ];
        }

        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($customerDirectInvoice->documentSystemiD, $customerDirectInvoice->companySystemID, $id, $input, $customerDirectInvoice->customerID, $customerDirectInvoice->isPerforma);

        if (!$validateVATCategories['status']) {
            return [
                'status' => false,
                'code' => 500,
                'type' => array('type' => 'vat'),
                'message' => $validateVATCategories['message']
            ];
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            return [
                'status' => false,
                'message' => trans('custom.discount_percentage_cannot_be_greater')
            ];
        }

        if (isset($input["discountAmount"]) && isset($input['salesPrice']) && $input['discountAmount'] > $input['salesPrice']) {
            return [
                'status' => false,
                'message' => trans('custom.discount_amount_cannot_be_greater')
            ];
        }


        $vatCategories = TaxVatCategories::where('taxVatSubCategoriesAutoID', $input["vatSubCategoryID"])
                                                ->where('mainCategory', $input["vatMasterCategoryID"])
                                                ->first();


        if (isset($input["VATPercentage"]) && $input["VATPercentage"] > 100) {
            return [
                'status' => false,
                'type' => array('type' => 'VATPercentageError'),
                'message' => trans('custom.vat_percentage_cannot_be_greater')
            ];
        }

        if(!empty($vatCategories)) {
            if ($vatCategories->applicableOn == 1) {
                if (isset($input["VATAmount"]) && isset($input['salesPrice']) && $input['VATAmount'] > $input['salesPrice']) {
                    return [
                        'status' => false,
                        'type' => array('type' => 'VATAmountError'),
                        'message' => trans('custom.vat_amount_cannot_be_greater_sales_price')
                    ];
                }
            }

            if ($vatCategories->applicableOn == 2) {
                if (isset($input["VATAmount"]) && isset($input['sellingCostAfterMargin']) && $input['VATAmount'] > $input['sellingCostAfterMargin']) {
                    return [
                        'status' => false,
                        'type' => array('type' => 'VATAmountError'),
                        'message' => trans('custom.vat_amount_cannot_be_greater_unit_price')
                    ];
                }
            }
        }


        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => trans('custom.unit_conversion_not_valid_configured')
                ];
            }

            if ($unitConvention) {
                $convention = $unitConvention->conversion;
                $input['convertionMeasureVal'] = $convention;
                if ($convention > 0) {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] / $convention), 2);
                } else {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] * $convention), 2);
                }
            }
        } else {
            $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'];
        }

        /*margin calculation*/
        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if($input['marginPercentage'] != 0){
                $checkMarginPercentage = ($input['salesPrice'] - $input['sellingCost'])/$input['sellingCost']*100;
                if($checkMarginPercentage != $input['marginPercentage']){
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => trans('custom.sales_price_margin_not_matching')
                    ];
                }
            }
            else {
                $input['marginPercentage'] = ($input['salesPrice'] - $input['sellingCost'])/$input['sellingCost']*100;
            }
        }
        else{
            if(isset($input['by']) && $input['by']== 'salesPrice' ){
                if($input['sellingCost'] > 0 && $input['issueCostRpt'] > 0){
                    $input['marginPercentage'] = ($input['salesPrice'] - $input['sellingCost'])/$input['sellingCost']*100;
                }
                else{
                    $input['marginPercentage']=0;
                    if($customerInvoiceItemDetails->itemFinanceCategoryID != 1){
                        $input['sellingCost'] = $input['salesPrice'];
                    }
                }
            }
            elseif (isset($input['by']) && $input['by']== 'margin'){
                $input['salesPrice'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
            }
            elseif ($customerDirectInvoice->isPerforma != 5){
                if (isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
                    $input['salesPrice'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
                }else{
                    if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
                        $input['salesPrice'] = $input['sellingCost'];
                    }else{
                        $input['sellingCost'] = $input['salesPrice'];
                    }
                }
            }
        }

        $input['sellingCostAfterMargin'] = $input['salesPrice'];

        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(($input["discountAmount"] != 0) && ($input["discountPercentage"] != 0)){
                $checkDiscountAmount = $input['salesPrice'] * $input["discountPercentage"] / 100;
                $checkDiscountPercentage = ($input["discountAmount"] / $input['salesPrice']) * 100;

                if(($checkDiscountAmount != $input["discountAmount"]) && ($checkDiscountPercentage != $input["discountPercentage"])){
                    return [
                        'status' => false,
                        'message' => trans('custom.discount_percentage_amount_not_matching')
                    ];
                }
            }
            elseif ($input["discountAmount"] != 0){
                $input["discountPercentage"] = ($input["discountAmount"] / $input['salesPrice']) * 100;
            }
            elseif ($input["discountPercentage"] != 0){
                $input["discountAmount"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            }
            else{
                $input["discountPercentage"] = 0;
                $input["discountAmount"] = 0;
            }
        }
        else{
            if(isset($input['by']) && ($input['by'] == 'discountPercentage' || $input['by'] == 'discountAmount')){
                if ($input['by'] === 'discountPercentage') {
                    $input["discountAmount"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
                } else if ($input['by'] === 'discountAmount') {
                    if($input['salesPrice'] > 0){
                        $input["discountPercentage"] = ($input["discountAmount"] / $input['salesPrice']) * 100;
                    } else {
                        $input["discountPercentage"] = 0;
                    }
                }
            }
            else if ($customerDirectInvoice->isPerforma != 5) {
                if ($input['discountPercentage'] != 0) {
                    $input["discountAmount"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
                } else {
                    if($input['salesPrice'] > 0){
                        $input["discountPercentage"] = ($input["discountAmount"] / $input['salesPrice']) * 100;
                    } else {
                        $input["discountPercentage"] = 0;
                    }
                }
            }
        }

        $input['sellingCostAfterMargin'] = $input['sellingCostAfterMargin'] - $input["discountAmount"];


        $costs = self::updateCostBySellingCost($input,$customerDirectInvoice);
        $input['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
        $input['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

        $unitCostForCalculation = ($vatCategories && $vatCategories->applicableOn == 1) ? $input['salesPrice'] : $input['sellingCostAfterMargin'];
        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            if(($input["VATAmount"] != 0) && ($input["VATPercentage"] != 0)){
                $checkVatAmount = $unitCostForCalculation * $input["VATPercentage"] / 100;
                $checkVatPercentage = ($input["VATAmount"] / $unitCostForCalculation) * 100;

                if(($checkVatAmount != $input["VATAmount"]) && ($checkVatPercentage != $input["VATPercentage"])){
                    return [
                        'status' => false,
                        'message' => trans('custom.vat_percentage_amount_not_matching')
                    ];
                }
            }
            elseif ($input["VATAmount"] != 0){
                $input["VATPercentage"] = ($input["VATAmount"] / $input['sellingCostAfterMargin']) * 100;
            }
            elseif ($input["VATPercentage"] != 0){
                $input["VATAmount"] = $input['sellingCostAfterMargin'] * $input["VATPercentage"] / 100;
            }
            else{
                $input["VATPercentage"] = 0;
                $input["VATAmount"] = 0;
            }

            if($input["VATPercentage"] > 100){
                return [
                    'status' => false,
                    'message' => trans('custom.vat_percentage_exceed_100')
                ];
            }

            if($input['VATAmount'] > $unitCostForCalculation){
                return [
                    'status' => false,
                    'message' => 'Vat amount cannot be greater than sales price'
                ];
            }
        }
        else{
            if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount')){
                if ($input['by'] === 'VATPercentage') {
                    $input["VATAmount"] = $unitCostForCalculation * $input["VATPercentage"] / 100;
                } else if ($input['by'] === 'VATAmount') {
                    if($unitCostForCalculation > 0){
                        $input["VATPercentage"] = ($input["VATAmount"] / $unitCostForCalculation) * 100;
                    } else {
                        $input["VATPercentage"] = 0;
                    }
                }
            }
            else {
                if ($input['VATPercentage'] != 0) {
                    $input["VATAmount"] = $unitCostForCalculation * $input["VATPercentage"] / 100;
                } else {
                    if($unitCostForCalculation > 0){
                        $input["VATPercentage"] = ($input["VATAmount"] / $unitCostForCalculation) * 100;
                    } else {
                        $input["VATPercentage"] = 0;
                    }
                }
            }
        }

        $currencyConversionVAT = \Helper::currencyConversion($customerDirectInvoice->companySystemID, $customerDirectInvoice->custTransactionCurrencyID, $customerDirectInvoice->custTransactionCurrencyID, $input['VATAmount']);

        $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
        $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);

        if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
            if ($customerInvoiceItemDetails->issueCostLocal == 0) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Cost is 0. You cannot issue."
                ];
            }

            if ($customerInvoiceItemDetails->issueCostLocal < 0 || $customerInvoiceItemDetails->issueCostRpt < 0) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Cost is negative. You cannot issue."
                ];
            }

            if ($customerInvoiceItemDetails->currentStockQty <= 0) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Stock Qty is 0. You cannot issue."
                ];
            }

            if ($customerInvoiceItemDetails->currentWareHouseStockQty <= 0) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Warehouse stock Qty is 0. You cannot issue."
                ];
            }

            if ($input['qtyIssuedDefaultMeasure'] > $customerInvoiceItemDetails->currentStockQty) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'type' => $qtyError,
                    'message' => "Current stock Qty is: " . $customerInvoiceItemDetails->currentStockQty . " .You cannot issue more than the current stock qty."
                ];
            }

            if ($input['qtyIssuedDefaultMeasure'] > $customerInvoiceItemDetails->currentWareHouseStockQty) {
                CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0]);
                return [
                    'status' => false,
                    'code' => 500,
                    'type' => $qtyError,
                    'message' => "Current warehouse stock Qty is: " . $customerInvoiceItemDetails->currentWareHouseStockQty . " .You cannot issue more than the current warehouse stock qty."
                ];
            }
        }

        $input['issueCostLocalTotal'] = $customerInvoiceItemDetails->issueCostLocal * $input['qtyIssuedDefaultMeasure'];
        $input['issueCostRptTotal'] = $customerInvoiceItemDetails->issueCostRpt * $input['qtyIssuedDefaultMeasure'];


        if($customerDirectInvoice->isPerforma == 2 && $customerDirectInvoice->salesType == 3){
            $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'] * $input['userQty'];
        }else{
            $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'];
        }

        if ($input['qtyIssued'] == '' || is_null($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }

        $input['issueCostLocal'] = Helper::roundValue($input['issueCostLocal']);
        $input['issueCostLocalTotal'] = Helper::roundValue($input['issueCostLocalTotal']);
        $input['issueCostRpt'] = Helper::roundValue($input['issueCostRpt']);
        $input['issueCostRptTotal'] = Helper::roundValue($input['issueCostRptTotal']);
        $input['sellingCost'] = Helper::roundValue($input['sellingCost']);
        $input['sellingCostAfterMargin'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['sellingTotal'] = Helper::roundValue($input['sellingTotal']);
        $input['sellingCostAfterMarginLocal'] = Helper::roundValue($input['sellingCostAfterMarginLocal']);
        $input['sellingCostAfterMarginRpt'] = Helper::roundValue($input['sellingCostAfterMarginRpt']);

        $customerInvoiceItemDetails = CustomerInvoiceItemDetails::where('customerItemDetailID', $id)->first();

        $customerInvoiceItemDetails->update($input);

        $customerInvoiceItemDetails->warningMsg = 0;

        if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
            if (($customerInvoiceItemDetails->currentStockQty - $customerInvoiceItemDetails->qtyIssuedDefaultMeasure) < $customerInvoiceItemDetails->minQty) {
                $minQtyPolicy = CompanyPolicyMaster::where('companySystemID', $customerInvoiceItemDetails->companySystemID)
                    ->where('companyPolicyCategoryID', 6)
                    ->first();
                if (!empty($minQtyPolicy)) {
                    if ($minQtyPolicy->isYesNO == 1) {
                        $customerInvoiceItemDetails->warningMsg = 1;
                        $message = 'Quantity is falling below the minimum inventory level.';
                    }
                }
            }
        }

        $resVat = self::updateVatFromSalesQuotation($customerDirectInvoice->custInvoiceDirectAutoID);
        if (!$resVat['status']) {
            return [
                'status' => false,
                'message' => $resVat['message']
            ];
        }

        return [
            'status' => true,
            'data' => $customerInvoiceItemDetails->toArray(),
            'message' => $message
        ];
    }

    public static function updateVatFromSalesQuotation($custInvoiceDirectAutoID): array {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->with(['sales_quotation_detail'])
            ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            if ($invoice->isPerforma == 2 || $invoice->isPerforma == 5) {
                if($invoice->salesType == 3 && isset($value->userQty) && $value->userQty != null){
                    $totalVATAmount += $value->qtyIssued * $value->VATAmount * $value->userQty;
                }else{
                    $totalVATAmount += $value->qtyIssued * $value->VATAmount;
                }
            } else {
                $totalVATAmount += $value->qtyIssued * ((isset($value->sales_quotation_detail->VATAmount) && !is_null($value->sales_quotation_detail->VATAmount)) ? $value->sales_quotation_detail->VATAmount : 0);
            }
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->delete();
        if ($totalVATAmount > 0) {
            $res = self::savecustomerInvoiceItemTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

    public static function savecustomerInvoiceItemTaxDetails($custInvoiceDirectAutoID, $totalVATAmount): array {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceItemDetails::select(DB::raw("SUM(sellingTotal) as amount"))->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);

        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }

    public static function updateCostBySellingCost($input,$customerDirectInvoice): array {
        $output = array();
        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->localCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->localCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginLocal'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginLocal'] = $input['sellingCostAfterMargin'];
        }

        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->companyReportingCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->companyReportingCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginRpt'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginRpt'] = $input['sellingCostAfterMargin'];
        }

        return $output;
    }

    public static function updateTotalVAT($custInvoiceDirectAutoID): array {
        $invoiceDetails = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)
            ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->invoiceQty * $value->VATAmount;
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->delete();

        if ($totalVATAmount > 0) {
            $res = self::savecustomerInvoiceDirectTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

            if (!$res['status']) {
                return ['status' => false, 'message' => $res['message']];
            }
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

    public static function savecustomerInvoiceDirectTaxDetails($custInvoiceDirectAutoID, $totalVATAmount): array {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();

        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceDirectDetail::select(DB::raw("SUM(invoiceAmount) as amount"))->where('custInvoiceDirectID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);

        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }

    public static function createResponceDataArray($invoiceNumber,$masterIndex,$fieldErrors, $headerData, $detailData): array
    {
        return [
            'identifier' => [
                'unique-key' => $invoiceNumber ?? "",
                'index' => $masterIndex + 1
            ],
            'fieldErrors' => $fieldErrors,
            'headerData' => [$headerData],
            'detailData' => [$detailData]
        ];
    }

    public static function storeCustomerInvoicesFromAPI($data): array {

        $invoices = $data['invoices'] ?? null;

        $fieldErrors = $errorDocuments = $masterDatasets = $detailsDataSets = [];
        $headerData = $detailData = ['status' => false , 'errors' => []];
        $validationMessage = "Validation failed";
        $validation = false;
        $masterIndex = 0;

        // Validate Customer Invoice Data
        if(is_array($invoices)) {
            foreach ($invoices as $invoice) {
                $validation = true;

                $invoice['company_id'] = $data['company_id'];

                //Validate Master Data
                $datasetMaster = self::validateMasterData($invoice);

                //Store Master data errors if available
                if (!$datasetMaster['status']) {
                    $fieldErrors = $datasetMaster['fieldErrors'];
                    $headerData['errors'] = $datasetMaster['data'];
                    $validation = false;
                }

                $detailIndex = 0;
                $invoiceDetails = $invoice['details'] ?? [];

                foreach ($invoiceDetails as $detail) {

                    //Validate Details Data
                    $datasetDetails = self::validateDetailsData($invoice,$detail);

                    if ($datasetDetails['status']) {
                        //Store Details data if details valid
                        $detailsDataSets[$masterIndex][] = $datasetDetails['data'];
                    }
                    else {
                        //Store details data errors if available
                        $detailData['errors'][] = [
                            'index' => $detailIndex + 1,
                            'error' => $datasetDetails['data']
                        ];
                        // delete invalid document old details from details data array
                        unset($detailsDataSets[$masterIndex]);
                        $validation = false;
                    }

                    $detailIndex++;
                }

                //Store Master data if all the details valid
                if (empty($headerData['errors']) && empty($detailData['errors']) && empty($fieldErrors)) {
                    $masterDatasets[] = $datasetMaster['data'];
                }
                else {
                    // Store full document error details to return array
                    if (empty($headerData['errors'])) {
                        $headerData['status'] = true;
                    }

                    if (empty($detailData['errors'])) {
                        $detailData['status'] = true;
                    }

                    $errorDocuments[] = self::createResponceDataArray($invoice['customer_invoice_number'] ?? "", $masterIndex, $fieldErrors, $headerData, $detailData);

                    $fieldErrors = [];
                    $headerData = $detailData = ['status' => false , 'errors' => []];
                }

                $masterIndex++;
            }
        }
        else {
            $validationMessage = "Invalid Data Format";
        }

        if (!empty($errorDocuments) || !$validation) {
            return [
                'status' => false,
                'message' => $validationMessage,
                'responseData' => $errorDocuments
            ];
        }

        // Remove empty array from detailsDataSets and reindex array
        $detailsDataSets = array_values($detailsDataSets);

        // Store Master and Details
        DB::beginTransaction();

        $returnData = [
            'status' => true,
            'message' => "",
            'data' => [],
            'responseData' => []
        ];

        $headerData = $detailData = ['status' => true , 'errors' => []];
        $i = 0;

        foreach ($masterDatasets as $masterData) {
            try {
                // Create Customer Invoice Document
                $customerInvoiceStoreData = self::customerInvoiceStore($masterData);
                if($customerInvoiceStoreData['status'] && $customerInvoiceStoreData['data'] != "e") {

                    // Create Customer Invoice Details
                    foreach ($detailsDataSets[$i] as $detailsData) {
                        $detailsData['custInvoiceDirectAutoID'] = $customerInvoiceStoreData['data']['custInvoiceDirectAutoID'];

                        // Check Invoice Type to switch item details store or direct item details type
                        if($customerInvoiceStoreData['data']['isPerforma'] == 0) {
                            $customerInvoiceDetailsStoreData = self::customerInvoiceDirectDetailsStore($detailsData);
                        }
                        elseif ($customerInvoiceStoreData['data']['isPerforma'] == 2) {
                            $customerInvoiceDetailsStoreData = self::customerInvoiceItemDetailsStore($detailsData);
                        }
                        else {
                            DB::rollBack();
                            $returnData['status'] = false;
                            $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                            $returnData['message'] = "Invoice Type Error";
                            break 2;
                        }

                        if($customerInvoiceDetailsStoreData['status']){
                            $returnData['status'] = true;
                        }
                        else{
                            DB::rollBack();
                            $returnData['status'] = false;
                            $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                            $returnData['message'] = $customerInvoiceDetailsStoreData['message'];
                            break 2;
                        }
                    }

                    if($returnData['status']){
                        // Confirm Document
                        $confirmDataSet = $customerInvoiceStoreData['data'];
                        $confirmDataSet['confirmedYN'] = 1;
                        $confirmDataSet['isAutoCreateDocument'] = true;
                        $customerInvoiceUpdateData = self::customerInvoiceUpdate($confirmDataSet['custInvoiceDirectAutoID'],$confirmDataSet);
                        if($customerInvoiceUpdateData['status']){

                            // Approve Document
                            $autoApproveParams = DocumentAutoApproveService::getAutoApproveParams($confirmDataSet['documentSystemiD'],$confirmDataSet['custInvoiceDirectAutoID']);
                            $autoApproveParams['db'] = $data['db'];

                            $approveDocument = Helper::approveDocument($autoApproveParams);
                            if ($approveDocument["success"]) {
                                $returnData['status'] = true;
                                $returnData['responseData'][] = [
                                    "ref" => $confirmDataSet['bookingInvCode'],
                                    "invoiceNumber" => $masterData['customerInvoiceNo']
                                ];
                                $returnData['data'][] = $confirmDataSet['custInvoiceDirectAutoID'];
                            }
                            else {
                                DB::rollBack();
                                $returnData['status'] = false;
                                $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                                $returnData['message'] = $approveDocument['message'];
                                break;
                            }
                        }
                        else{
                            DB::rollBack();
                            $returnData['status'] = false;
                            $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                            $returnData['message'] = $customerInvoiceUpdateData['message'];
                            break;
                        }
                    }
                    else{
                        break;
                    }
                }
                else{
                    $returnData['status'] = false;
                    $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                    $returnData['message'] = $customerInvoiceStoreData['message'];
                    break;
                }
            } catch (\Exception $e){
                DB::rollBack();
                $returnData['status'] = false;
                $returnData['responseData'] = self::createResponceDataArray($masterData['customerInvoiceNo'], $i, [], $headerData, $detailData);
                $returnData['message'] = $e->getMessage();
                break;
            }

            $i++;
        }

        if($returnData['status']){
            DB::commit();
        }

        return [
            'status' => $returnData['status'],
            'data' => $returnData['data'],
            'message' => $returnData['message'],
            'responseData' => $returnData['responseData']
        ];
    }
}
