<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSJvMasterAPIRequest;
use App\Http\Requests\API\UpdateHRMSJvMasterAPIRequest;
use App\Models\HRMSJvMaster;
use App\Repositories\HRMSJvMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSJvMasterController
 * @package App\Http\Controllers\API
 */

class HRMSJvMasterAPIController extends AppBaseController
{
    /** @var  HRMSJvMasterRepository */
    private $hRMSJvMasterRepository;

    public function __construct(HRMSJvMasterRepository $hRMSJvMasterRepo)
    {
        $this->hRMSJvMasterRepository = $hRMSJvMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSJvMasters",
     *      summary="Get a listing of the HRMSJvMasters.",
     *      tags={"HRMSJvMaster"},
     *      description="Get all HRMSJvMasters",
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
     *                  @SWG\Items(ref="#/definitions/HRMSJvMaster")
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
        $this->hRMSJvMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSJvMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSJvMasters = $this->hRMSJvMasterRepository->all();

        return $this->sendResponse($hRMSJvMasters->toArray(), trans('custom.h_r_m_s_jv_masters_retrieved_successfully'));
    }

    /**
     * @param CreateHRMSJvMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSJvMasters",
     *      summary="Store a newly created HRMSJvMaster in storage",
     *      tags={"HRMSJvMaster"},
     *      description="Store HRMSJvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSJvMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSJvMaster")
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
     *                  ref="#/definitions/HRMSJvMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSJvMasterAPIRequest $request)
    {
        $input = $request->all();

        $hRMSJvMasters = $this->hRMSJvMasterRepository->create($input);

        return $this->sendResponse($hRMSJvMasters->toArray(), trans('custom.h_r_m_s_jv_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSJvMasters/{id}",
     *      summary="Display the specified HRMSJvMaster",
     *      tags={"HRMSJvMaster"},
     *      description="Get HRMSJvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvMaster",
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
     *                  ref="#/definitions/HRMSJvMaster"
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
        /** @var HRMSJvMaster $hRMSJvMaster */
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_master_not_found'));
        }

        return $this->sendResponse($hRMSJvMaster->toArray(), trans('custom.h_r_m_s_jv_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSJvMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSJvMasters/{id}",
     *      summary="Update the specified HRMSJvMaster in storage",
     *      tags={"HRMSJvMaster"},
     *      description="Update HRMSJvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSJvMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSJvMaster")
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
     *                  ref="#/definitions/HRMSJvMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSJvMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSJvMaster $hRMSJvMaster */
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_master_not_found'));
        }

        $hRMSJvMaster = $this->hRMSJvMasterRepository->update($input, $id);

        return $this->sendResponse($hRMSJvMaster->toArray(), trans('custom.hrmsjvmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSJvMasters/{id}",
     *      summary="Remove the specified HRMSJvMaster from storage",
     *      tags={"HRMSJvMaster"},
     *      description="Delete HRMSJvMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSJvMaster",
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
        /** @var HRMSJvMaster $hRMSJvMaster */
        $hRMSJvMaster = $this->hRMSJvMasterRepository->findWithoutFail($id);

        if (empty($hRMSJvMaster)) {
            return $this->sendError(trans('custom.h_r_m_s_jv_master_not_found'));
        }

        $hRMSJvMaster->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_jv_master_deleted_successfully'));
    }
}
