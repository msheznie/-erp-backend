<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSMappingMasterAPIRequest;
use App\Http\Requests\API\UpdatePOSMappingMasterAPIRequest;
use App\Models\POSMappingMaster;
use App\Repositories\POSMappingMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSMappingMasterController
 * @package App\Http\Controllers\API
 */

class POSMappingMasterAPIController extends AppBaseController
{
    /** @var  POSMappingMasterRepository */
    private $pOSMappingMasterRepository;

    public function __construct(POSMappingMasterRepository $pOSMappingMasterRepo)
    {
        $this->pOSMappingMasterRepository = $pOSMappingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSMappingMasters",
     *      summary="Get a listing of the POSMappingMasters.",
     *      tags={"POSMappingMaster"},
     *      description="Get all POSMappingMasters",
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
     *                  @SWG\Items(ref="#/definitions/POSMappingMaster")
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
        $this->pOSMappingMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSMappingMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSMappingMasters = $this->pOSMappingMasterRepository->all();

        return $this->sendResponse($pOSMappingMasters->toArray(), trans('custom.p_o_s_mapping_masters_retrieved_successfully'));
    }

    /**
     * @param CreatePOSMappingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSMappingMasters",
     *      summary="Store a newly created POSMappingMaster in storage",
     *      tags={"POSMappingMaster"},
     *      description="Store POSMappingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSMappingMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSMappingMaster")
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
     *                  ref="#/definitions/POSMappingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSMappingMasterAPIRequest $request)
    {
        $input = $request->all();

        $pOSMappingMaster = $this->pOSMappingMasterRepository->create($input);

        return $this->sendResponse($pOSMappingMaster->toArray(), trans('custom.p_o_s_mapping_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSMappingMasters/{id}",
     *      summary="Display the specified POSMappingMaster",
     *      tags={"POSMappingMaster"},
     *      description="Get POSMappingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingMaster",
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
     *                  ref="#/definitions/POSMappingMaster"
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
        /** @var POSMappingMaster $pOSMappingMaster */
        $pOSMappingMaster = $this->pOSMappingMasterRepository->findWithoutFail($id);

        if (empty($pOSMappingMaster)) {
            return $this->sendError(trans('custom.p_o_s_mapping_master_not_found'));
        }

        return $this->sendResponse($pOSMappingMaster->toArray(), trans('custom.p_o_s_mapping_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSMappingMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSMappingMasters/{id}",
     *      summary="Update the specified POSMappingMaster in storage",
     *      tags={"POSMappingMaster"},
     *      description="Update POSMappingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSMappingMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSMappingMaster")
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
     *                  ref="#/definitions/POSMappingMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSMappingMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSMappingMaster $pOSMappingMaster */
        $pOSMappingMaster = $this->pOSMappingMasterRepository->findWithoutFail($id);

        if (empty($pOSMappingMaster)) {
            return $this->sendError(trans('custom.p_o_s_mapping_master_not_found'));
        }

        $pOSMappingMaster = $this->pOSMappingMasterRepository->update($input, $id);

        return $this->sendResponse($pOSMappingMaster->toArray(), trans('custom.posmappingmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSMappingMasters/{id}",
     *      summary="Remove the specified POSMappingMaster from storage",
     *      tags={"POSMappingMaster"},
     *      description="Delete POSMappingMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSMappingMaster",
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
        /** @var POSMappingMaster $pOSMappingMaster */
        $pOSMappingMaster = $this->pOSMappingMasterRepository->findWithoutFail($id);

        if (empty($pOSMappingMaster)) {
            return $this->sendError(trans('custom.p_o_s_mapping_master_not_found'));
        }

        $pOSMappingMaster->delete();

        return $this->sendSuccess('P O S Mapping Master deleted successfully');
    }
}
