<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSegmentMasterAPIRequest;
use App\Http\Requests\API\UpdateSegmentMasterAPIRequest;
use App\Models\SegmentMaster;
use App\Models\Company;
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

        $messages = array(
            'ServiceLineCode.unique'   => 'Segment code already exists'
        );

        $validator = \Validator::make($input, [
            'ServiceLineCode' => 'unique:serviceline'
        ],$messages);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422 );
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
        $segmentMaster = $this->segmentMasterRepository->findWithoutFail($id);

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
        $segmentMaster = $this->segmentMasterRepository->findWithoutFail($id);

        if (empty($segmentMaster)) {
            return $this->sendError('Segment Master not found');
        }

        $segmentMaster->delete();

        return $this->sendResponse($id, 'Segment Master deleted successfully');
    }

    /**
     * Loading data table using below query
     */

    public function getAllSegmentMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

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
            $segmentMasters =   $segmentMasters->where('ServiceLineCode','LIKE',"%{$search}%")
                                             ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
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
            $masterCompany = Company::where("companySystemID",$selectedCompanyId)->first();
            /**  Companies by group  Drop Down */
            $allCompanies = Company::where("masterComapanyID",$masterCompany->CompanyID)
                ->where("isGroup",0)
                ->select('companySystemID', 'CompanyID', 'CompanyName')
                ->get();
        }else{
            $allCompanies = Company::where("companySystemID",$selectedCompanyId)
                ->select('companySystemID', 'CompanyID', 'CompanyName')
                ->get();
        }

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

        $segmentMaster = $this->segmentMasterRepository->findWithoutFail($input['serviceLineSystemID']);

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

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $empId = $user->employee['empID'];
        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $empId;

        $data =array_except($input, ['serviceLineSystemID', 'timestamp', 'createdUserGroup', 'createdPcID', 'createdUserID']);



        $segmentMaster = $this->segmentMasterRepository->update($data, $input['serviceLineSystemID']);

        return $this->sendResponse($segmentMaster->toArray(), 'Segment master updated successfully');
    }
}
