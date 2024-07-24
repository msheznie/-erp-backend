<?php

namespace App\Services\ExchangeSetup;
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
            "documentRptAmount"
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
                    $data = array();
                    $values = Arr::only($finalData[2], $changeIndexArray);
                    $bankValues =  Arr::only($finalData[1], $changeIndexArray);
                    $bankValues['documentLocalAmount'] += $values['documentLocalAmount'];
                    $bankValues['documentRptAmount'] += $values['documentRptAmount'];

                    foreach ($bankValues as $key => $value) {
                        if (array_key_exists($key, $finalData[1])) {
                            $finalData[1][$key] = $value;
                        }
                    }
                    unset($finalData[2]);
                }

                foreach ($finalData as $dt)
                {
                    $dt['documentLocalCurrencyER'] = $exchangeRatesLinkedDocuments['localCurrencyER'];
                    $dt['documentRptCurrencyER'] = $exchangeRatesLinkedDocuments['reportingCurrencyER'];
                    array_push($data,$dt);
                }

                $finalData = $data;

        }
        return $finalData;
    }
}
