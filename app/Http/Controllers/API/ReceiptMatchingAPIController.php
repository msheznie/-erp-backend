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
        $externalRef = $input['externalRef'] ?? Str::uuid()->toString();
        $db = isset($request->db) ? $request->db : "";

        // Check for duplicate externalRef in erp_matchdocumentmaster
        $exists = DB::table('erp_matchdocumentmaster')->where('externalRef', $externalRef)->exists();
        if ($exists) {
            return response()->json([
                'externalRef' => $externalRef,
                'status' => 'failed',
                'message' => 'Duplicate externalRef. This reference has already been processed.'
            ], 409);
        }

        // Dispatch background job
        $apiExternalKey = $request->api_external_key;
        $apiExternalUrl = $request->api_external_url;
        $authorization = $request->header('Authorization');

        CreateReceiptMatching::dispatch($input, $db, $apiExternalKey, $apiExternalUrl, $authorization, $externalRef);

        // Initial response
        return response()->json([
            'externalRef' => $externalRef,
            'status' => 'processing',
            'message' => 'Receipt matching request has been queued for processing.'
        ], 202);
    }
} 