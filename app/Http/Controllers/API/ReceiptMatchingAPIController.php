<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Jobs\CreateReceiptMatching;
use App\Http\Controllers\AppBaseController;

class ReceiptMatchingAPIController extends AppBaseController
{
    public function createReceiptMatchingAPI(Request $request)
    {
        $input = $request->all();
        $db = isset($request->db) ? $request->db : "";

        $authorization = $request->header('Authorization');
        $externalReference = $request->get('external_reference');
        $tenantUuid = $request->get('tenant_uuid') ?? env('TENANT_UUID', 'local');

        CreateReceiptMatching::dispatch($input, $db, $request->api_external_key, $request->api_external_url, $authorization, $externalReference, $tenantUuid);

        return $this->sendResponse(['external_reference' => $externalReference], "Receipt matching request has been successfully queued for processing!");
        
    }
} 