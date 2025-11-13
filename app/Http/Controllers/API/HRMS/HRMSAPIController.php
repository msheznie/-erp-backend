<?php

namespace App\Http\Controllers\API\HRMS;

use App\helper\SupplierInvoice;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateBookInvSuppMasterAPIRequest;
use App\Jobs\CreateHrmsSupplierInvoice;
use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\UserToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HRMSAPIController extends AppBaseController
{
    public function createSupplierInvoice(CreateBookInvSuppMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $suppInvoiceArray = array();

            if (!empty($input[0])) {
                foreach ($input[0] as $dt) {
                    $dt['companySystemID'] = $request->company_id;
                    $dt['employee_id'] = $request->employee_id;
                    $status = $dt['status'];
                    $employee = Employee::where('employeeSystemID', $dt['employee_id'])->first();
                    if (empty($employee)) {
                        return $this->sendError(trans('custom.employee_not_found'));
                    }
                    UserToken::where('token', $request->user_token)->delete();

                    $company = Company::where('companySystemID', $dt['companySystemID'])->first();
                    if (empty($company)) {
                        return $this->sendError(trans('custom.company_not_found'));
                    }

                    $companyCurrency = \Helper::companyCurrency($dt['companySystemID']);

                    if (empty($dt['comments'])) {
                        return $this->sendError(trans('custom.narration_field_is_required'));
                    }

                    if (empty($dt['supplierInvoiceNo'])) {
                        return $this->sendError(trans('custom.supplier_invoice_no_field_is_required'));
                    }

                    if (empty($dt['supplierInvoiceDate'])) {
                        return $this->sendError(trans('custom.supplier_invoice_date_field_is_required'));
                    }

                    if (empty($dt['documentType'])) {
                        return $this->sendError(trans('custom.document_type_field_is_required'));
                    }

                    if (empty($dt['bookingDate'])) {
                        return $this->sendError(trans('custom.booking_date_field_is_required'));
                    }

                    $financeYear = CompanyFinanceYear::where('companySystemID', $dt['companySystemID'])->where('isActive', -1)->where('bigginingDate', "<=", $dt['bookingDate'])->where('endingDate', ">=", $dt['bookingDate'])->first();
                    if (empty($financeYear)) {
                        return $this->sendError(trans('custom.finance_year_not_found_1'));
                    }

                    $lastSerial = BookInvSuppMaster::where('companySystemID', $dt['companySystemID'])
                        ->where('companyFinanceYearID', $financeYear->companyFinanceYearID)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }


                    $financePeriod = CompanyFinancePeriod::where('companySystemID', $dt['companySystemID'])->where('departmentSystemID', 1)->where('dateFrom', "<=", $dt['bookingDate'])->where('dateTo', ">=", $dt['bookingDate'])->where('isActive', -1)->first();
                    if (empty($financePeriod)) {
                        return $this->sendError(trans('custom.finance_period_not_found'));
                    }

                    $startYear = $financeYear->bigginingDate;
                    $finYearExp = explode('-', $startYear);
                    $finYear = $finYearExp[0];
                    $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

                    if($dt['documentType'] == 1) {
                        if (empty($dt['supplierID'])) {
                            return $this->sendError(trans('custom.supplier_id_field_is_required'));
                        }
                    $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                        'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'VATPercentage')
                        ->where('supplierCodeSytem', $dt['supplierID'])
                        ->where('companySystemID', $dt['companySystemID'])
                        ->first();
                    if (empty($supplierAssignedDetail)) {
                        return $this->sendError(trans('custom.supplier_not_found'));
                    }

                    $supplierCurr = SupplierCurrency::where('supplierCodeSystem', $dt['supplierID'])->first();
                    if (empty($supplierCurr)) {
                        return $this->sendError(trans('custom.supplier_currency_not_found_1'));
                    }
                    if ($supplierCurr) {
                        $myCurr = $supplierCurr->currencyID;
                    }

                    $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
                    $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
                    $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['vatAmount']);



                    $suppInvoiceArray = array(
                        'companySystemID' => $dt['companySystemID'],
                        'companyID' => isset($company->CompanyID) ? $company->CompanyID : null,
                        'documentSystemID' => 11,
                        'documentID' => "SI",
                        'serialNo' => $lastSerialNumber,
                        'companyFinanceYearID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID : null,
                        'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate : null,
                        'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate : null,
                        'companyFinancePeriodID' => isset($financePeriod->companyFinancePeriodID) ? $financePeriod->companyFinancePeriodID : null,
                        'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom : null,
                        'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo : null,
                        'bookingInvCode' => $bookingInvCode,
                        'bookingDate' => $dt['bookingDate'],
                        'comments' => $dt['comments'],
                        'secondaryRefNo' => $dt['secondaryRefNo'],
                        'supplierID' => $dt['supplierID'],
                        'supplierVATEligible' => $supplierAssignedDetail->vatEligible,
                        'supplierGLCodeSystemID' => $supplierAssignedDetail->liabilityAccountSysemID,
                        'supplierGLCode' => $supplierAssignedDetail->liabilityAccount,
                        'UnbilledGRVAccountSystemID' => $supplierAssignedDetail->UnbilledGRVAccountSystemID,
                        'UnbilledGRVAccount' => $supplierAssignedDetail->UnbilledGRVAccount,
                        'VATPercentage' => $dt['vatPercentage'],
                        'VATAmount' => $dt['vatAmount'],
                        'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                        'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                        'supplierInvoiceNo' => $dt['supplierInvoiceNo'],
                        'supplierInvoiceDate' => $dt['supplierInvoiceDate'],
                        'supplierTransactionCurrencyID' => $myCurr,
                        'supplierTransactionCurrencyER' => 1,
                        'companyReportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                        'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                        'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                        'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                        'bookingAmountTrans' => \Helper::roundValue($dt['bookingAmountTrans']),
                        'bookingAmountLocal' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                        'bookingAmountRpt' => \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount']),
                        'documentType' => 1,
                        'createdPcID' => gethostname(),
                        'createdUserID' => $employee->empID,
                        'createdUserSystemID' =>  $dt['employee_id']
                    );
                } else if ($dt['documentType'] == 4){
                        if (empty($dt['currency'])) {
                            return $this->sendError(trans('custom.currency_field_is_required'));
                        }
                    $myCurr = $dt['currency'];

                    $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                        ->where('companySystemID', $dt['companySystemID'])
                        ->first();

                    if($employeeInvoice->isYesNO != 1){
                        return $this->sendError('Unable to create Employee Direct Invoice as policy is disabled');
                    }

                    if (empty($dt['employeeID'])) {
                            return $this->sendError(trans('custom.employee_field_is_required'));
                    }

                    $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
                    $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);

                    $companyCurrencyConversionVat = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['vatAmount']);

                    $employee = Employee::where('employeeSystemID', $dt['employeeID'])->first();
                    if (empty($employee)) {
                        return $this->sendError(trans('custom.employee_not_found'), 500);
                    }

                    $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($dt['companySystemID'], 11, "employee-control-account");

                    if (is_null($checkEmployeeControlAccount)) {
                        return $this->sendError('Please configure Employee control account for this company', 500);
                    }

                    $suppInvoiceArray = array(
                        'companySystemID' => $dt['companySystemID'],
                        'companyID' => isset($company->CompanyID) ? $company->CompanyID : null,
                        'documentSystemID' => 11,
                        'documentID' => "SI",
                        'serialNo' => $lastSerialNumber,
                        'companyFinanceYearID' => isset($financeYear->companyFinanceYearID) ? $financeYear->companyFinanceYearID : null,
                        'FYBiggin' => isset($financeYear->bigginingDate) ? $financeYear->bigginingDate : null,
                        'FYEnd' => isset($financeYear->endingDate) ? $financeYear->endingDate : null,
                        'companyFinancePeriodID' => isset($financePeriod->companyFinancePeriodID) ? $financePeriod->companyFinancePeriodID : null,
                        'FYPeriodDateFrom' => isset($financePeriod->dateFrom) ? $financePeriod->dateFrom : null,
                        'FYPeriodDateTo' => isset($financePeriod->dateTo) ? $financePeriod->dateTo : null,
                        'bookingInvCode' => $bookingInvCode,
                        'bookingDate' => $dt['bookingDate'],
                        'comments' => $dt['comments'],
                        'secondaryRefNo' => $dt['secondaryRefNo'],
                        'VATPercentage' => $dt['vatPercentage'],
                        'VATAmount' => $dt['vatAmount'],
                        'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                        'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                        'supplierInvoiceNo' => $dt['supplierInvoiceNo'],
                        'supplierInvoiceDate' => $dt['supplierInvoiceDate'],
                        'supplierTransactionCurrencyID' => $myCurr,
                        'supplierTransactionCurrencyER' => 1,
                        'companyReportingCurrencyID' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                        'companyReportingER' => $companyCurrencyConversion['trasToRptER'],
                        'localCurrencyID' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                        'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                        'bookingAmountTrans' => \Helper::roundValue($dt['bookingAmountTrans']),
                        'bookingAmountLocal' => \Helper::roundValue($companyCurrencyConversionTrans['localAmount']),
                        'bookingAmountRpt' => \Helper::roundValue($companyCurrencyConversionTrans['reportingAmount']),
                        'documentType' => 4,
                        'employeeID' => $dt['employeeID'],
                        'employeeControlAcID' => $checkEmployeeControlAccount,
                        'createdPcID' => gethostname(),
                        'createdUserID' => $employee->empID,
                        'createdUserSystemID' =>  $dt['employee_id']
                  );
                 }
                }
                 $bookInvSupp = BookInvSuppMaster::create($suppInvoiceArray);
            }

            if (!empty($input[1])) {
                foreach ($input[1] as $dt) {
                    $segment = SegmentMaster::find($dt['serviceLineSystemID']);
                    $glCode = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $dt['glSystemID'])->where('companySystemID', $bookInvSupp->companySystemID)->first();
                    if ($bookInvSupp->documentType == 1) {
                        $supplierCurr = SupplierCurrency::where('supplierCodeSystem', $dt['supplierID'])->first();
                        if (empty($supplierCurr)) {
                            return $this->sendError(trans('custom.customer_currency_not_found_1'));
                        }
                        if ($supplierCurr) {
                            $myCurr = $supplierCurr->currencyID;
                        }
                        $companyCurrencyConversion = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, 0);
                        $companyCurrencyConversionTrans = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['DIAmount']);
                        $companyCurrencyConversionVat = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['vatAmount']);
                        $companyCurrencyConversionNet = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['netAmount']);

                        $suppInvoiceDetArray[] = array(
                            'directInvoiceAutoID' => $bookInvSupp->bookingSuppMasInvAutoID,
                            'companyID' => $bookInvSupp->companyID,
                            'companySystemID' => $bookInvSupp->companySystemID,
                            'serviceLineSystemID' => $dt['serviceLineSystemID'],
                            'serviceLineCode' => isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null,
                            'chartOfAccountSystemID' => $dt['glSystemID'],
                            'glCode' => isset($glCode->AccountCode) ? $glCode->AccountCode : null,
                            'glCodeDes' => isset($glCode->AccountDescription) ? $glCode->AccountDescription : null,
                            'comments' => $dt['comments'],
                            'DIAmountCurrency' => $myCurr,
                            'DIAmountCurrencyER' => 1,
                            'DIAmount' => $dt['DIAmount'],
                            'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                            'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                            'comRptCurrency' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                            'comRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                            'localCurrency' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                            'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                            'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                            'vatSubCategoryID' => $dt['vatSubCategoryID'],
                            'VATPercentage' => $dt['vatPercentage'],
                            'VATAmount' => $dt['vatAmount'],
                            'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                            'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                            'netAmount' => $dt['netAmount'],
                            'netAmountLocal' => $companyCurrencyConversionNet['localAmount'],
                            'netAmountRpt' => $companyCurrencyConversionNet['reportingAmount']
                        );
                    }
                    if ($bookInvSupp->documentType == 4) {
                        $myCurr = $dt['currency'];

                        $companyCurrencyConversion = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, 0);
                        $companyCurrencyConversionTrans = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['DIAmount']);
                        $companyCurrencyConversionVat = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['vatAmount']);
                        $companyCurrencyConversionNet = \Helper::currencyConversion($bookInvSupp->companySystemID, $myCurr, $myCurr, $dt['netAmount']);

                        $suppInvoiceDetArray[] = array(
                            'directInvoiceAutoID' => $bookInvSupp->bookingSuppMasInvAutoID,
                            'companyID' => $bookInvSupp->companyID,
                            'companySystemID' => $bookInvSupp->companySystemID,
                            'serviceLineSystemID' => $dt['serviceLineSystemID'],
                            'serviceLineCode' => isset($segment->ServiceLineCode) ? $segment->ServiceLineCode : null,
                            'chartOfAccountSystemID' => $dt['glSystemID'],
                            'glCode' => isset($glCode->AccountCode) ? $glCode->AccountCode : null,
                            'glCodeDes' => isset($glCode->AccountDescription) ? $glCode->AccountDescription : null,
                            'comments' => $dt['comments'],
                            'DIAmountCurrency' => $myCurr,
                            'DIAmountCurrencyER' => 1,
                            'DIAmount' => $dt['DIAmount'],
                            'localAmount' => $companyCurrencyConversionTrans['localAmount'],
                            'comRptAmount' => $companyCurrencyConversionTrans['reportingAmount'],
                            'comRptCurrency' => isset($companyCurrency->reportingcurrency->currencyID) ? $companyCurrency->reportingcurrency->currencyID : null,
                            'comRptCurrencyER' => $companyCurrencyConversion['trasToRptER'],
                            'localCurrency' => isset($companyCurrency->localcurrency->currencyID) ? $companyCurrency->localcurrency->currencyID : null,
                            'localCurrencyER' => $companyCurrencyConversion['trasToLocER'],
                            'vatMasterCategoryID' => $dt['vatMasterCategoryID'],
                            'vatSubCategoryID' => $dt['vatSubCategoryID'],
                            'VATPercentage' => $dt['vatPercentage'],
                            'VATAmount' => $dt['vatAmount'],
                            'VATAmountLocal' => $companyCurrencyConversionVat['localAmount'],
                            'VATAmountRpt' => $companyCurrencyConversionVat['reportingAmount'],
                            'netAmount' => $dt['netAmount'],
                            'netAmountLocal' => $companyCurrencyConversionNet['localAmount'],
                            'netAmountRpt' => $companyCurrencyConversionNet['reportingAmount']
                        );
                    }
                }
                DirectInvoiceDetails::insert($suppInvoiceDetArray);
            }

            $db = isset($request->db) ? $request->db : "";

            $params = array('autoID' => $bookInvSupp->bookingSuppMasInvAutoID,
                'company' => $bookInvSupp->companySystemID,
                'document' => $bookInvSupp->documentSystemID,
                'segment' => '',
                'category' => '',
                'amount' => '',
                'employee_id' => $employee->employeeSystemID
            );


            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
            }
            if($status == 2) {
                $documentApproveds = DocumentApproved::where('documentSystemCode', $bookInvSupp->bookingSuppMasInvAutoID)->where('documentSystemID', 11)->get();

                foreach ($documentApproveds as $documentApproved) {
                    $documentApproved["approvedComments"] = "Generated Supplier Invoice through HRMS system";
                    $documentApproved["db"] = $db;
                    \Helper::approveDocumentForApi($documentApproved);

                }
            }
                DB::commit();

            if($status == 1){
                return $this->sendResponse($bookInvSupp, trans('custom.supplier_invoice_created_successfully'));
            }

            if($status == 2) {
                return $this->sendResponse($bookInvSupp, trans('custom.supplier_invoice_created_successfully'));
            }
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
}
