<?php

namespace App\Services\ExchangeSetup\DocumentConfigs;

use App\Classes\ExchangeSetup\AccountsPayable\PaymentVoucherExchangeSetupConfig;
use App\enums\paymentVoucher\PaymentVoucherType;
use App\Interfaces\DocumentExchangeSetupConfigInterface;
use App\Models\ExchangeSetupConfiguration;
use App\Models\ExchangeSetupDocument;
use App\Models\PaySupplierInvoiceMaster;
use App\Services\ExchangeSetup\CrossExchangeRateService;
use ExchangeSetupConfig;
use phpDocumentor\Reflection\Types\Collection;

class PaymentVoucherDocumentExchangeSetupConfig implements DocumentExchangeSetupConfigInterface
{
    private $companyId;
    private $documentMasterId;
    private $masterRecord;
    private $transcationCurrencyId;
    private $localCurrencyId;
    private $rptCurrencyId;
    private $bankCurrencyId;
    private $documentPolicyAccess = false;
    private $exchangeRateDocumentConfigAccess = false;
    private $paymentVoucherExchangeSetupConfig;

    private $fieldChanged= [
       "bankCurrency" => false,
       "localCurrency" => false,
       "reportingCurrency" => false
    ];

    public function __construct($companySystemId,$documentMasterId)
    {
        $this->companyId = $companySystemId;
        $this->documentMasterId = $documentMasterId;
        $this->masterRecord = PaySupplierInvoiceMaster::where('PayMasterAutoId',$documentMasterId)->first();
        $this->paymentVoucherExchangeSetupConfig  = new PaymentVoucherExchangeSetupConfig();

        if($this->masterRecord)
        {
            $this->transcationCurrencyId = $this->masterRecord->supplierTransCurrencyID;
            $this->localCurrencyId = $this->masterRecord->localCurrencyID;
            $this->rptCurrencyId = $this->masterRecord->companyRptCurrencyID;
            $this->bankCurrencyId = $this->masterRecord->BPVbankCurrency;
        }

        $this->checkDocumentRestricationPolicyAccess();
        $this->checkDocumentTypeExchangeSetupConfigAccess();
        $this->checkScenario();

    }

    public function checkDocumentRestricationPolicyAccess()
    {
        $this->documentPolicyAccess = (ExchangeSetupConfig::checkPolicy($this->companyId)) ? ExchangeSetupConfig::checkPolicy($this->companyId)['policy'] : false;
    }

    public function checkDocumentTypeExchangeSetupConfigAccess()
    {
        if($this->documentPolicyAccess && $this->masterRecord)
        {
            $exchangeSetupDocument = ExchangeSetupDocument::where('documentSystemID' , $this->masterRecord->documentSystemID)->first();
            $exchangeSetupDocumentType = $exchangeSetupDocument->types
                                        ->where('exchangeSetupDocumentId',$exchangeSetupDocument->id)
                                        ->where('slug',PaymentVoucherType::getSlugById($this->masterRecord->invoiceType))
                                        ->first();

            $this->exchangeRateDocumentConfigAccess = ExchangeSetupConfig::checkExchageSetupDocumentAllowERAccess($this->companyId,$exchangeSetupDocumentType->id);
        }
    }

    public  function checkScenario()
    {

        if($this->documentPolicyAccess && $this->exchangeRateDocumentConfigAccess &&  (($this->masterRecord->directdetail->isEmpty()) && ($this->masterRecord->advancedetail->isEmpty()) && ($this->masterRecord->supplierdetail->isEmpty())))
        {
            if($this->transcationCurrencyId != $this->localCurrencyId)
                $this->paymentVoucherExchangeSetupConfig->setEnableLocalCurrency(true);

            if($this->transcationCurrencyId != $this->bankCurrencyId)
                $this->paymentVoucherExchangeSetupConfig->setEnableBankCurrency(true);

            if($this->transcationCurrencyId != $this->rptCurrencyId)
                $this->paymentVoucherExchangeSetupConfig->setEnableRptCurrency(true);
        }



    }

    public function checkScenarioOne()
    {
        /*
         * Scenario
         * -----------------------------------------------------------------------------
         *
         * Transcation currency not matching with any other currencies
         * But Other currencies matching with each currency
         *
         */
        // transcation currency != any other currency

        $array = [$this->bankCurrencyId,$this->localCurrencyId,$this->rptCurrencyId];
        $transId = $this->transcationCurrencyId;

        $countOfOnes = count(array_filter($array, function($value) use ($transId) {
            return $value != $transId;
        }));

        if((count(array_unique($array)) != count($array)) && ($countOfOnes >= 2 ))
        {
            $this->paymentVoucherExchangeSetupConfig->setScenario(1);
            $this->paymentVoucherExchangeSetupConfig->setMessage("Same exchange rate will be applied to other similar currencies");
        }

    }

    public function checkScenarioTwo()
    {

        /*
         * Scenario
         * -----------------------------------------------------------------------------
         *
         * Transcation Currency does not match with local, reporting currency
         * Other currencies does not match with each other currencies
         *
         */


//        if(!in_array($this->transcationCurrencyId,[$this->rptCurrencyId,$this->localCurrencyId]))
//        {
//
//        }

        $array = [$this->bankCurrencyId,$this->localCurrencyId,$this->rptCurrencyId];
        $transcationCurrency = $this->transcationCurrencyId;
        $array = array_filter($array,function($value) use ($transcationCurrency) {
            return $value != $transcationCurrency;
        });
        if(count(array_unique($array)) > 1)
        {
            $this->paymentVoucherExchangeSetupConfig->setScenario(2);
            $this->paymentVoucherExchangeSetupConfig->setMessage("Are you want to update the cross exchange for currency ?");
        }


    }

    public function checkScenarioThree()
    {

        /*
         *
         * scenario
         * -----------------------------------------------------------------------------------
         * transcation currency not match with other currencies
         * local currency equal to reporting currency
         *
         * */

        // transcation currency != any other currency
        if(!in_array($this->transcationCurrencyId,[$this->bankCurrencyId,$this->rptCurrencyId,$this->localCurrencyId]))
        {

        }


    }

    public function getDocumentExchangeRateConfigAccess()
    {

        return $this->paymentVoucherExchangeSetupConfig;
    }

    public function updateTheExchangeRateDocument($input)
    {

        $paymentVoucherMasterData = $input['exchangeRateData'];

        if($input['documentCurrentExchangeRateScenario'] == 0)
        {
            $mapCurrencyRateWithIdArray = [
                "companyRptCurrencyER" => "companyRptCurrencyID",
                "localCurrencyER" => "localCurrencyID",
                "BPVbankCurrencyER" => "BPVbankCurrency",
            ];

            $array = [$this->bankCurrencyId,$this->localCurrencyId,$this->rptCurrencyId];
            $currencyChangedId = $paymentVoucherMasterData[$mapCurrencyRateWithIdArray[$input['editedFiles']]];
            $count = collect($array)->filter(function($value) use ($currencyChangedId){
                return $value == $currencyChangedId;
            })->count();

            if($count > 1) {
                $this->checkScenarioOne();
                if($this->paymentVoucherExchangeSetupConfig->message)
                {
                    return ['success' => false, 'message' => $this->paymentVoucherExchangeSetupConfig->message, 'scenario' => 1];
                }
            }


            $this->checkScenarioTwo();

            if($this->paymentVoucherExchangeSetupConfig->message)
            {
                return ['success' => false, 'message' => $this->paymentVoucherExchangeSetupConfig->message, 'scenario' => 2];
            }
            else
            {
                $service = new ExchangeSetupDocumentConfigurationService();
                $result = $service->updateExchangeRate($input);
                return $result;
            }

        }


        if(isset($input['documentCurrentExchangeRateScenario']) && $input['documentCurrentExchangeRateScenario'] == 1 && (isset($input['updateScenrioOne']) && $input['updateScenrioOne']))
        {
            $this->checkScenarioTwo();
            if($this->paymentVoucherExchangeSetupConfig->message)
            {
                return ['success' => false, 'message' => $this->paymentVoucherExchangeSetupConfig->message, 'scenario' => 2];
            }

        }

        return $this->checkConditionsOfScenarioAndUpdate($input);
    }

    public function checkConditionsOfScenarioAndUpdate($input)
    {
        $service = new ExchangeSetupDocumentConfigurationService();
        $masterData = PaySupplierInvoiceMaster::find($input['exchangeRateData']['PayMasterAutoId']);

        if(isset($input['updateScenrioOne']) && $input['updateScenrioOne'] === true)
        {
            $result = $service->updateSimilarCurrenciesExchangeRates($input);
        }


        if((isset($input['updateScenrioOne']) && $input['updateScenrioOne'] === true) && (isset($input['updateScenrioTwo']) && $input['updateScenrioTwo'] === true))
        {
            $crossExchangeRateService = new CrossExchangeRateService();
            $result = $crossExchangeRateService->calculateCrossExchangeRate($input);
        }


        if((isset($input['updateScenrioOne']) && $input['updateScenrioOne'] === false) && (isset($input['updateScenrioTwo']) && $input['updateScenrioTwo'] === true))
        {
            $inputData = $input['exchangeRateData'];

            $crossExchangeRateService = new CrossExchangeRateService();
            $result = $crossExchangeRateService->calculateCrossExchangeRate($input);
        }

        if((isset($input['updateScenrioOne']) && $input['updateScenrioOne'] === false) && (isset($input['updateScenrioTwo']) && $input['updateScenrioTwo'] === false))
        {
            $paymentVoucherMaster = $input['exchangeRateData'];
            $paymentVoucherMasterOrg = PaySupplierInvoiceMaster::find($paymentVoucherMaster['PayMasterAutoId'])->only('supplierTransCurrencyER','companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER');
            $paymentVoucherMasterOrg = collect($paymentVoucherMasterOrg);
            $paymentVoucherMasterData = collect($paymentVoucherMaster)->only('supplierTransCurrencyER','companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER');
            $difference = $paymentVoucherMasterData->diffAssoc($paymentVoucherMasterOrg);
            if($input['documentCurrentExchangeRateScenario'] != 1)
            {
                $masterData = $masterData->update($difference->toArray());
            }
            $result =   ['success' => true, 'data' => $masterData, 'message' => 'Data not updated'];
        }


       return $result;
    }


}
