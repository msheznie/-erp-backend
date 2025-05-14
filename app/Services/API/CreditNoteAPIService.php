<?php

namespace App\Services\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\Contract;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CustomerMaster;
use App\Models\ModuleAssigned;
use App\Models\SegmentMaster;
use App\Models\Taxdetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditNoteAPIService extends AppBaseController
{


    public static function createCreditNote($input)
    {
        $company = Company::select('CompanyID')->where('companySystemID', $input['companySystemID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        /**/

        if (isset($input['debitNoteAutoID'])) {
            $alreadyUsed = CreditNote::where('debitNoteAutoID', $input['debitNoteAutoID'])
                ->first();

            if ($alreadyUsed) {
                return [
                    'status' => false,
                    'message' => "Entered debit note was already used in ($alreadyUsed->creditNoteCode). Please check again",
                    'type' => []
                ];
            }
        }

        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($input['creditNoteDate'] > $curentDate) {
            return [
                'status' => false,
                'message' => "Document date cannot be greater than current date",
                'type' => []
            ];
        }

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        $lastSerial = CreditNote::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $input['FYBiggin'] = $companyfinanceyear->bigginingDate;
            $input['FYEnd'] = $companyfinanceyear->endingDate;
        } else {
            $finYear = date("Y");
        }

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemiD'] = 19;
        $input['documentID'] = 'CN';
        $input['serialNo'] = $lastSerialNumber;
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['documentType'] = 12;

        $documentDate = $input['creditNoteDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return [
                'status' => false,
                'message' => "Document date is not within the financial period!",
                'type' => []
            ];
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['customerCurrencyID'], $input['customerCurrencyID'], 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $creditNoteCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'CN' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['creditNoteCode'] = $creditNoteCode;

        $input['customerCurrencyER'] = 1;
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        $creditNotes = CreditNote::create($input);

        return [
            'status' => true,
            'data' => $creditNotes->refresh()->toArray(),
            'message' => 'Credit Note Master saved successfully'
        ];
    }

    public static function createCreditNoteDetails($request): array {

        $companySystemID = $request['companySystemID'];
        $creditNoteAutoID = $request['creditNoteAutoID'];
        $glCode = $request['glCode'];


        /*get master*/
        $master = CreditNote::select('*')->where('creditNoteAutoID', $creditNoteAutoID)->first();
        $myCurr = $master->customerCurrencyID;               /*currencyID*/
        //$companyCurrency = \Helper::companyCurrency($myCurr);
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);
        $x = 0;


        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $glCode)->first();

        if ($master->projectID) {
            $inputData['detail_project_id'] = $master->projectID;
        }

        $inputData['VATAmount'] = $request['vatAmount'];
        $inputData['netAmount'] = $request['netAmount'];
        $inputData['creditNoteAutoID'] = $creditNoteAutoID;
        $inputData['companyID'] = $master->companyID;
        $inputData['companySystemID'] = $companySystemID;
        $inputData['customerID'] = $master->customerID;
        $inputData['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
        $inputData['glCode'] = $chartOfAccount->AccountCode;
        $inputData['glCodeDes'] = $chartOfAccount->AccountDescription;
        $inputData['comments'] = $master->comments;
        $inputData['creditAmountCurrency'] = $myCurr;
        $inputData['creditAmountCurrencyER'] = 1;
        $inputData['creditAmount'] = $request['amount'];
        $inputData['localCurrency'] = $master->localCurrencyID;
        $inputData['localCurrencyER'] = $master->localCurrencyER;
        $inputData['localAmount'] = 0;
        $inputData['comRptCurrency'] = $master->companyReportingCurrencyID;
        $inputData['comRptCurrencyER'] = $master->companyReportingER;
        if ($master->FYBiggin) {
            $finYearExp = explode('-', $master->FYBiggin);
            $inputData['budgetYear'] = $finYearExp[0];
        } else {
            $inputData['budgetYear'] = date("Y");
        }
        $inputData['comRptAmount'] = 0;

        $isVATEligible = TaxService::checkCompanyVATEligible($master->companySystemID);

        if ($isVATEligible) {
            $defaultVAT = TaxService::getDefaultVAT($master->companySystemID, $master->customerID, 0);
            $inputData['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
            $inputData['VATPercentage'] = $master->VATPercentage;
            $inputData['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
        }


        DB::beginTransaction();

        try {
            $creditNoteDetails = CreditNoteDetails::create($inputData);
            $details = CreditNoteDetails::select(DB::raw("SUM(creditAmount) as creditAmountTrans"), DB::raw("SUM(localAmount) as creditAmountLocal"), DB::raw("SUM(comRptAmount) as creditAmountRpt"))->where('creditNoteAutoID', $creditNoteAutoID)->first()->toArray();

            CreditNote::where('creditNoteAutoID', $creditNoteAutoID)->update($details);


            DB::commit();
            return [
                'status' => true,
                'data' => $creditNoteDetails->refresh(),
                'message' => "successfully created"
            ];
        } catch (\Exception $exception) {
            DB::rollback();
            return [
                'status' => false,
                'message' => "Error Occured !"
            ];
        }
    }


    public static function updateCreditNoteDetails($id, $request){

        $input = $request;
        $detail = CreditNoteDetails::where('creditNoteDetailsID', $id)->first();


        if (empty($detail)) {
            return [
                'status' => false,
                'code' => 500,
                'message' => 'Credit note details not found'
            ];
        }

        $master = CreditNote::select('*')->where('creditNoteAutoID', $detail->creditNoteAutoID)->first();

        $contract = Contract::select('ContractNumber', 'isRequiredStamp', 'paymentInDaysForJob')
            ->where('contractUID', $input['contractUID'])
            ->first();

        if ($contract) {
            $input['clientContractID'] = $contract->ContractNumber;
        }

        if ($input['serviceLineSystemID'] != $detail->serviceLineSystemID) {

            $serviceLine = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode')->where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
            $input['serviceLineSystemID'] = $serviceLine->serviceLineSystemID;
            $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        if(isset($input['detail_project_id'])){
            $input['detail_project_id'] = $input['detail_project_id'];
        } else {
            $input['detail_project_id'] = null;
        }

        if ($master->FYBiggin) {
            $finYearExp = explode('-', $master->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = date("Y");
        }

        $myCurr = $master->customerCurrencyID;
        $decimal = \Helper::getCurrencyDecimalPlace($myCurr);

        $input['creditAmountCurrency'] = $master->customerCurrencyID;
        $input['creditAmountCurrencyER'] = 1;
        $totalAmount = $input['creditAmount'];
        $input['creditAmount'] = round($input['creditAmount'], $decimal);
        /**/
        $currency = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $totalAmount);
        $input["comRptAmount"] = $currency['reportingAmount'];
        $input["localAmount"] = $currency['localAmount'];

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;


        if($policy == true){
            $input['localAmount']        = \Helper::roundValue($input['creditAmount'] / $master->localCurrencyER);
            $input['comRptAmount']        = \Helper::roundValue($input['creditAmount'] / $master->companyReportingER);
            $input['localCurrencyER' ]    = $master->localCurrencyER;
            $input['comRptCurrencyER']    = $master->companyReportingER;
        }

        // vat amount
        $vatAmount = isset($input['VATAmount'])?$input['VATAmount']:0;
        $currencyVAT = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $vatAmount);
        if($policy == true) {
            $input["VATAmountRpt"] = \Helper::roundValue($vatAmount/$master->companyReportingER);
            $input["VATAmountLocal"] = \Helper::roundValue($vatAmount/$master->localCurrencyER);
        } if($policy == false) {
            $input["VATAmountRpt"] = \Helper::roundValue($currencyVAT['reportingAmount']);
            $input["VATAmountLocal"] = \Helper::roundValue($currencyVAT['localAmount']);
        }
        $input["VATAmount"] = \Helper::roundValue($vatAmount);
        // net amount
        $netAmount = isset($input['netAmount'])?$input['netAmount']:0;
        $currencyNet = \Helper::convertAmountToLocalRpt(19, $detail->creditNoteAutoID, $netAmount);


        if($policy == true) {
            $input["netAmountRpt"] = \Helper::roundValue($netAmount/$master->companyReportingER);
            $input["netAmountLocal"] = \Helper::roundValue($netAmount/$master->localCurrencyER);
        }
        if($policy == false) {
        $input["netAmountRpt"] = $currencyNet['reportingAmount'];
        $input["netAmountLocal"] = $currencyNet['localAmount'];
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



        DB::beginTransaction();

        try {
            $updatedcreditNoteDetails = CreditNoteDetails::where('creditNoteDetailsID', $id)->update($input->toArray());

            DB::commit();
            return [
                'status' => true,
                'data' => CreditNoteDetails::find($id),
                'message' => "successfully Updated"
            ];
        } catch (\Exception $exception) {
            DB::rollback();
            return [
                'status' => false,
                'message' => "Error Occured !"
            ];
        }
    }
    
    public static function updateCreditNote($id, $request)
    {
        $input = $request;


        $creditNote = CreditNote::where('creditNoteAutoID',  $id)->first();
        if (empty($creditNote)) {
            return [
                'status' => false,
                'code' => 500,
                'message' => 'Credit note not found'
            ];
        }

        if(empty($input['projectID'])){
            $input['projectID'] = null;
        }

        if (isset($input['debitNoteAutoID'])) {
            $alreadyUsed = CreditNote::where('debitNoteAutoID', $input['debitNoteAutoID'])
                ->where('creditNoteAutoID', '<>', $id)
                ->first();

            if ($alreadyUsed) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => "Entered debit note was already used in ($alreadyUsed->creditNoteCode). Please check again"
                ];
            }
        }

        $detail = CreditNoteDetails::where('creditNoteAutoID', $id)->get();

        $input['departmentSystemID'] = 4;

        /*financial Year check*/
        $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYearCheck["success"]) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $companyFinanceYearCheck["message"]
            ];
        }
        /*financial Period check*/
        $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriodCheck["success"]) {
            return [
                'status' => false,
                'code' => 500,
                'message' => $companyFinancePeriodCheck["message"]
            ];
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;


        if(isset($input['customerCurrencyID']) && isset($input['companySystemID'])){
            $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['customerCurrencyID'], $input['customerCurrencyID'], 0);
            $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyPolicyCategoryID', 67)
                ->where('isYesNO', 1)
                ->first();
            $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

            if($policy == false) {
                if ($companyCurrencyConversion) {
                    $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
            }
        }
        if (isset($input['secondaryLogoCompanySystemID']) && $input['secondaryLogoCompanySystemID'] != $creditNote->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompanySystemID'] != '') {
                $company = Company::where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $input['secondaryLogoCompID'] = $company->CompanyID;
                $input['secondaryLogo'] = $company->logo_url;
            } else {
                $input['secondaryLogoCompID'] = NULL;
                $input['secondaryLogo'] = NULL;
            }
        }

        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        if ($customer) {
            $input['customerGLCode'] = $customer->custGLaccount;
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        }

        // updating header amounts
        $totalAmount = CreditNoteDetails::selectRaw("COALESCE(SUM(creditAmount),0) as creditAmountTrans, 
                                                    COALESCE(SUM(localAmount),0) as creditAmountLocal, 
                                                    COALESCE(SUM(comRptAmount),0) as creditAmountRpt,
                                                    COALESCE(SUM(VATAmount),0) as VATAmount,
                                                    COALESCE(SUM(VATAmountLocal),0) as VATAmountLocal, 
                                                    COALESCE(SUM(VATAmountRpt),0) as VATAmountRpt,
                                                    COALESCE(SUM(netAmount),0) as netAmount,
                                                    COALESCE(SUM(netAmountLocal),0) as netAmountLocal, 
                                                    COALESCE(SUM(netAmountRpt),0) as netAmountRpt
                                                    ")
                                            ->where('creditNoteAutoID', $id)
                                            ->first();

        $input['creditAmountTrans'] = \Helper::roundValue($totalAmount->creditAmountTrans);
        $input['creditAmountLocal'] = \Helper::roundValue($totalAmount->creditAmountLocal);
        $input['creditAmountRpt'] = \Helper::roundValue($totalAmount->creditAmountRpt);


        $input['VATAmount'] = \Helper::roundValue($totalAmount->VATAmount);
        $input['VATAmountLocal'] = \Helper::roundValue($totalAmount->VATAmountLocal);
        $input['VATAmountRpt'] = \Helper::roundValue($totalAmount->VATAmountRpt);


        $input['netAmount'] = \Helper::roundValue($totalAmount->netAmount);
        $input['netAmountLocal'] = \Helper::roundValue($totalAmount->netAmountLocal);
        $input['netAmountRpt'] = \Helper::roundValue($totalAmount->netAmountRpt);

        $input['customerCurrencyER'] = 1;

        $_post['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $curentDate = Carbon::parse(now())->format('Y-m-d') . ' 00:00:00';
        if ($_post['creditNoteDate'] > $curentDate) {
            return [
                'status' => false,
                'code' => 500,
                'message' => "Document date cannot be greater than current date"
            ];
        }

        if ($creditNote->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $messages = [
                'customerCurrencyID.required' => 'Currency is required.',
                'customerID.required' => 'Customer is required.',
                'companyFinanceYearID.required' => 'Financial Year is required.',
                'companyFinancePeriodID.required' => 'Financial Period is required.',

            ];
            $validator = \Validator::make($input, [
                'customerCurrencyID' => 'required|numeric|min:1',
                'customerID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'companyFinancePeriodID' => 'required|numeric|min:1',

            ], $messages);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'code' => 422,
                    'message' => $validator->messages()
                ];
            }

            $documentDate = $input['creditNoteDate'];
            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];
            if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
            } else {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => 'Document date is not within the selected financial period !'
                ];
            }

            if (count($detail) == 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => 'You cannot confirm. Credit note should have at least one item.'
                ];
            }

            $detailValidation = CreditNoteDetails::selectRaw("IF ( serviceLineCode IS NULL OR serviceLineCode = '', null, 1 ) AS serviceLineCode,IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( contractUID IS NULL OR contractUID = '' OR contractUID = 0, null, 1 ) AS contractUID,
                    IF ( creditAmount IS NULL OR creditAmount = '' OR creditAmount = 0, null, 1 ) AS creditAmount")->
            where('creditNoteAutoID', $id)
                ->where(function ($query) {

                    $query->whereRaw('serviceLineSystemID IS NULL OR serviceLineSystemID =""')
                        ->orwhereRaw('serviceLineCode IS NULL OR serviceLineCode =""')
                        ->orwhereRaw('contractUID IS NULL OR contractUID =""')
                        ->orwhereRaw('creditAmount IS NULL OR creditAmount =""');
                });

            $isOperationIntergrated = ModuleAssigned::where('moduleID', 3)->where('companySystemID', $creditNote->companySystemID)->exists();


            /*serviceline and contract validation*/
            $groupby = CreditNoteDetails::select('serviceLineSystemID')->where('creditNoteAutoID', $id)->groupBy('serviceLineSystemID')->get();
            $groupbycontract = CreditNoteDetails::select('contractUID')->where('creditNoteAutoID', $id)->groupBy('contractUID')->get();
            if(count($groupby) == 0) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => 'Credit note details not found.'
                ];
            }

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemiD"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && isset($input['VATAmount']) && $input['VATAmount'] > 0){

                if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => 'Cannot confirm. Output VAT GL Account not configured.'
                    ];
                }

                $outputChartOfAc = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($outputChartOfAc->outputVatGLAccountAutoID, $input["companySystemID"]);

                if (!$checkAssignedStatus) {
                    return [
                        'status' => false,
                        'code' => 500,
                        'message' => 'Cannot confirm. Output VAT GL Account not assigned to company.'
                    ];
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemiD'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $creditNote->creditNoteCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];
                $taxDetail['payeeSystemCode'] = $input['customerID'];

                if(!empty($customer)) {
                    $taxDetail['payeeCode'] = $customer->CutomerCode;
                    $taxDetail['payeeName'] = $customer->CustomerName;
                }

                $taxDetail['amount'] = $input['VATAmount'];
                $taxDetail['localCurrencyER']  = $input['localCurrencyER'];
                $taxDetail['rptCurrencyER'] = $input['companyReportingER'];
                $taxDetail['localAmount'] = $input['VATAmountLocal'];
                $taxDetail['rptAmount'] = $input['VATAmountRpt'];
                $taxDetail['currency'] =  $input['customerCurrencyID'];
                $taxDetail['currencyER'] =  1;

                $taxDetail['localCurrencyID'] =  $creditNote->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $creditNote->companyReportingCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['customerCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $input['VATAmount'];

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemiD"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['creditAmountTrans'],
                'isAutoCreateDocument' => $input['isAutoCreateDocument']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return [
                    'status' => false,
                    'code' => 500,
                    'message' => $confirm["message"]
                ];
            }

        }

        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        DB::beginTransaction();

        try {
            unset($input['isAutoCreateDocument']);
            unset($input['departmentSystemID']);
            $creditNote = CreditNote::where('creditNoteAutoID', $id)->update($input);


            DB::commit();
            return [
                'status' => true,
                'data' => CreditNote::find($id),
                'message' => "Credit Note Updated Successfully"
            ];
        } catch (\Exception $exception) {
            DB::rollback();
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }

    }

}