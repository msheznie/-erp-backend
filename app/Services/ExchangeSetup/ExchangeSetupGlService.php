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
            case 5:
            case 7:
            case 6:
                if(isset($finalData) && isset($finalData[2]))
                    unset($finalData[2]);

                break;
            case 3:
                if(isset($finalData) && isset($finalData[3]))
                    unset($finalData[3]);
                break;
        }

        return $finalData;

    }

}
