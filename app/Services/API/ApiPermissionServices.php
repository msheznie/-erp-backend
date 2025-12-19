<?php

namespace App\Services\API;

use App\Models\DocumentSystemMapping;

class ApiPermissionServices
{

    public static function checkAmendPermission($documentId,$documentSystemID)
    {

        $master = DocumentSystemMapping::where('documentSystemId',$documentSystemID)->where('documentId',$documentId)->first();
        if($master)
            return false;

        return  true;
    }
}
