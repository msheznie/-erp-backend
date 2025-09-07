<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePosSourceMenuMasterAPIRequest;
use App\Http\Requests\API\UpdatePosSourceMenuMasterAPIRequest;
use App\Models\PosSourceMenuMaster;
use App\Repositories\PosSourceMenuMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PosSourceMenuMasterController
 * @package App\Http\Controllers\API
 */

class PosSourceMenuMasterAPIController extends AppBaseController
{
    /** @var  PosSourceMenuMasterRepository */
    private $posSourceMenuMasterRepository;

    public function __construct(PosSourceMenuMasterRepository $posSourceMenuMasterRepo)
    {
        $this->posSourceMenuMasterRepository = $posSourceMenuMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/posSourceMenuMasters",
     *      summary="Get a listing of the PosSourceMenuMasters.",
     *      tags={"PosSourceMenuMaster"},
     *      description="Get all PosSourceMenuMasters",
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
     *                  @SWG\Items(ref="#/definitions/PosSourceMenuMaster")
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
        $this->posSourceMenuMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->posSourceMenuMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $posSourceMenuMasters = $this->posSourceMenuMasterRepository->all();

        return $this->sendResponse($posSourceMenuMasters->toArray(), trans('custom.pos_source_menu_masters_retrieved_successfully'));
    }

    /**
     * @param CreatePosSourceMenuMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posSourceMenuMasters",
     *      summary="Store a newly created PosSourceMenuMaster in storage",
     *      tags={"PosSourceMenuMaster"},
     *      description="Store PosSourceMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosSourceMenuMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosSourceMenuMaster")
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
     *                  ref="#/definitions/PosSourceMenuMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePosSourceMenuMasterAPIRequest $request)
    {
        $input = $request->all();

        $posSourceMenuMaster = $this->posSourceMenuMasterRepository->create($input);

        return $this->sendResponse($posSourceMenuMaster->toArray(), trans('custom.pos_source_menu_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posSourceMenuMasters/{id}",
     *      summary="Display the specified PosSourceMenuMaster",
     *      tags={"PosSourceMenuMaster"},
     *      description="Get PosSourceMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuMaster",
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
     *                  ref="#/definitions/PosSourceMenuMaster"
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
        /** @var PosSourceMenuMaster $posSourceMenuMaster */
        $posSourceMenuMaster = $this->posSourceMenuMasterRepository->findWithoutFail($id);

        if (empty($posSourceMenuMaster)) {
            return $this->sendError(trans('custom.pos_source_menu_master_not_found'));
        }

        return $this->sendResponse($posSourceMenuMaster->toArray(), trans('custom.pos_source_menu_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePosSourceMenuMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/posSourceMenuMasters/{id}",
     *      summary="Update the specified PosSourceMenuMaster in storage",
     *      tags={"PosSourceMenuMaster"},
     *      description="Update PosSourceMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosSourceMenuMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosSourceMenuMaster")
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
     *                  ref="#/definitions/PosSourceMenuMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePosSourceMenuMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PosSourceMenuMaster $posSourceMenuMaster */
        $posSourceMenuMaster = $this->posSourceMenuMasterRepository->findWithoutFail($id);

        if (empty($posSourceMenuMaster)) {
            return $this->sendError(trans('custom.pos_source_menu_master_not_found'));
        }

        $posSourceMenuMaster = $this->posSourceMenuMasterRepository->update($input, $id);

        return $this->sendResponse($posSourceMenuMaster->toArray(), trans('custom.possourcemenumaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posSourceMenuMasters/{id}",
     *      summary="Remove the specified PosSourceMenuMaster from storage",
     *      tags={"PosSourceMenuMaster"},
     *      description="Delete PosSourceMenuMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuMaster",
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
        /** @var PosSourceMenuMaster $posSourceMenuMaster */
        $posSourceMenuMaster = $this->posSourceMenuMasterRepository->findWithoutFail($id);

        if (empty($posSourceMenuMaster)) {
            return $this->sendError(trans('custom.pos_source_menu_master_not_found'));
        }

        $posSourceMenuMaster->delete();

        return $this->sendSuccess('Pos Source Menu Master deleted successfully');
    }
}
