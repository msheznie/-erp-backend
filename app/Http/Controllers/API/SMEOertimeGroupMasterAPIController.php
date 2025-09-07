<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSMEOertimeGroupMasterAPIRequest;
use App\Http\Requests\API\UpdateSMEOertimeGroupMasterAPIRequest;
use App\Models\SMEOertimeGroupMaster;
use App\Repositories\SMEOertimeGroupMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SMEOertimeGroupMasterController
 * @package App\Http\Controllers\API
 */

class SMEOertimeGroupMasterAPIController extends AppBaseController
{
    /** @var  SMEOertimeGroupMasterRepository */
    private $sMEOertimeGroupMasterRepository;

    public function __construct(SMEOertimeGroupMasterRepository $sMEOertimeGroupMasterRepo)
    {
        $this->sMEOertimeGroupMasterRepository = $sMEOertimeGroupMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEOertimeGroupMasters",
     *      summary="Get a listing of the SMEOertimeGroupMasters.",
     *      tags={"SMEOertimeGroupMaster"},
     *      description="Get all SMEOertimeGroupMasters",
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
     *                  @SWG\Items(ref="#/definitions/SMEOertimeGroupMaster")
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
        $this->sMEOertimeGroupMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sMEOertimeGroupMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sMEOertimeGroupMasters = $this->sMEOertimeGroupMasterRepository->all();

        return $this->sendResponse($sMEOertimeGroupMasters->toArray(), trans('custom.s_m_e_oertime_group_masters_retrieved_successfully'));
    }

    /**
     * @param CreateSMEOertimeGroupMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sMEOertimeGroupMasters",
     *      summary="Store a newly created SMEOertimeGroupMaster in storage",
     *      tags={"SMEOertimeGroupMaster"},
     *      description="Store SMEOertimeGroupMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEOertimeGroupMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEOertimeGroupMaster")
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
     *                  ref="#/definitions/SMEOertimeGroupMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSMEOertimeGroupMasterAPIRequest $request)
    {
        $input = $request->all();

        $sMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepository->create($input);

        return $this->sendResponse($sMEOertimeGroupMaster->toArray(), trans('custom.s_m_e_oertime_group_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sMEOertimeGroupMasters/{id}",
     *      summary="Display the specified SMEOertimeGroupMaster",
     *      tags={"SMEOertimeGroupMaster"},
     *      description="Get SMEOertimeGroupMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEOertimeGroupMaster",
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
     *                  ref="#/definitions/SMEOertimeGroupMaster"
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
        /** @var SMEOertimeGroupMaster $sMEOertimeGroupMaster */
        $sMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepository->findWithoutFail($id);

        if (empty($sMEOertimeGroupMaster)) {
            return $this->sendError(trans('custom.s_m_e_oertime_group_master_not_found'));
        }

        return $this->sendResponse($sMEOertimeGroupMaster->toArray(), trans('custom.s_m_e_oertime_group_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSMEOertimeGroupMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sMEOertimeGroupMasters/{id}",
     *      summary="Update the specified SMEOertimeGroupMaster in storage",
     *      tags={"SMEOertimeGroupMaster"},
     *      description="Update SMEOertimeGroupMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEOertimeGroupMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SMEOertimeGroupMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SMEOertimeGroupMaster")
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
     *                  ref="#/definitions/SMEOertimeGroupMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSMEOertimeGroupMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SMEOertimeGroupMaster $sMEOertimeGroupMaster */
        $sMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepository->findWithoutFail($id);

        if (empty($sMEOertimeGroupMaster)) {
            return $this->sendError(trans('custom.s_m_e_oertime_group_master_not_found'));
        }

        $sMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepository->update($input, $id);

        return $this->sendResponse($sMEOertimeGroupMaster->toArray(), trans('custom.smeoertimegroupmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sMEOertimeGroupMasters/{id}",
     *      summary="Remove the specified SMEOertimeGroupMaster from storage",
     *      tags={"SMEOertimeGroupMaster"},
     *      description="Delete SMEOertimeGroupMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SMEOertimeGroupMaster",
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
        /** @var SMEOertimeGroupMaster $sMEOertimeGroupMaster */
        $sMEOertimeGroupMaster = $this->sMEOertimeGroupMasterRepository->findWithoutFail($id);

        if (empty($sMEOertimeGroupMaster)) {
            return $this->sendError(trans('custom.s_m_e_oertime_group_master_not_found'));
        }

        $sMEOertimeGroupMaster->delete();

        return $this->sendSuccess('S M E Oertime Group Master deleted successfully');
    }
}
