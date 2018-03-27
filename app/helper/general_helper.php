<?php
namespace App\helper;

use App\Models;

class Helper{
    public static function getAllDocument()
    {
        $document = Models\DocumentMaster::all();
        return $document;
    }

    public static function getCompanyServiceline($company)
    {
        $serviceline = Models\SegmentMaster::where('companySystemID','=',$company)->get();
        return $serviceline;
    }
}
