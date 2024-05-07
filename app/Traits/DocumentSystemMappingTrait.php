<?php

namespace App\Traits;

use App\Models\DocumentSystemMapping;
use App\Models\ThirdPartySystems;
use Carbon\Carbon;

trait DocumentSystemMappingTrait
{
    public static function storeToDocumentSystemMapping($documentSystemID, $documentAutoIds,$header)
    {

        $thirdPartySystemId = 0;
        $data = [];
        if(isset($header))
        {
            $explodedHeader = explode(' ',$header);
            if(isset($explodedHeader[0]))
            {
                $masterThirdPartySystem = ThirdPartySystems::where('description',$explodedHeader[0])->first();
                if(isset($masterThirdPartySystem))
                    $thirdPartySystemId = $masterThirdPartySystem->id;
            }
        }

        if(is_array($documentAutoIds))
        {
           foreach ($documentAutoIds as $documentAutoId)
           {
               array_push($data,[
                   'documentSystemID' => $documentSystemID,
                   'documentId' => $documentAutoId,
                   'thirdPartySystemId' => $thirdPartySystemId,
                   'created_at' => Carbon::now()
               ]);
           }
        }else if ($documentAutoIds instanceof \Illuminate\Database\Eloquent\Collection) {
            self::storeToDocumentSystemMapping($documentSystemID,$documentAutoIds->toArray(),$header);
        }else{
            self::storeToDocumentSystemMapping($documentSystemID,[$documentAutoIds],$header);
        }

        DocumentSystemMapping::insert($data);
    }

}
