<?php

namespace App\Http\Controllers\API\POS;

use App\Http\Controllers\AppBaseController;
use App\Jobs\POS\POSSyncJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Company;

class PosSyncAPIController extends AppBaseController
{
  
    public function syncShift(Request $request): JsonResponse
    {
        $externalReference = $request->get('external_reference');
        $tenantUuid = $request->get('tenant_uuid') ?? env('TENANT_UUID', 'local');
        $companySystemID = $request->get('company_id');
        $companyMaster = Company::where('companySystemID', $companySystemID)->first();
        if (!$companyMaster) {
            return $this->sendError(trans('custom.the_company_system_ID_not_matching_with_system', ['companySystemID' => $companySystemID]), 422);
        }

  
        
        POSSyncJob::dispatchSync(
            $request->all(),
            $request->input('db', ''),
            (string) $request->get('api_external_key', ''),
            (string) $request->get('api_external_url', ''),
            (string) $request->get('webhook_url', ''),
            $externalReference,
            $tenantUuid,
            $request->get('company_id'),
            $request->get('log_id'),
            $request->get('thirdPartyIntegrationKeyId')
        );

        return $this->sendResponse(
            ['external_reference' => $externalReference],
            'POS shift sync accepted and queued.'
        );
    }
}
