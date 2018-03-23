<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalLevelAPIRequest;
use App\Http\Requests\API\UpdateApprovalLevelAPIRequest;
use App\Models\ApprovalLevel;
use App\Models\Company;
use App\Repositories\ApprovalLevelRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ApprovalLevelController
 * @package App\Http\Controllers\API
 */

class ApprovalLevelAPIController extends AppBaseController
{
    /** @var  ApprovalLevelRepository */
    private $approvalLevelRepository;

    public function __construct(ApprovalLevelRepository $approvalLevelRepo)
    {
        $this->approvalLevelRepository = $approvalLevelRepo;
    }

    /**
     * Display a listing of the ApprovalLevel.
     * GET|HEAD /approvalLevels
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->approvalLevelRepository->pushCriteria(new RequestCriteria($request));
        $this->approvalLevelRepository->pushCriteria(new LimitOffsetCriteria($request));
        $approvalLevels = $this->approvalLevelRepository->all();

        return $this->sendResponse($approvalLevels->toArray(), 'Approval Levels retrieved successfully');
    }

    /**
     * Store a newly created ApprovalLevel in storage.
     * POST /approvalLevels
     *
     * @param CreateApprovalLevelAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateApprovalLevelAPIRequest $request)
    {
        $input = $request->all();

        $approvalLevels = $this->approvalLevelRepository->create($input);

        return $this->sendResponse($approvalLevels->toArray(), 'Approval Level saved successfully');
    }

    /**
     * Display the specified ApprovalLevel.
     * GET|HEAD /approvalLevels/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError('Approval Level not found');
        }

        return $this->sendResponse($approvalLevel->toArray(), 'Approval Level retrieved successfully');
    }

    /**
     * Update the specified ApprovalLevel in storage.
     * PUT/PATCH /approvalLevels/{id}
     *
     * @param  int $id
     * @param UpdateApprovalLevelAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateApprovalLevelAPIRequest $request)
    {
        $input = $request->all();

        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError('Approval Level not found');
        }

        $approvalLevel = $this->approvalLevelRepository->update($input, $id);

        return $this->sendResponse($approvalLevel->toArray(), 'ApprovalLevel updated successfully');
    }

    /**
     * Remove the specified ApprovalLevel from storage.
     * DELETE /approvalLevels/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ApprovalLevel $approvalLevel */
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

        if (empty($approvalLevel)) {
            return $this->sendError('Approval Level not found');
        }

        $approvalLevel->delete();

        return $this->sendResponse($id, 'Approval Level deleted successfully');
    }


    public function getGroupApprovalLevelDatatable(Request $request){
        $input = $request->all();
        $approvalLevel = $this->approvalLevelRepository->getGroupApprovalLevelDatatable($input);
        return $approvalLevel;
    }

    public function getGroupFilterData(Request $request){
        /** all Company  Drop Down */
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = Company::with('child')->where("masterCompanySystemIDReorting", $selectedCompanyId)->get();
        $groupCompany = [];
        if($companiesByGroup){
            foreach ($companiesByGroup as $val){
                if($val['child']){
                    foreach ($val['child'] as $val1){
                        $groupCompany[] = array('companySystemID' => $val1["companySystemID"],'CompanyID' => $val1["CompanyID"],'CompanyName' => $val1["CompanyName"]);
                    }
                }else{
                    $groupCompany[] = array('companySystemID' => $val["companySystemID"],'CompanyID' => $val["CompanyID"],'CompanyName' => $val["CompanyName"]);
                }

            }
        }

        /** all document Drop Down */
        $document = \Helper::getAllDocument();

        $output = array('company' => $groupCompany,
            'document' => $document,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
