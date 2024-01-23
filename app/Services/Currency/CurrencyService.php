<?php

namespace App\Services\Currency;

use App\Models\CurrencyMaster;
use Aws\DynamoDb\NumberValue;

class CurrencyService
{
    public static function getCurrencyDecimalPlace($currencyID):int {
        $currencyObj = CurrencyMaster::select('DecimalPlaces')->where('currencyID',$currencyID)->first();
        return $currencyObj->DecimalPlaces;
    }

    public static function convertNumberFormatToNumber($value) {
        return ($value) ? +(str_replace(',','',$value)) : 0;
    }
}
