<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalaryProcessMasterAPIRequest;
use App\Http\Requests\API\UpdateSalaryProcessMasterAPIRequest;
use App\Models\SalaryProcessMaster;
use App\Repositories\SalaryProcessMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalaryProcessMasterController
 * @package App\Http\Controllers\API
 */

class SalaryProcessMasterAPIController extends AppBaseController
{
    /** @var  SalaryProcessMasterRepository */
    private $salaryProcessMasterRepository;

    public function __construct(SalaryProcessMasterRepository $salaryProcessMasterRepo)
    {
        $this->salaryProcessMasterRepository = $salaryProcessMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessMasters",
     *      summary="Get a listing of the SalaryProcessMasters.",
     *      tags={"SalaryProcessMaster"},
     *      description="Get all SalaryProcessMasters",
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
     *                  @SWG\Items(ref="#/definitions/SalaryProcessMaster")
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
        $this->salaryProcessMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->salaryProcessMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salaryProcessMasters = $this->salaryProcessMasterRepository->all();

        return $this->sendResponse($salaryProcessMasters->toArray(), trans('custom.salary_process_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSalaryProcessMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salaryProcessMasters",
     *      summary="Store a newly created SalaryProcessMaster in storage",
     *      tags={"SalaryProcessMaster"},
     *      description="Store SalaryProcessMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessMaster")
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
     *                  ref="#/definitions/SalaryProcessMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalaryProcessMasterAPIRequest $request)
    {
        $input = $request->all();

        $salaryProcessMasters = $this->salaryProcessMasterRepository->create($input);

        return $this->sendResponse($salaryProcessMasters->toArray(), trans('custom.salary_process_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salaryProcessMasters/{id}",
     *      summary="Display the specified SalaryProcessMaster",
     *      tags={"SalaryProcessMaster"},
     *      description="Get SalaryProcessMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessMaster",
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
     *                  ref="#/definitions/SalaryProcessMaster"
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
        /** @var SalaryProcessMaster $salaryProcessMaster */
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            return $this->sendError(trans('custom.salary_process_master_not_found'));
        }

        return $this->sendResponse($salaryProcessMaster->toArray(), trans('custom.salary_process_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalaryProcessMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salaryProcessMasters/{id}",
     *      summary="Update the specified SalaryProcessMaster in storage",
     *      tags={"SalaryProcessMaster"},
     *      description="Update SalaryProcessMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalaryProcessMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalaryProcessMaster")
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
     *                  ref="#/definitions/SalaryProcessMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalaryProcessMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalaryProcessMaster $salaryProcessMaster */
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            return $this->sendError(trans('custom.salary_process_master_not_found'));
        }

        $salaryProcessMaster = $this->salaryProcessMasterRepository->update($input, $id);

        return $this->sendResponse($salaryProcessMaster->toArray(), trans('custom.salaryprocessmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salaryProcessMasters/{id}",
     *      summary="Remove the specified SalaryProcessMaster from storage",
     *      tags={"SalaryProcessMaster"},
     *      description="Delete SalaryProcessMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalaryProcessMaster",
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
        /** @var SalaryProcessMaster $salaryProcessMaster */
        $salaryProcessMaster = $this->salaryProcessMasterRepository->findWithoutFail($id);

        if (empty($salaryProcessMaster)) {
            return $this->sendError(trans('custom.salary_process_master_not_found'));
        }

        $salaryProcessMaster->delete();

        return $this->sendResponse($id, trans('custom.salary_process_master_deleted_successfully'));
    }
}
