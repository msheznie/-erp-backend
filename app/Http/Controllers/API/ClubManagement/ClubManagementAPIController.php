<?php

namespace App\Http\Controllers\API\ClubManagement;

use App\helper\inventory;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateStageCustomerInvoiceAPIRequest;
use App\Http\Requests\CreateCustomerMasterRequest;
use App\Jobs\CreateStageCustomerInvoice;
use App\Http\Requests\CreateStageReceiptVoucherAPIRequest;
use App\Jobs\CreateStageReceiptVoucher;
use App\Models\AccountsReceivableLedger;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CountryMaster;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Models\CustomerMasterCategory;
use App\Models\CustomerReceivePayment;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\MatchDocumentMaster;
use App\Models\SegmentMaster;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Models\Tax;
use App\Repositories\CustomerMasterRepository;
use App\Models\StageCustomerReceivePayment;
use App\Models\StageCustomerReceivePaymentDetail;
use App\Models\StageDirectReceiptDetail;
use App\Services\API\CustomerMasterAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClubManagementAPIController extends AppBaseController
{
        /** @var  CustomerMasterRepository */
        private $customerMasterRepository;
        public function __construct(CustomerMasterRepository $customerMasterRepo)
        {
            $this->customerMasterRepository = $customerMasterRepo;
        }


    public function createCustomerInvoice(CreateStageCustomerInvoiceAPIRequest  $request){
        DB::beginTransaction();
        try {
            $input = $request->all();

            $custInvoiceArray = array();
            if (!empty($input[0])) {
                foreach ($input[0] as $dt) {
                    $dt['companySystemID'] = $request->company_id;
                    $financeYear = CompanyFinanceYear::where('companySystemID', $dt['companySystemID'])->where('bigginingDate', "<=", $dt['bookingDate'])->where('endingDate', ">=", $dt['bookingDate'])->first();
                    if (empty($financeYear)) {
                        return $this->sendError('Finance Year not found');
                    }


                    $financePeriod = CompanyFinancePeriod::where('companySystemID', $dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=", $dt['bookingDate'])->where('dateTo', ">=", $dt['bookingDate'])->first();
                    if (empty($financePeriod)) {
                        return $this->sendError('Finance Period not found');
                    }

                    $customerCurr = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();
                    if (empty($customerCurr)) {
                        return $this->sendError('Customer currency not found');
                    }
                    if ($customerCurr) {
                        $myCurr = $customerCurr->currencyID;
                    }

                    $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);

                    $segment = SegmentMaster::find($dt['serviceLineSystemID']);
                    if (empty($segment)) {
                        return $this->sendError('Segment not found');
                    }


                    $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);

                    $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
                    $customer = CustomerMaster::where('customerCodeSystem', $dt['customerID'])->first();
                    if (empty($customer)) {
                        return $this->sendError('Customer not found');
                    }
                    $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);

                    $company = Company::where('companySystemID', $dt['companySystemID'])->first();
                    if (empty($company)) {
                        return $this->sendError('Company not found');
                    }

                    $custInvoiceArray[] = array(
                        'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                        'referenceNumber' => $dt['referenceNumber'],
                        'companySystemID' => $dt['companySystemID'],
                        'companyID' => isset($company->CompanyID) ? $company->CompanyID : null,
                        'documentSystemiD' => 20,
                        'documentID' => "INV",
                        'isPerforma' => $dt['isPerforma'],
                        'customerID' => $dt['customerID'],
                        'customerGLCode' => isset($customer->custGLaccount) ? $customer->custGLaccount : null,
                        'customerGLSystemID' => isset($customer->custGLAccountSystemID) ? $customer->custGLAccountSystemID : null,
                        'customerInvoiceNo' => $dt['customerInvoiceNo'],
                        'custTransactionCurrencyID' => $myCurr,
                        'custTransactionCurrencyER' => 1,
                        'companyReportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                        'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                        'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                        'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                        'comments' => $dt['comments'],
                        'bookingDate' => $dt['bookingDate'],
                        'customerInvoiceDate' => $dt['bookingDate'],
                        'invoiceDueDate' => $dt['invoiceDueDate'],
                        'date_of_supply' => $dt['dateOfSupply'],
                        'bookingAmountTrans' => \Helper::roundValue($dt['bookingAmountTrans']),
                        'bookingAmountLocal' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                        'bookingAmountRpt' => \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount']),
                        'VATPercentage' => $dt['VATPercentage'],
                        'VATAmount' => $dt['VATAmount'],
                        'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                        'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                        'companyFinanceYearID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID : null,
                        'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate : null,
                        'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate : null,
                        'companyFinancePeriodID' => isset($financePeriod->companyFinancePeriodID) ? $financePeriod->companyFinancePeriodID : null,
                        'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom : null,
                        'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo : null,
                        'serviceLineSystemID' => isset($segment->serviceLineSystemID) ? $segment->serviceLineSystemID: null,
                        'serviceLineCode' => isset($segment->ServiceLineCode) ? $segment->ServiceLineCode: null,
                        'bankID' => $dt['bankID'],
                        'bankAccountID' => $dt['bankAccountID'],

                    );
                }
                StageCustomerInvoice::insert($custInvoiceArray);
            }

            $custInvoiceDetArray = array();
            $custInvoiceItemDetArray = array();
            if (!empty($input[1])) {

                foreach ($input[1] as $dt) {
                    $custInvoice = StageCustomerInvoice::where('custInvoiceDirectAutoID', $dt['custInvoiceDirectAutoID'])->first();
                    if (empty($custInvoice)) {
                        return $this->sendError('Customer Invoice not found');
                    }
                    if ($custInvoice->isPerforma == 0) {

                        $segment = SegmentMaster::find($dt['serviceLineSystemID']);
                        $glCode = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $dt['glSystemID'])->where('companySystemID', $custInvoice->companySystemID)->first();

                        $customer = CustomerCurrency::where('customerCodeSystem', $custInvoice->customerID)->first();
                        $companyCurrency = \Helper::companyCurrency($custInvoice->companySystemID);
                        if (empty($customer)) {
                            return $this->sendError('Customer not found');
                        }
                        if ($customer) {
                            $myCurr = $customer->currencyID;
                        }

                        $companyCurrencyConversion = \Helper::currencyConversion($custInvoice->companySystemID, $myCurr, $myCurr, 0);
                        $companyCurrencyConversionTrans = \Helper::currencyConversion($custInvoice->companySystemID, $myCurr, $myCurr, $dt['invoiceAmount']);
                        $companyCurrencyConversionVat = \Helper::currencyConversion($custInvoice->companySystemID, $myCurr, $myCurr, $dt['VATAmount']);
                        $company = Company::where('companySystemID', $custInvoice->companySystemID)->first();
                        if (empty($company)) {
                            return $this->sendError('Company not found');
                        }

                        $custInvoiceDetArray[] = array(
                            'custInvoiceDirectID' => $dt['custInvoiceDirectAutoID'],
                            'companyID' => isset($company->CompanyID) ? $company->CompanyID : null,
                            'companySystemID' => $custInvoice->companySystemID,
                            'serviceLineSystemID' => $dt['serviceLineSystemID'],
                            'serviceLineCode' => isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null,
                            'customerID' => $custInvoice->customerID,
                            'glSystemID' => $dt['glSystemID'],
                            'glCode' => isset($glCode->AccountCode) ? $glCode->AccountCode : null,
                            'glCodeDes' => isset($glCode->AccountDescription) ? $glCode->AccountDescription : null,
                            'accountType' => isset($glCode->catogaryBLorPL) ? $glCode->AccountDescription : null,
                            'comments' => $dt['comments'],
                            'invoiceAmountCurrency' => $myCurr,
                            'invoiceAmountCurrencyER' => 1,
                            'unitOfMeasure' => $dt['unitOfMeasure'],
                            'invoiceQty' => $dt['invoiceQty'],
                            'unitCost' => $dt['unitCost'],
                            'invoiceAmount' => $dt['invoiceAmount'],
                            'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                            'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                            'comRptCurrency' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                            'comRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                            'localCurrency' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                            'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                            'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                            'vatSubCategoryID' => $dt['vatSubCategoryID'],
                            'VATPercentage' => $dt['VATPercentage'],
                            'VATAmount' => $dt['VATAmount'],
                            'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                            'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                            'salesPrice' => $dt['salesPrice']
                        );
                    } else if ($custInvoice->isPerforma == 2) {
                        $companyCurrencyConversion = \Helper::currencyConversion($custInvoice->companySystemID, $dt['localCurrencyID'], $dt['localCurrencyID'], 0);
                        $companyCurrency = \Helper::companyCurrency($custInvoice->companySystemID);
                        $companyCurrencyConversionMargin = \Helper::currencyConversion($custInvoice->companySystemID, $dt['localCurrencyID'], $dt['localCurrencyID'], $dt['sellingCostAfterMargin']);
                        $companyCurrencyConversionVat = \Helper::currencyConversion($custInvoice->companySystemID, $dt['localCurrencyID'], $dt['localCurrencyID'], $dt['VATAmount']);
                        $item = ItemAssigned::where('itemCodeSystem', $dt['itemCodeSystem'])->first();
                        if (empty($item)) {
                            return $this->sendError('Item not found');
                        }

                        $data = array('companySystemID' => $custInvoice->companySystemID,
                            'itemCodeSystem' => $dt['itemCodeSystem'],
                            'wareHouseId' => $dt['wareHouseSystemCode']);

                        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $custInvoice->companySystemID)
                            ->where('mainItemCategoryID', $dt['itemFinanceCategoryID'])
                            ->where('itemCategorySubID', $dt['itemFinanceCategorySubID'])
                            ->first();

                        $custInvoiceItemDetArray[] = array(
                            'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                            'itemCodeSystem' => $dt['itemCodeSystem'],
                            'itemPrimaryCode' => isset($item->itemPrimaryCode) ? $item->itemPrimaryCode : null,
                            'itemDescription' => isset($item->itemDescription) ? $item->itemDescription : null,
                            'itemUnitOfMeasure' => isset($item->itemUnitOfMeasure) ? $item->itemUnitOfMeasure : null,
                            'unitOfMeasureIssued' => $dt['unitOfMeasureIssued'],
                            'convertionMeasureVal' => $dt['convertionMeasureVal'],
                            'qtyIssued' => $dt['qtyIssued'],
                            'qtyIssuedDefaultMeasure' => $dt['qtyIssuedDefaultMeasure'],
                            'currentStockQty' => $itemCurrentCostAndQty['currentStockQty'],
                            'currentWareHouseStockQty' => $itemCurrentCostAndQty['currentWareHouseStockQty'],
                            'currentStockQtyInDamageReturn' => $itemCurrentCostAndQty['currentStockQtyInDamageReturn'],
                            'comments' => $dt['comments'],
                            'itemFinanceCategoryID' => $dt['itemFinanceCategoryID'],
                            'itemFinanceCategorySubID' => $dt['itemFinanceCategorySubID'],
                            'financeGLcodebBS' => isset($financeItemCategorySubAssigned->financeGLcodebBS) ? $financeItemCategorySubAssigned->financeGLcodebBS : null,
                            'financeGLcodebBSSystemID' => isset($financeItemCategorySubAssigned->financeGLcodebBSSystemID) ? $financeItemCategorySubAssigned->financeGLcodebBSSystemID : null,
                            'financeGLcodePLSystemID' => isset($financeItemCategorySubAssigned->financeGLcodePLSystemID) ? $financeItemCategorySubAssigned->financeGLcodePLSystemID : null,
                            'financeGLcodePL' => isset($financeItemCategorySubAssigned->financeGLcodePL) ? $financeItemCategorySubAssigned->financeGLcodePL : null,
                            'financeGLcodeRevenueSystemID' => isset($financeItemCategorySubAssigned->financeGLcodeRevenueSystemID) ? $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID : null,
                            'financeGLcodeRevenue' => isset($financeItemCategorySubAssigned->financeGLcodeRevenue) ? $financeItemCategorySubAssigned->financeGLcodeRevenue : null,
                            'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                            'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                            'issueCostLocal' => $itemCurrentCostAndQty['wacValueLocal'],
                            'issueCostLocalTotal' => $itemCurrentCostAndQty['wacValueLocal'] * $dt['qtyIssuedDefaultMeasure'],
                            'reportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                            'reportingCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                            'issueCostRpt' => $itemCurrentCostAndQty['wacValueReporting'],
                            'issueCostRptTotal' => $itemCurrentCostAndQty['wacValueReporting'] * $dt['qtyIssuedDefaultMeasure'],
                            'marginPercentage' => $dt['marginPercentage'],
                            'sellingCurrencyID' => $dt['sellingCurrencyID'],
                            'sellingCurrencyER' => $dt['sellingCurrencyER'],
                            'sellingCost' => $dt['sellingCost'],
                            'sellingCostAfterMargin' => $dt['sellingCostAfterMargin'],
                            'sellingTotal' => $dt['sellingTotal'],
                            'sellingCostAfterMarginLocal' => $companyCurrencyConversionMargin['localAmount'],
                            'sellingCostAfterMarginRpt' => $companyCurrencyConversionMargin['reportingAmount'],
                            'deliveryOrderDetailID' => $dt['deliveryOrderDetailID'],
                            'deliveryOrderID' => $dt['deliveryOrderID'],
                            'quotationMasterID' => $dt['quotationMasterID'],
                            'quotationDetailsID' => $dt['quotationDetailsID'],
                            'VATPercentage' => $dt['VATPercentage'],
                            'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                            'vatSubCategoryID' => $dt['vatSubCategoryID'],
                            'VATAmount' => $dt['VATAmount'],
                            'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                            'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                            'salesPrice' => $dt['salesPrice']
                        );
                    }

                }
                StageCustomerInvoiceDirectDetail::insert($custInvoiceDetArray);
                StageCustomerInvoiceItemDetails::insert($custInvoiceItemDetArray);
            }
            DB::commit();


            $db = isset($request->db) ? $request->db : "";

            CreateStageCustomerInvoice::dispatch($db, $request->api_external_key, $request->api_external_url);

            return $this->sendResponse($custInvoiceArray, trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));
        }  catch(\Exception $e){
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error File: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
            return $this->sendError($e->getMessage(),500);
        }
    }

    public function createReceiptVoucher(CreateStageReceiptVoucherAPIRequest  $request)
    {
        DB::beginTransaction();
        try {
        $input = $request->all();

        $custReceiptVoucherArray = array();
        if(!empty($input[0])) {
            foreach ($input[0] as $dt) {
            $dt['companySystemID'] = $request->company_id;

            $financeYear = CompanyFinanceYear::where('companySystemID', $dt['companySystemID'])->where('bigginingDate', "<=", $dt['custPaymentReceiveDate'])->where('endingDate', ">=", $dt['custPaymentReceiveDate'])->first();
            $financePeriod = CompanyFinancePeriod::where('companySystemID', $dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=", $dt['custPaymentReceiveDate'])->where('dateTo', ">=", $dt['custPaymentReceiveDate'])->first();
            $customer = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();

                if(!isset($dt['customerGLCodeSystemID'])){
                    return $this->sendError('customerGLCodeSystemID is required');
                }
                
            $customerGLCode = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $dt['customerGLCodeSystemID'])->where('companySystemID', $dt['companySystemID'])->first();



            if (empty($customerGLCode)) {
                    return $this->sendError('Customer GL Code not found');
            }


            if (empty($customer)) {
                return $this->sendError('Customer not found');
            }

            if ($customer) {
                $myCurr = $customer->currencyID;
            }

            if (empty($financeYear)) {
                return $this->sendError('Company finance year not found');
            }

            if (empty($financePeriod)) {
                return $this->sendError('Company finance period not found');
            }


            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['receivedAmount']);
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);
            $companyCurrencyConversionNet = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['netAmount']);


            $company = Company::where('companySystemID', $dt['companySystemID'])->first();
            if (empty($company)) {
                return $this->sendError('Company not found');
            }

            $custReceiptVoucherArray[] = array(
                'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                'referenceNumber' => $dt['referenceNumber'],
                'companySystemID' => $dt['companySystemID'],
                'companyID' => isset($company->CompanyID) ? $company->CompanyID : null,
                'documentSystemID' => 21,
                'documentID' => 'BRV',
                'companyFinanceYearID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID : null,
                'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate : null,
                'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom : null,
                'companyFinancePeriodID' => isset($financePeriod->companyFinancePeriodID) ? $financePeriod->companyFinancePeriodID : null,
                'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate : null,
                'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo : null,
                'custPaymentReceiveDate' => $dt['custPaymentReceiveDate'],
                'narration' => $dt['narration'],
                'customerID' => $dt['customerID'],
                'customerGLCodeSystemID' => $dt['customerGLCodeSystemID'],
                'customerGLCode' => isset($customerGLCode->AccountCode) ? $customerGLCode->AccountCode: null,
                'custTransactionCurrencyID' => $myCurr,
                'custTransactionCurrencyER' => 1,
                'bankID' => $dt['bankID'],
                'bankAccount' => $dt['bankAccount'],
                'bankCurrency' => $dt['bankCurrency'],
                'bankCurrencyER' => 1,
                'custChequeDate' => $dt['custChequeDate'],
                'receivedAmount' => $dt['receivedAmount'],
                'localCurrencyID' => isset($company->localCurrencyID) ? $company->localCurrencyID : null,
                'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                'localAmount' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                'companyRptCurrencyID' => isset($company->reportingCurrency) ? $company->reportingCurrency : null,
                'companyRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                'companyRptAmount' => \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount']),
                'bankAmount' => $dt['bankAmount'],
                'documentType' => 13,
                'isVATApplicable' => $dt['isVATApplicable'],
                'VATPercentage' => $dt['VATPercentage'],
                'VATAmount' => $dt['VATAmount'],
                'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                'netAmount' => $dt['netAmount'],
                'netAmountLocal' => $companyCurrencyConversionNet['localAmount'],
                'netAmountRpt' => $companyCurrencyConversionNet['reportingAmount'],
                'RollLevForApp_curr' => 1,
            );
        }
        StageCustomerReceivePayment::insert($custReceiptVoucherArray);
    }

        $custReceiptVoucherDetArray = array();
        if(!empty($input[1])) {

            foreach ($input[1] as $dt) {
                $master = StageCustomerReceivePayment::where('custReceivePaymentAutoID', $dt['custReceivePaymentAutoID'])->first();
                if (empty($master)) {
                    return $this->sendError('Receipt voucher master not found');
                }
                $company = Company::where('companySystemID', $master->companySystemID)->first();
                if (empty($company)) {
                    return $this->sendError('Company not found');
                }
                $myCurr = $dt['custTransactionCurrencyID'];
                $companyCurrencyConversion = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, 0);
                $companyCurrency = \Helper::companyCurrency($master->companySystemID);
                $companyCurrencyConversionTrans = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $dt['bookingAmountTrans']);
                $companyCurrencyConversionReceive = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $dt['receiveAmountTrans']);
                $arAutoID = AccountsReceivableLedger::where('documentCodeSystem', $dt['bookingInvCodeSystem'])->first();
                if (empty($arAutoID)) {
                    return $this->sendError('Customer Invoice not found');
                }


                $custReceiptVoucherDetArray[] = array(
                    'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                    'companySystemID' => isset($master->companySystemID) ? $master->companySystemID : null,
                    'companyID' => isset($master->companyID) ? $master->companyID : null,
                    'addedDocumentSystemID' => 20,
                    'addedDocumentID' => "INV",
                    'bookingInvCodeSystem' => $dt['bookingInvCodeSystem'],
                    'bookingInvCode' => isset($arAutoID->documentCode) ? $arAutoID->documentCode : null,
                    'bookingDate' => isset($arAutoID->documentDate) ? $arAutoID->documentDate : null,
                    'arAutoID' => isset($arAutoID->arAutoID) ? $arAutoID->arAutoID : null,
                    'comments' => $dt['comments'],
                    'custTransactionCurrencyID' => $dt['custTransactionCurrencyID'],
                    'custTransactionCurrencyER' => 1,
                    'companyReportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                    'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                    'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                    'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                    'bookingAmountTrans' => \Helper::roundValue($dt['bookingAmountTrans']),
                    'bookingAmountLocal' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                    'bookingAmountRpt' => \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount']),
                    'custReceiveCurrencyID' => $myCurr,
                    'custReceiveCurrencyER' => 1,
                    'custbalanceAmount' => $dt['custbalanceAmount'],
                    'receiveAmountTrans' => \Helper::roundValue($dt['receiveAmountTrans']),
                    'receiveAmountLocal' => \Helper::roundValue($companyCurrencyConversionReceive['localAmount']),
                    'receiveAmountRpt' => \Helper::roundValue($companyCurrencyConversionReceive['reportingAmount'])
                );
            }
            StageCustomerReceivePaymentDetail::insert($custReceiptVoucherDetArray);
        }

        $custReceiptDetails = array();
        if(!empty($input[2])){
            foreach ($input[2] as $dt){

                $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')
                    ->where('serviceLineSystemID', $dt['serviceLineSystemID'])
                    ->first();
                if(empty($serviceLine)){
                    return $this->sendError('Segment not found');
                }

                $master = StageCustomerReceivePayment::where('custReceivePaymentAutoID', $dt['directReceiptAutoID'])->first();
                if(empty($master)){
                    return $this->sendError('Receipt voucher master not found');
                }
                $company = Company::where('companySystemID', $master->companySystemID)->first();
                if(empty($company)){
                    return $this->sendError('Company not found');
                }
                $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID', 'controlAccounts')
                    ->where('chartOfAccountSystemID', $dt['chartOfAccountSystemID'])
                    ->first();
                if(empty($chartOfAccount)){
                    return $this->sendError('Chart of account not found');
                }

                if($master){
                    $myCurr = $master->custTransactionCurrencyID;
                }

                $companyCurrencyConversionTrans = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $dt['DRAmount']);
                $companyCurrencyConversionVat = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $dt['VATAmount']);
                $companyCurrencyConversionNet = \Helper::currencyConversion($master->companySystemID, $myCurr, $myCurr, $dt['netAmount']);


                $custReceiptDetails[] = array(
                    'directReceiptAutoID' => $dt['directReceiptAutoID'],
                    'companySystemID' => isset($company->companySystemID) ? $company->companySystemID: null,
                    'companyID' => isset($company->CompanyID) ? $company->CompanyID: null,
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => isset($serviceLine->ServiceLineCode) ? $serviceLine->ServiceLineCode: null,
                    'chartOfAccountSystemID' => $dt['chartOfAccountSystemID'],
                    'glCode' => isset($chartOfAccount->AccountCode) ? $chartOfAccount->AccountCode: null,
                    'glCodeDes' => isset($chartOfAccount->AccountDescription) ? $chartOfAccount->AccountDescription: null,
                    'comments' => isset($master->narration) ? $master->narration: null,
                    'DRAmountCurrency' => isset($master->custTransactionCurrencyID) ? $master->custTransactionCurrencyID: null,
                    'DDRAmountCurrencyER' => isset($master->custTransactionCurrencyER) ? $master->custTransactionCurrencyER: null,
                    'DRAmount' => $dt['DRAmount'],
                    'localCurrency' => isset($master->localCurrencyID) ? $master->localCurrencyID: null,
                    'localCurrencyER' => isset($master->localCurrencyER) ? $master->localCurrencyER: null,
                    'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                    'comRptCurrency' => isset($master->companyRptCurrencyID) ? $master->companyRptCurrencyID: null,
                    'comRptCurrencyER' => isset($master->companyRptCurrencyER) ? $master->companyRptCurrencyER: null,
                    'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                    'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                    'netAmount' => $dt['netAmount'],
                    'netAmountLocal' => $companyCurrencyConversionNet['localAmount'],
                    'netAmountRpt' => $companyCurrencyConversionNet['reportingAmount'],
                );
            }
            StageDirectReceiptDetail::insert($custReceiptDetails);
        }
        DB::commit();

        $db = isset($request->db) ? $request->db : "";
        CreateStageReceiptVoucher::dispatch($db,$request->api_external_key,$request->api_external_url);
        return $this->sendResponse($custReceiptVoucherArray, trans('custom.save', ['attribute' => trans('custom.receipt_voucher')]));
    }
    catch(\Exception $e){
        DB::rollback();
        Log::info('Error Line No: ' . $e->getLine());
        Log::info('Error File: ' . $e->getFile());
        Log::info($e->getMessage());
        Log::info('---- GL  End with Error-----' . date('H:i:s'));
        return $this->sendError($e->getMessage(),500);
        }
    }

    public function createCustomerMaster(Request $request){
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('custGLAccountSystemID', 'custUnbilledAccountSystemID'));

        $commonValidorMessages = [
            'customerCountry.required' => 'Country field is required.',
            'customerCountry.numeric' => 'Country value should ne numeric.',
            'custUnbilledAccountSystemID.required' => 'Unbilled Receivable Account field is required.',
            'custGLAccountSystemID.required' => 'Control Account field is required.',
            'custAdvanceAccountSystemID.required' => 'Advance Account field is required.',
            'customerShortCode.required' => 'Customer Short Code field is required.',
            'CustomerName.required' => 'Customer Name field is required.',
            'customerCategoryID.required' => 'Customer Category field is required.',
            'ReportTitle.required' => 'Report Title field is required.',
            'creditLimit.required' => 'Credit limit field is required',
            'creditLimit.numeric' => 'Credit limit value should be numeric',
            'creditDays.numeric'  => 'Credit days value should be numeric',
            'creditDays.required' => 'Credit days field is required'
        ];

        $commonValidator = \Validator::make($input, [
            'customerCountry' => 'required|numeric',
            'custUnbilledAccountSystemID' => 'required',
            'custGLAccountSystemID' => 'required',
            'custAdvanceAccountSystemID' => 'required',
            'customerShortCode' => 'required',
            'CustomerName' => 'required',
            'customerCategoryID' => 'required',
            'ReportTitle' => 'required',
            'creditLimit' => 'required|numeric',
            'creditDays' => 'required|numeric'
        ], $commonValidorMessages);

        if(isset($request->company_id)) {
            $input['primaryCompanySystemID'] = $request->company_id;
        }
        else {
            return $this->sendError('Company System ID not found.');
        }

        if(key_exists('customerCountry',$input))
        {
            $countryMaster = CountryMaster::find($input['customerCountry']);

            if(empty($countryMaster))
                return $this->sendError("Customer country not found!", 422);
        }

        if(key_exists('customerCodeSystem',$input))
        {

            $customerMaster = $this->updateCustomer($input);
        }else {

            if ($commonValidator->fails()) {
                return $this->sendError($commonValidator->messages(), 422);
            }

            $duplicateCustomerShortCode = CustomerMaster::where('customerShortCode', $input['customerShortCode'])->first();

            if($duplicateCustomerShortCode){
                return $this->sendError('Secondary code already exists.' ,500);
            }

            Log::useFiles(storage_path().'/logs/laravel.log');

            $input['isAutoCreateDocument'] = true;

            $customerMaster = CustomerMasterAPIService::storeCustomerMasterFromAPI($input);
        }


        if(!$customerMaster['status']){
            return $this->sendError(
                $customerMaster['message'],
                $customerMaster['code'] ?? 404
            );
        }

        return $this->sendResponse($customerMaster['data']->toArray(), 'Customer Master created successfully');
    }

    private function updateCustomer($input)
    {

        $inputParameterArray = [
            'customerCountry',
//            'custUnbilledAccountSystemID',
//            'custGLAccountSystemID',
//            'custAdvanceAccountSystemID',
            'CustomerName',
            'creditLimit',
            'creditDays',
            'customerCountry',
            'customerAddress1',
            'customerCategoryID',
            'customerCodeSystem',
            'customerShortCode',
            'isCustomerActive'
        ];

        $data = array_only($input,$inputParameterArray);

        $customerMaster = CustomerMaster::find($input['customerCodeSystem'],$inputParameterArray);

//        $customerUpdateValidation = $this->validateCustomerUpdate($customerMaster,$data);
        if($customerMaster->customerShortCode != $input['customerShortCode'])
        {
            $duplicateCustomerShortCode = CustomerMaster::where('customerShortCode', $input['customerShortCode'])->first();

            if($duplicateCustomerShortCode)
                return ['status' => false , 'message' => 'Secondary code already exists.'];

        }

        if(!($input['isCustomerActive'] == 0 || $input['isCustomerActive'] == 1))
        {
            return ['status' => false , 'message' => 'isCustomerActive value should be 0 or 1'];
        }

        $updateData = array_diff_assoc($data, $customerMaster->toArray());
        $updateData['customerCodeSystem'] = $input['customerCodeSystem'];
        return CustomerMasterAPIService::updateCustomerMaster($updateData);
    }

    public function validateCustomerUpdate($customer,$input)
    {
        $errorMessages = array();
        $cusInvoice = CustomerInvoice::where('customerID', $customer->customerCodeSystem)->where('customerGLSystemID', $customer->custGLAccountSystemID)->first();
        $isFullyMatched = CustomerReceivePayment::where('customerID',$customer->customerCodeSystem)->where('matchInvoice','!=',2)->first();
        $matDoc = MatchDocumentMaster::where('BPVsupplierID',$customer->customerCodeSystem)->where('matchingConfirmedYN',0)->first();
        if($cusInvoice)
        {
            $errorMessages[] = "Receivable Account cannot be amended. it had used in customer Invoice";
        }


        if($isFullyMatched || $matDoc)
        {
            $errorMessages[] = "Advance Account cannot be amended. Match all pending Advance Receipt Voucher documents to update this account";
        }

        return $errorMessages;
    }

    public function createCustomerCategory(Request $request){
        DB::beginTransaction();
        try {
            $company = Company::where('companySystemID', $request->company_id)->first();
            if (empty($company)) {
                return $this->sendError('Company not found');
            }

            if (empty($request->categoryDescription)){
                return $this->sendError('Category Description cannot be empty',500);
            }

            $duplicateCategoryDescription = CustomerMasterCategory::where('categoryDescription', $request->categoryDescription)->first();

            if ($duplicateCategoryDescription) {
                return $this->sendError('Customer master category description already exists.', 500);
            }

            $customerMasterCategory = ['categoryDescription' => $request->categoryDescription, 'companySystemID' => $request->company_id, 'companyID' => $company->CompanyID];
            $customerMasterCategory = CustomerMasterCategory::create($customerMasterCategory);
            DB::commit();

            return $this->sendResponse($customerMasterCategory->toArray(), 'Customer Master Category created successfully');
        }
        catch(\Exception $e){
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error File: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
            return $this->sendError($e->getMessage(),500);
        }

    }

    public function pullTaxDetails(Request $request){
        DB::beginTransaction();
        try {
            $taxes = Tax::with(['authority', 'type', 'vat_categories', 'formula_detail'])->where('companySystemID', $request->company_id)->get();

            $taxArray = array();

            foreach ($taxes as $tax) {
                $subCategories = array();
                if ($tax->vat_categories) {
                    foreach ($tax->vat_categories as $sub) {
                        $subCategories[] = array(
                            'taxVatSubCategoriesAutoID' => $sub->taxVatSubCategoriesAutoID,
                            'mainCategory' => $sub->mainCategory,
                            'percentage' => $sub->percentage,
                            'subCategoryDescription' => $sub->subCategoryDescription
                        );
                    }
                }


                $formulaDetails = array();
                if ($tax->formula_detail) {
                    foreach ($tax->formula_detail as $formula) {
                        $formulaDetails[] = array(
                            'formulaDetailID' => $formula->formulaDetailID,
                            'taxCalculationformulaID' => $formula->taxCalculationformulaID,
                            'formula' => $formula->formula
                        );
                    }
                }


                $taxArray[] = array(
                    'taxTypeID' => isset($tax->type->taxTypeID) ? $tax->type->taxTypeID : null,
                    'taxTypeDescription' => isset($tax->type->typeDescription) ? $tax->type->typeDescription : null,
                    'taxDescription' => $tax->taxDescription,
                    'authority' => isset($tax->type->taxTypeID) ? $tax->type->taxTypeID : null,
                    'taxAuthourityMasterID' => isset($tax->authority->taxAuthourityMasterID) ? $tax->authority->taxAuthourityMasterID : null,
                    'authorityName' => isset($tax->authority->AuthorityName) ? $tax->authority->AuthorityName : null,
                    'categories' => $subCategories,
                    'formula' => $formulaDetails
                );
            }

            DB::commit();

            return $this->sendResponse($taxArray, 'Data retrieved successfully');
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function pullBankAccounts(Request $request){
        DB::beginTransaction();
        try {
            $company_id = $request->get('company_id');

            $bank_master_id = $request->get('bank_master_id');

            if($bank_master_id) {

                $banks = BankAccount::selectRaw('bankAccountAutoID,bankMasterAutoID, bankShortCode As bankCode, bankName As bankName, AccountNo as accountNo, accountCurrencyID as currency, chartOfAccountSystemID as glCode, bankBranch as bankBranch, accountSwiftCode as swiftCode, isAccountActive as isActive,isDefault')
                    ->where('companySystemID', $company_id)
                    ->where('bankMasterAutoID', $bank_master_id)
                    ->where('approvedYN', 1)
                    ->get();
            } else{

                $banks = BankAccount::selectRaw('bankAccountAutoID,bankMasterAutoID, bankShortCode As bankCode, bankName As bankName, AccountNo as accountNo, accountCurrencyID as currency, chartOfAccountSystemID as glCode, bankBranch as bankBranch, accountSwiftCode as swiftCode, isAccountActive as isActive,isDefault')
                    ->where('companySystemID', $company_id)
                    ->where('approvedYN', 1)
                    ->get();
            }






            DB::commit();
            return $this->sendResponse($banks, 'Data Retrieved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}
