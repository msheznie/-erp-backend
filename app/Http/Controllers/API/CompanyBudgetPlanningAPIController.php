<?php

namespace App\Http\Controllers\API;

use App\helper\CreateExcel;
use App\Http\Requests\API\CreateCompanyBudgetPlanningAPIRequest;
use App\Http\Requests\API\UpdateCompanyBudgetPlanningAPIRequest;
use App\Jobs\ProcessDepartmentBudgetPlanning;
use App\Models\BudgetControl;
use App\Models\BudgetDelegateAccess;
use App\Models\BudgetDelegateAccessRecord;
use App\Models\Company;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDepartment;
use App\Models\CompanyDepartmentEmployee;
use App\Models\CompanyDepartmentSegment;
use App\Models\CompanyFinanceYear;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DocumentApproved;
use App\Models\DepartmentBudgetPlanningsDelegateAccess;
use App\Models\DepartmentBudgetTemplate;
use App\Models\DepartmentUserBudgetControl;
use App\Models\DeptBudgetPlanningTimeRequest;
use App\Models\Revision;
use App\Models\EmployeesDepartment;
use App\Models\WorkflowConfiguration;
use App\Repositories\CompanyBudgetPlanningRepository;
use App\Services\BudgetPermissionService;
use App\Services\BudgetNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyBudgetPlanningController
 * @package App\Http\Controllers\API
 */

class CompanyBudgetPlanningAPIController extends AppBaseController
{
    /** @var  CompanyBudgetPlanningRepository */
    private $companyBudgetPlanningRepository;
    
    /** @var  BudgetPermissionService */
    private $budgetPermissionService;

    /** @var  BudgetNotificationService */
    private $budgetNotificationService;

    public function __construct(CompanyBudgetPlanningRepository $companyBudgetPlanningRepo, BudgetPermissionService $budgetPermissionService, BudgetNotificationService $budgetNotificationService)
    {
        $this->companyBudgetPlanningRepository = $companyBudgetPlanningRepo;
        $this->budgetPermissionService = $budgetPermissionService;
        $this->budgetNotificationService = $budgetNotificationService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/companyBudgetPlannings",
     *      summary="getCompanyBudgetPlanningList",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Get all CompanyBudgetPlannings",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/CompanyBudgetPlanning")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->companyBudgetPlanningRepository->pushCriteria(new RequestCriteria($request));
        $this->companyBudgetPlanningRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyBudgetPlannings = $this->companyBudgetPlanningRepository->with('departmentBudgetPlannings')->all();

        return $this->sendResponse($companyBudgetPlannings->toArray(), trans('custom.company_budget_plannings_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/companyBudgetPlannings",
     *      summary="createCompanyBudgetPlanning",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Create CompanyBudgetPlanning",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'primaryCompany' => 'required',
            'budgetInitiateDate' => 'required',
            'budgetPeriod' => 'required',
            'workflow' => 'required',
            'budgetYear' => 'required',
            'budgetType' => 'required',
            'dateOfSubmission' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $data = [
            'companySystemID' => $input['primaryCompany'],
            'initiatedDate' => Carbon::parse($input['budgetInitiateDate']),
            'periodID' => $input['budgetPeriod'],
            'workflowID' => $input['workflow'],
            'yearID' => $input['budgetYear'],
            'typeID' => $input['budgetType'],
            'submissionDate' => Carbon::parse($input['dateOfSubmission']),
            'status' => 1
        ];

        $company = Company::where('companySystemID', $input['primaryCompany'])->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company')]), 500);
        }

        $data['companyID'] = $company->CompanyID;

        $lastSerial = CompanyBudgetPlanning::where('companySystemID', $input['primaryCompany'])
            ->where('yearID', $input['budgetYear'])
            ->orderBy('serialNo', 'desc')
            ->first();
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        $financeYear = CompanyFinanceYear::where('companySystemID', $input['primaryCompany'])->where('companyFinanceYearID', $input['budgetYear'])->first();
        $financeYear = explode('-', $financeYear->bigginingDate)[0];

        $planingCode = ($company->CompanyID . '\\' . $financeYear . '\\' . 'BDP' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $data['planningCode'] = $planingCode;

        $data['serialNo'] = $lastSerialNumber;
        $data['documentSystemID'] = '133';
        $data['documentID'] = 'BDP';

        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->create($data);



        $uuid = $request->get('tenant_uuid', 'local');

        ProcessDepartmentBudgetPlanning::dispatch($request->db ?? '', $companyBudgetPlanning->id, $uuid,Auth::user()->employee_id);

        return $this->sendResponse($companyBudgetPlanning->toArray(), trans('custom.budget_planning_initiated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/companyBudgetPlannings/{id}",
     *      summary="getCompanyBudgetPlanningItem",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Get CompanyBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CompanyBudgetPlanning $companyBudgetPlanning */
        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->with('departmentBudgetPlannings')->findWithoutFail($id);

        $companyBudgetPlanning['primaryCompany'] = [$companyBudgetPlanning->companySystemID];
        $companyBudgetPlanning['budgetYear'] = [$companyBudgetPlanning->yearID];
        if (empty($companyBudgetPlanning)) {
            return $this->sendError(trans('custom.company_budget_planning_not_found'));
        }

        // Ensure rejected_yn is returned as 0 or 1 instead of boolean
        $data = $companyBudgetPlanning->toArray();
        if (isset($data['rejected_yn'])) {
            $data['rejected_yn'] = $data['rejected_yn'] ? 1 : 0;
        }

        return $this->sendResponse($data, trans('custom.company_budget_planning_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/companyBudgetPlannings/{id}",
     *      summary="updateCompanyBudgetPlanning",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Update CompanyBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyBudgetPlanning $companyBudgetPlanning */
        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->with('departmentBudgetPlannings')->findWithoutFail($id);

        if (empty($companyBudgetPlanning)) {
            return $this->sendError(trans('custom.company_budget_planning_not_found'));
        }


        if($input['confirmed_yn'] == 1) {
            // Validate department budget planning statuses before allowing confirmation
            $validationResult = $this->validateDepartmentBudgetPlanningStatuses($companyBudgetPlanning);
            
            if (!$validationResult['valid']) {
                return $this->sendError($validationResult['message']);
            }

            
            $params = array('autoID' => $companyBudgetPlanning->id,
                'company' => $companyBudgetPlanning->companySystemID,
                'document' => 133,
                'segment' => null,
                'category' => null,
                'amount' => null
            );

            $confirm = \Helper::confirmDocument($params);

            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            } 

            $companyBudgetPlanning->departmentBudgetPlannings;
            $input['confirmed_yn'] = 1;
            $input['confirmed_by_name'] = Auth::user()->name;
            $input['confirmed_by_emp_id'] = Auth::user()->id;
            $input['confirmed_by_emp_system_id'] =  Auth::user()->employee_id;
            $input['confirmed_at'] = Carbon::now();
        }

        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->update($input, $id);

        return $this->sendResponse($companyBudgetPlanning->toArray(), trans('custom.companybudgetplanning_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/companyBudgetPlannings/{id}",
     *      summary="deleteCompanyBudgetPlanning",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Delete CompanyBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CompanyBudgetPlanning $companyBudgetPlanning */
        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->findWithoutFail($id);

        if (empty($companyBudgetPlanning)) {
            return $this->sendError(trans('custom.company_budget_planning_not_found'));
        }

        $companyBudgetPlanning->delete();

        return $this->sendSuccess('Company Budget Planning deleted successfully');
    }

    public function getBudgetPlanningUserPermissions(Request $request) {
        $input = $request->all();
        $result = $this->budgetPermissionService->getBudgetPlanningUserPermissions($input);
        
        if (!$result['success']) {
            return $this->sendError($result['message']);
        }
        
        return $this->sendResponse($result['data'], $result['message']);
    }

    public function getBudgetPlanningFormData(Request $request) {
        $companyId = $request['companyId'];

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
            ->where('companySystemID', $companyId)
            ->where('isClosed', 0)
            ->get();

        if (isset($request['type']) && $request['type'] == 'create') {
            $companyData = Company::where('companySystemID',$companyId)->get();

            $workflows = WorkflowConfiguration::where('isActive', 1)->where('companySystemID', $companyId)->get();

            $output = array(
                'companyFinanceYear' => $companyFinanceYear,
                'primaryCompany' => $companyData,
                'workflows' => $workflows
            );
        }
        else {
            $employeeID = \Helper::getEmployeeSystemID();
            $isFinanceUser = $request['isFinanceUser'];

            $years = CompanyBudgetPlanning::select('yearID')->groupby('yearID')->get()->pluck('yearID')->toArray();
            $years = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,YEAR(bigginingDate) as year"))
                ->where('companySystemID', $companyId)
                ->whereIn('companyFinanceYearID', $years)
                ->orderby('year', 'desc')
                ->get();

            $companyPlanningCodes = CompanyBudgetPlanning::where('companySystemID', $companyId)->select('planningCode','id')->get();

            $departmentPlanningCodes = [];
            $departments = [];

            $companyBudgetPlanning = CompanyBudgetPlanning::where('companySystemID', $companyId)->get();
            if ($companyBudgetPlanning) {
                $companyBudgetPlanningID = $companyBudgetPlanning->pluck('id')->toArray();


                $userPermission = $this->budgetPermissionService->getBudgetPlanningUserPermissions([
                    'companyId' => $companyId,
                    'delegateUser' =>  $employeeID
                ]);

                if ($userPermission['success'] && $userPermission['data']['financeUser']['status']) {
                    $departmentPlanningCodes = $companyPlanningCodes;

                    $departments = DepartmentBudgetPlanning::with('department')
                        ->whereHas('department', function($query) use ($companyId) {
                            $query->where('companySystemID', $companyId);
                        })
                        ->groupBy('departmentID')
                        ->get()
                        ->map(function($item) {
                            return [
                                'departmentID' => $item->departmentID,
                                'departmentCode' => $item->department->departmentCode ?? ''
                            ];
                        });
                }

                if ($userPermission['success'] && $userPermission['data']['delegateUser']['status']) {

                    $delegateBudgetDetails = BudgetDelegateAccessRecord::with('delegatee')->whereHas('delegatee',function ($q) use ($employeeID) {
                        $q->where('employeeSystemID',$employeeID);
                    })->pluck('id')->toArray();

                    $companyPlanningCodes = CompanyBudgetPlanning::with('departmentBudgetPlannings')
                        ->whereHas('departmentBudgetPlannings.budgetPlanningDetails', function ($q) use ($delegateBudgetDetails) {
                            $q->whereIn('id', $delegateBudgetDetails);
                        })
                        ->select('planningCode','id')
                        ->get();

                    $departmentPlanningCodes = $companyPlanningCodes;

                    $departments = DepartmentBudgetPlanning::with('department')
                        ->whereHas('department', function($query) use ($companyId) {
                            $query->whereHas('employees',function ($q) {
                                $q->where('employeeSystemID',Auth::user()->employee_id);
                            })->where('companySystemID', $companyId);
                        })
                        ->groupBy('departmentID')
                        ->get()
                        ->map(function($item) {
                            return [
                                'departmentID' => $item->departmentID,
                                'departmentCode' => $item->department->departmentCode ?? ''
                            ];
                        });
                }

                if($userPermission['success'] && $userPermission['data']['hodUser']['status']) {
                    $hodDepartment = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)
                        ->where('isHOD', 1)
                        ->whereHas('department', function($query) use ($companyId) {
                            $query->where('companySystemID', $companyId);
                        })
                        ->first();

                    $childDepartmentIds = [];
                    if ($hodDepartment) {
                        $hodDeptId = $hodDepartment->departmentSystemID;
                        $childDepartmentIds[] = $hodDeptId;

                        $this->getChildDepartmentIds($hodDeptId, $companyId, $childDepartmentIds);
                    }
                    $childDepartmentIds = array_unique($childDepartmentIds);

                    $departmentPlanningCodes = CompanyBudgetPlanning::where('companySystemID', $companyId)
                        ->whereIn('id', $companyBudgetPlanningID)
                        ->whereHas('departmentBudgetPlannings', function($query) use ($childDepartmentIds) {
                            $query->whereIn('departmentID', $childDepartmentIds);
                        })
                        ->select('planningCode','id')->get();

                    $departments = DepartmentBudgetPlanning::with('department')
                        ->whereIn('departmentID', $childDepartmentIds)
                        ->whereHas('department', function($query) use ($companyId) {
                            $query->where('companySystemID', $companyId);
                        })
                        ->groupBy('departmentID')
                        ->get()
                        ->map(function($item) {
                            return [
                                'departmentID' => $item->departmentID,
                                'departmentCode' => $item->department->departmentCode ?? ''
                            ];
                        });
                }
            }


            $companies = CompanyDepartmentEmployee::with('department.company')->where('employeeSystemID',Auth::user()->employee_id)->get()->pluck('department.company.companySystemID')->toArray();
            $companyCodes = Company::whereIn('companySystemID', $companies)->select('companyID','companySystemID')->groupBy('companySystemID')->get();

            $output = array(
                'companyFinanceYear' => $companyFinanceYear,
                'years' => $years,
                'companyPlanningCodes' => $companyPlanningCodes,
                'departmentPlanningCodes' => $departmentPlanningCodes,
                'departments' => $departments,
                'companies' => $companyCodes
            );
        }

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getBudgetPlanningMasterData(Request $request) {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('company','planningCode', 'budgetYear', 'budgetType', 'status'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if ($input['type'] == 'company') {
//            $data = CompanyBudgetPlanning::with(['financeYear'])->where('companySystemID', $input['companyId'])->orderBy('id', $sort);
            $companyCodes = [$input['companyId']];

            if (array_key_exists('company', $input)) {
                if (!empty($request['company'])) {
                    $companyCodes = collect($request['company'])->pluck('id')->toArray();
                }else {
                    $companyCodes = [$input['companyId']];
                }
            }

            $data = CompanyBudgetPlanning::with(['financeYear', 'departmentBudgetPlannings'])->whereIn('companySystemID', $companyCodes)->orderBy('id', $sort);
            /*if (array_key_exists('from', $input)) {
                if (!is_null($request['from']) && ($request['from'] == 'erp')) {
                    $data->where('companySystemID', $input['companyId']);
                }
                else if (!is_null($request['from']) && ($request['from'] == 'portal')) {
                    $data->where('companySystemID', $input['company']);
                }
            }*/

            if (array_key_exists('planningCode', $input)) {
                if (!is_null($request['planningCode'])) {
                    $planningCode = (array)$request['planningCode'];
                    $planningCode = collect($planningCode)->pluck('id')->toArray();
                    if (count($planningCode) > 0) {
                        $data->whereIn('id', $planningCode);
                    }
                }
            }

            if (array_key_exists('yearID', $input)) {
                if (!is_null($input['yearID'])) {
                    $data->where('yearID', $input['yearID']);
                }
            }

            if (array_key_exists('typeID', $input)) {
                if (!is_null($input['typeID'])) {
                    $data->where('typeID', $input['typeID']);
                }
            }

            if (array_key_exists('status', $input)) {
                if (!is_null($input['status'])) {
                    $data->where('status', $input['status']);
                }
            }

            $search = $request->input('search.value');

            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $data = $data->where(function ($query) use ($search) {
                    $query->where('planningCode', 'LIKE', "%{$search}%");
                });
            }
        }
        else {

            $companyCodes = [$input['companyId']];

            if (array_key_exists('company', $input)) {
                if (!empty($request['company'])) {
                    $companyCodes = collect($request['company'])->pluck('id')->toArray();
                }else {
                    $companyCodes = [$input['companyId']];
                }
            }

            $companyBudgetPlanning = CompanyBudgetPlanning::whereIn('companySystemID', $companyCodes)->get();
            $data = collect();
            if ($companyBudgetPlanning) {
                $companyBudgetPlanningID = $companyBudgetPlanning->pluck('id')->toArray();
                $employeeID = \Helper::getEmployeeSystemID();
                $isFinanceUser = false;

                
                $checkUserHasApprovalAccess = EmployeesDepartment::whereIn('companySystemID', $companyCodes)
                ->where('employeeSystemID', $employeeID)
                ->where('documentSystemID', 133)
                ->where('departmentSystemID', 5)
                ->where('isActive', 1)
                ->where('removedYN', 0);

                if($checkUserHasApprovalAccess->exists()) {
                    $isFinanceUser = true;
                }

                $financeDepartment = CompanyDepartment::with(['employees'])
                    ->where('isFinance', 1)
                    ->where('companySystemID', $input['companyId'])
                    ->first();
                if ($financeDepartment) {
                    $departmentEmployee = $financeDepartment->employees
                        ->where('employeeSystemID', $employeeID)
                        ->first();
                    if ($departmentEmployee) {
                        $isFinanceUser = true;
                    }
                }
                
                if ($isFinanceUser) {
                    $data = DepartmentBudgetPlanning::with(['department.hod.employee','financeYear'])
                        ->whereIn('companyBudgetPlanningID', $companyBudgetPlanningID)
                        ->orderBy('id', $sort);
                      
                } else {
                    $hodDepartment = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)
                        ->where('isHOD', 1)
                        ->whereHas('department', function($query) use ($input) {
                            $query->where('companySystemID', $input['companyId']);
                        })
                        ->first();
                    
                    $childDepartmentIds = [];
                    if ($hodDepartment) {
                        $hodDeptId = $hodDepartment->departmentSystemID;
                        $childDepartmentIds[] = $hodDeptId;
                        
                        $this->getChildDepartmentIds($hodDeptId, $input['companyId'], $childDepartmentIds);

                        $childDepartmentIds = array_unique($childDepartmentIds);

                        $data = DepartmentBudgetPlanning::with(['department.hod.employee','financeYear','delegateAccess'])
                            ->whereIn('companyBudgetPlanningID', $companyBudgetPlanningID)
                            ->whereHas('department', function($query) use ($childDepartmentIds) {
                                $query->whereIn('departmentSystemID', $childDepartmentIds);
                            })
                            ->orderBy('id', $sort);
                    }else {
                        // delegate
                        
                        $uniqueIds = BudgetDelegateAccessRecord::with(['budgetPlanningDetail.departmentBudgetPlanning', 'delegatee'])
//                            ->whereDate('submission_time', '>=', Carbon::today()->format('Y-m-d'))
                            ->whereHas('delegatee', function ($query) use ($employeeID) {
                                $query->where('employeeSystemID', $employeeID)
                                    ->where('isActive', true);
                            })
//                            ->where('status',1)
                            ->get()->pluck('budgetPlanningDetail.departmentBudgetPlanning.id')->unique();

                        $data = DepartmentBudgetPlanning::with(['revisions','department.hod.employee','financeYear','delegateAccess','confirmedBy'])
                            ->whereIn('companyBudgetPlanningID', $companyBudgetPlanningID)
                            ->whereIn('id', $uniqueIds)
                            ->orderBy('id', $sort);


                    }
                    

                }

                if(isset($input['sltCompanyBudgetPlan'])) {
                    $data->where('companyBudgetPlanningID', $input['sltCompanyBudgetPlan']);
                }
                if (array_key_exists('department', $input)) {
                    if (!is_null($request['department'])) {
                        $department = (array)$request['department'];
                        $department = collect($department)->pluck('id')->toArray();
                        if (count($department) > 0) {
                            $data->whereIn('departmentID', $department);
                        }
                    }
                }

                if (array_key_exists('planningCode', $input)) {
                    if (!is_null($request['planningCode'])) {
                        $planningCode = (array)$request['planningCode'];
                        $planningCode = collect($planningCode)->pluck('id')->toArray();
                        if (count($planningCode) > 0) {
                            $data->whereIn('companyBudgetPlanningID', $planningCode);
                        }
                    }
                }

                if (array_key_exists('yearID', $input)) {
                    if (!is_null($input['yearID'])) {
                        $data->where('yearID', $input['yearID']);
                    }
                }
                

                if (array_key_exists('typeID', $input)) {
                    if (!is_null($input['typeID'])) {
                        $data->where('typeID', $input['typeID']);
                    }
                }

                if (array_key_exists('status', $input)) {
                    if (!is_null($input['status'])) {
                        $data->where('status', $input['status']);
                    }
                }

                $search = $request->input('search.value');

                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $data = $data->where(function ($query) use ($search) {
                        $query->where('planningCode', 'LIKE', "%{$search}%")
                            ->orWhereHas('department', function ($query) use ($search) {
                                $query->where('departmentCode', 'LIKE', "%{$search}%");
                            });
                    });
                }
            }
        }

        return \DataTables::of($data)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    private function getChildDepartmentIds($parentDepartmentId, $companyId, &$childDepartmentIds)
    {
        $children = CompanyDepartment::where('parentDepartmentID', $parentDepartmentId)
            ->where('companySystemID', $companyId)
            ->get();
        
        foreach ($children as $child) {
            $childDepartmentIds[] = $child->departmentSystemID;
            $this->getChildDepartmentIds($child->departmentSystemID, $companyId, $childDepartmentIds);
        }
    }

    public function getBudgetType($id) {
        switch ($id) {
            case 1: return 'OPEX';
            case 2: return 'CAPEX';
            case 3: return 'Common';
            default: return '';
        }
    }

    public function exportBudgetPlanning(Request $request) {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('company','planningCode', 'budgetYear', 'budgetType', 'status'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        if ($input['type'] == 'company') {
            $data = CompanyBudgetPlanning::with(['financeYear'])->where('companySystemID', $input['companyId']);

            /*if (array_key_exists('from', $input)) {
                if (!is_null($request['from']) && ($request['from'] == 'erp')) {
                }
                else if (!is_null($request['from']) && ($request['from'] == 'portal')) {
                    $data->where('companySystemID', $input['company']);
                }
            }*/

            if (array_key_exists('planningCode', $input)) {
                if (!is_null($request['planningCode'])) {
                    $planningCode = (array)$request['planningCode'];
                    $planningCode = collect($planningCode)->pluck('id')->toArray();
                    if (count($planningCode) > 0) {
                        $data->whereIn('id', $planningCode);
                    }
                }
            }

            if (array_key_exists('yearID', $input)) {
                if (!is_null($input['yearID'])) {
                    $data->where('yearID', $input['yearID']);
                }
            }

            if (array_key_exists('typeID', $input)) {
                if (!is_null($input['typeID'])) {
                    $data->where('typeID', $input['typeID']);
                }
            }

            if (array_key_exists('status', $input)) {
                if (!is_null($input['status'])) {
                    $data->where('status', $input['status']);
                }
            }

            $search = $request->input('search.value');

            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $data = $data->where(function ($query) use ($search) {
                    $query->where('planningCode', 'LIKE', "%{$search}%");
                });
            }
        }
        else {
            $companyBudgetPlanning = CompanyBudgetPlanning::where('companySystemID', $input['companyId'])->get();
            $data = collect();
            if ($companyBudgetPlanning) {
                $companyBudgetPlanningID = $companyBudgetPlanning->pluck('id')->toArray();
                $employeeID = \Helper::getEmployeeSystemID();

                $isFinanceUser = false;
                $financeDepartment = CompanyDepartment::with(['employees'])
                    ->where('isFinance', 1)
                    ->where('companySystemID', $input['companyId'])
                    ->first();
                if ($financeDepartment) {
                    $departmentEmployee = $financeDepartment->employees
                        ->where('employeeSystemID', $employeeID)
                        ->first();
                    if ($departmentEmployee) {
                        $isFinanceUser = true;
                    }
                }

                if ($isFinanceUser) {
                    $data = DepartmentBudgetPlanning::with(['department','financeYear'])
                        ->whereIn('companyBudgetPlanningID', $companyBudgetPlanningID)
                        ->orderBy('id', $sort);
                } else {
                    $hodDepartment = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)
                        ->where('isHOD', 1)
                        ->whereHas('department', function($query) use ($input) {
                            $query->where('companySystemID', $input['companyId']);
                        })
                        ->first();

                    $childDepartmentIds = [];
                    if ($hodDepartment) {
                        $hodDeptId = $hodDepartment->departmentSystemID;
                        $childDepartmentIds[] = $hodDeptId;

                        $this->getChildDepartmentIds($hodDeptId, $input['companyId'], $childDepartmentIds);
                    }

                    $childDepartmentIds = array_unique($childDepartmentIds);

                    $data = DepartmentBudgetPlanning::with(['department','financeYear'])
                        ->whereIn('companyBudgetPlanningID', $companyBudgetPlanningID)
                        ->whereHas('department', function($query) use ($childDepartmentIds) {
                            $query->whereIn('departmentSystemID', $childDepartmentIds);
                        })
                        ->orderBy('id', $sort);
                }

                if (array_key_exists('department', $input)) {
                    if (!is_null($request['department'])) {
                        $department = (array)$request['department'];
                        $department = collect($department)->pluck('id')->toArray();
                        if (count($department) > 0) {
                            $data->whereIn('departmentID', $department);
                        }
                    }
                }

                if (array_key_exists('planningCode', $input)) {
                    if (!is_null($request['planningCode'])) {
                        $planningCode = (array)$request['planningCode'];
                        $planningCode = collect($planningCode)->pluck('id')->toArray();
                        if (count($planningCode) > 0) {
                            $data->whereIn('companyBudgetPlanningID', $planningCode);
                        }
                    }
                }

                if (array_key_exists('yearID', $input)) {
                    if (!is_null($input['yearID'])) {
                        $data->where('yearID', $input['yearID']);
                    }
                }

                if (array_key_exists('typeID', $input)) {
                    if (!is_null($input['typeID'])) {
                        $data->where('typeID', $input['typeID']);
                    }
                }

                if (array_key_exists('status', $input)) {
                    if (!is_null($input['status'])) {
                        $data->where('status', $input['status']);
                    }
                }

                $search = $request->input('search.value');

                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $data = $data->where(function ($query) use ($search) {
                        $query->where('planningCode', 'LIKE', "%{$search}%")
                            ->orWhereHas('department', function ($query) use ($search) {
                                $query->where('departmentCode', 'LIKE', "%{$search}%");
                            });
                    });
                }
            }
        }

        $dataset = $data->orderBy('id', $sort)->get();

        $data = array();
        $x = 0;
        foreach ($dataset as $val) {
            $x++;
            if ($input['type'] == 'company') {
                $data[$x]['Company'] = $val->companyID;
            }
            else {
                $data[$x]['Department'] = $val->department->departmentCode;
            }
            $data[$x]['Budget Planning Code'] = $val->planningCode ?? '';
            $data[$x]['Budget Initiate Date'] = $val->initiatedDate ? $val->initiatedDate->format('d/m/Y') : '';
            $data[$x]['Budget Period'] = $val->periodID ? ($val->periodID == 1) ? 'Yearly' : '-' : '';
            $data[$x]['Budget Year'] = $val->financeYear ? \Illuminate\Support\Carbon::parse($val->financeYear->bigginingDate)->format('d/m/Y') . "|" . \Illuminate\Support\Carbon::parse($val->financeYear->endingDate)->format('d/m/Y') : '';
            $data[$x]['Budget Type'] = $val->typeID ? $this->getbudgetType($val->typeID) : '';
            $data[$x]['Date of Submission'] = $val->submissionDate ? $val->submissionDate->format('d/m/Y') : '';
            $data[$x]['Status'] = ($val->status == 1) ? 'In Progress' : 'Open';
        }

        $companyMaster = Company::find(isset($request->companyId) ? $request->companyId : null);
        $companyCode = $companyMaster->CompanyID ?? 'common';
        $detail_array = array(
            'company_code' => $companyCode,
        );

        if ($input['type'] == 'company') {
            $fileName = 'company_budget_planning';
            $path = 'system/company_budget_planning/excel/';
        }
        else {
            $fileName = 'department_budget_planning';
            $path = 'system/department_budget_planning/excel/';
        }
        $type = 'xls';
        $basePath = CreateExcel::process($data, $type, $fileName, $path, $detail_array);

        if ($basePath == '') {
            return $this->sendError('Unable to export excel');
        } else {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function validateBudgetPlanning(Request $request) {
        $data = $request->all();

        if (isset($data['primaryCompany'])) {
            $companyID = $data['primaryCompany'];
        }
        else {
            return $this->sendError(trans('custom.primary_company_is_required'));
        }


        if(Carbon::parse($data['dateOfSubmission'])->lessThan(now())) {
            return $this->sendError('The date of submission should be greater than the current date',404,['duplicate_budget_planning']);
        }
        
        $duplicateBudgetPlanning = CompanyBudgetPlanning::where('companySystemID', $companyID)
            ->where('status', 1)
            ->where('periodID', $data['budgetPeriod'])
            ->where('yearID', $data['budgetYear'])
            ->where('typeID', $data['budgetType'])
            ->exists();

        if ($duplicateBudgetPlanning) {
            $budgetType = $this->getbudgetType($data['budgetType']);
            $errorMessage = 'For the selected budget type ('.$budgetType.') and period, budget planning has already been initiated.';
            return $this->sendError($errorMessage, 404, ['duplicate_budget_planning']);
        }

        $activeDepartments = CompanyDepartment::where('companySystemID', $companyID)
            ->whereNotNull('parentDepartmentID')
            ->where('isActive', 1)
            ->get();

        if ($activeDepartments->isEmpty()) {
            return $this->sendError('No active departments found for this company');
        }

        $allDepartments = CompanyDepartment::where('companySystemID', $companyID)->get();

        $departmentsWithoutHOD = [];
        $departmentsWithoutBudgetTemplate = [];
        $departmentsWithoutSegments = [];
        $finalDepartments = CompanyDepartment::where('companySystemID', $companyID)->where('isActive', 1)->where('type',2)->where('isFinance',0)->doesntHave('children')->get();


        $workflow = WorkflowConfiguration::find($data['workflow']);

        if($workflow->method == 1) {

           foreach($finalDepartments as $department) {
                $segments = CompanyDepartmentSegment::where('departmentSystemID', $department->departmentSystemID)
                    ->where('isActive', 1)
                    ->get();

                if ($segments->isEmpty()) {
                    $departmentsWithoutSegments[] = $department->departmentCode;
                }
                
            }


            if (!empty($departmentsWithoutSegments)) {
                $errorMessage = 'The segments are not assigned for the following department(s)';
    
                $errorMessage .= "<br>" . implode("<br>", $departmentsWithoutSegments);
                return $this->sendError($errorMessage, 404,  ['hod_error']);
            }
        }

        foreach ($finalDepartments as $department) {
            // Check if this is a child department (has parentDepartmentID)
            $isChildDepartment = !empty($department->parentDepartmentID);
            
            // Only check for HOD if it's a child department
            if ($isChildDepartment) {
                $hod = CompanyDepartmentEmployee::where('departmentSystemID', $department->departmentSystemID)
                    ->where('isHOD', 1)
                    ->where('isActive', 1)
                    ->first();

                if (empty($hod)) {
                    $departmentsWithoutHOD[] = $department->departmentCode;
                }
            }
        }

        foreach ($finalDepartments as $department) {
            $budgetTemplate = DepartmentBudgetTemplate::where('departmentSystemID', $department->departmentSystemID)
                ->where('isActive', 1)
                ->first();

            if (empty($budgetTemplate)) {
                $departmentsWithoutBudgetTemplate[] = $department->departmentCode;
            }
        }

        if (!empty($departmentsWithoutHOD)) {
            $errorMessage = $data['from'] == 'portal'
                ? 'hod_error|The HODs for the departments listed below have not been assigned'
                : 'The HODs for the departments listed below have not been assigned';

            $errorMessage .= "<br>" . implode("<br>", $departmentsWithoutHOD);
            return $this->sendError($errorMessage, 404, ['hod_error']);
        }

        if (!empty($departmentsWithoutBudgetTemplate)) {
            $errorMessage = $data['from'] == 'portal'
                ? 'template_error|The departments listed below do not have any active budget templates. Are you sure you want to initiate the budget planning?'
                : 'The departments listed below do not have any active budget templates. Are you sure you want to initiate the budget planning?';

            $errorMessage .= "<br>" . implode("<br>", $departmentsWithoutBudgetTemplate);
            return $this->sendError($errorMessage, 404, ['template_error']);
        }


        return $this->sendResponse(null, 'Budget Planning validation successful');
    }

    public function checkBudgetPlanningInProgress(Request $request) {
        $data = $request->all();

        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'employee':
                case 'segment':
                    if (isset($data['departmentID'])) {
                        $departmentBudgetPlanningState = DepartmentBudgetPlanning::where('departmentID', $data['departmentID'])->where('status', 1)->exists();
                        if ($departmentBudgetPlanningState) {
                            return $this->sendError(trans('custom.budget_planning_is_already_in_progress_for_this_de'));
                        }
                    }
                    break;
                case 'workflow':
                    if (isset($data['workflowID'])) {
                        $budgetPlanningStatus = CompanyBudgetPlanning::where('workflowID', $data['workflowID'])->where('status', 1)->exists();
                        if ($budgetPlanningStatus) {
                            return $this->sendError(trans('custom.budget_planning_is_already_in_progress'));
                        }
                    }
                    break;
                default:
                    return $this->sendResponse(null, 'Budget Planning validation successful');
                    break;
            }
        }

        return $this->sendResponse(null, 'Budget Planning validation successful');
    }


    public function updateBudgetPlanningDelegateWorkStatus(Request $request)
    {
        try {
            $input = $request->all();

            $input['empID'] =  \Helper::getEmployeeSystemID();
            $input['created_by'] = \Helper::getEmployeeSystemID();
            // Validate that we have the required data
            if (empty($input)) {
                return $this->sendError('No data provided', 400);
            }

            // Validate required fields for single record
            if (!isset($input['empID']) || !isset($input['budgetPlanningID'])) {
                return $this->sendError('Missing required fields: empID and budgetPlanningID are required', 400);
            }

            

            // Process single record
            $result = DepartmentBudgetPlanningsDelegateAccess::createOrUpdateDelegateAccess($input);
            
            // Update the main budget planning work status
            $budgetDeleageAccessRecord = BudgetDelegateAccessRecord::with(['budgetPlanningDetail.departmentBudgetPlanning','delegatee'])
                                         ->whereHas('budgetPlanningDetail.departmentBudgetPlanning',function ($query) use ($input) {
                                             $query->where('id', $input['budgetPlanningID']);
                                         })->whereHas('delegatee',function ($q) use ($input) {
                                             $q->where('employeeSystemID',$input['empID']);
                                        })->update(['work_status' => $input['workStatus']]);

            $budgetPlan = DepartmentBudgetPlanning::with('delegateAccess','masterBudgetPlannings')->find($input['budgetPlanningID']);


            if($input['workStatus'] == 3)
            {
                $this->budgetNotificationService->sendNotification($input['budgetPlanningID'],'delegatee-submission', $budgetPlan->masterBudgetPlannings->companySystemID,Auth::user()->employee_id);
            }

            return $this->sendResponse([
                'record' => $result,
                'budgetPlanning' => $budgetPlan,
                'message' => 'Delegate access record and budget planning work status updated successfully'
            ], 'Success');

        } catch (\Exception $e) {
            return $this->sendError('Error processing delegate access: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get revisions by company budget planning ID, grouped by department
     *
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/getRevisionsByCompanyBudget",
     *      summary="getRevisionsByCompanyBudget",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Get revisions by company budget planning ID, grouped by department",
     *      @OA\Parameter(
     *          name="companyBudgetId",
     *          description="Company Budget Planning ID",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getRevisionsByCompanyBudget(Request $request)
    {
        try {
            $companyBudgetId = $request->get('companyBudgetId');
            
            if (!$companyBudgetId) {
                return $this->sendError('Company Budget ID is required', 400);
            }

            $revisions = Revision::with([
                'budgetPlanning.department.hod.employee',
                'budgetPlanning',
                'attachments',
                'createdBy'
            ])
            ->whereHas('budgetPlanning', function ($query) use ($companyBudgetId) {
                $query->where('companyBudgetPlanningID', $companyBudgetId);
            });

            return \DataTables::of($revisions)
                ->addColumn('department', function ($revision) {
                    $department = $revision->budgetPlanning->department ?? null;
                    return $department ? $department->departmentCode : 'N/A';
                })
                ->addColumn('hod', function ($revision) {
                    $department = $revision->budgetPlanning->department ?? null;
                    if ($department && $department->hod && $department->hod->employee) {
                        return $department->hod->employee->empFullName;
                    }
                    return 'N/A';
                })
                ->addColumn('statusText', function ($revision) {
                    return $revision->revision_status_text;
                })
                ->addColumn('reviewBy', function ($revision) {
                    return $revision->createdBy ? $revision->createdBy->empFullName.' - '.$revision->createdBy->empID : 'N/A';
                })
                ->addColumn('reviewComment', function ($revision) {
                    return $revision->reviewComments;
                })
                ->addColumn('sendDateAndTime', function ($revision) {
                    return $revision->sentDateTime ? $revision->sentDateTime->format('d/m/Y H:i') : null;
                })
                ->addColumn('attachment_count', function ($revision) {
                    return $revision->attachments->count();
                })
                ->addColumn('reopenFields', function ($revision) {
                    return $revision->reopenEditableSection;
                })
                ->addColumn('revisionCount', function ($revision) {
                    return 0;
                })
                ->addColumn('revisionTypeText', function ($revision) {
                    return $revision->revision_type_text;
                })
                ->addColumn('submittedDate', function ($revision) {
                    return $revision->submittedDate ? $revision->submittedDate->format('d/m/Y') : null;
                })
                ->addColumn('completionComments', function ($revision) {
                    return $revision->completionComments;
                })
                ->addColumn('completedDateTime', function ($revision) {
                    return $revision->completedDateTime ? $revision->completedDateTime->format('d/m/Y H:i') : null;
                })
                ->make(true);

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving revisions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get time extension requests by company budget planning ID
     *
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/getTimeExtensionRequestsByCompanyBudget",
     *      summary="getTimeExtensionRequestsByCompanyBudget",
     *      tags={"CompanyBudgetPlanning"},
     *      description="Get time extension requests by company budget planning ID",
     *      @OA\Parameter(
     *          name="companyBudgetId",
     *          description="Company Budget Planning ID",
     *          required=true,
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function getTimeExtensionRequestsByCompanyBudget(Request $request)
    {
        try {
            $companyBudgetId = $request->get('companyBudgetPlanningId');
            if (!$companyBudgetId) {
                return $this->sendError('Company Budget ID is required', 400);
            }

            $timeExtensionRequests = DeptBudgetPlanningTimeRequest::with(['departmentBudgetPlanning.department.hod.employee','reviewer'])
            ->whereHas('departmentBudgetPlanning', function ($query) use ($companyBudgetId) {
                $query->where('companyBudgetPlanningID', $companyBudgetId);
            });
           
            return \DataTables::of($timeExtensionRequests)
                ->make(true);

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving time extension requests: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate department budget planning statuses before allowing confirmation
     *
     * @param CompanyBudgetPlanning $companyBudgetPlanning
     * @return array
     */
    private function validateDepartmentBudgetPlanningStatuses($companyBudgetPlanning)
    {
        // Check if budget plan has department budget planning records
        if (!$companyBudgetPlanning->departmentBudgetPlannings || $companyBudgetPlanning->departmentBudgetPlannings->count() === 0) {
            return [
                'valid' => false,
                'message' => trans('custom.no_department_budget_planning_found')
            ];
        }


        // Check if all department budget planning records have workStatus = 3 (submitted to finance)
        $notSubmittedToFinance = $companyBudgetPlanning->departmentBudgetPlannings->filter(function ($dept) {
            return $dept->workStatus != 3;
        });

        if ($notSubmittedToFinance->count() > 0) {
            return [
                'valid' => false,
                'message' => trans('custom.not_all_departments_submitted_to_finance')
            ];
        }

        // Check if all department budget planning records have financeTeamStatus = 4 (completed)
        $notCompletedByFinance = $companyBudgetPlanning->departmentBudgetPlannings->filter(function ($dept) {
            return $dept->financeTeamStatus != 4;
        });

        if ($notCompletedByFinance->count() > 0) {
            return [
                'valid' => false,
                'message' => trans('custom.not_all_finance_status_completed')
            ];
        }



        
        $notConfirmedDepartments = $companyBudgetPlanning->departmentBudgetPlannings->filter(function ($dept) {
            return $dept->confirmed_yn == 0;
        });

        if ($notConfirmedDepartments->count() > 0) {
            return [
                'valid' => false,
                'message' => trans('custom.not_all_departments_confirmed')
            ];
        }

        return [
            'valid' => true,
            'message' => 'Validation passed'
        ];
    }

    /**
     * Reopen budget planning
     * 
     * @param Request $request
     * @return Response
     */
    public function requestBudgetPlanningReopen(Request $request)
    {
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'companyBudgetPlanningID' => 'required|integer',
            'reopenComments' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError(trans('custom.validation_error'), 422, $validator->errors()->toArray());
        }

        try {
            DB::beginTransaction();

            $companyBudgetPlanning = CompanyBudgetPlanning::find($input['companyBudgetPlanningID']);
            
            if (!$companyBudgetPlanning) {
                return $this->sendError(trans('custom.budget_planning_not_found'), 404);
            }

            // Check if confirmed_yn == 1 and approved_yn == 0
            if ($companyBudgetPlanning->confirmed_yn != 1) {
                return $this->sendError(trans('custom.cannot_reopen_budget_planning_not_confirmed'), 400);
            }

            if ($companyBudgetPlanning->approved_yn == -1) {
                return $this->sendError(trans('custom.cannot_reopen_budget_planning_fully_approved'), 400);
            }

            if ($companyBudgetPlanning->approved_yn != 0) {
                return $this->sendError(trans('custom.cannot_reopen_budget_planning_already_approved'), 400);
            }

            // Update the budget planning to reopen it
            $companyBudgetPlanning->confirmed_yn = 0;
            $companyBudgetPlanning->confirmed_by_emp_id = null;
            $companyBudgetPlanning->confirmed_by_emp_system_id = null;
            $companyBudgetPlanning->confirmed_at = null;
            $companyBudgetPlanning->confirmed_by_name = null;
    


            $delete = DocumentApproved::where('document_system_id', 133)->where('documentSystemCode', $companyBudgetPlanning->id)->delete();

            // TODO: Add email notification logic here if needed
            // Similar to ReopenDocument helper

            DB::commit();

            return $this->sendResponse($companyBudgetPlanning->toArray(), trans('custom.budget_planning_reopened_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'), 500);
        }
    }

    /**
     * Pre-check for returning budget planning back to amend
     * 
     * @param Request $request
     * @return Response
     */
    public function returnBudgetPlanningPreCheck(Request $request)
    {
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'companyBudgetPlanningID' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError(trans('custom.validation_error'), 422, $validator->errors()->toArray());
        }

        try {
            $companyBudgetPlanning = CompanyBudgetPlanning::with('departmentBudgetPlannings')->find($input['companyBudgetPlanningID']);
            
            if (!$companyBudgetPlanning) {
                return $this->sendError(trans('custom.budget_planning_not_found'), 404);
            }

            // Check if budget planning is confirmed
            if ($companyBudgetPlanning->confirmed_yn != 1) {
                return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this'), 400);
            }

            // Check if budget planning is approved
            if ($companyBudgetPlanning->approved_yn == -1) {
                return $this->sendError(trans('custom.cannot_return_back_to_amend_budget_planning_fully_approved'), 400);
            }

            // Check if any department budget plannings have related documents that prevent return
            $errors = [];
            if ($companyBudgetPlanning->departmentBudgetPlannings) {
                foreach ($companyBudgetPlanning->departmentBudgetPlannings as $deptBudget) {
                    // Add any validation checks here if needed
                    // For example, check if there are any related documents
                }
            }

            if (!empty($errors)) {
                return $this->sendAPIError(trans('custom.cannot_return_back_to_amend'), 400, ['data' => $errors]);
            }

            return $this->sendResponse($companyBudgetPlanning->toArray(), trans('custom.budget_planning_can_be_returned_to_amend'));
        } catch (\Exception $e) {
            return $this->sendError(trans('custom.error_occurred') . ': ' . $e->getMessage(), 500);
        }
    }

    /**
     * Return budget planning back to amend
     * 
     * @param Request $request
     * @return Response
     */
    public function returnBudgetPlanningToAmend(Request $request)
    {
        $input = $request->all();
        
        // Validate input
        $validator = Validator::make($input, [
            'companyBudgetPlanningID' => 'required|integer',
            'ammendComments' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError(trans('custom.validation_error'), 422, $validator->errors()->toArray());
        }

        try {
            DB::beginTransaction();

            $companyBudgetPlanning = CompanyBudgetPlanning::with('departmentBudgetPlannings')->find($input['companyBudgetPlanningID']);
            
            if (!$companyBudgetPlanning) {
                return $this->sendError(trans('custom.budget_planning_not_found'), 404);
            }

            // Check if budget planning is confirmed
            if ($companyBudgetPlanning->confirmed_yn != 1) {
                return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this'), 400);
            }

            // Check if budget planning is approved
            if ($companyBudgetPlanning->approved_yn == -1) {
                return $this->sendError(trans('custom.cannot_return_back_to_amend_budget_planning_fully_approved'), 400);
            }

            $employee = \Helper::getEmployeeInfo();

            // Store confirmed_by_emp_system_id before clearing it for email notification
            $confirmedByEmpSystemID = $companyBudgetPlanning->confirmed_by_emp_system_id;

            // Update the budget planning to return it back to amend
            $companyBudgetPlanning->confirmed_yn = 0;
            $companyBudgetPlanning->confirmed_by_emp_id = null;
            $companyBudgetPlanning->confirmed_by_emp_system_id = null;
            $companyBudgetPlanning->confirmed_at = null;
            $companyBudgetPlanning->confirmed_by_name = null;
            $companyBudgetPlanning->approved_yn = 0;
            $companyBudgetPlanning->approved_by_emp_id = null;
            $companyBudgetPlanning->approved_by_emp_system_id = null;
            $companyBudgetPlanning->approved_at = null;
            $companyBudgetPlanning->approved_by_name = null;
            $companyBudgetPlanning->rejected_yn = 0;
            $companyBudgetPlanning->timesReferred = 0;

            $companyBudgetPlanning->save();

            // Delete document approvals
            DocumentApproved::where('documentSystemID', 133)
                ->where('documentSystemCode', $companyBudgetPlanning->id)
                ->delete();

            // // Create audit trail
            // \App\Models\AuditTrial::createAuditTrial(
            //     133, // document_system_id for budget planning
            //     $companyBudgetPlanning->id,
            //     $input['ammendComments'],
            //     'returned back to amend'
            // );

            // Send email notifications if needed
            $emails = [];
            if ($confirmedByEmpSystemID) {
                $document = \App\Models\DocumentMaster::where('documentSystemID', 133)->first();
                $docNameBody = $document ? $document->documentDescription : 'Budget Planning';
                $docNameBody .= ' <b>' . $companyBudgetPlanning->planningCode . '</b>';
                $docNameSubject = ($document ? $document->documentDescription : 'Budget Planning') . ' ' . $companyBudgetPlanning->planningCode;
                
                $body = '<p>' . $docNameBody . ' ' . trans('email.has_been_returned_back_to_amend_by', ['empName' => $employee->empName]) . ' ' . trans('email.due_to_below_reason') . '.</p><p>' . trans('email.comment') . ' : ' . $input['ammendComments'] . '</p>';
                $subject = $docNameSubject . ' ' . trans('email.has_been_returned_back_to_amend');

                $emails[] = [
                    'empSystemID' => $confirmedByEmpSystemID,
                    'companySystemID' => $companyBudgetPlanning->companySystemID,
                    'docSystemID' => 133,
                    'alertMessage' => $subject,
                    'emailAlertMessage' => $body,
                    'docSystemCode' => $companyBudgetPlanning->id
                ];
            }

            if (!empty($emails)) {
                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    DB::rollBack();
                    return $this->sendError($sendEmail["message"], 500);
                }
            }

            DB::commit();

            return $this->sendResponse($companyBudgetPlanning->toArray(), trans('custom.budget_planning_returned_back_to_amend_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred') . ': ' . $e->getMessage(), 500);
        }
    }
}
