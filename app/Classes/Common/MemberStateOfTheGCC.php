<?php

namespace App\Classes\Common;

use App\Models\CountryMaster;

class MemberStateOfTheGCC
{

    /*
        1.Bahrain
        2.Kuwait
        3.Qatar
        4.Oman
        5.Saudi Arabia
        6.United Arab Emirates
    */
    public static $gccCountriesArray = [1,11,8,9,5,26];
    public static function  getMemberStateOfTheGCCCountries() : Array
    {
        $countries = CountryMaster::whereIn('countryID',self::$gccCountriesArray)->pluck('countryID')->toArray();
        return $countries;
    }
}
