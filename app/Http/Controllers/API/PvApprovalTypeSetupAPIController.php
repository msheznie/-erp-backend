<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePvApprovalTypeSetupAPIRequest;
use App\Http\Requests\API\UpdatePvApprovalTypeSetupAPIRequest;
use App\Models\PvApprovalTypeSetup;
use App\Services\PvApprovalTypeSetupService;
use Illuminate\Http\Request;
use App\Criteria\LimitOffsetCriteria;
use App\Repositories\PvApprovalTypeSetupRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class PvApprovalTypeSetupAPIController extends AppBaseController
{
    /** @var PvApprovalTypeSetupRepository */
    private $pvApprovalTypeSetupRepository;

    /** @var PvApprovalTypeSetupService */
    private $pvApprovalTypeSetupService;

    public function __construct(
        PvApprovalTypeSetupRepository $pvApprovalTypeSetupRepository,
        PvApprovalTypeSetupService $pvApprovalTypeSetupService
    ) {
        $this->pvApprovalTypeSetupRepository = $pvApprovalTypeSetupRepository;
        $this->pvApprovalTypeSetupService = $pvApprovalTypeSetupService;
    }

    public function index(Request $request)
    {
        $this->pvApprovalTypeSetupRepository->pushCriteria(new RequestCriteria($request));
        $this->pvApprovalTypeSetupRepository->pushCriteria(new LimitOffsetCriteria($request));

        $setups = $this->pvApprovalTypeSetupRepository->all();

        return $this->sendResponse($setups->toArray(), 'PV approval type setups retrieved successfully.');
    }

    public function store(CreatePvApprovalTypeSetupAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $response = $this->pvApprovalTypeSetupService->store($input);

        if (!$response->isSuccess()) {
            return $this->sendError($response->getMessage(), $response->getStatusCode());
        }

        return $this->sendResponse($response->getData(), $response->getMessage());
    }

    public function show($id)
    {
        /** @var PvApprovalTypeSetup $setup */
        $setup = $this->pvApprovalTypeSetupRepository->findWithoutFail($id);

        if (empty($setup)) {
            return $this->sendError(__('PV approval type setup not found.'));
        }

        return $this->sendResponse($setup->toArray(), 'PV approval type setup retrieved successfully.');
    }

    public function update($id, UpdatePvApprovalTypeSetupAPIRequest $request)
    {
        /** @var PvApprovalTypeSetup $setup */
        $setup = $this->pvApprovalTypeSetupRepository->findWithoutFail($id);

        if (empty($setup)) {
            return $this->sendError(trans('custom.pv_approval_type_setup_not_found'));
        }

        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $setup = $this->pvApprovalTypeSetupRepository->update($input, $id);

        return $this->sendResponse($setup->toArray(), 'PV approval type setup updated successfully.');
    }

    public function destroy($id)
    {
        $response = $this->pvApprovalTypeSetupService->deletePvTypeBaseApproval($id);

        if (!$response->isSuccess()) {
            return $this->sendError($response->getMessage(), $response->getStatusCode());
        }

        return $this->sendResponse($response->getData(), $response->getMessage());
    }

    public function getPVTypeBaseApprovals(Request $request) {
        $input = $request->all();
        
        $pvApprovalTypeSetups = PvApprovalTypeSetup::where('company_system_id', $input['companySystemID'])->where('document_attachment_id', $input['id'])->get();

        return $this->sendResponse($pvApprovalTypeSetups->toArray(), 'PV type base approvals retrieved successfully.');
    }

    public function updatePVTypeBaseApprovalRow(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
        ]);

        $input = $request->except(['id', 'created_at', 'updated_at']);

        $response = $this->pvApprovalTypeSetupService->updatePvTypeBaseApprovalRow($validatedData['id'], $input);

        if (!$response->isSuccess()) {
            return $this->sendError($response->getMessage(), $response->getStatusCode());
        }

        return $this->sendResponse($response->getData(), $response->getMessage());
    }

    public function checkApprovalLevelExists(Request $request) {
        $input = $request->all();

        $response = $this->pvApprovalTypeSetupService->checkApprovalLevelExists($input);

        if (!$response->isSuccess()) {
            return $this->sendError($response->getMessage());
        }

        return $this->sendResponse(null, $response->getMessage());
    }
}

