<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateApprovalLevelAPIRequest;
use App\Http\Requests\API\UpdateApprovalLevelAPIRequest;
use App\Models\ApprovalLevel;
use App\Models\ApprovalRole;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\SegmentMaster;
use App\Repositories\ApprovalLevelRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
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
        $approvalLevel = "";
        $input = $this->convertArrayToValue($input);
        $companyID = Company::where('companySystemID',$input["companySystemID"])->first();
        $input["companyID"] = $companyID->CompanyID;

        $documentID = DocumentMaster::where('documentSystemID',$input["documentSystemID"])->first();
        $input["documentID"] = $documentID->documentID;
        $input["departmentID"] = $documentID->departmentID;
        $input["departmentSystemID"] = $documentID->departmentSystemID;

        if (isset($request->serviceLineSystemID))
        {
            $ServiceLineCode = SegmentMaster::where('serviceLineSystemID',$input["serviceLineSystemID"])->first();
            $input["serviceLineCode"] = $ServiceLineCode->ServiceLineCode;
        }

        if (isset($request->approvalLevelID))
        {
            $id = $request->approvalLevelID;
            $approvalLevel = $this->approvalLevelRepository->findWithoutFail($id);

            if (empty($approvalLevel)) {
                return $this->sendError('Approval Level not found');
            }
            $approvalLevel = $this->approvalLevelRepository->update($input, $id);

        }else{
            $approvalLevel = $this->approvalLevelRepository->create($input);
        }

        return $this->sendResponse($approvalLevel->toArray(), 'Approval Level saved successfully');
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
        $input = $this->convertArrayToValue($input);
        $approvalLevel = "";
        $input = $this->convertArrayToValue($input);
        $companyID = Company::where('companySystemID',$input["companySystemID"])->first();
        $input["companyID"] = $companyID->CompanyID;

        $documentID = DocumentMaster::where('documentSystemID',$input["documentSystemID"])->first();
        $input["documentID"] = $documentID->documentID;
        $input["departmentID"] = $documentID->departmentID;
        $input["departmentSystemID"] = $documentID->departmentSystemID;

        if (isset($request->serviceLineSystemID))
        {
            $ServiceLineCode = SegmentMaster::where('serviceLineSystemID',$input["serviceLineSystemID"])->first();
            $input["serviceLineCode"] = $ServiceLineCode->ServiceLineCode;
        }

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

        $approvalLevel->approvalRole()->delete();

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
        $document = \Helper::getAllDocuments();

        /** all finance category Drop Down */
        $financeCategory = FinanceItemCategoryMaster::all();

        $output = array('company' => $groupCompany,
            'document' => $document,
            'financeCategory' => $financeCategory,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getCompanyServiceLine(Request $request){
        /** all Service line  Drop Down */
        $selectedCompanyId = $request['companySystemID'];
        $serviceline = \Helper::getCompanyServiceline($selectedCompanyId);
        $output = array('serviceline' => $serviceline
        );
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function activateApprovalLevel(Request $request){
        $approvalLevel = $this->approvalLevelRepository->findWithoutFail($request->approvalLevelID);

        if (empty($approvalLevel)) {
            return $this->sendError('Approval Level not found');
        }
        if($request->isActive){
            $approvalLevel->isActive = -1;
        }else{
            $approvalLevel->isActive = 0;
        }
        $approvalLevel->save();

        $approvalRole = "";
        if($approvalLevel->isActive) {
            $isExist = ApprovalRole::where('approvalLevelID', $request->approvalLevelID)->exists();
            if (!$isExist) {
                $approvalRollMaster = [];
                if ($approvalLevel) {
                    for ($i = 1; $i <= $approvalLevel->noOfLevels; $i++) {
                        $approvalRollMaster[] = array('rollDescription' => $approvalLevel->levelDescription . ' ' . $i, 'documentSystemID' => $approvalLevel->documentSystemID, 'documentID' => $approvalLevel->documentID, 'companySystemID' => $approvalLevel->companySystemID, 'companyID' => $approvalLevel->companyID, 'departmentSystemID' => $approvalLevel->departmentSystemID, 'departmentID' => $approvalLevel->departmentID, 'serviceLineSystemID' => $approvalLevel->serviceLineSystemID, 'serviceLineID' => $approvalLevel->serviceLineCode, 'rollLevel' => $i, 'approvalLevelID' => $approvalLevel->approvalLevelID);
                    }
                    ApprovalRole::insert($approvalRollMaster);
                }
            }
        }
        $approvalRole = ApprovalRole::with(['company' => function($query) {
           // $query->select('CompanyName');
        },'department' => function($query) {
            //$query->select('DepartmentDescription');
        },'document' => function($query) {
            //$query->select('documentDescription');
        },'serviceline' => function($query) {
            //$query->select('ServiceLineDes');
        }])->where('approvalLevelID', $request->approvalLevelID)->orderBy('rollLevel', 'asc')->get();

        return $this->sendResponse($approvalRole->toArray(), 'Record updated successfully');

    }

    public function confirmDocTest(){
        $param = array('autoID' => 1008,'company' => 31,'document' => 2,'segment' => 6,'category' => null,'amount' => 997.99992);
        return $test = \Helper::confirmDocument($param);
    }
}
