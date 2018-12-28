<?php
/**
 * =============================================
 * -- File Name : CompanyFinanceYearperiodMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Company Finance Year period Master
 * -- Author : Mohamed Fayas
 * -- Create date : 28-December 2018
 * -- Description : This file contains the all CRUD for Company Finance Year period Master
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyFinanceYearperiodMasterAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinanceYearperiodMasterAPIRequest;
use App\Models\CompanyFinanceYearperiodMaster;
use App\Repositories\CompanyFinanceYearperiodMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyFinanceYearperiodMasterController
 * @package App\Http\Controllers\API
 */

class CompanyFinanceYearperiodMasterAPIController extends AppBaseController
{
    /** @var  CompanyFinanceYearperiodMasterRepository */
    private $companyFinanceYearperiodMasterRepository;

    public function __construct(CompanyFinanceYearperiodMasterRepository $companyFinanceYearperiodMasterRepo)
    {
        $this->companyFinanceYearperiodMasterRepository = $companyFinanceYearperiodMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYearperiodMasters",
     *      summary="Get a listing of the CompanyFinanceYearperiodMasters.",
     *      tags={"CompanyFinanceYearperiodMaster"},
     *      description="Get all CompanyFinanceYearperiodMasters",
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
     *                  @SWG\Items(ref="#/definitions/CompanyFinanceYearperiodMaster")
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
        $this->companyFinanceYearperiodMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->companyFinanceYearperiodMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyFinanceYearperiodMasters = $this->companyFinanceYearperiodMasterRepository->all();

        return $this->sendResponse($companyFinanceYearperiodMasters->toArray(), 'Company Finance Yearperiod Masters retrieved successfully');
    }

    /**
     * @param CreateCompanyFinanceYearperiodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyFinanceYearperiodMasters",
     *      summary="Store a newly created CompanyFinanceYearperiodMaster in storage",
     *      tags={"CompanyFinanceYearperiodMaster"},
     *      description="Store CompanyFinanceYearperiodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYearperiodMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYearperiodMaster")
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
     *                  ref="#/definitions/CompanyFinanceYearperiodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyFinanceYearperiodMasterAPIRequest $request)
    {
        $input = $request->all();

        $companyFinanceYearperiodMasters = $this->companyFinanceYearperiodMasterRepository->create($input);

        return $this->sendResponse($companyFinanceYearperiodMasters->toArray(), 'Company Finance Yearperiod Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyFinanceYearperiodMasters/{id}",
     *      summary="Display the specified CompanyFinanceYearperiodMaster",
     *      tags={"CompanyFinanceYearperiodMaster"},
     *      description="Get CompanyFinanceYearperiodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYearperiodMaster",
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
     *                  ref="#/definitions/CompanyFinanceYearperiodMaster"
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
        /** @var CompanyFinanceYearperiodMaster $companyFinanceYearperiodMaster */
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            return $this->sendError('Company Finance Yearperiod Master not found');
        }

        return $this->sendResponse($companyFinanceYearperiodMaster->toArray(), 'Company Finance Yearperiod Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCompanyFinanceYearperiodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyFinanceYearperiodMasters/{id}",
     *      summary="Update the specified CompanyFinanceYearperiodMaster in storage",
     *      tags={"CompanyFinanceYearperiodMaster"},
     *      description="Update CompanyFinanceYearperiodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYearperiodMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyFinanceYearperiodMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyFinanceYearperiodMaster")
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
     *                  ref="#/definitions/CompanyFinanceYearperiodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyFinanceYearperiodMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyFinanceYearperiodMaster $companyFinanceYearperiodMaster */
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            return $this->sendError('Company Finance Yearperiod Master not found');
        }

        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->update($input, $id);

        return $this->sendResponse($companyFinanceYearperiodMaster->toArray(), 'CompanyFinanceYearperiodMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyFinanceYearperiodMasters/{id}",
     *      summary="Remove the specified CompanyFinanceYearperiodMaster from storage",
     *      tags={"CompanyFinanceYearperiodMaster"},
     *      description="Delete CompanyFinanceYearperiodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyFinanceYearperiodMaster",
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
        /** @var CompanyFinanceYearperiodMaster $companyFinanceYearperiodMaster */
        $companyFinanceYearperiodMaster = $this->companyFinanceYearperiodMasterRepository->findWithoutFail($id);

        if (empty($companyFinanceYearperiodMaster)) {
            return $this->sendError('Company Finance Yearperiod Master not found');
        }

        $companyFinanceYearperiodMaster->delete();

        return $this->sendResponse($id, 'Company Finance Yearperiod Master deleted successfully');
    }
}
