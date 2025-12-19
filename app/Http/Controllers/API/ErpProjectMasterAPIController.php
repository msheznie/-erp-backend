<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpProjectMasterAPIRequest;
use App\Http\Requests\API\UpdateErpProjectMasterAPIRequest;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\ErpProjectMaster;
use App\Models\ServiceLine;
use App\Models\ProjectGlDetail;
use App\Models\SegmentMaster;
use App\Repositories\ErpProjectMasterRepository;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Response;

/**
 * Class ErpProjectMasterController
 *
 * @package App\Http\Controllers\API
 */
class ErpProjectMasterAPIController extends AppBaseController
{
    /** @var  ErpProjectMasterRepository */
    private $erpProjectMasterRepository;

    public function __construct(ErpProjectMasterRepository $erpProjectMasterRepo)
    {
        $this->erpProjectMasterRepository = $erpProjectMasterRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpProjectMasters",
     *      summary="Get a listing of the ErpProjectMasters.",
     *      tags={"ErpProjectMaster"},
     *      description="Get all ErpProjectMasters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ErpProjectMaster")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    
     public function get_projects(Request $request){

        $input = $request->all();
        $companySystemID = $input['company_id'];

        $companyID = "";
        $checkIsGroup = Company::find($companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($companySystemID);
        } else {
            $companyID = [$companySystemID];
        }

        $projectMaster = ErpProjectMaster::with('company:CompanyID,companySystemID,CompanyName','currency', 'service_line')
                                            ->whereIN('companySystemID', $companyID)
                                            ->get();
        return $this->sendResponse($projectMaster, trans('custom.projects_retrieved_successfully'));
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $projectMaster = ErpProjectMaster::where('companySystemID',$companyId)
                                        ->with(['company:CompanyID,companySystemID,CompanyName','currency', 'service_line',
                                        'gl_details' => function($query){
                                                $query->selectRaw('SUM(amount) as total, projectID')
                                                      ->groupBy('projectID');
                                        }])
                                        ->select(['id', 'projectCode', 'description', 'start_date', 'end_date','companySystemID','projectCurrencyID', 'serviceLineSystemID', 'estimatedAmount']);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $projectMaster->where('serviceLineSystemID', '=', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('companySystemID', $input)) {
            if ($input['companySystemID'] && !is_null($input['companySystemID'])) {
                $projectMaster->where('companySystemID', '=', $input['companySystemID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $projectMaster = $projectMaster->where(function ($query) use ($search) {
                $query->where('projectCode', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($projectMaster)
                        ->addIndexColumn()->order(function ($query) use ($input) {
                            if (request()->has('order') ) {
                                if($input['order'][0]['column'] == 0)
                                {
                                    $query->orderBy('id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addColumn('totalConsumedAmount', function ($project) {
                            $projectDetails = ProjectGlDetail::where('projectID', $project->id)->first();
                            if($projectDetails) {
                                return $projectDetails->consumed_amount_project;
                            }
                            else{
                                return 0;
                            }
                        })
                        ->with('orderCondition', $sort)
                        ->make(true);
    }

    /**
     * @param CreateErpProjectMasterAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpProjectMasters",
     *      summary="Store a newly created ErpProjectMaster in storage",
     *      tags={"ErpProjectMaster"},
     *      description="Store ErpProjectMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpProjectMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpProjectMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ErpProjectMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpProjectMasterAPIRequest $request)
    {
        $input = $request->all();

        $projectCode = $input['projectCode'];
        $checkProjectCode = ErpProjectMaster::where('projectCode',$projectCode)->first();

        if($checkProjectCode){
            return $this->sendError(trans('custom.project_code_already_exist'), 500);
        }

        if (isset($input['companySystemID'])) {

            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();

            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        if (isset($input['serviceLineSystemID'])) {

            $serviceLine = ServiceLine::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();

            if ($serviceLine) {
                $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            }
        }

        $employee = \Helper::getEmployeeInfo();
        $input['start_date'] = Carbon::parse($input['startDate']);
        $input['end_date'] = Carbon::parse($input['endDate']);
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;

        $erpProjectMaster = $this->erpProjectMasterRepository->create($input);

        return $this->sendResponse($erpProjectMaster->toArray(), trans('custom.project_master_saved_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpProjectMasters/{id}",
     *      summary="Display the specified ErpProjectMaster",
     *      tags={"ErpProjectMaster"},
     *      description="Get ErpProjectMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpProjectMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ErpProjectMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var ErpProjectMaster $erpProjectMaster */
       $erpProjectMaster = $this->erpProjectMasterRepository->with('gl_details')->findWithoutFail($id);

        if (empty($erpProjectMaster)) {
            return $this->sendError(trans('custom.erp_project_master_not_found'));
        }

        return $this->sendResponse($erpProjectMaster->toArray(), trans('custom.project_master_retrieved_successfully'));
    }

    /**
     * @param int                              $id
     * @param UpdateErpProjectMasterAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpProjectMasters/{id}",
     *      summary="Update the specified ErpProjectMaster in storage",
     *      tags={"ErpProjectMaster"},
     *      description="Update ErpProjectMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpProjectMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpProjectMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpProjectMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/ErpProjectMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpProjectMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        /** @var ErpProjectMaster $erpProjectMaster */
        $erpProjectMaster = $this->erpProjectMasterRepository->findWithoutFail($id);

        if (empty($erpProjectMaster)) {
            return $this->sendError(trans('custom.erp_project_master_not_found'));
        }

        if (isset($input['companySystemID'])) {
            $companyMaster = Company::where('companySystemID', $input['companySystemID'])->first();
            if ($companyMaster) {
                $input['companyID'] = $companyMaster->CompanyID;
            }
        }

        if (isset($input['serviceLineSystemID'])) {

            $serviceLine = ServiceLine::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();

            if ($serviceLine) {
                $input['serviceLineCode'] = $serviceLine->ServiceLineCode;
            }
        }

        $employee = \Helper::getEmployeeInfo();
        $input['start_date'] = Carbon::parse($input['startDate']);
        $input['end_date'] = Carbon::parse($input['endDate']);
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedDateTime'] = now();
        $input['modifiedUserName'] = $employee->empName;

        $erpProjectMaster = $this->erpProjectMasterRepository->update($input, $id);

        return $this->sendResponse($erpProjectMaster->toArray(), trans('custom.project_master_updated_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpProjectMasters/{id}",
     *      summary="Remove the specified ErpProjectMaster from storage",
     *      tags={"ErpProjectMaster"},
     *      description="Delete ErpProjectMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpProjectMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var ErpProjectMaster $erpProjectMaster */
        $erpProjectMaster = $this->erpProjectMasterRepository->findWithoutFail($id);

        if (empty($erpProjectMaster)) {
            return $this->sendError(trans('custom.project_master_not_found'));
        }

        $erpProjectMaster->delete();

        return $this->sendSuccess('Project Master deleted successfully');
    }

    public function formData(Request $request)
    {
        $allCompanies = Company::where("isGroup", 0)->get(['companySystemID', 'CompanyName', 'CompanyID']);
        $serviceLines = ServiceLine::all(['serviceLineSystemID', 'ServiceLineCode', 'serviceLineMasterCode', 'ServiceLineDes']);
        $currencyMaster = CurrencyMaster::orderBy('CurrencyName', 'asc')
            ->get(['currencyID', 'CurrencyName', 'CurrencyCode']);
        $data = [
            'allCompanies' => $allCompanies,
            'currencyMaster' => $currencyMaster,
            'serviceLines' => $serviceLines,
        ];
        return $this->sendResponse($data, '');
    }

    public function segmentsByCompany(Request $request)
    {

        $companySystemID = $request['companySystemID'];
        $serviceLines = SegmentMaster::where('companySystemID', $companySystemID)->approved()->withAssigned($companySystemID)->get();
        return $this->sendResponse($serviceLines, trans('custom.segments_projects_retrieved_successfully'));
    }
}
