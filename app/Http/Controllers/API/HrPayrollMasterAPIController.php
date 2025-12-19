<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrPayrollMasterAPIRequest;
use App\Http\Requests\API\UpdateHrPayrollMasterAPIRequest;
use App\Models\HrPayrollMaster;
use App\Repositories\HrPayrollMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrPayrollMasterController
 * @package App\Http\Controllers\API
 */

class HrPayrollMasterAPIController extends AppBaseController
{
    /** @var  HrPayrollMasterRepository */
    private $hrPayrollMasterRepository;

    public function __construct(HrPayrollMasterRepository $hrPayrollMasterRepo)
    {
        $this->hrPayrollMasterRepository = $hrPayrollMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollMasters",
     *      summary="Get a listing of the HrPayrollMasters.",
     *      tags={"HrPayrollMaster"},
     *      description="Get all HrPayrollMasters",
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
     *                  @SWG\Items(ref="#/definitions/HrPayrollMaster")
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
        $this->hrPayrollMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hrPayrollMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrPayrollMasters = $this->hrPayrollMasterRepository->all();

        return $this->sendResponse($hrPayrollMasters->toArray(), trans('custom.hr_payroll_masters_retrieved_successfully'));
    }

    /**
     * @param CreateHrPayrollMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrPayrollMasters",
     *      summary="Store a newly created HrPayrollMaster in storage",
     *      tags={"HrPayrollMaster"},
     *      description="Store HrPayrollMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollMaster")
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
     *                  ref="#/definitions/HrPayrollMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrPayrollMasterAPIRequest $request)
    {
        $input = $request->all();

        $hrPayrollMaster = $this->hrPayrollMasterRepository->create($input);

        return $this->sendResponse($hrPayrollMaster->toArray(), trans('custom.hr_payroll_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrPayrollMasters/{id}",
     *      summary="Display the specified HrPayrollMaster",
     *      tags={"HrPayrollMaster"},
     *      description="Get HrPayrollMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollMaster",
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
     *                  ref="#/definitions/HrPayrollMaster"
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
        /** @var HrPayrollMaster $hrPayrollMaster */
        $hrPayrollMaster = $this->hrPayrollMasterRepository->findWithoutFail($id);

        if (empty($hrPayrollMaster)) {
            return $this->sendError(trans('custom.hr_payroll_master_not_found'));
        }

        return $this->sendResponse($hrPayrollMaster->toArray(), trans('custom.hr_payroll_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrPayrollMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrPayrollMasters/{id}",
     *      summary="Update the specified HrPayrollMaster in storage",
     *      tags={"HrPayrollMaster"},
     *      description="Update HrPayrollMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrPayrollMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrPayrollMaster")
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
     *                  ref="#/definitions/HrPayrollMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrPayrollMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrPayrollMaster $hrPayrollMaster */
        $hrPayrollMaster = $this->hrPayrollMasterRepository->findWithoutFail($id);

        if (empty($hrPayrollMaster)) {
            return $this->sendError(trans('custom.hr_payroll_master_not_found'));
        }

        $hrPayrollMaster = $this->hrPayrollMasterRepository->update($input, $id);

        return $this->sendResponse($hrPayrollMaster->toArray(), trans('custom.hrpayrollmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrPayrollMasters/{id}",
     *      summary="Remove the specified HrPayrollMaster from storage",
     *      tags={"HrPayrollMaster"},
     *      description="Delete HrPayrollMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrPayrollMaster",
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
        /** @var HrPayrollMaster $hrPayrollMaster */
        $hrPayrollMaster = $this->hrPayrollMasterRepository->findWithoutFail($id);

        if (empty($hrPayrollMaster)) {
            return $this->sendError(trans('custom.hr_payroll_master_not_found'));
        }

        $hrPayrollMaster->delete();

        return $this->sendSuccess('Hr Payroll Master deleted successfully');
    }
}
