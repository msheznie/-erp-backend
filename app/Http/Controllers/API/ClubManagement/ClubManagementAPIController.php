<?php

namespace App\Http\Controllers\API\ClubManagement;

use App\helper\inventory;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateStageCustomerInvoiceAPIRequest;
use App\Http\Requests\CreateStageReceiptVoucherAPIRequest;
use App\Jobs\CreateStageCustomerInvoice;
use App\Jobs\CreateStageReceiptVoucher;
use App\Models\AccountsReceivableLedger;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\CustomerCurrency;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectReceiptDetail;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GeneralLedger;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Models\StageCustomerReceivePayment;
use App\Models\StageCustomerReceivePaymentDetail;
use App\Models\StageDirectReceiptDetail;
use Illuminate\Support\Facades\DB;

class ClubManagementAPIController extends AppBaseController
{

    public function createCustomerInvoice(CreateStageCustomerInvoiceAPIRequest  $request){
        $input = $request->all();

        $custInvoiceArray = array();

        foreach ($input[0] as $dt){

            $financeYear = CompanyFinanceYear::where('companySystemID',$dt['companySystemID'])->where('bigginingDate', "<=",  $dt['bookingDate'])->where('endingDate', ">=", $dt['bookingDate'])->first();

            $financePeriod = CompanyFinancePeriod::where('companySystemID',$dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=",  $dt['bookingDate'])->where('dateTo', ">=", $dt['bookingDate'])->first();

            $customerCurr = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();


            $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);
            $myCurr = 1;
            if($customerCurr){
                $myCurr = $customerCurr->currencyID;
            }

            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);

            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
            $customer = CustomerMaster::where('customerCodeSystem', $dt['customerID'])->first();
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);

            $company = Company::where('companySystemID', $dt['companySystemID'])->first();


            $custInvoiceArray[] = array(
                'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                'companySystemID' => $dt['companySystemID'],
                'companyID' => isset($company->CompanyID) ? $company->CompanyID: null,
                'documentSystemiD' => 20,
                'documentID' => "INV",
                'isPerforma' => $dt['isPerforma'],
                'customerID' => $dt['customerID'],
                'customerGLCode' => isset($customer->custGLaccount) ? $customer->custGLaccount: null,
                'customerGLSystemID' => isset($customer->custGLAccountSystemID) ? $customer->custGLAccountSystemID: null,
                'customerInvoiceNo' => $dt['customerInvoiceNo'],
                'custTransactionCurrencyID' => $myCurr,
                'custTransactionCurrencyER' => 1,
                'companyReportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID: null,
                'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID: null,
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
                'companyFinanceYearID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID: null,
                'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate: null,
                'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate: null,
                'companyFinancePeriodID' => isset($financePeriod->companyFinancePeriodID) ? $financePeriod->companyFinancePeriodID: null,
                'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom: null,
                'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo: null,
                'bankID' => $dt['bankID'],
                'bankAccountID' => $dt['bankAccountID'],

            );
        }
        StageCustomerInvoice::insert($custInvoiceArray);

        $custInvoiceDetArray = array();
        $custInvoiceItemDetArray = array();

        foreach ($input[1] as $dt) {
            if ($dt['isPerforma'] == 0) {
            $segment = SegmentMaster::find($dt['serviceLineSystemID']);
            $glCode = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $dt['glSystemID'])->where('companySystemID', $dt['companySystemID'])->first();
            $customer = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();
            $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);
                $myCurr = 1;
            if($customer){
                $myCurr = $customer->currencyID;
            }

            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['invoiceAmount']);
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);
                $company = Company::where('companySystemID', $dt['companySystemID'])->first();

                $custInvoiceDetArray[] = array(
                    'custInvoiceDirectID' => $dt['custInvoiceDirectAutoID'],
                    'companyID' => isset($company->CompanyID) ? $company->CompanyID: null,
                    'companySystemID' => $dt['companySystemID'],
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => isset($segment->ServiceLineCode) ? $segment->ServiceLineCode: null,
                    'customerID' => $dt['customerID'],
                    'glSystemID' => $dt['glSystemID'],
                    'glCode' => isset($glCode->AccountCode) ? $glCode->AccountCode: null,
                    'glCodeDes' => isset($glCode->AccountDescription) ? $glCode->AccountDescription: null,
                    'accountType' => isset($glCode->catogaryBLorPL) ? $glCode->AccountDescription: null,
                    'comments' => $dt['comments'],
                    'invoiceAmountCurrency' => $myCurr,
                    'invoiceAmountCurrencyER' => 1,
                    'unitOfMeasure' => $dt['unitOfMeasure'],
                    'invoiceQty' => $dt['invoiceQty'],
                    'unitCost' => $dt['unitCost'],
                    'invoiceAmount' => $dt['invoiceAmount'],
                    'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                    'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                    'comRptCurrency' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID: null,
                    'comRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                    'localCurrency' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID: null,
                    'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                    'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                    'vatSubCategoryID' => $dt['vatSubCategoryID'],
                    'VATPercentage' => $dt['VATPercentage'],
                    'VATAmount' => $dt['VATAmount'],
                    'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                    'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                    'salesPrice' => $dt['salesPrice']
                );
            } else if($dt['isPerforma'] == 0) {
                $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $dt['localCurrencyID'], $dt['localCurrencyID'], 0);
                $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);
                $companyCurrencyConversionMargin = \Helper::currencyConversion($dt['companySystemID'], $dt['localCurrencyID'], $dt['localCurrencyID'], $dt['sellingCostAfterMargin']);
                $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $dt['localCurrencyID'], $dt['localCurrencyID'], $dt['VATAmount']);
                $item = ItemAssigned::where('itemCodeSystem',$dt['itemCodeSystem'])->first();


                $data = array('companySystemID' => $dt['companySystemID'],
                    'itemCodeSystem' => $dt['itemCodeSystem'],
                    'wareHouseId' => $dt['wareHouseSystemCode']);

                $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $dt['companySystemID'])
                    ->where('mainItemCategoryID', $dt['itemFinanceCategoryID'])
                    ->where('itemCategorySubID', $dt['itemFinanceCategorySubID'])
                    ->first();

                $custInvoiceItemDetArray[] = array(
                    'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                    'itemCodeSystem' => $dt['itemCodeSystem'],
                    'itemPrimaryCode' => isset($item->itemPrimaryCode) ? $item->itemPrimaryCode: null,
                    'itemDescription' => isset($item->itemDescription) ? $item->itemDescription: null,
                    'itemUnitOfMeasure' => isset($item->itemUnitOfMeasure) ? $item->itemUnitOfMeasure: null,
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
                   'financeGLcodebBSSystemID' => isset($financeItemCategorySubAssigned->financeGLcodebBSSystemID) ? $financeItemCategorySubAssigned->financeGLcodebBSSystemID: null,
                   'financeGLcodePLSystemID' => isset($financeItemCategorySubAssigned->financeGLcodePLSystemID) ? $financeItemCategorySubAssigned->financeGLcodePLSystemID: null,
                   'financeGLcodePL' => isset($financeItemCategorySubAssigned->financeGLcodePL) ? $financeItemCategorySubAssigned->financeGLcodePL: null,
                  'financeGLcodeRevenueSystemID' => isset($financeItemCategorySubAssigned->financeGLcodeRevenueSystemID) ? $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID: null,
                   'financeGLcodeRevenue' => isset($financeItemCategorySubAssigned->financeGLcodeRevenue) ? $financeItemCategorySubAssigned->financeGLcodeRevenue: null,
                    'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID: null,
                    'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                    'issueCostLocal' => $itemCurrentCostAndQty['wacValueLocal'],
                    'issueCostLocalTotal' => $itemCurrentCostAndQty['wacValueLocal'] * $dt['qtyIssuedDefaultMeasure'],
                    'reportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID: null,
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

            StageCustomerInvoiceDirectDetail::insert($custInvoiceDetArray);
            StageCustomerInvoiceItemDetails::insert($custInvoiceItemDetArray);


        }


        CreateStageCustomerInvoice::dispatch();

        return $this->sendResponse($custInvoiceArray, trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));
    }

    public function createReceiptVoucher(CreateStageReceiptVoucherAPIRequest  $request){

        $input = $request->all();

        $custReceiptVoucherArray = array();
        foreach ($input[0] as $dt){
            $financeYear = CompanyFinanceYear::where('companySystemID',$dt['companySystemID'])->where('bigginingDate', "<=",  $dt['custPaymentReceiveDate'])->where('endingDate', ">=", $dt['custPaymentReceiveDate'])->first();
            $financePeriod = CompanyFinancePeriod::where('companySystemID',$dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=",  $dt['custPaymentReceiveDate'])->where('dateTo', ">=", $dt['custPaymentReceiveDate'])->first();
            $customer = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();

            $myCurr = 1;
            if($customer){
                $myCurr = $customer->currencyID;
            }

            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['receivedAmount']);
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);
            $companyCurrencyConversionNet = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['netAmount']);


            $company = Company::where('companySystemID', $dt['companySystemID'])->first();

            $custReceiptVoucherArray[] = array(
                'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                'companySystemID' => $dt['companySystemID'],
                'companyID' => isset($company->CompanyID) ? $company->CompanyID: null,
                'documentSystemID' => 21,
                'documentID' => 'BRV',
                'companyFinanceYearID' =>  isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID: null,
                'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate: null,
                'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom: null,
                'companyFinancePeriodID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID: null,
                'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate: null,
                'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo: null,
                'custPaymentReceiveDate' => $dt['custPaymentReceiveDate'],
                'narration' => $dt['narration'],
                'customerID' => $dt['customerID'],
                'customerGLCodeSystemID' => isset($customer->custGLAccountSystemID) ? $customer->custGLAccountSystemID: null,
                'customerGLCode' => isset($customer->custGLaccount) ? $customer->custGLaccount: null,
                'custTransactionCurrencyID' => $myCurr,
                'custTransactionCurrencyER' => 1,
                'bankID' => $dt['bankID'],
                'bankAccount' => $dt['bankAccount'],
                'bankCurrency' => $dt['bankCurrency'],
                'bankCurrencyER' => 1,
                'custChequeDate' => $dt['custChequeDate'],
                'receivedAmount' => $dt['receivedAmount'],
                'localCurrencyID' => isset($company->localCurrencyID) ? $company->localCurrencyID: null,
                'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                'localAmount' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                'companyRptCurrencyID' => isset($company->reportingCurrency) ? $company->reportingCurrency: null,
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

        $custReceiptVoucherDetArray = array();
        foreach ($input[1] as $dt){
            $company = Company::where('companySystemID', $dt['companySystemID'])->first();
            $myCurr = $dt['custTransactionCurrencyID'];
            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
            $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);
            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
            $companyCurrencyConversionReceive = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['receiveAmountTrans']);
            $arAutoID = AccountsReceivableLedger::where('documentCodeSystem', $dt['bookingInvCodeSystem'])->first();

            $custReceiptVoucherDetArray[] = array(
                'custReceivePaymentAutoID' => $dt['custReceivePaymentAutoID'],
                'companySystemID' => $dt['companySystemID'],
                'companyID' => isset($company->CompanyID) ? $company->CompanyID: null,
                'addedDocumentSystemID' => 20,
                'addedDocumentID' => "INV",
                'bookingInvCodeSystem' => $dt['bookingInvCodeSystem'],
                'bookingInvCode' => isset($arAutoID->documentCode) ? $arAutoID->documentCode: null,
                'bookingDate' => isset($arAutoID->documentDate) ? $arAutoID->documentDate: null,
                'arAutoID' => isset($arAutoID->arAutoID) ? $arAutoID->arAutoID: null,
                'comments' => $dt['comments'],
                'custTransactionCurrencyID' => $dt['custTransactionCurrencyID'],
                'custTransactionCurrencyER' => 1,
                'companyReportingCurrencyID' =>  isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID: null,
                'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID: null,
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

        $custReceiptDetails = array();
        foreach ($input[2] as $dt){
            $company = Company::where('companySystemID', $dt['companySystemID'])->first();
            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')
                ->where('serviceLineSystemID', $dt['serviceLineSystemID'])
                ->first();


            $master = StageCustomerReceivePayment::where('custReceivePaymentAutoID', $dt['directReceiptAutoID'])->first();
            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID', 'controlAccounts')
                ->where('chartOfAccountSystemID', $dt['chartOfAccountSystemID'])
                ->first();
            $myCurr = $master->custTransactionCurrencyID;

            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['DRAmount']);
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);
            $companyCurrencyConversionNet = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['netAmount']);


            $custReceiptDetails[] = array(
            'directReceiptAutoID' => $dt['directReceiptAutoID'],
            'companySystemID' => $dt['companySystemID'],
            'companyID' => isset($company->CompanyID) ? $company->CompanyID: 1,
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

        CreateStageReceiptVoucher::dispatch();

        return $this->sendResponse(1, trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));

    }



}
