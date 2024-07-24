<?php

namespace App\helper;

use App\enums\paymentVoucher\PaymentVoucherType;
use App\Models\DocumentRestrictionAssign;
use App\Models\EmployeeNavigation;
use App\Models\ExchangeSetupConfiguration;
use App\Models\ExchangeSetupDocument;
use App\Models\ExchangeSetupDocumentType;
use Auth;

class ExchangeSetupConfig
{

    public function checkPolicy($companyID)
    {
        $user = Auth::user();

        if (!isset($user))
            return ['sucess' => false, 'message' => "User details not found!", 'policy' => false];

        if (!isset($user->employee))
            return ['sucess' => false, 'message' => "Employee details not found!", 'policy' => false];

        $employeeNavigation = EmployeeNavigation::where('employeeSystemID', $user->employee->employeeSystemID)->where('companyID', $companyID)->first();

        if (!isset($employeeNavigation))
            return ['sucess' => false, 'message' => "Employee user group data not found!", 'policy' => false];

        $documentRestrictionPolicy = DocumentRestrictionAssign::where('documentRestrictionPolicyID', 14)->where('userGroupID', $employeeNavigation->userGroupID)->where('companySystemID', $companyID)->first();

        if (!isset($documentRestrictionPolicy))
            return ['sucess' => false, 'message' => "User group dosen't have access to the document restriction policy!", 'policy' => false];

        return ['sucess' => true, 'message' => "Access available for the user group", 'policy' => true];
    }

    public function checkExchageSetupDocumentAllowERAccess($companySystemId, $exchangeSetupDocumentTypeId)
    {
        $exchangeSetupDocumentConfig = ExchangeSetupConfiguration::where('companyId', $companySystemId)->where('exchangeSetupDocumentTypeId', $exchangeSetupDocumentTypeId)->first();
        if (!isset($exchangeSetupDocumentConfig))
            return false;

        return $exchangeSetupDocumentConfig->allowErChanges ?? false;
    }

    public function checkExchageSetupDocumentAllowPostExchangeOrGainLossEntryAccess($companySystemId, $documentSlug)
    {
        if(!$documentSlug)
            return false;
        $exchangeSetupDocumentTypeId = ExchangeSetupDocumentType::where('slug',$documentSlug)->first();
        if(!$exchangeSetupDocumentTypeId)
            return false;
        $exchangeSetupDocumentConfig = ExchangeSetupConfiguration::where('companyId', $companySystemId)->where('exchangeSetupDocumentTypeId', $exchangeSetupDocumentTypeId->id)->first();
        if (!$exchangeSetupDocumentConfig)
            return false;
        return $exchangeSetupDocumentConfig->allowGainOrLossCal ?? false;
    }


    public function checkExchangeRateChangedOnDocumnentLevel($masterData)
    {
        $masterExchangeRates = collect($masterData->only('companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER'));
        $paymentVoucherMasterOrg = [];

        $currencyRate = \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['supplierDefCurrencyID'],0);
        $localExchangeRate =  \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['localCurrencyID'], 0);
        $currencyRateBank = \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['BPVbankCurrency'],0);
        $paymentVoucherMasterOrg['companyRptCurrencyER'] = $currencyRate['trasToRptER'];
        $paymentVoucherMasterOrg['localCurrencyER'] = $localExchangeRate['transToDocER'];
        $paymentVoucherMasterOrg['BPVbankCurrencyER'] = $currencyRateBank['transToDocER'];
        $paymentVoucherMasterOrg = collect($paymentVoucherMasterOrg);

        if(count($masterExchangeRates->diffAssoc($paymentVoucherMasterOrg)) > 0);
        {
            return
                $this->checkExchageSetupDocumentAllowPostExchangeOrGainLossEntryAccess($masterData['companySystemID'],PaymentVoucherType::getSlugById($masterData['invoiceType']));

        }
    }

    public function isMasterDocumentExchageRateChanged($masterData)
    {
        $masterExchangeRates = collect($masterData->only('companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER'));
        $paymentVoucherMasterOrg = [];

        $currencyRate = \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['supplierDefCurrencyID'],0);
        $localExchangeRate =  \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['localCurrencyID'], 0);
        $currencyRateBank = \Helper::currencyConversion($masterData['companySystemID'], $masterData['supplierTransCurrencyID'], $masterData['BPVbankCurrency'],0);
        $paymentVoucherMasterOrg['companyRptCurrencyER'] = $currencyRate['trasToRptER'];
        $paymentVoucherMasterOrg['localCurrencyER'] = $localExchangeRate['transToDocER'];
        $paymentVoucherMasterOrg['BPVbankCurrencyER'] = $currencyRateBank['transToDocER'];
        $paymentVoucherMasterOrg = collect($paymentVoucherMasterOrg);

        if(count($masterExchangeRates->diffAssoc($paymentVoucherMasterOrg)) > 0);
        {
            return true;
        }

        return false;
    }


}
