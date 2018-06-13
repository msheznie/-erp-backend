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
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyFinanceYearAPIRequest;
use App\Http\Requests\API\UpdateCompanyFinanceYearAPIRequest;
use App\Models\CompanyFinanceYear;
use App\Repositories\CompanyFinanceYearRepository;
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

    public function __construct(CompanyFinanceYearRepository $companyFinanceYearRepo)
    {
        $this->companyFinanceYearRepository = $companyFinanceYearRepo;
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

        $companyFinanceYears = $this->companyFinanceYearRepository->create($input);

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

        /** @var CompanyFinanceYear $companyFinanceYear */
        $companyFinanceYear = $this->companyFinanceYearRepository->findWithoutFail($id);

        if (empty($companyFinanceYear)) {
            return $this->sendError('Company Finance Year not found');
        }

        $companyFinanceYear = $this->companyFinanceYearRepository->update($input, $id);

        return $this->sendResponse($companyFinanceYear->toArray(), 'CompanyFinanceYear updated successfully');
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
}
