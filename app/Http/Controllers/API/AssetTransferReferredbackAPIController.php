<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetTransferReferredbackAPIRequest;
use App\Http\Requests\API\UpdateAssetTransferReferredbackAPIRequest;
use App\Models\AssetTransferReferredback;
use App\Repositories\AssetTransferReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetTransferReferredbackController
 * @package App\Http\Controllers\API
 */

class AssetTransferReferredbackAPIController extends AppBaseController
{
    /** @var  AssetTransferReferredbackRepository */
    private $assetTransferReferredbackRepository;

    public function __construct(AssetTransferReferredbackRepository $assetTransferReferredbackRepo)
    {
        $this->assetTransferReferredbackRepository = $assetTransferReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetTransferReferredbacks",
     *      summary="Get a listing of the AssetTransferReferredbacks.",
     *      tags={"AssetTransferReferredback"},
     *      description="Get all AssetTransferReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/AssetTransferReferredback")
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
        $this->assetTransferReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->assetTransferReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetTransferReferredbacks = $this->assetTransferReferredbackRepository->all();

        return $this->sendResponse($assetTransferReferredbacks->toArray(), trans('custom.asset_transfer_referredbacks_retrieved_successfull'));
    }

    /**
     * @param CreateAssetTransferReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetTransferReferredbacks",
     *      summary="Store a newly created AssetTransferReferredback in storage",
     *      tags={"AssetTransferReferredback"},
     *      description="Store AssetTransferReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetTransferReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetTransferReferredback")
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
     *                  ref="#/definitions/AssetTransferReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetTransferReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $assetTransferReferredback = $this->assetTransferReferredbackRepository->create($input);

        return $this->sendResponse($assetTransferReferredback->toArray(), trans('custom.asset_transfer_referredback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetTransferReferredbacks/{id}",
     *      summary="Display the specified AssetTransferReferredback",
     *      tags={"AssetTransferReferredback"},
     *      description="Get AssetTransferReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetTransferReferredback",
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
     *                  ref="#/definitions/AssetTransferReferredback"
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
        /** @var AssetTransferReferredback $assetTransferReferredback */
        $assetTransferReferredback = $this->assetTransferReferredbackRepository->findWithoutFail($id);

        if (empty($assetTransferReferredback)) {
            return $this->sendError(trans('custom.asset_transfer_referredback_not_found'));
        }

        return $this->sendResponse($assetTransferReferredback->toArray(), trans('custom.asset_transfer_referredback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateAssetTransferReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetTransferReferredbacks/{id}",
     *      summary="Update the specified AssetTransferReferredback in storage",
     *      tags={"AssetTransferReferredback"},
     *      description="Update AssetTransferReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetTransferReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetTransferReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetTransferReferredback")
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
     *                  ref="#/definitions/AssetTransferReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetTransferReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetTransferReferredback $assetTransferReferredback */
        $assetTransferReferredback = $this->assetTransferReferredbackRepository->findWithoutFail($id);

        if (empty($assetTransferReferredback)) {
            return $this->sendError(trans('custom.asset_transfer_referredback_not_found'));
        }

        $assetTransferReferredback = $this->assetTransferReferredbackRepository->update($input, $id);

        return $this->sendResponse($assetTransferReferredback->toArray(), trans('custom.assettransferreferredback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetTransferReferredbacks/{id}",
     *      summary="Remove the specified AssetTransferReferredback from storage",
     *      tags={"AssetTransferReferredback"},
     *      description="Delete AssetTransferReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetTransferReferredback",
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
        /** @var AssetTransferReferredback $assetTransferReferredback */
        $assetTransferReferredback = $this->assetTransferReferredbackRepository->findWithoutFail($id);

        if (empty($assetTransferReferredback)) {
            return $this->sendError(trans('custom.asset_transfer_referredback_not_found'));
        }

        $assetTransferReferredback->delete();

        return $this->sendSuccess('Asset Transfer Referredback deleted successfully');
    }
    public function getAssetTransferAmendHistory(Request $request){
        $input = $request->all();
        $assetTransferAutoID = $input['assetTransferID'];
        $assetTransferAmendHistory = AssetTransferReferredback::with(['confirmed_by','approved_by','created_by', 'modified_by'])
        ->where('id',$assetTransferAutoID)
        ->get();
        return $this->sendResponse($assetTransferAmendHistory, trans('custom.asset_transfer_retrieved_successfully'));

    }
    public function fetchAssetTransferMasterAmend($id){ 
        $assetTransfer = AssetTransferReferredback::with(['confirmed_by'])->where('assetTransferMasterRefferedBackID', $id)->first();
        return $this->sendResponse($assetTransfer, 'Asset Request Transfer Referredback data');
    }
}
