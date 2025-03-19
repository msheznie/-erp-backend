<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteBySupplierIdRequest;
use App\Http\Requests\GetCustomEmailSupplierRequest;
use App\Http\Requests\GetSupplierListCustomEmailRequest;
use App\Http\Requests\StoreTenderCustomEmailRequest;
use Illuminate\Http\Request;
use App\Repositories\TenderCustomEmailRepository;
use Illuminate\Support\Facades\Log;

class TenderCustomEmailController extends AppBaseController
{
    protected $repository;

    public function __construct(TenderCustomEmailRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(StoreTenderCustomEmailRequest $request)
    {
        try {
            $result = $this->repository->storeCustomEmailData($request);
            if (!$result['success']) {
                return $this->sendError('Error occurred');
            }
            return $this->sendResponse($result,'Saved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error occurred');
        }
    }

    public function getCustomEmailSupplier(GetCustomEmailSupplierRequest $request)
    {
        try {
            $tenderUUID = $request->input('tender_uuid');
            $supplierUuid = $request->input('supplier_uuid');
            $documentCode = $request->input('document_code');
            $record = $this->repository->getCustomEmailSupplier($tenderUUID, $supplierUuid, $documentCode);

            if (isset($responseData['success']) && $responseData['success']) {
                return $this->sendError('Error occurred', ['error' => $record['data']]);
            }

            return $this->sendResponse($record,'The Email Record successfully Received');

        } catch (\Exception $e) {
            return $this->sendError('Error occurred', ['error' => $e->getMessage()]);
        }
    }

    public function getSupplierListCustomEmail(GetSupplierListCustomEmailRequest $request)
    {
        try {
            $tenderId = $request->input('tender_uuid');
            $negotiationId = $request->input('negotiationId');
            $responseData = $this->repository->getCustomEmailData($tenderId, $negotiationId);

            if (isset($responseData['success']) && $responseData['success']) {
                return $this->sendError('Error occurred');
            }

            return $this->sendResponse($responseData,'Supplier List successfully Received');

        } catch (\Exception $e) {
            return $this->sendError('Error occurred');
        }
    }

    public function deleteBySupplierId(DeleteBySupplierIdRequest $request)
    {
        try {
            $tenderId = $request->input('tender_uuid');
            $supplierUuid = $request->input('supplier_uuid');

            $deleted = $this->repository->deleteByTenderAndSupplier($tenderId, $supplierUuid);

            if (isset($deleted['success']) && $deleted['success']) {
                return $this->sendError('Error occurred', ['error' => $deleted['data']]);
            }

            return $this->sendResponse(true, 'Records deleted successfully.');

        } catch (\Exception $e) {
            return $this->sendError('Error occurred', ['error' => $e->getMessage()]);
        }
    }
}
