<?php

namespace App\Http\Controllers\API\HRMS;

use App\helper\SupplierInvoice;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateBookInvSuppMasterAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SystemGlCodeScenarioDetail;
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
                    $company = Company::where('companySystemID', $dt['companySystemID'])->first();
                    if (empty($company)) {
                        return $this->sendError('Company not found');
                    }


                    $financeYear = CompanyFinanceYear::where('companySystemID', $dt['companySystemID'])->where('bigginingDate', "<=", $dt['bookingDate'])->where('endingDate', ">=", $dt['bookingDate'])->first();
                    if (empty($financeYear)) {
                        return $this->sendError('Finance Year not found');
                    }

                    $lastSerial = BookInvSuppMaster::where('companySystemID', $dt['companySystemID'])
                        ->where('companyFinanceYearID', $financeYear->companyFinanceYearID)
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }


                    $financePeriod = CompanyFinancePeriod::where('companySystemID', $dt['companySystemID'])->where('departmentSystemID', 4)->where('dateFrom', "<=", $dt['bookingDate'])->where('dateTo', ">=", $dt['bookingDate'])->first();
                    if (empty($financePeriod)) {
                        return $this->sendError('Finance Period not found');
                    }

                    $startYear = $financeYear->bigginingDate;
                    $finYearExp = explode('-', $startYear);
                    $finYear = $finYearExp[0];
                    $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));



                if($dt['documentType'] == 1) {
                    $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                        'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount', 'VATPercentage')
                        ->where('supplierCodeSytem', $dt['supplierID'])
                        ->where('companySystemID', $dt['companySystemID'])
                        ->first();
                    if (empty($supplierAssignedDetail)) {
                        return $this->sendError('Supplier not found');
                    }

                    $supplierCurr = SupplierCurrency::where('supplierCodeSystem', $dt['supplierID'])->first();
                    if (empty($supplierCurr)) {
                        return $this->sendError('Customer currency not found');
                    }
                    if ($supplierCurr) {
                        $myCurr = $supplierCurr->currencyID;
                    }

                    $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
                    $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);
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
                        'VATPercentage' => $supplierAssignedDetail->VATPercentage,
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
                        'documentType' => 1
                    );
                } else if ($dt['documentType'] == 4){
                    $supplierCurr = SupplierCurrency::where('supplierCodeSystem', $dt['supplierID'])->first();
                    if (empty($supplierCurr)) {
                        return $this->sendError('Customer currency not found');
                    }
                    if ($supplierCurr) {
                        $myCurr = $supplierCurr->currencyID;
                    }

                    $companyCurrencyConversion = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, 0);
                    $companyCurrencyConversionTrans = \Helper::currencyConversion($dt['companySystemID'], $myCurr, $myCurr, $dt['bookingAmountTrans']);

                    $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($dt['companySystemID'], 11, 12);

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
                        'VATPercentage' => 0,
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
                        'employeeControlAcID' => $checkEmployeeControlAccount

                );
                }

                }
                BookInvSuppMaster::insert($suppInvoiceArray);

            }
            DB::commit();

            return $this->sendResponse($suppInvoiceArray, 'Supplier Invoice created successfully');


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
