<?php

namespace App\Services\ExchangeSetup\DocumentConfigs;

use App\Models\PaySupplierInvoiceMaster;
use App\Services\ExchangeSetup\CrossExchangeRateService;
use Illuminate\Http\Request;

class ExchangeSetupDocumentConfigurationService
{

    private $firstCurrencyExchangeRate;
    private $firstCurrencyId;

    public function updateDocumentExchangeRate($input)
    {
        switch ($input['documentSystemId'])
        {
            case 4 :
                $paymentVoucherDocumentExchangeSetupConfig = new PaymentVoucherDocumentExchangeSetupConfig($input['companySystemId'],$input['payMasterAutoId']);
                $data = $paymentVoucherDocumentExchangeSetupConfig->updateTheExchangeRateDocument($input);
                return $data;
        }
    }

    public function updateSimilarCurrenciesExchangeRates($input)
    {

        $mapCurrencyRateWithIdArray = [
            "companyRptCurrencyER" => "companyRptCurrencyID",
            "localCurrencyER" => "localCurrencyID",
            "BPVbankCurrencyER" => "BPVbankCurrency",
        ];

        $paymentVoucherMaster = $input['exchangeRateData'];
        $paymentVoucherMasterOrg = PaySupplierInvoiceMaster::find($paymentVoucherMaster['PayMasterAutoId'])->only('supplierTransCurrencyER','companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER');
        $paymentVoucherMasterOrg = collect($paymentVoucherMasterOrg);
        $paymentVoucherMasterData = collect($paymentVoucherMaster)->only('supplierTransCurrencyER','companyRptCurrencyER','localCurrencyER','BPVbankCurrencyER');
        $difference = $paymentVoucherMasterData->diffAssoc($paymentVoucherMasterOrg);
        $master = $paymentVoucherMaster;
        if(count($difference) > 0)
        {
            $similarCurencyId = $paymentVoucherMaster[$mapCurrencyRateWithIdArray[$difference->keys()->first()]];
            $similarCurencyExchangeRate = $paymentVoucherMasterData[$difference->keys()->first()];

            $changeSimilarCurrencies = PaySupplierInvoiceMaster::find($paymentVoucherMaster['PayMasterAutoId'])->only('companyRptCurrencyID','localCurrencyID','BPVbankCurrency');
            $master  = PaySupplierInvoiceMaster::find($paymentVoucherMaster['PayMasterAutoId']);
            foreach ($changeSimilarCurrencies as $key => $changeSimilarCurrency)
            {
                if($changeSimilarCurrency == $similarCurencyId)
                {
                    $this->firstCurrencyExchangeRate =  (!isset( $this->firstCurrencyExchangeRate)) ? $similarCurencyExchangeRate : $this->firstCurrencyExchangeRate;
                    $this->firstCurrencyId =  (!isset( $this->firstCurrencyId)) ? $similarCurencyId : $this->firstCurrencyId;

                    $master[array_search($key,$mapCurrencyRateWithIdArray)] = $similarCurencyExchangeRate;
                    $master->save();

                }
            }
        }
        return ['success' => true, 'data' => $master, 'message' => 'Data updated successfully'];

    }


    public function updateExchangeRate($input)
    {
        if(!isset($input['exchangeRateData']))
            return ['success' => false, 'data' => [], 'message' => 'Cannot update data'];

        $masterData = PaySupplierInvoiceMaster::find($input['exchangeRateData']['PayMasterAutoId']);
        $inputData = $input['exchangeRateData'];

        $masterData['companyRptCurrencyER'] = $inputData['companyRptCurrencyER'];
        $masterData['localCurrencyER'] = $inputData['localCurrencyER'];
        $masterData['BPVbankCurrencyER'] = $inputData['BPVbankCurrencyER'];

        $updatedData =  $masterData->save();

        if(!$updatedData)
            return ['success' => false, 'data' => [], 'message' => 'Cannot update data'];

        return ['success' => true, 'data' => $masterData, 'message' => 'Data updated successfully'];


    }
}
