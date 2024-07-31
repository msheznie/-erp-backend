<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\ThirdPartyIntegrationKeys;
use App\Models\ThirdPartySystems;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ThirdPartyAPIController extends AppBaseController
{
    public function getSupplierContracts(Request $request)
    {
        $data = $request->all();

        $thirdPartySystem = ThirdPartySystems::where('description','CM')->where('status',1)->first();
        if(!empty($thirdPartySystem)) {
            $thirdPartyIntegrationKey = ThirdPartyIntegrationKeys::where('third_party_system_id',$thirdPartySystem->id)
                ->where('company_id',$data['companyId'])
                ->whereNotNull('api_external_key')
                ->whereNotNull('api_external_url')
                ->first();
            if(!empty($thirdPartyIntegrationKey)) {
                try {
                    $client = new Client();

                    $url = $thirdPartyIntegrationKey->api_external_url.'get_contract_data?supplierId='.$data['supplierId'];

                    $response = $client->request('GET', $url, [
                        'headers' => [
                            'Authorization' => $thirdPartyIntegrationKey->api_external_key,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ],
                    ]);

                    if ($response->getStatusCode() == 200) {
                        $contracts = json_decode($response->getBody(), true);
                        $data = $contracts['data'];
                        return $this->sendResponse($data, '');
                    }
                    else {
                        return $this->sendResponse([], '');
                    }
                } catch (\Exception $e) {
                    return $this->sendError('Failed to get supplier contracts');
                }
            }
            else {
                return $this->sendError('Contract ID Policy has been enabled. Please Check Contract Management Integration');
            }
        }
        else {
            return $this->sendError('Contract ID Policy has been enabled. Please Check Contract Management Integration');
        }
    }
}
