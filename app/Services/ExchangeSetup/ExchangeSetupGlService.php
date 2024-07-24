<?php

namespace App\Services\ExchangeSetup;
use App\Models\CurrencyConversion;
use ExchangeSetupConfig;
use Illuminate\Support\Arr;

class ExchangeSetupGlService
{
    public function postGlEntry($finalData,$masterData,$linkedDocument)
    {
        $changeIndexArray = [
            "documentLocalCurrencyER",
            "documentLocalAmount",
            "documentRptCurrencyER",
            "documentRptAmount",
            "BPVbankCurrencyER"
        ];

        $linkedDocumentIndexArray  = [
            'reportingCurrencyER',
            'localCurrencyER'
        ];

        $allowGainOrLossEntry = ExchangeSetupConfig::checkExchangeRateChangedOnDocumnentLevel($masterData);

        if($allowGainOrLossEntry)
            return $finalData;

        switch ($masterData->invoiceType)
        {
            case 2:
            case 6:
                $exchangeRatesLinkedDocuments = Arr::only($linkedDocument->toArray(), $linkedDocumentIndexArray);

                if(isset($finalData[2]))
                {

                    $values = Arr::only($finalData[2], $changeIndexArray);
                    $bankValues =  Arr::only($finalData[1], $changeIndexArray);

                    if($values['documentLocalAmount'] < 0)
                    {
                        $bankValues['documentLocalAmount'] += $values['documentLocalAmount'];
                    }else{
                        $bankValues['documentLocalAmount'] -= $values['documentLocalAmount'];
                    }

                    if($values['documentRptAmount'] < 0)
                    {
                        $bankValues['documentRptAmount'] += $values['documentRptAmount'];
                    }else {
                        $bankValues['documentRptAmount'] -= $values['documentRptAmount'];
                    }


                    foreach ($bankValues as $key => $value) {
                        if (array_key_exists($key, $finalData[1])) {
                            $finalData[1][$key] = $value;
                        }
                    }
                }

                if(isset($finalData) && isset($finalData[2]))
                    unset($finalData[2]);

                $data = array();

                foreach ($finalData as $dt)
                {
                    $dt['documentLocalCurrencyER'] = $exchangeRatesLinkedDocuments['localCurrencyER'];
                    $dt['documentRptCurrencyER'] = $exchangeRatesLinkedDocuments['reportingCurrencyER'];
                    array_push($data,$dt);
                }
                $finalData = $data;
                break;
            case 3:
                $conversion = CurrencyConversion::where('masterCurrencyID', $masterData->supplierTransCurrencyID)->where('subCurrencyID', $masterData->localCurrencyID)->first();
                $conversionRpt = CurrencyConversion::where('masterCurrencyID', $masterData->supplierTransCurrencyID)->where('subCurrencyID', $masterData->companyRptCurrencyID)->first();
                $bankCurrencyConversion = CurrencyConversion::where('masterCurrencyID', $masterData->supplierTransCurrencyID)->where('subCurrencyID', $masterData->BPVbankCurrency)->first();
                $exchangeRatesLinkedDocuments = [];
                $exchangeRatesLinkedDocuments['documentLocalCurrencyER'] = $conversion['conversion'];
                $exchangeRatesLinkedDocuments['documentRptCurrencyER'] = $conversionRpt['conversion'];
                $exchangeRatesLinkedDocuments['BPVbankCurrencyER'] = $bankCurrencyConversion['conversion'];

                $values = Arr::only($finalData[3], $changeIndexArray);
                $bankValues =  Arr::only($finalData[1], $changeIndexArray);

                if($values['documentLocalAmount'] < 0)
                {
                    $bankValues['documentLocalAmount'] += $values['documentLocalAmount'];
                }else{
                    $bankValues['documentLocalAmount'] -= $values['documentLocalAmount'];
                }

                if($values['documentRptAmount'] < 0)
                {
                    $bankValues['documentRptAmount'] += $values['documentRptAmount'];
                }else {
                    $bankValues['documentRptAmount'] -= $values['documentRptAmount'];
                }


                foreach ($bankValues as $key => $value) {
                    if (array_key_exists($key, $finalData[1])) {
                        $finalData[1][$key] = $value;
                    }
                }

                if(isset($finalData) && isset($finalData[3]))
                    unset($finalData[3]);

                $data = [];

                foreach ($finalData as $dt)
                {
                    $dt['documentLocalCurrencyER'] = $exchangeRatesLinkedDocuments['documentLocalCurrencyER'];
                    $dt['documentRptCurrencyER'] = $exchangeRatesLinkedDocuments['documentRptCurrencyER'];
                    $dt['BPVbankCurrencyER'] = $exchangeRatesLinkedDocuments['BPVbankCurrencyER'];
                    array_push($data,$dt);
                }
                $finalData = $data;
                break;
        }

        return $finalData;

    }

}
