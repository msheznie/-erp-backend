<?php
/**
 * =============================================
 * -- File Name : CompanyFinancePeriodAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Period
 * -- Author : Mohamed Nazir
 * -- Create date : 12-June 2018
 * -- Description : This file contains the all CRUD for Company Finance Period
 * -- REVISION HISTORY
 * -- Date: 12-June 2018 By: Nazir Description: Added new functions named as getAllFinancePeriod() For load all finance period
 * -- Date: 08-November 2018 By: Nazir Description: Added new functions named as getAllFinancePeriodForYear() For load all finance period
 * -- Date: 27-December 2018 By: Fayas Description: Added new functions named as getFinancialPeriodsByYear()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyFinancePeriodAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinancePeriodAPIRequest;
use App\Models\CompanyFinancePeriod;
use App\Repositories\CompanyFinancePeriodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CompanyFinancePeriodController
 * @package App\Http\Controllers\API
 */
class CompanyFinancePeriodAPIController extends AppBaseController
{
    /** @var  CompanyFinancePeriodRepository */
    private $companyFinancePeriodRepository;

    public function __construct(CompanyFinancePeriodRepository $companyFinancePeriodRepo)
    {
        $this->companyFinancePeriodRepository = $companyFinancePeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinancePeriods",
     *      summary="Get a listing of the CompanyFinancePeriods.",
     *      tags={"CompanyFinancePeriod"},
     *      description="Get all CompanyFinancePeriods",
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
     *                  @SWG\Items(ref="#/definitions/CompanyFinancePeriod")
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
        $this->companyFinancePeriodRepository->pushCriteria(new RequestCriteria($request));
        $this->companyFinancePeriodRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyFinancePeriods = $this->companyFinancePeriodRepository->all();

        return $this->sendResponse($companyFinancePeriods->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_finance_periods')]));
    }

    /**
     * @param CreateCompanyFinancePeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyFinancePeriods",
     *      summary="Store a newly created CompanyFinancePeriod in storage",
     *      tags={"CompanyFinancePeriod"},
     *      description="Store CompanyFinancePeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinancePeriod that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinancePeriod")
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
     *                  ref="#/definitions/CompanyFinancePeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyFinancePeriodAPIRequest $request)
    {
        $input = $request->all();

        $companyFinancePeriods = $this->companyFinancePeriodRepository->create($input);

        return $this->sendResponse($companyFinancePeriods->toArray(), trans('custom.save', ['attribute' => trans('custom.company_finance_periods')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinancePeriods/{id}",
     *      summary="Display the specified CompanyFinancePeriod",
     *      tags={"CompanyFinancePeriod"},
     *      description="Get CompanyFinancePeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinancePeriod",
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
     *                  ref="#/definitions/CompanyFinancePeriod"
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
        /** @var CompanyFinancePeriod $companyFinancePeriod */
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_periods')]));
        }

        return $this->sendResponse($companyFinancePeriod->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.company_finance_periods')]));
    }

    /**
     * @param int $id
     * @param UpdateCompanyFinancePeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyFinancePeriods/{id}",
     *      summary="Update the specified CompanyFinancePeriod in storage",
     *      tags={"CompanyFinancePeriod"},
     *      description="Update CompanyFinancePeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinancePeriod",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinancePeriod that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinancePeriod")
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
     *                  ref="#/definitions/CompanyFinancePeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyFinancePeriodAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyFinancePeriod $companyFinancePeriod */
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_periods')]));
        }

        if ($input['isActive']) {
            $input['isActive'] = -1;
            if(!$companyFinancePeriod->isActive && $input['isActive'] && $companyFinancePeriod->departmentSystemID != 5) {
                $checkGlAccount = CompanyFinancePeriod::where('companySystemID', $companyFinancePeriod->companySystemID)
                                                        ->where('departmentSystemID', 5)
                                                        ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                                                        ->whereDate('dateFrom', $companyFinancePeriod->dateFrom)
                                                        ->where('isActive', -1)
                                                        ->count();

                if ($checkGlAccount == 0) {
                    return $this->sendError(trans('custom.general_ledger_financial_period_is_not_active_you_cannot_activate_financial_period'));
                }
            }
        } else if ($companyFinancePeriod->isActive && !$input['isActive'] && $companyFinancePeriod->departmentSystemID == 5) {
                $checkOtherDepartments = CompanyFinancePeriod::where('companySystemID', $companyFinancePeriod->companySystemID)
                                                        ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                                                        ->whereDate('dateFrom', $companyFinancePeriod->dateFrom)
                                                        ->where('departmentSystemID','!=',5)
                                                        ->where('isActive', -1)
                                                        ->count();

                if ($checkOtherDepartments > 0) {
                    return $this->sendError('Deactivate other department periods before deactivating general ledger period.',500);
                }
        }

        if ($input['isCurrent']) {
            $input['isCurrent'] = -1;
            if(!$companyFinancePeriod->isCurrent){
                $checkCurrentFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinancePeriod->companySystemID)
                                                                    ->where('departmentSystemID', $companyFinancePeriod->departmentSystemID)
                                                                    ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                                                                    ->where('isCurrent', -1)
                                                                    ->count();

                if ($checkCurrentFinancePeriod > 0) {
                    return $this->sendError(trans('custom.company_already_has_a_current_financial_period_for_this_department'));
                }
            }
        }

        if ($input['isClosed']) {
            $input['isClosed']  = -1;

            if($companyFinancePeriod->departmentSystemID == 5){

                $checkOtherDepartments = CompanyFinancePeriod::where('companySystemID', $companyFinancePeriod->companySystemID)
                                                            ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                                                            ->where('departmentSystemID','!=', 5)
                                                            ->where('isActive', -1)
                                                            ->count();

                if ($checkOtherDepartments > 0 && $input['closeAllPeriods'] == 0) {
                    return $this->sendError(trans('custom.there_are_some_department_has_active_financial_period'),500,array('type' => 'active_period_exist'));
                }

                //if($input['closeAllPeriods'] == 1){
                    $updateFinancePeriod = CompanyFinancePeriod::where('companySystemID', $companyFinancePeriod->companySystemID)
                                                                ->where('companyFinanceYearID', $companyFinancePeriod->companyFinanceYearID)
                                                                ->whereDate('dateFrom', $companyFinancePeriod->dateFrom)
                                                                ->get();

                    foreach ($updateFinancePeriod as $period){
                        $this->companyFinancePeriodRepository->update(['isActive' => 0,'isCurrent' => 0,'isClosed' => -1],$period->companyFinancePeriodID);
                    }
                //}
            }
            $input['isCurrent'] = 0;
            $input['isActive']  = 0;
        }

        $companyFinancePeriod = $this->companyFinancePeriodRepository->update($input, $id);

        return $this->sendResponse($companyFinancePeriod->toArray(), trans('custom.update', ['attribute' => trans('custom.company_finance_periods')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyFinancePeriods/{id}",
     *      summary="Remove the specified CompanyFinancePeriod from storage",
     *      tags={"CompanyFinancePeriod"},
     *      description="Delete CompanyFinancePeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinancePeriod",
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
        /** @var CompanyFinancePeriod $companyFinancePeriod */
        $companyFinancePeriod = $this->companyFinancePeriodRepository->findWithoutFail($id);

        if (empty($companyFinancePeriod)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.company_finance_periods')]));
        }

        $companyFinancePeriod->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.company_finance_periods')]));
    }

    public function getAllFinancePeriod(Request $request)
    {
        $companyId = $request['companyId'];
        $companyFinanceYearID = $request['companyFinanceYearID'];
        $departmentSystemID = $request['departmentSystemID'];

        $companyFinancePeriod = \Helper::companyFinancePeriod($companyId, $companyFinanceYearID, $departmentSystemID);

        return $this->sendResponse($companyFinancePeriod, trans('custom.retrieve', ['attribute' => trans('custom.finance_periods')]));

    }

    public function getAllFinancePeriodBasedFY(Request $request)
    {
        $companyId = $request['companyId'];
        $companyFinanceYearID = $request['companyFinanceYearID'];
        $departmentSystemID = $request['departmentSystemID'];

        //$companyFinancePeriod = \Helper::companyFinancePeriod($companyId, $companyFinanceYearID, $departmentSystemID);

        $output = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ', DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod"))
            ->where('companySystemID', '=', $companyId)
            ->where('companyFinanceYearID', $companyFinanceYearID)
            ->where('departmentSystemID', $departmentSystemID)
            ->where('isActive', -1)
            ->get();

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.finance_periods')]));

    }

    public function getAllFinancePeriodForYear(Request $request)
    {
        $companyId = $request['companyId'];
        $companyFinanceYearID = $request['companyFinanceYearID'];
        $departmentSystemID = $request['departmentSystemID'];

        $output = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ', DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod"))
            ->where('companySystemID', '=', $companyId)
            ->where('companyFinanceYearID', $companyFinanceYearID)
            ->where('departmentSystemID', $departmentSystemID)
            ->get();

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.finance_periods')]));
    }

    public function getFinancialPeriodsByYear(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('departmentSystemID', 'year'));

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

        $companyFinancialPeriods = CompanyFinancePeriod::whereIn('companySystemID', $subCompanies)
                                                       ->where('companyFinanceYearID', $request['companyFinanceYearID']);

        if (array_key_exists('departmentSystemID', $input)) {
            if ($input['departmentSystemID'] && !is_null($input['departmentSystemID'])) {
                $companyFinancialPeriods->where('departmentSystemID', $input['departmentSystemID']);
            }else{
                $companyFinancialPeriods->where('departmentSystemID', 0);
            }
        }else{
            $companyFinancialPeriods->where('departmentSystemID', 0);
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $companyFinancialPeriods = $companyFinancialPeriods->where(function ($query) use ($search) {
                /*$query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('issueRefNo', 'LIKE', "%{$search}%");*/
            });
        }

        return \DataTables::eloquent($companyFinancialPeriods)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('companyFinancePeriodID', $input['order'][0]['dir']);
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

}
