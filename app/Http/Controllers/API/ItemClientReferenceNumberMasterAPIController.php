<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemClientReferenceNumberMasterAPIRequest;
use App\Http\Requests\API\UpdateItemClientReferenceNumberMasterAPIRequest;
use App\Models\ItemClientReferenceNumberMaster;
use App\Repositories\ItemClientReferenceNumberMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemClientReferenceNumberMasterController
 * @package App\Http\Controllers\API
 */

class ItemClientReferenceNumberMasterAPIController extends AppBaseController
{
    /** @var  ItemClientReferenceNumberMasterRepository */
    private $itemClientReferenceNumberMasterRepository;

    public function __construct(ItemClientReferenceNumberMasterRepository $itemClientReferenceNumberMasterRepo)
    {
        $this->itemClientReferenceNumberMasterRepository = $itemClientReferenceNumberMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemClientReferenceNumberMasters",
     *      summary="Get a listing of the ItemClientReferenceNumberMasters.",
     *      tags={"ItemClientReferenceNumberMaster"},
     *      description="Get all ItemClientReferenceNumberMasters",
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
     *                  @SWG\Items(ref="#/definitions/ItemClientReferenceNumberMaster")
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
        $this->itemClientReferenceNumberMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemClientReferenceNumberMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemClientReferenceNumberMasters = $this->itemClientReferenceNumberMasterRepository->all();

        return $this->sendResponse($itemClientReferenceNumberMasters->toArray(), trans('custom.item_client_reference_number_masters_retrieved_suc'));
    }

    /**
     * @param CreateItemClientReferenceNumberMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemClientReferenceNumberMasters",
     *      summary="Store a newly created ItemClientReferenceNumberMaster in storage",
     *      tags={"ItemClientReferenceNumberMaster"},
     *      description="Store ItemClientReferenceNumberMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemClientReferenceNumberMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemClientReferenceNumberMaster")
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
     *                  ref="#/definitions/ItemClientReferenceNumberMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemClientReferenceNumberMasterAPIRequest $request)
    {
        $input = $request->all();

        $itemClientReferenceNumberMasters = $this->itemClientReferenceNumberMasterRepository->create($input);

        return $this->sendResponse($itemClientReferenceNumberMasters->toArray(), trans('custom.item_client_reference_number_master_saved_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemClientReferenceNumberMasters/{id}",
     *      summary="Display the specified ItemClientReferenceNumberMaster",
     *      tags={"ItemClientReferenceNumberMaster"},
     *      description="Get ItemClientReferenceNumberMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemClientReferenceNumberMaster",
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
     *                  ref="#/definitions/ItemClientReferenceNumberMaster"
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
        /** @var ItemClientReferenceNumberMaster $itemClientReferenceNumberMaster */
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            return $this->sendError(trans('custom.item_client_reference_number_master_not_found'));
        }

        return $this->sendResponse($itemClientReferenceNumberMaster->toArray(), trans('custom.item_client_reference_number_master_retrieved_succ'));
    }

    /**
     * @param int $id
     * @param UpdateItemClientReferenceNumberMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemClientReferenceNumberMasters/{id}",
     *      summary="Update the specified ItemClientReferenceNumberMaster in storage",
     *      tags={"ItemClientReferenceNumberMaster"},
     *      description="Update ItemClientReferenceNumberMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemClientReferenceNumberMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemClientReferenceNumberMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemClientReferenceNumberMaster")
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
     *                  ref="#/definitions/ItemClientReferenceNumberMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemClientReferenceNumberMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemClientReferenceNumberMaster $itemClientReferenceNumberMaster */
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            return $this->sendError(trans('custom.item_client_reference_number_master_not_found'));
        }

        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->update($input, $id);

        return $this->sendResponse($itemClientReferenceNumberMaster->toArray(), trans('custom.itemclientreferencenumbermaster_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemClientReferenceNumberMasters/{id}",
     *      summary="Remove the specified ItemClientReferenceNumberMaster from storage",
     *      tags={"ItemClientReferenceNumberMaster"},
     *      description="Delete ItemClientReferenceNumberMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemClientReferenceNumberMaster",
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
        /** @var ItemClientReferenceNumberMaster $itemClientReferenceNumberMaster */
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            return $this->sendError(trans('custom.item_client_reference_number_master_not_found'));
        }

        $itemClientReferenceNumberMaster->delete();

        return $this->sendResponse($id, trans('custom.item_client_reference_number_master_deleted_succes'));
    }
}
