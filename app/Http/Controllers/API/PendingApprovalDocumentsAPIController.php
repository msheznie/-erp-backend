<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Services\Common\PendingApprovalDocumentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendingApprovalDocumentsAPIController extends AppBaseController
{
    private PendingApprovalDocumentsService $service;

    public function __construct(PendingApprovalDocumentsService $service)
    {
        $this->service = $service;
    }

    public function getPendingDocumentsByFinancePeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companySystemID' => 'required|integer|min:1',
            'companyFinancePeriodID' => 'required|integer|min:1',
            'departmentSystemID' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companySystemID = (int)$request->get('companySystemID');
        $companyFinancePeriodID = (int)$request->get('companyFinancePeriodID');
        $departmentSystemID = $request->has('departmentSystemID') ? (int)$request->get('departmentSystemID') : null;

        $rows = $this->service->listByFinancePeriod($companySystemID, $companyFinancePeriodID, $departmentSystemID);

        return $this->sendResponse(
            ['data' => $rows, 'count' => count($rows)],
            trans('custom.retrieve', ['attribute' => trans('custom.record')])
        );
    }
}

