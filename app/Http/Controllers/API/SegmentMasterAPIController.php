<?php
/**
 * =============================================
 * -- File Name : SegmentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Segment Master
 * -- Author : Mohamed Nazir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Segment Master
 * -- REVISION HISTORY
 * -- Date: 15-March 2018 By: Nazir Description: Added new functions named as getAllSegmentMaster()
 * -- Date: 16-March 2018 By: Nazir Description: Added new functions named as getSegmentMasterFormData()
 * -- Date: 05-June 2018 By: Mubashir Description: Modified getAllSegmentMaster() to handle filters from local storage
 **/
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSegmentMasterAPIRequest;
use App\Http\Requests\API\UpdateSegmentMasterAPIRequest;
use App\Models\SegmentMaster;
use App\Models\ProcumentOrder;
use App\Models\GeneralLedger;
use App\Models\PurchaseRequest;
use App\Models\Company;
use App\Models\SrpEmployeeDetails;
use App\Models\YesNoSelection;
use App\Repositories\SegmentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class SegmentMasterController
 * @package App\Http\Controllers\API
 */

class SegmentMasterAPIController extends AppBaseController
{
    /** @var  SegmentMasterRepository */
    private $segmentMasterRepository;
    private $userRepository;
    public function __construct(SegmentMasterRepository $segmentMasterRepo, UserRepository $userRepo)
    {
        $this->segmentMasterRepository = $segmentMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the SegmentMaster.
     * GET|HEAD /segmentMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->segmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->segmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $segmentMasters = $this->segmentMasterRepository->all();

        return $this->sendResponse($segmentMasters->toArray(), 'Segment Masters retrieved successfully');
    }

    /**
     * Store a newly created SegmentMaster in storage.
     * POST /segmentMasters
     *
     * @param CreateSegmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSegmentMasterAPIRequest $request)
    {
        $input = $request->all();

        if(isset($input['companySystemID']))
        {
            $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        }

        $segmentCodeCheck = SegmentMaster::withoutGlobalScope('final_level')
                                         ->where('ServiceLineCode', $input['ServiceLineCode'])
                                         ->where('isDeleted',0)
                                         ->first();

        if ($segmentCodeCheck) {
           return $this->sendError(['ServiceLineCode' => ["Segment code already exists"]], 422);
        }

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $empId;

        $input['serviceLineMasterCode'] =  $input['ServiceLineCode'];

        $segmentMasters = $this->segmentMasterRepository->create($input);

        return $this->sendResponse($segmentMasters->toArray(), 'Segment Master saved successfully');
    }

    /**
     * Display the specified SegmentMaster.
     * GET|HEAD /segmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->withcount(['sub_levels'])->find($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        return $this->sendResponse($segmentMaster->toArray(), 'Segment Master retrieved successfully');
    }

    /**
     * Update the specified SegmentMaster in storage.
     * PUT/PATCH /segmentMasters/{id}
     *
     * @param  int $id
     * @param UpdateSegmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSegmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->findWithoutFail($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;

        $segmentMaster = $this->segmentMasterRepository->update($input, $id);

        return $this->sendResponse($segmentMaster->toArray(), 'SegmentMaster updated successfully');
    }

    /**
     * Remove the specified SegmentMaster from storage.
     * DELETE /segmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SegmentMaster $segmentMaster */
        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->with(['sub_levels'])->find($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        //delete validation 
        $segmentUsed = false;
        $procumentOrderCheck = ProcumentOrder::where('serviceLineSystemID', $id)
                                             ->first();

        if ($procumentOrderCheck) {
            $segmentUsed = true;
        }

        $checkPR = PurchaseRequest::where('serviceLineSystemID', $id)
                                             ->first();

        if ($checkPR) {
            $segmentUsed = true;
        }

        $checkGeneralLedger = GeneralLedger::where('serviceLineSystemID', $id)
                                 ->first();

        if ($checkGeneralLedger) {
            $segmentUsed = true;
        }

        $company = Company::find($segmentMaster->companySystemID);
        if ($company && $company->isHrmsIntergrated) {
            $checkEmployeeDetails = SrpEmployeeDetails::where('segmentID', $id)
                                 ->first();

            if ($checkEmployeeDetails) {
                $segmentUsed = true;
            }
        }

        if ($segmentUsed) {
            return $this->sendError("This segment is used in some documents. Therefore, cannot delete", 500);
        }

        DB::beginTransaction();
        try {
            if (sizeof($segmentMaster->sub_levels) > 0) {
                $this->deleteSubLevels($segmentMaster->sub_levels);
            }

            $segmentMaster->isDeleted = 1;
            $segmentMaster->save();
            DB::commit();
            return $this->sendResponse($id, 'Segment Master deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error occred while deleting segments'.$e->getMessage());
        }
    }

    public function deleteSubLevels($sub_levels)
    {
        foreach ($sub_levels as $key => $value) {
            if (sizeof($value->sub_levels) > 0) {
                $this->deleteSubLevels($value->sub_levels);
            }

            $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->with(['sub_levels'])->find($value->serviceLineSystemID);

            if (empty($segmentMaster)) {
                return $this->sendError('Segment Master not found');
            }

            $segmentMaster->isDeleted = 1;
            $segmentMaster->save();
        }
    }

    /**
     * Loading data table using below query
     */

    public function getAllSegmentMaster(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,array('companyId'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $segmentMasters = SegmentMaster::whereIn('companySystemID',$childCompanies)
                                ->with(['company']);

        $search = $request->input('search.value');
        if($search){
            $search = str_replace("\\", "\\\\", $search);
            $segmentMasters =   $segmentMasters->where(function ($query) use($search) {
                $query->where('ServiceLineCode','LIKE',"%{$search}%")
                    ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($segmentMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('serviceLineSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getSegmentMasterFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $allCompanies = Company::whereIn("companySystemID",$subCompanies)
            ->select('companySystemID', 'CompanyID', 'CompanyName')
            ->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $yesNoSelectionMaster = YesNoSelection::all();

        $output = array(
            'allCompanies' => $allCompanies,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionMaster' => $yesNoSelectionMaster
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Get company by id
     * @param $companySystemID
     * @return mixed
     */
    private function getCompanyById($companySystemID)
    {
        $company = Company::select('CompanyID')->where("companySystemID",$companySystemID)->first();

        return $company->CompanyID;
    }

    /**
     * Update segment master
     */

    public function updateSegmentMaster(Request $request)
    {
        $input = $request->all();

        $segmentMaster = $this->segmentMasterRepository->withoutGlobalScope('final_level')->find($input['serviceLineSystemID']);

        if (!$segmentMaster) {
            return $this->sendError("Segment not found", 500);
        }

        if(isset($input['companySystemID']))
        {
            $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        }

        if (is_array($input['companySystemID']))
            $input['companySystemID'] = $input['companySystemID'][0];

        if (is_array($input['isActive']))
            $input['isActive'] = $input['isActive'][0];

        if (is_array($input['isMaster']))
            $input['isMaster'] = $input['isMaster'][0];


        $checkForDuplicateCode = $this->segmentMasterRepository->withoutGlobalScope('final_level')
                                                               ->where('serviceLineSystemID', '!=', $input['serviceLineSystemID'])
                                                               ->where('ServiceLineCode', $input['ServiceLineCode'])
                                                               ->where('isDeleted', 0)
                                                               ->first();
        if ($checkForDuplicateCode) {
            return $this->sendError("Segment code already exists", 500);
        }


        $segmentUsed = false;
        if ($segmentMaster->isFinalLevel != $input['isFinalLevel']) {
            //validate
            $procumentOrderCheck = ProcumentOrder::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                 ->first();

            if ($procumentOrderCheck) {
                $segmentUsed = true;
            }

            $checkPR = PurchaseRequest::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                                 ->first();

            if ($checkPR) {
                $segmentUsed = true;
            }

            $checkGeneralLedger = GeneralLedger::where('serviceLineSystemID', $input['serviceLineSystemID'])
                                     ->first();

            if ($checkGeneralLedger) {
                $segmentUsed = true;
            }

            $company = Company::find($segmentMaster->companySystemID);
            if ($company && $company->isHrmsIntergrated) {
                $checkEmployeeDetails = SrpEmployeeDetails::where('segmentID', $input['serviceLineSystemID'])
                                     ->first();

                if ($checkEmployeeDetails) {
                    $segmentUsed = true;
                }
            }


            if ($segmentUsed) {
                return $this->sendError("This segment is used in some documents. Therefore, Final level status cannot be changed", 500);
            }
        }

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;

        $data = array_except($input, ['serviceLineSystemID', 'timestamp', 'createdUserGroup', 'createdPcID', 'createdUserID', 'sub_levels_count']);

        $segmentMaster = SegmentMaster::withoutGlobalScope('final_level')
                                      ->where('serviceLineSystemID', $input['serviceLineSystemID'])
                                      ->update($data);

        return $this->sendResponse($segmentMaster, 'Segment master updated successfully');
    }

    public function getOrganizationStructure(Request $request)
    {
        $input = $request->all();

        $selectedCompanyId = $input['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);

            $companyData = Company::find($selectedCompanyId);
            $segmenntData = [];
            foreach ($subCompanies as $key => $value) {
                $segmenntData[] = $this->getNonGroupCompanyOrganizationStructure($value, true);
            }

            $companyData->subCompanies = $segmenntData;

            return $this->sendResponse(['orgData' => $companyData, 'isGroup' => true], 'Organization Levels retrieved successfully');
        }else{
            return $this->getNonGroupCompanyOrganizationStructure($selectedCompanyId);
        }
    }

    public function getNonGroupCompanyOrganizationStructure($companySystemID, $dataReturn = false)
    {
         $orgStructure = Company::withcount(['segments'])
                             ->with(['segments' => function ($q) {
                                $q->withoutGlobalScope('final_level')
                                  ->where(function($query) {
                                        $query->whereNull('masterID')
                                              ->orWhere('masterID', 0);
                                    })
                                  ->where('isDeleted', 0)
                                  ->withcount(['sub_levels' => function($query) {
                                        $query->where('isDeleted', 0);
                                  }])
                                  ->with(['sub_levels' => function($query) {
                                        $query->where('isDeleted', 0);
                                  }]);
                             }])
                             ->find($companySystemID);

        if ($dataReturn) {
            return $orgStructure;
        }

        if (empty($orgStructure)) {
            return $this->sendError('Warehouse not found');
        }

        return $this->sendResponse(['orgData' => $orgStructure, 'isGroup' => false], 'Organization Levels retrieved successfully');
    }
}
