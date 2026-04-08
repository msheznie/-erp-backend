<?php

namespace App\Http\Controllers\API;
use App\Http\Requests\SRM\SRMScenarioRequest;
use App\Repositories\SRMScenarioDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
class SRMScenarioDetailsAPIController extends AppBaseController
{
    private $sRMScenarioDetailsRepository;

    public function __construct(SRMScenarioDetailsRepository $sRMScenarioDetailsRepo)
    {
        $this->sRMScenarioDetailsRepository = $sRMScenarioDetailsRepo;
    }

    public function saveEmailData(SRMScenarioRequest $request)
    {
        try {
            $document = $this->sRMScenarioDetailsRepository->saveEmailData($request);
            if(!$document['success']){
                return $this->sendError($document['message']);
            } else {
                return $this->sendResponse([], $document['message']);
            }
        } catch (\Exception $e) {
            return $this->sendError('Error creating document', $e->getMessage());
        }
    }

    public function getEmailDetailsData(Request $request)
    {
        try {
            $data = $this->sRMScenarioDetailsRepository->getEmailDetailsData($request);
            return $this->sendResponse($data, 'Email master data fetched successfully');
        } catch (\Throwable $e) {
            return $this->sendError('Something went wrong', $e->getMessage());
        }
    }

}
