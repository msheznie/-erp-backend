<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalaryProcessEmploymentTypesAPIRequest;
use App\Http\Requests\API\UpdateSalaryProcessEmploymentTypesAPIRequest;
use App\Models\SalaryProcessEmploymentTypes;
use App\Repositories\SalaryProcessEmploymentTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalaryProcessEmploymentTypesController
 * @package App\Http\Controllers\API
 */

class SalaryProcessEmploymentTypesAPIController extends AppBaseController
{
    /** @var  SalaryProcessEmploymentTypesRepository */
    private $salaryProcessEmploymentTypesRepository;

    public function __construct(SalaryProcessEmploymentTypesRepository $salaryProcessEmploymentTypesRepo)
    {
        $this->salaryProcessEmploymentTypesRepository = $salaryProcessEmploymentTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessEmploymentTypes",
     *      summary="Get a listing of the SalaryProcessEmploymentTypes.",
     *      tags={"SalaryProcessEmploymentTypes"},
     *      description="Get all SalaryProcessEmploymentTypes",
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
     *                  @SWG\Items(ref="#/definitions/SalaryProcessEmploymentTypes")
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
        $this->salaryProcessEmploymentTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->salaryProcessEmploymentTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->all();

        return $this->sendResponse($salaryProcessEmploymentTypes->toArray(), trans('custom.salary_process_employment_types_retrieved_successf'));
    }

    /**
     * @param CreateSalaryProcessEmploymentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salaryProcessEmploymentTypes",
     *      summary="Store a newly created SalaryProcessEmploymentTypes in storage",
     *      tags={"SalaryProcessEmploymentTypes"},
     *      description="Store SalaryProcessEmploymentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessEmploymentTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessEmploymentTypes")
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
     *                  ref="#/definitions/SalaryProcessEmploymentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalaryProcessEmploymentTypesAPIRequest $request)
    {
        $input = $request->all();

        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->create($input);

        return $this->sendResponse($salaryProcessEmploymentTypes->toArray(), trans('custom.salary_process_employment_types_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessEmploymentTypes/{id}",
     *      summary="Display the specified SalaryProcessEmploymentTypes",
     *      tags={"SalaryProcessEmploymentTypes"},
     *      description="Get SalaryProcessEmploymentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessEmploymentTypes",
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
     *                  ref="#/definitions/SalaryProcessEmploymentTypes"
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
        /** @var SalaryProcessEmploymentTypes $salaryProcessEmploymentTypes */
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            return $this->sendError(trans('custom.salary_process_employment_types_not_found'));
        }

        return $this->sendResponse($salaryProcessEmploymentTypes->toArray(), trans('custom.salary_process_employment_types_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateSalaryProcessEmploymentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salaryProcessEmploymentTypes/{id}",
     *      summary="Update the specified SalaryProcessEmploymentTypes in storage",
     *      tags={"SalaryProcessEmploymentTypes"},
     *      description="Update SalaryProcessEmploymentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessEmploymentTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessEmploymentTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessEmploymentTypes")
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
     *                  ref="#/definitions/SalaryProcessEmploymentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalaryProcessEmploymentTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalaryProcessEmploymentTypes $salaryProcessEmploymentTypes */
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            return $this->sendError(trans('custom.salary_process_employment_types_not_found'));
        }

        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->update($input, $id);

        return $this->sendResponse($salaryProcessEmploymentTypes->toArray(), trans('custom.salaryprocessemploymenttypes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salaryProcessEmploymentTypes/{id}",
     *      summary="Remove the specified SalaryProcessEmploymentTypes from storage",
     *      tags={"SalaryProcessEmploymentTypes"},
     *      description="Delete SalaryProcessEmploymentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessEmploymentTypes",
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
        /** @var SalaryProcessEmploymentTypes $salaryProcessEmploymentTypes */
        $salaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepository->findWithoutFail($id);

        if (empty($salaryProcessEmploymentTypes)) {
            return $this->sendError(trans('custom.salary_process_employment_types_not_found'));
        }

        $salaryProcessEmploymentTypes->delete();

        return $this->sendResponse($id, trans('custom.salary_process_employment_types_deleted_successful'));
    }
}
