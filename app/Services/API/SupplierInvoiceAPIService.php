<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\SystemGlCodeScenarioDetail;
use Carbon\Carbon;

class SupplierInvoiceAPIService extends AppBaseController
{
    public static function storeBookingInvoice($input)
    {
        if (isset($input['bookingDate'])) {
            if ($input['bookingDate']) {
                $input['bookingDate'] = new Carbon($input['bookingDate']);
            }
        }

        if (isset($input['supplierInvoiceDate'])) {
            if ($input['supplierInvoiceDate']) {
                $input['supplierInvoiceDate'] = new Carbon($input['supplierInvoiceDate']);
            }
        }

        $lastSerial = BookInvSuppMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $input['serialNo'] = $lastSerialNumber;
        $input['supplierTransactionCurrencyER'] = 1;
        $input['documentSystemID'] = '11';
        $input['documentID'] = 'SI';

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        $companyfinanceyear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();

        if ($companyfinanceyear) {
            $startYear = $companyfinanceyear['bigginingDate'];
            $finYearExp = explode('-', $startYear);
            $finYear = $finYearExp[0];

            $input['FYBiggin'] = $companyfinanceyear->bigginingDate;
            $input['FYEnd'] = $companyfinanceyear->endingDate;
        } else {
            $finYear = date("Y");
        }

        if ($documentMaster) {
            $bookingInvCode = ($company->CompanyID . '\\' . $finYear . '\\' . 'BSI' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['bookingInvCode'] = $bookingInvCode;
        }

        if ($input['documentType'] != 4) {
            // adding supplier grv details
            $supplierAssignedDetail = SupplierAssigned::select('liabilityAccountSysemID',
                'liabilityAccount', 'UnbilledGRVAccountSystemID', 'UnbilledGRVAccount','VATPercentage')
                ->where('supplierCodeSytem', $input['supplierID'])
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            $input['isLocalSupplier'] = Helper::isLocalSupplier($input['supplierID'], $input['companySystemID']);

            if ($supplierAssignedDetail) {
                $input['supplierVATEligible'] = $supplierAssignedDetail->vatEligible;
                $input['supplierGLCodeSystemID'] = $supplierAssignedDetail->liabilityAccountSysemID;
                $input['supplierGLCode'] = $supplierAssignedDetail->liabilityAccount;
                $input['UnbilledGRVAccountSystemID'] = $supplierAssignedDetail->UnbilledGRVAccountSystemID;
                $input['UnbilledGRVAccount'] = $supplierAssignedDetail->UnbilledGRVAccount;
                $input['VATPercentage'] = $supplierAssignedDetail->VATPercentage;
            }
        } else {
            $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

            if (is_null($checkEmployeeControlAccount)) {
                return $returnData = [
                    'status' => 'error',
                    'message' => 'Please configure Employee control account for this company',
                    'data' => []
                ];
            }

            $input['employeeControlAcID'] = $checkEmployeeControlAccount;
        }

        $bookInvSuppMasters = BookInvSuppMaster::create($input);

        return $returnData = [
            'status' => 'success',
            'message' => 'Supplier Invoice created successfully',
            'data' => $bookInvSuppMasters->toArray()
        ];
    }

}
