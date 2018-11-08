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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyFinancePeriodAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinancePeriodAPIRequest;
use App\Models\CompanyFinancePeriod;
use App\Repositories\CompanyFinancePeriodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
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

        return $this->sendResponse($companyFinancePeriods->toArray(), 'Company Finance Periods retrieved successfully');
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

        return $this->sendResponse($companyFinancePeriods->toArray(), 'Company Finance Period saved successfully');
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
            return $this->sendError('Company Finance Period not found');
        }

        return $this->sendResponse($companyFinancePeriod->toArray(), 'Company Finance Period retrieved successfully');
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
            return $this->sendError('Company Finance Period not found');
        }

        $companyFinancePeriod = $this->companyFinancePeriodRepository->update($input, $id);

        return $this->sendResponse($companyFinancePeriod->toArray(), 'CompanyFinancePeriod updated successfully');
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
            return $this->sendError('Company Finance Period not found');
        }

        $companyFinancePeriod->delete();

        return $this->sendResponse($id, 'Company Finance Period deleted successfully');
    }

    public function getAllFinancePeriod(Request $request)
    {
        $companyId = $request['companyId'];
        $companyFinanceYearID = $request['companyFinanceYearID'];
        $departmentSystemID = $request['departmentSystemID'];

        $companyFinancePeriod = \Helper::companyFinancePeriod($companyId, $companyFinanceYearID, $departmentSystemID);

        return $this->sendResponse($companyFinancePeriod, 'Finance periods retrieved successfully');

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

        return $this->sendResponse($output, 'Finance periods retrieved successfully');

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

        return $this->sendResponse($output, 'Finance periods retrieved successfully');
    }

}
