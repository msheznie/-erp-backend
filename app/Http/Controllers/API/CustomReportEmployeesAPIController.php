<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomReportEmployeesAPIRequest;
use App\Http\Requests\API\UpdateCustomReportEmployeesAPIRequest;
use App\Models\CustomReportEmployees;
use App\Models\Employee;
use App\Repositories\CustomReportEmployeesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Services\BoldReport\BoldReportsService;

/**
 * Class CustomReportEmployeesController
 * @package App\Http\Controllers\API
 */

class CustomReportEmployeesAPIController extends AppBaseController
{
    /** @var  CustomReportEmployeesRepository */
    private $customReportEmployeesRepository;
    private $boldReports;
    public function __construct(CustomReportEmployeesRepository $customReportEmployeesRepo, BoldReportsService $boldReports)
    {
        $this->customReportEmployeesRepository = $customReportEmployeesRepo;
        $this->boldReports = $boldReports;
    }

    public function authenticateCustomReport()
    {
        $response = $this->boldReports->getUserKey();
        return $this->sendResponse($response, 'Token generated successfully');
    }

    /**
     * Get all Bold Reports with datatable support
     * @param Request $request
     * @return Response
     */
    public function getBoldReportsDatatable(Request $request)
    {
        $input = $request->all();
        $tenant = $request->get('tenant', 'osos-qa');
        
        // Get reports from Bold Reports server
        $reportsData = $this->boldReports->getAllReports($tenant);
        
        if (!$reportsData) {
            return $this->sendError('Failed to fetch reports from Bold Reports server');
        }

        // Extract reports array from the response
        $reports = $reportsData ?? [];
        
        // Apply search if provided
        $searchValue = $input['search']['value'] ?? '';
        if ($searchValue) {
            $reports = array_filter($reports, function($report) use ($searchValue) {
                return (stripos($report['Name'] ?? '', $searchValue) !== false) ||
                       (stripos($report['Description'] ?? '', $searchValue) !== false) ||
                       (stripos($report['Category'] ?? '', $searchValue) !== false);
            });
        }

        // Calculate totals
        $totalRecords = count($reports);
        
        // Apply ordering
        if (isset($input['order'][0])) {
            $orderColumn = $input['order'][0]['column'];
            $orderDir = $input['order'][0]['dir'];
            $orderFields = ['Id', 'Name', 'Description', 'Category', 'CreatedDate', 'ModifiedDate'];
            
            if (isset($orderFields[$orderColumn])) {
                $orderField = $orderFields[$orderColumn];
                usort($reports, function($a, $b) use ($orderField, $orderDir) {
                    $aValue = $a[$orderField] ?? '';
                    $bValue = $b[$orderField] ?? '';
                    
                    if ($orderDir === 'asc') {
                        return strcmp($aValue, $bValue);
                    } else {
                        return strcmp($bValue, $aValue);
                    }
                });
            }
        }

        // Apply pagination
        $start = $input['start'] ?? 0;
        $length = $input['length'] ?? 15;
        $paginatedReports = array_slice($reports, $start, $length);

        // Add index to each report for display
        foreach ($paginatedReports as $index => &$report) {
            $report['DT_Row_Index'] = $start + $index + 1;
        }

        return response()->json([
            'draw' => $input['draw'] ?? 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $paginatedReports
        ]);
    }

    /**
     * Get available tenants from Bold Reports
     * @return Response
     */
    public function getBoldReportsTenants()
    {
        $tenants = $this->boldReports->getTenants();
        
        if (!$tenants) {
            return $this->sendError('Failed to fetch tenants from Bold Reports server');
        }

        return $this->sendResponse($tenants, 'Tenants retrieved successfully');
    }

    /**
     * Get report categories from Bold Reports
     * @param Request $request
     * @return Response
     */
    public function getBoldReportsCategories(Request $request)
    {
        $tenant = $request->get('tenant', 'site1');
        $categories = $this->boldReports->getReportCategories($tenant);
        
        if (!$categories) {
            return $this->sendError('Failed to fetch categories from Bold Reports server');
        }

        return $this->sendResponse($categories, 'Categories retrieved successfully');
    }

    /**
     * Get specific report by ID from Bold Reports
     * @param Request $request
     * @param string $reportId
     * @return Response
     */
    public function getBoldReportById(Request $request, $reportId)
    {
        $tenant = $request->get('tenant', 'site1');
        $report = $this->boldReports->getReportById($reportId, $tenant);
        
        if (!$report) {
            return $this->sendError('Report not found or failed to fetch from Bold Reports server');
        }

        return $this->sendResponse($report, 'Report retrieved successfully');
    }

    /**
     * Get report details by ID for viewer
     * @param Request $request
     * @return Response
     */
    public function getReportDetailsById(Request $request)
    {
        $reportId = $request->get('reportId');
        // $tenant = $request->get('tenant', 'site1');
        $tenant = 'osos-qa';

        if (!$reportId) {
            return $this->sendError('Report ID is required');
        }

        $report = $this->boldReports->getReportById($reportId, $tenant);
        
        if (!$report) {
            return $this->sendError('Report not found or failed to fetch from Bold Reports server');
        }

        return $this->sendResponse($report, 'Report details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportEmployees",
     *      summary="Get a listing of the CustomReportEmployees.",
     *      tags={"CustomReportEmployees"},
     *      description="Get all CustomReportEmployees",
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
     *                  @SWG\Items(ref="#/definitions/CustomReportEmployees")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->customReportEmployeesRepository->pushCriteria(new RequestCriteria($request));
        $this->customReportEmployeesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customReportEmployees = $this->customReportEmployeesRepository->all();

        return $this->sendResponse($customReportEmployees->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_employees')]));
    }

    /**
     * @param CreateCustomReportEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customReportEmployees",
     *      summary="Store a newly created CustomReportEmployees in storage",
     *      tags={"CustomReportEmployees"},
     *      description="Store CustomReportEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportEmployees that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportEmployees")
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
     *                  ref="#/definitions/CustomReportEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomReportEmployeesAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'employeeSystemID' => 'required|array',
            'user_report_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        foreach ($input['employeeSystemID'] as $val) {
            $data['user_report_id'] = $input['user_report_id'];
            $data['user_id'] = $val['employeeSystemID'];
            $this->customReportEmployeesRepository->create($data);
        }

        return $this->sendResponse([], trans('custom.save', ['attribute' => trans('custom.custom_report_employees')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customReportEmployees/{id}",
     *      summary="Display the specified CustomReportEmployees",
     *      tags={"CustomReportEmployees"},
     *      description="Get CustomReportEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportEmployees",
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
     *                  ref="#/definitions/CustomReportEmployees"
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
        /** @var CustomReportEmployees $customReportEmployees */
        $customReportEmployees = $this->customReportEmployeesRepository->findWithoutFail($id);

        if (empty($customReportEmployees)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_employees')]));
        }

        return $this->sendResponse($customReportEmployees->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.custom_report_employees')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomReportEmployeesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customReportEmployees/{id}",
     *      summary="Update the specified CustomReportEmployees in storage",
     *      tags={"CustomReportEmployees"},
     *      description="Update CustomReportEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportEmployees",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomReportEmployees that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomReportEmployees")
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
     *                  ref="#/definitions/CustomReportEmployees"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomReportEmployeesAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomReportEmployees $customReportEmployees */
        $customReportEmployees = $this->customReportEmployeesRepository->findWithoutFail($id);

        if (empty($customReportEmployees)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_employees')]));
        }

        $customReportEmployees = $this->customReportEmployeesRepository->update($input, $id);

        return $this->sendResponse($customReportEmployees->toArray(), trans('custom.update', ['attribute' => trans('custom.custom_report_employees')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customReportEmployees/{id}",
     *      summary="Remove the specified CustomReportEmployees from storage",
     *      tags={"CustomReportEmployees"},
     *      description="Delete CustomReportEmployees",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomReportEmployees",
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
        /** @var CustomReportEmployees $customReportEmployees */
        $customReportEmployees = $this->customReportEmployeesRepository->findWithoutFail($id);

        if (empty($customReportEmployees)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.custom_report_employees')]));
        }

        $customReportEmployees->delete();
        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.custom_report_employees')]));
    }

    function getEmployees(Request $request)
    {
        $companySystemID = $request['companySystemID'];
        //$parentCompany = Company::find($companySystemID);
        //$allCompanies = Company::where('masterCompanySystemIDReorting',$parentCompany->masterCompanySystemIDReorting)->pluck('companySystemID')->toArray();
        $employees = Employee::select('empID', 'empName', 'employeeSystemID','empCompanySystemID')
                            //->with(['company'])
                            //->whereIN('empCompanySystemID', $allCompanies)
                            ->whereDoesntHave('custom_reports')
                            ->where('discharegedYN', 0)
                            ->get();

        return $this->sendResponse($employees, trans('custom.retrieve', ['attribute' => trans('custom.report_template')]));
    }

    public function getCustomReportAssignedEmployee(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $output = CustomReportEmployees::with('employee_by')
                                       ->where('user_report_id',$request->id);

        return \DataTables::eloquent($output)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }
}
