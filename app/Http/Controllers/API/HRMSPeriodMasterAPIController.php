<?php
/**
=============================================
-- File Name : HRMSPeriodMasterAPIController.php
-- Project Name : ERP
-- Module Name :  LEAVE
-- Author : Mohamed Rilwan
-- Create date : 19 - November 2019
-- Description :
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSPeriodMasterAPIRequest;
use App\Http\Requests\API\UpdateHRMSPeriodMasterAPIRequest;
use App\Models\HRMSPeriodMaster;
use App\Repositories\HRMSPeriodMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSPeriodMasterController
 * @package App\Http\Controllers\API
 */

class HRMSPeriodMasterAPIController extends AppBaseController
{
    /** @var  HRMSPeriodMasterRepository */
    private $hRMSPeriodMasterRepository;

    public function __construct(HRMSPeriodMasterRepository $hRMSPeriodMasterRepo)
    {
        $this->hRMSPeriodMasterRepository = $hRMSPeriodMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSPeriodMasters",
     *      summary="Get a listing of the HRMSPeriodMasters.",
     *      tags={"HRMSPeriodMaster"},
     *      description="Get all HRMSPeriodMasters",
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
     *                  @SWG\Items(ref="#/definitions/HRMSPeriodMaster")
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
        $this->hRMSPeriodMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSPeriodMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSPeriodMasters = $this->hRMSPeriodMasterRepository->all();

        return $this->sendResponse($hRMSPeriodMasters->toArray(), trans('custom.h_r_m_s_period_masters_retrieved_successfully'));
    }

    /**
     * @param CreateHRMSPeriodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSPeriodMasters",
     *      summary="Store a newly created HRMSPeriodMaster in storage",
     *      tags={"HRMSPeriodMaster"},
     *      description="Store HRMSPeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSPeriodMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSPeriodMaster")
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
     *                  ref="#/definitions/HRMSPeriodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSPeriodMasterAPIRequest $request)
    {
        $input = $request->all();

        $hRMSPeriodMaster = $this->hRMSPeriodMasterRepository->create($input);

        return $this->sendResponse($hRMSPeriodMaster->toArray(), trans('custom.h_r_m_s_period_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSPeriodMasters/{id}",
     *      summary="Display the specified HRMSPeriodMaster",
     *      tags={"HRMSPeriodMaster"},
     *      description="Get HRMSPeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPeriodMaster",
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
     *                  ref="#/definitions/HRMSPeriodMaster"
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
        /** @var HRMSPeriodMaster $hRMSPeriodMaster */
        $hRMSPeriodMaster = $this->hRMSPeriodMasterRepository->findWithoutFail($id);

        if (empty($hRMSPeriodMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_period_master_not_found'));
        }

        return $this->sendResponse($hRMSPeriodMaster->toArray(), trans('custom.h_r_m_s_period_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSPeriodMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSPeriodMasters/{id}",
     *      summary="Update the specified HRMSPeriodMaster in storage",
     *      tags={"HRMSPeriodMaster"},
     *      description="Update HRMSPeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPeriodMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSPeriodMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSPeriodMaster")
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
     *                  ref="#/definitions/HRMSPeriodMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSPeriodMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSPeriodMaster $hRMSPeriodMaster */
        $hRMSPeriodMaster = $this->hRMSPeriodMasterRepository->findWithoutFail($id);

        if (empty($hRMSPeriodMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_period_master_not_found'));
        }

        $hRMSPeriodMaster = $this->hRMSPeriodMasterRepository->update($input, $id);

        return $this->sendResponse($hRMSPeriodMaster->toArray(), trans('custom.hrmsperiodmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSPeriodMasters/{id}",
     *      summary="Remove the specified HRMSPeriodMaster from storage",
     *      tags={"HRMSPeriodMaster"},
     *      description="Delete HRMSPeriodMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSPeriodMaster",
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
        /** @var HRMSPeriodMaster $hRMSPeriodMaster */
        $hRMSPeriodMaster = $this->hRMSPeriodMasterRepository->findWithoutFail($id);

        if (empty($hRMSPeriodMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_period_master_not_found'));
        }

        $hRMSPeriodMaster->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_period_master_deleted_successfully'));
    }
}
