<?php

namespace App\Http\Controllers\API\ClubManagement;

use App\helper\inventory;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateStageCustomerInvoiceAPIRequest;
use App\Http\Requests\CreateCustomerMasterRequest;
use App\Jobs\CreateStageCustomerInvoice;
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
use App\Models\DocumentMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GeneralLedger;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\StageCustomerInvoice;
use App\Models\StageCustomerInvoiceDirectDetail;
use App\Models\StageCustomerInvoiceItemDetails;
use App\Repositories\CustomerMasterRepository;
use Illuminate\Support\Facades\DB;

class ClubManagementAPIController extends AppBaseController
{
        /** @var  CustomerMasterRepository */
        private $customerMasterRepository;
        public function __construct(CustomerMasterRepository $customerMasterRepo)
        {
            $this->customerMasterRepository = $customerMasterRepo;
        }


    public function createCustomerInvoice(CreateStageCustomerInvoiceAPIRequest  $request){
        $input = $request->all();

        $custInvoiceArray = array();

        foreach ($input[0] as $dt){

            $financeYear = CompanyFinanceYear::where('companySystemID',$dt['companySystemID'])->where('bigginingDate', "<=",  $dt['bookingDate'])->where('endingDate', ">=", $dt['bookingDate'])->first();

            $financePeriod = CompanyFinancePeriod::where('companySystemID',$dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=",  $dt['bookingDate'])->where('dateTo', ">=", $dt['bookingDate'])->first();

            $customer = CustomerCurrency::where('customerCodeSystem', $dt['customerID'])->first();
            $currency = CurrencyMaster::where('currencyID', $customer->currencyID)->first();


            $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);

            $myCurr = $customer->currencyID;

            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);

            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
            $customer = CustomerMaster::where('customerCodeSystem', $dt['customerID'])->first();
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);

            $company = Company::where('companySystemID', $dt['companySystemID'])->first();


            $custInvoiceArray[] = array(
                'custInvoiceDirectAutoID' => $dt['custInvoiceDirectAutoID'],
                'companySystemID' => $dt['companySystemID'],
                'companyID' => $company->CompanyID,
                'documentSystemiD' => 20,
                'documentID' => "INV",
                'isPerforma' => $dt['isPerforma'],
                'customerID' => $dt['customerID'],
                'customerGLCode' => $customer->custGLaccount,
                'customerGLSystemID' => $customer->custGLAccountSystemID,
                'customerInvoiceNo' => $dt['customerInvoiceNo'],
                'custTransactionCurrencyID' => $myCurr,
                'custTransactionCurrencyER' => 1,
                'companyReportingCurrencyID' => $companyCurrency->reportingcurrency->currencyID,
                'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                'localCurrencyID' => $companyCurrency->localcurrency->currencyID,
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
                'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                'FYBiggin' => $financeYear->bigginingDate,
                'FYEnd' => $financeYear->endingDate,
                'companyFinancePeriodID' => $financePeriod->companyFinancePeriodID,
                'FYPeriodDateFrom' => $financePeriod->dateFrom,
                'FYPeriodDateTo' => $financePeriod->dateTo,
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

                $myCurr = $customer->currencyID;

            $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
            $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['invoiceAmount']);
            $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['VATAmount']);
                $company = Company::where('companySystemID', $dt['companySystemID'])->first();

                $custInvoiceDetArray[] = array(
                    'custInvoiceDirectID' => $dt['custInvoiceDirectAutoID'],
                    'companyID' => $company->CompanyID,
                    'companySystemID' => $dt['companySystemID'],
                    'serviceLineSystemID' => $dt['serviceLineSystemID'],
                    'serviceLineCode' => $segment->ServiceLineCode,
                    'customerID' => $dt['customerID'],
                    'glSystemID' => $dt['glSystemID'],
                    'glCode' => $glCode->AccountCode,
                    'glCodeDes' => $glCode->AccountDescription,
                    'accountType' => $glCode->catogaryBLorPL,
                    'comments' => $dt['comments'],
                    'invoiceAmountCurrency' => $myCurr,
                    'invoiceAmountCurrencyER' => 1,
                    'unitOfMeasure' => $dt['unitOfMeasure'],
                    'invoiceQty' => $dt['invoiceQty'],
                    'unitCost' => $dt['unitCost'],
                    'invoiceAmount' => $dt['invoiceAmount'],
                    'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                    'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                    'comRptCurrency' => $companyCurrency->reportingcurrency->currencyID,
                    'comRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                    'localCurrency' => $companyCurrency->localcurrency->currencyID,
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
                    'itemPrimaryCode' => $item->itemPrimaryCode,
                    'itemDescription' => $item->itemDescription,
                    'itemUnitOfMeasure' => $item->itemUnitOfMeasure,
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
                    'localCurrencyID' => $companyCurrency->localcurrency->currencyID,
                    'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                    'issueCostLocal' => $itemCurrentCostAndQty['wacValueLocal'],
                    'issueCostLocalTotal' => $itemCurrentCostAndQty['wacValueLocal'] * $dt['qtyIssuedDefaultMeasure'],
                    'reportingCurrencyID' => $companyCurrency->reportingcurrency->currencyID,
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
        $stagCustomerUpdateInvoices = StageCustomerInvoice::all();
        $i = 1;

        foreach ($stagCustomerUpdateInvoices as $dt){
            $lastSerial = CustomerInvoiceDirect::where('companySystemID', $dt['companySystemID'])
                ->where('companyFinanceYearID', $dt['companyFinanceYearID'])
                ->orderBy('serialNo', 'desc')
                ->first();

            $lastAutoID = CustomerInvoiceDirect::orderBy('custInvoiceDirectAutoID', 'desc')
                ->first();


                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + $i;
                }

                    $custInvoiceDirectAutoID = 1;
                  if ($lastAutoID) {
                      $custInvoiceDirectAutoID = intval($lastAutoID->custInvoiceDirectAutoID) +$i;
                  }




                $y = date('Y', strtotime($dt->FYBiggin));
                $bookingInvCode = ($dt->companyID . '\\' . $y . '\\INV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                StageCustomerInvoice::where('custInvoiceDirectAutoID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectAutoID' => $custInvoiceDirectAutoID,'serialNo' => $lastSerialNumber, 'bookingInvCode' => $bookingInvCode]);
            StageCustomerInvoiceDirectDetail::where('custInvoiceDirectID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectID' => $custInvoiceDirectAutoID]);
            StageCustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $dt->custInvoiceDirectAutoID)->update(['custInvoiceDirectAutoID' => $custInvoiceDirectAutoID]);
            $i++;
        }


        CreateStageCustomerInvoice::dispatch();

        return $this->sendResponse($lastSerialNumber, trans('custom.save', ['attribute' => trans('custom.customer_invoice')]));
    }

    public function createCustomerMaster(CreateCustomerMasterRequest  $request){
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('custGLAccountSystemID', 'custUnbilledAccountSystemID'));

        if($input['custGLAccountSystemID'] == $input['custUnbilledAccountSystemID'] ){
           return $this->sendError('Receivable account and unbilled account cannot be same. Please select different chart of accounts.');
        }

        if($input['custUnbilledAccountSystemID'] == 0){
            return $this->sendError('Unbilled Receivable Account field is required.');
        }

        $validatorResult = \Helper::checkCompanyForMasters($input['primaryCompanySystemID']);
        if (!$validatorResult['success']) {
            return $this->sendError($validatorResult['message']);
        }

        $company = Company::where('companySystemID', $input['primaryCompanySystemID'])->first();

        if ($company) {
            $input['primaryCompanyID'] = $company->CompanyID;
        }


        if (array_key_exists('custGLAccountSystemID', $input)) {
            $financePL = ChartOfAccount::where('chartOfAccountSystemID', $input['custGLAccountSystemID'])->first();
            if ($financePL) {
                $input['custGLaccount'] = $financePL->AccountCode;
            }
        }

        if (array_key_exists('custUnbilledAccountSystemID', $input)) {
            $unbilled = ChartOfAccount::where('chartOfAccountSystemID', $input['custUnbilledAccountSystemID'])->first();
            if ($unbilled) {
                $input['custUnbilledAccount'] = $unbilled->AccountCode;
            }
        }

        $commonValidorMessages = [
            'customerCountry.required' => 'Country field is required.',
            'custUnbilledAccountSystemID.required' => 'Unbilled Receivable Account field is required.'
        ];

        $commonValidator = \Validator::make($input, [
            'customerCountry' => 'required',
            'custUnbilledAccountSystemID' => 'required'

        ], $commonValidorMessages);

        if ($commonValidator->fails()) {
            return $this->sendError($commonValidator->messages(), 422);
        }

        if($input['customerCountry']==0 || $input['customerCountry']==''){
            return $this->sendError('Country field is required',500);
        }

        $document = DocumentMaster::where('documentID', 'CUSTM')->first();
        $input['documentSystemID'] = $document->documentSystemID;
        $input['documentID'] = $document->documentID;

        $lastCustomer = CustomerMaster::orderBy('customerCodeSystem', 'DESC')->first();
        $lastSerialOrder = 1;
        if(!empty($lastCustomer)){
            $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
        }

        $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);

        $input['lastSerialOrder'] = $lastSerialOrder;
        $input['CutomerCode'] = $customerCode;
        $input['isCustomerActive'] = 1;
   
        $customerMasters = $this->customerMasterRepository->create($input);

        return $this->sendResponse($customerMasters->toArray(), 'Customer Master created successfully');


    }

}
