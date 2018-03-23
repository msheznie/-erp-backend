<?php
namespace App\helper;

use App\Models;

class Helper{
    public static function getAllDocument()
    {
        $document = Models\DocumentMaster::all();
        return $document;
    }
}
