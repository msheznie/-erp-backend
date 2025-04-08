<?php

namespace App\Services\Currency;

use App\Models\CurrencyMaster;
use Aws\DynamoDb\NumberValue;

class CurrencyService
{
    public static function getCurrencyDecimalPlace($currencyID):int {
        $currencyObj = CurrencyMaster::select('DecimalPlaces')->where('currencyID',$currencyID)->first();
        if(!isset($currencyObj))
        {
            return 2;
        }
        return $currencyObj->DecimalPlaces;
    }

    public static function convertNumberFormatToNumber($value) {
        return ($value) ? +(str_replace(',','',$value)) : 0;
    }

    public static function formatNumberWithPrecision($value) {

        $numericValue = (float) str_replace(',', '', $value);
    
        if ($numericValue >= 1) {
            return number_format($numericValue, 7, '.', '');
        }
    
        return number_format($value, 7, '.', '');
    }
    
}
