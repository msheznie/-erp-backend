<?php

namespace App\Services\ExchangeSetup;

use App\Models\ExchangeSetupConfiguration;

class ExchangSetupConfigurationService
{

    public function setConfiguration($input) {

        $exchangeSetupConfiguration = ExchangeSetupConfiguration::where('companyId',$input['companyId'])->where('exchangeSetupDocumentTypeId',$input['exchangeSetupDocumentTypeId'])->first();

        if($exchangeSetupConfiguration)
        {
            $result = $this->update($exchangeSetupConfiguration,$input);
            $message = 'Configurtion updated successfully!';
        }else {
            $result = $this->store($input);
            $message = 'Configurtion created successfully!';
        }

        if($result)
            return ['success' => true, "message" => $message , "data" => $result];

        return ['success' => false , 'message'=> "cannot create or update configuration", "data" => []];
    }

    public function store($input) {
        $exchangeSetupConfig = ExchangeSetupConfiguration::create($input);

        return $exchangeSetupConfig;
    }

    public function update($exchangeSetupDocumentTypeId,$input) {
       $exchangeSetupDocumentTypeId->allowErChanges = $input['allowErChanges'];
       $exchangeSetupDocumentTypeId->allowGainOrLossCal = $input['allowGainOrLossCal'];
       $exchangeSetupDocumentTypeId->isActive = $input['isActive'];
       $exchangeSetupDocumentTypeId->createdBy = $input['createdBy'];
       $res = $exchangeSetupDocumentTypeId->save();

       return $res;
    }

    public static function mapTypesWithExchangeSetupConfig($documentTypes,$companySystemId = NULL)
    {
        $data = $documentTypes->map(function ($type) use ($companySystemId) {
            $exchangeSetupConfiguration = ExchangeSetupConfiguration::where('companyId',$companySystemId)->where('exchangeSetupDocumentTypeId',$type->id)->first();

            return [
                "id" => $type->id,
                "exchangeSetupDocumentId" => $type->exchangeSetupDocumentId,
                "sort" => $type->sort,
                "name" => $type->name,
                "slug" => $type->slug,
                "isActive" => $type->isActive,
                "allowErChanges" => ($exchangeSetupConfiguration) ? (int) $exchangeSetupConfiguration->allowErChanges : null,
                "allowGainOrLossCal" => ($exchangeSetupConfiguration) ? (int) $exchangeSetupConfiguration->allowGainOrLossCal : null
            ];
        });

        return $data;
    }

}
