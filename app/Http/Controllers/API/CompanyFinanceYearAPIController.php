<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYearAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year
 * -- Author : Mohamed Nazir
 * -- Create date : 12-June 2018
 * -- Description : This file contains the all CRUD for Company Finance Year
 * -- REVISION HISTORY
 * -- Date: 27-December 2018 By: Fayas Description: Added new functions named as getFinancialYearsByCompany()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyFinanceYearAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinanceYearAPIRequest;
use App\Jobs\CreateFinancePeriod;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DepartmentMaster;
use App\Repositories\CompanyFinancePeriodRepository;
use App\Repositories\CompanyFinanceYearRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyFinanceYearController
 * @package App\Http\Controllers\API
 */
class CompanyFinanceYearAPIController extends AppBaseController
{
    /** @var  CompanyFinanceYearRepository */
    private $companyFinanceYearRepository;
    private $companyFinancePeriodRepository;

    public function __construct(CompanyFinanceYearRepository $companyFinanceYearRepo,CompanyFinancePeriodRepository $companyFinancePeriodRepo)
    {
        $this->companyFinanceYearRepository = $companyFinanceYearRepo;
        $this->companyFinancePeriodRepository = $companyFinancePeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYears",
     *      summary="Get a listing of the CompanyFinanceYears.",
     *      tags={"CompanyFinanceYear"},
     *      description="Get all CompanyFinanceYears",
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
     *                  @SWG\Items(ref="#/definitions/CompanyFinanceYear")
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
        $this->companyFinanceYearRepository->pushCriteria(new RequestCriteria($request));
        $this->companyFinanceYearRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyFinanceYears = $this->companyFinanceYearRepository->all();

        return $this->sendResponse($companyFinanceYears->toArray(), 'Company Finance Years retrieved successfully');
    }

    /**
     * @param CreateCompanyFinanceYearAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyFinanceYears",
     *      summary="Store a newly created CompanyFinanceYear in storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Store CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYear that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYear")
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
     *                  ref="#/definitions/CompanyFinanceYear"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyFinanceYearAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'bigginingDate' => 'required',
            'endingDate' => 'required|after:bigginingDate'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $input['companyID'] = $company->CompanyID;
        $fromDate  = new Carbon($request->bigginingDate);
        $input['bigginingDate'] = $fromDate->format('Y-m-d');
        $toDate = new Carbon($request->endingDate);
        $input['endingDate'] = $toDate->format('Y-m-d');

        $checkLastFinancialYear = CompanyFinanceYear::where('companySystemID',$input['companySystemID'])
                                                        ->max('endingDate');

        if($checkLastFinancialYear){
            $lastDate  = new Carbon($checkLastFinancialYear);
            $lastDate  = $lastDate->format('Y-m-d');

            if($lastDate >= $input['bigginingDate']){
                return $this->sendError('You cannot create financial year, Please select the beginning date after ' . (new Carbon($lastDate))->format('d/m/Y'));
            }
        }

        $diffMonth = (Carbon::createFromFormat('Y-m-d',$input['bigginingDate']))->diffInMonths(Carbon::createFromFormat('Y-m-d',$input['endingDate']));

        if($diffMonth != 11){
            return  $this->sendError('Financial year must contain 12 months.');
        }

        $employee = \Helper::getEmployeeInfo();
        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYears = $this->companyFinanceYearRepository->create($input);
        CreateFinancePeriod::dispatch($companyFinanceYears);

        return $this->sendResponse($companyFinanceYears->toArray(), 'Company Finance Year saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYears/{id}",
     *      summary="Display the specified CompanyFinanceYear",
     *      tags={"CompanyFinanceYear"},
     *      description="Get CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
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
     *                  ref="#/definitions/CompanyFinanceYear"
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
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError('Company Finance Year not found');
        }

        return $this->sendResponse($companyFinanceYear->toArray(), 'Company Finance Year retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCompanyFinanceYearAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyFinanceYears/{id}",
     *      summary="Update the specified CompanyFinanceYear in storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Update CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYear that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYear")
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
     *                  ref="#/definitions/CompanyFinanceYear"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyFinanceYearAPIRequest $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError('Company Finance Year not found');
        }

        $checkFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinanceYear->companySystemID)
                                                    ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                                    ->where('isActive', -1)
                                                    ->count();

        if ($input['isActive']) {
            $input['isActive'] = -1;
        } else if ($companyFinanceYear->isActive && !$input['isActive'] && $checkFinancePeriod > 0) {
            return $this->sendError('Cannot deactivate, There are some active finance periods for this finance year.');
        }

        if ($input['isCurrent']) {
            $input['isCurrent'] = -1;
            if(!$companyFinanceYear->isCurrent){
                $checkCurrentFinanceYear = CompanyFinanceYear::where('companySystemID', $companyFinanceYear->companySystemID)
                    ->where('isCurrent', -1)
                    ->count();

                if ($checkCurrentFinanceYear > 0) {
                    return $this->sendError('Company already has a current financial year.');
                }
            }
        }

        if ($input['isClosed']) {
            $input['isClosed']  = -1;

            if(!$companyFinanceYear->isClosed && $checkFinancePeriod > 0 && $input['closeAllPeriods'] == 0){
                return $this->sendError('Cannot close, There are some open financial periods for the selected financial year. Do you want to close all the financial periods?',500,array('type' => 'active_period_exist' ));
            }

            //if($input['closeAllPeriods'] == 1){
                $updateFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinanceYear->companySystemID)
                                                            ->where('companyFinanceYearID', $companyFinanceYear->companyFinanceYearID)
                                                            ->get();

                foreach ($updateFinancePeriod as $period){
                    $this->companyFinancePeriodRepository->update(['isActive' => 0,'isCurrent' => 0,'isClosed' => -1],$period->companyFinancePeriodID);
                }
            //}

            $input['isCurrent'] = 0;
            $input['isActive']  = 0;

            $input['closedByEmpSystemID'] = $employee->employeeSystemID;
            $input['closedByEmpID']       = $employee->empID;
            $input['closedByEmpName']     = $employee->empName;
            $input['closedDate']          = now();
        }else if($companyFinanceYear->isClosed == -1 && $input['isClosed'] == 0){
            return $this->sendError('Cannot open this finance year.');
        }


        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $companyFinanceYear = $this->companyFinanceYearRepository->update($input, $id);

        return $this->sendResponse($companyFinanceYear->toArray(), 'Company financial Year updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyFinanceYears/{id}",
     *      summary="Remove the specified CompanyFinanceYear from storage",
     *      tags={"CompanyFinanceYear"},
     *      description="Delete CompanyFinanceYear",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYear",
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
        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError('Company Finance Year not found');
        }

        $companyFinanceYear->delete();

        return $this->sendResponse($id, 'Company Finance Year deleted successfully');
    }

    public function getFinancialYearsByCompany(Request $request)
    {

        $input = $request->all();
        //$input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $companyFinancialYears = CompanyFinanceYear::whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $companyFinancialYears = $companyFinancialYears->where(function ($query) use ($search) {
                /*$query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('issueRefNo', 'LIKE', "%{$search}%");*/
            });
        }

        return \DataTables::eloquent($companyFinancialYears)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companyFinanceYearID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('closeAllPeriods', function ($row) {
                return 0;
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFinanceYearFormData(Request $request){

        $input = $request->all();
        $departments = DepartmentMaster::select('departmentSystemID','DepartmentDescription','DepartmentID');


        if (array_key_exists('isFinancialYearYN', $input)) {
            if (!is_null($input['isFinancialYearYN'])) {
                $departments->where('isFinancialYearYN', $input['isFinancialYearYN']);
            }
        }

        $departments = $departments->get();

        $output = array(
            'departments' => $departments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
