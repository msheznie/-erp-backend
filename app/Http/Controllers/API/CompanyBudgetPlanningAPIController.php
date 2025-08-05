<?php

namespace App\Http\Controllers\API;

use App\helper\CreateExcel;
use App\Http\Requests\API\CreateCompanyBudgetPlanningAPIRequest;
use App\Http\Requests\API\UpdateCompanyBudgetPlanningAPIRequest;
use App\Jobs\ProcessDepartmentBudgetPlanning;
use App\Models\Company;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDepartment;
use App\Models\CompanyDepartmentEmployee;
use App\Models\CompanyFinanceYear;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetTemplate;
use App\Models\DepartmentUserBudgetControl;
use App\Models\WorkflowConfiguration;
use App\Repositories\CompanyBudgetPlanningRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

    public function __construct(CompanyBudgetPlanningRepository $companyBudgetPlanningRepo)
    {
        $this->companyBudgetPlanningRepository = $companyBudgetPlanningRepo;
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
        $companyBudgetPlannings = $this->companyBudgetPlanningRepository->all();

        return $this->sendResponse($companyBudgetPlannings->toArray(), 'Company Budget Plannings retrieved successfully');
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

        ProcessDepartmentBudgetPlanning::dispatch($request->db ?? '', $companyBudgetPlanning->id);

        return $this->sendResponse($companyBudgetPlanning->toArray(), 'Budget Planning initiated successfully');
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
        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->findWithoutFail($id);

        if (empty($companyBudgetPlanning)) {
            return $this->sendError('Company Budget Planning not found');
        }

        return $this->sendResponse($companyBudgetPlanning->toArray(), 'Company Budget Planning retrieved successfully');
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
        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->findWithoutFail($id);

        if (empty($companyBudgetPlanning)) {
            return $this->sendError('Company Budget Planning not found');
        }

        $companyBudgetPlanning = $this->companyBudgetPlanningRepository->update($input, $id);

        return $this->sendResponse($companyBudgetPlanning->toArray(), 'CompanyBudgetPlanning updated successfully');
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
            return $this->sendError('Company Budget Planning not found');
        }

        $companyBudgetPlanning->delete();

        return $this->sendSuccess('Company Budget Planning deleted successfully');
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

            $initiateBudgetPlanningAccess = false;
            $isFinanceUser = false;
            $isHodUser = false;

            $financeDepartment = CompanyDepartment::with(['employees.budgetControls'])
                ->where('isFinance', 1)
                ->where('companySystemID', $companyId)
                ->first();
            if ($financeDepartment) {
                $departmentEmployee = $financeDepartment->employees
                    ->where('employeeSystemID', $employeeID)
                    ->first();
                if ($departmentEmployee) {
                    $isFinanceUser = true;
                    $employeeAccess = $departmentEmployee->budgetControls
                        ->where('budgetControlID', 1)
                        ->isNotEmpty();
                    if ($employeeAccess) {
                        $initiateBudgetPlanningAccess = true;
                    }
                }
            }

            $userHODDepartments = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)
                ->where('isHOD', 1)
                ->whereHas('department', function($query) use ($companyId) {
                    $query->where('companySystemID', $companyId);
                })
                ->first();

            if ($userHODDepartments) {
                $isHodUser = true;
            }

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

            $years = CompanyBudgetPlanning::select('yearID')->groupby('yearID')->get()->pluck('yearID')->toArray();
            $years = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,YEAR(bigginingDate) as year"))
                ->where('companySystemID', $companyId)
                ->whereIn('companyFinanceYearID', $years)
                ->orderby('year', 'desc')
                ->get();

            $companyPlanningCodes = CompanyBudgetPlanning::where('companySystemID', $companyId)->select('planningCode','id')->get();
            $departmentPlanningCodes = CompanyBudgetPlanning::where('companySystemID', $companyId)
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

            $companyCodes = CompanyBudgetPlanning::where('companySystemID', $companyId)->select('companyID','companySystemID')->groupBy('companySystemID')->get();

            $output = array(
                'companyFinanceYear' => $companyFinanceYear,
                'years' => $years,
                'companyPlanningCodes' => $companyPlanningCodes,
                'departmentPlanningCodes' => $departmentPlanningCodes,
                'departments' => $departments,
                'companies' => $companyCodes,
                'initiateBudgetPlanningAccess' => $initiateBudgetPlanningAccess,
                'isFinanceUser' => $isFinanceUser,
                'isHodUser' => $isHodUser
            );
        }

        return $this->sendResponse($output, 'Record retrieved successfully');
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
            $data = CompanyBudgetPlanning::with(['financeYear'])->where('companySystemID', $input['companyId'])->orderBy('id', $sort);

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
                    $data->whereIn('id', $planningCode);
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
                        $data->whereIn('departmentID', $department);
                    }
                }

                if (array_key_exists('planningCode', $input)) {
                    if (!is_null($request['planningCode'])) {
                        $planningCode = (array)$request['planningCode'];
                        $planningCode = collect($planningCode)->pluck('id')->toArray();
                        $data->whereIn('companyBudgetPlanningID', $planningCode);
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
                    $data->whereIn('id', $planningCode);
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
                        $data->whereIn('departmentID', $department);
                    }
                }

                if (array_key_exists('planningCode', $input)) {
                    if (!is_null($request['planningCode'])) {
                        $planningCode = (array)$request['planningCode'];
                        $planningCode = collect($planningCode)->pluck('id')->toArray();
                        $data->whereIn('companyBudgetPlanningID', $planningCode);
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
            return $this->sendError('Primary Company is required');
        }

        $activeDepartments = CompanyDepartment::where('companySystemID', $companyID)
            ->where('isActive', 1)
            ->get();

        if ($activeDepartments->isEmpty()) {
            return $this->sendError('No active departments found for this company');
        }

        $allDepartments = CompanyDepartment::where('companySystemID', $companyID)->get();

        $departmentsWithoutHOD = [];
        $departmentsWithoutBudgetTemplate = [];

        foreach ($activeDepartments as $department) {
            $hod = CompanyDepartmentEmployee::where('departmentSystemID', $department->departmentSystemID)
                ->where('isHOD', 1)
                ->first();

            if (empty($hod)) {
                $departmentsWithoutHOD[] = $department->departmentCode;
            }
        }

        foreach ($allDepartments as $department) {
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
                            return $this->sendError('Budget Planning is already in progress for this department');
                        }
                    }
                    break;
                case 'workflow':
                    if (isset($data['workflowID'])) {
                        $budgetPlanningStatus = CompanyBudgetPlanning::where('workflowID', $data['workflowID'])->where('status', 1)->exists();
                        if ($budgetPlanningStatus) {
                            return $this->sendError('Budget Planning is already in progress');
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
}
