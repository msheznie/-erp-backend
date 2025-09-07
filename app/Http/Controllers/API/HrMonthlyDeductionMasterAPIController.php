<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrMonthlyDeductionMasterAPIRequest;
use App\Http\Requests\API\UpdateHrMonthlyDeductionMasterAPIRequest;
use App\Models\HrMonthlyDeductionMaster;
use App\Repositories\HrMonthlyDeductionMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrMonthlyDeductionMasterController
 * @package App\Http\Controllers\API
 */

class HrMonthlyDeductionMasterAPIController extends AppBaseController
{
    /** @var  HrMonthlyDeductionMasterRepository */
    private $hrMonthlyDeductionMasterRepository;

    public function __construct(HrMonthlyDeductionMasterRepository $hrMonthlyDeductionMasterRepo)
    {
        $this->hrMonthlyDeductionMasterRepository = $hrMonthlyDeductionMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrMonthlyDeductionMasters",
     *      summary="Get a listing of the HrMonthlyDeductionMasters.",
     *      tags={"HrMonthlyDeductionMaster"},
     *      description="Get all HrMonthlyDeductionMasters",
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
     *                  @SWG\Items(ref="#/definitions/HrMonthlyDeductionMaster")
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
        $this->hrMonthlyDeductionMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hrMonthlyDeductionMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrMonthlyDeductionMasters = $this->hrMonthlyDeductionMasterRepository->all();

        return $this->sendResponse($hrMonthlyDeductionMasters->toArray(), trans('custom.hr_monthly_deduction_masters_retrieved_successfull'));
    }

    /**
     * @param CreateHrMonthlyDeductionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrMonthlyDeductionMasters",
     *      summary="Store a newly created HrMonthlyDeductionMaster in storage",
     *      tags={"HrMonthlyDeductionMaster"},
     *      description="Store HrMonthlyDeductionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrMonthlyDeductionMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrMonthlyDeductionMaster")
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
     *                  ref="#/definitions/HrMonthlyDeductionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrMonthlyDeductionMasterAPIRequest $request)
    {
        $input = $request->all();

        $hrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepository->create($input);

        return $this->sendResponse($hrMonthlyDeductionMaster->toArray(), trans('custom.hr_monthly_deduction_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrMonthlyDeductionMasters/{id}",
     *      summary="Display the specified HrMonthlyDeductionMaster",
     *      tags={"HrMonthlyDeductionMaster"},
     *      description="Get HrMonthlyDeductionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionMaster",
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
     *                  ref="#/definitions/HrMonthlyDeductionMaster"
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
        /** @var HrMonthlyDeductionMaster $hrMonthlyDeductionMaster */
        $hrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionMaster)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_master_not_found'));
        }

        return $this->sendResponse($hrMonthlyDeductionMaster->toArray(), trans('custom.hr_monthly_deduction_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrMonthlyDeductionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrMonthlyDeductionMasters/{id}",
     *      summary="Update the specified HrMonthlyDeductionMaster in storage",
     *      tags={"HrMonthlyDeductionMaster"},
     *      description="Update HrMonthlyDeductionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrMonthlyDeductionMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrMonthlyDeductionMaster")
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
     *                  ref="#/definitions/HrMonthlyDeductionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrMonthlyDeductionMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrMonthlyDeductionMaster $hrMonthlyDeductionMaster */
        $hrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionMaster)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_master_not_found'));
        }

        $hrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepository->update($input, $id);

        return $this->sendResponse($hrMonthlyDeductionMaster->toArray(), trans('custom.hrmonthlydeductionmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrMonthlyDeductionMasters/{id}",
     *      summary="Remove the specified HrMonthlyDeductionMaster from storage",
     *      tags={"HrMonthlyDeductionMaster"},
     *      description="Delete HrMonthlyDeductionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionMaster",
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
        /** @var HrMonthlyDeductionMaster $hrMonthlyDeductionMaster */
        $hrMonthlyDeductionMaster = $this->hrMonthlyDeductionMasterRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionMaster)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_master_not_found'));
        }

        $hrMonthlyDeductionMaster->delete();

        return $this->sendSuccess('Hr Monthly Deduction Master deleted successfully');
    }
}
