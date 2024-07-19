<?php

namespace App\Services\ExchangeSetup;

use App\Models\CurrencyConversion;
use App\Models\CurrencyConversionDetail;
use App\Models\CurrencyConversionMaster;
use App\Models\PaySupplierInvoiceMaster;

class CrossExchangeRateService
{
        public function calculateCrossExchangeRate($input)
        {

            $baseCurrencyExchangeId = 0;
            $secondCurrencyExchaneRate = 0;

            $mapCurrencyExchangeRateWithId = [
                "BPVbankCurrencyER"  => 'BPVbankCurrency',
                "localCurrencyER"  => 'localCurrencyID',
                "companyRptCurrencyER"  => 'companyRptCurrencyID'
            ];
            /*
            * 1 EUR (Currency A) = 1.20 USD - A
            * 1 GBP (Currency B) = 1.30 USD - B
            * The cross exchange rate from EUR to GBP = (A USD/EURO) / (B USD/GBP) = B/A (GBP/EUR)
            */


            $currencyIds = PaySupplierInvoiceMaster::find($input['payMasterAutoId'])->only('companyRptCurrencyID','localCurrencyID','BPVbankCurrency');
            $baseCurrencyExchangeId = $input['exchangeRateData']['supplierTransCurrencyID'];

            if(count(array_unique($currencyIds,$baseCurrencyExchangeId)) >= 2)
            {
                $master = PaySupplierInvoiceMaster::find($input['payMasterAutoId']);
                $currencyValues = PaySupplierInvoiceMaster::find($input['payMasterAutoId'])->only('BPVbankCurrency','companyRptCurrencyID','localCurrencyID');
                $currencyConversionMaster = CurrencyConversionMaster::where('approvedYN',1)->orderBy('id','desc')->first();
                $secondCurrencyExchaneRate = $master[$input['editedFiles']];

                $currencyChanged = $master[$mapCurrencyExchangeRateWithId[$input['editedFiles']]];
                $filterData = collect($currencyValues)->filter(function($item)  use ($currencyChanged,$baseCurrencyExchangeId) {
                    return ($item != $currencyChanged && $item != $baseCurrencyExchangeId);
                });


                foreach ($filterData as $key => $data) {
                    $currencyConversionDetail = CurrencyConversionDetail::where('masterCurrencyID', $data)
                        ->where('currencyConversioMasterID', $currencyConversionMaster->id)
                        ->where('subCurrencyID',$currencyChanged)
                        ->first();
                    if(array_search($key,$mapCurrencyExchangeRateWithId))
                    {
                        $master[array_search($key,$mapCurrencyExchangeRateWithId)] = round($input['exchangeRateData'][$input['editedFiles']]/ $currencyConversionDetail['conversion'],2);
                        $master->save();
                    }
                };
            }

            return ['success' => true, 'data' => '', 'message' => 'Data updated successfully'];

        }

}
