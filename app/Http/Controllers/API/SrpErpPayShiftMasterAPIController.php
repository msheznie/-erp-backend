<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpPayShiftMasterAPIRequest;
use App\Http\Requests\API\UpdateSrpErpPayShiftMasterAPIRequest;
use App\Models\SrpErpPayShiftMaster;
use App\Repositories\SrpErpPayShiftMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpPayShiftMasterController
 * @package App\Http\Controllers\API
 */

class SrpErpPayShiftMasterAPIController extends AppBaseController
{
    /** @var  SrpErpPayShiftMasterRepository */
    private $srpErpPayShiftMasterRepository;

    public function __construct(SrpErpPayShiftMasterRepository $srpErpPayShiftMasterRepo)
    {
        $this->srpErpPayShiftMasterRepository = $srpErpPayShiftMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpPayShiftMasters",
     *      summary="Get a listing of the SrpErpPayShiftMasters.",
     *      tags={"SrpErpPayShiftMaster"},
     *      description="Get all SrpErpPayShiftMasters",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpPayShiftMaster")
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
        $this->srpErpPayShiftMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpPayShiftMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpPayShiftMasters = $this->srpErpPayShiftMasterRepository->all();

        return $this->sendResponse($srpErpPayShiftMasters->toArray(), trans('custom.srp_erp_pay_shift_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSrpErpPayShiftMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpPayShiftMasters",
     *      summary="Store a newly created SrpErpPayShiftMaster in storage",
     *      tags={"SrpErpPayShiftMaster"},
     *      description="Store SrpErpPayShiftMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpPayShiftMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpPayShiftMaster")
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
     *                  ref="#/definitions/SrpErpPayShiftMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpPayShiftMasterAPIRequest $request)
    {
        $input = $request->all();

        $srpErpPayShiftMaster = $this->srpErpPayShiftMasterRepository->create($input);

        return $this->sendResponse($srpErpPayShiftMaster->toArray(), trans('custom.srp_erp_pay_shift_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpPayShiftMasters/{id}",
     *      summary="Display the specified SrpErpPayShiftMaster",
     *      tags={"SrpErpPayShiftMaster"},
     *      description="Get SrpErpPayShiftMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftMaster",
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
     *                  ref="#/definitions/SrpErpPayShiftMaster"
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
        /** @var SrpErpPayShiftMaster $srpErpPayShiftMaster */
        $srpErpPayShiftMaster = $this->srpErpPayShiftMasterRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftMaster)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_master_not_found'));
        }

        return $this->sendResponse($srpErpPayShiftMaster->toArray(), trans('custom.srp_erp_pay_shift_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpPayShiftMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpPayShiftMasters/{id}",
     *      summary="Update the specified SrpErpPayShiftMaster in storage",
     *      tags={"SrpErpPayShiftMaster"},
     *      description="Update SrpErpPayShiftMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpPayShiftMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpPayShiftMaster")
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
     *                  ref="#/definitions/SrpErpPayShiftMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpPayShiftMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpPayShiftMaster $srpErpPayShiftMaster */
        $srpErpPayShiftMaster = $this->srpErpPayShiftMasterRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftMaster)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_master_not_found'));
        }

        $srpErpPayShiftMaster = $this->srpErpPayShiftMasterRepository->update($input, $id);

        return $this->sendResponse($srpErpPayShiftMaster->toArray(), trans('custom.srperppayshiftmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpPayShiftMasters/{id}",
     *      summary="Remove the specified SrpErpPayShiftMaster from storage",
     *      tags={"SrpErpPayShiftMaster"},
     *      description="Delete SrpErpPayShiftMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpPayShiftMaster",
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
        /** @var SrpErpPayShiftMaster $srpErpPayShiftMaster */
        $srpErpPayShiftMaster = $this->srpErpPayShiftMasterRepository->findWithoutFail($id);

        if (empty($srpErpPayShiftMaster)) {
            return $this->sendError(trans('custom.srp_erp_pay_shift_master_not_found'));
        }

        $srpErpPayShiftMaster->delete();

        return $this->sendSuccess('Srp Erp Pay Shift Master deleted successfully');
    }
}
