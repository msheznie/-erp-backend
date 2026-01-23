<?php
/**
 * =============================================
 * -- File Name : AssetDisposalDetailReferredAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Fixed Asset Master Referback
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDisposalDetailReferredAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalDetailReferredAPIRequest;
use App\Models\AssetDisposalDetailReferred;
use App\Models\AssetDisposalReferred;
use App\Repositories\AssetDisposalDetailReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalDetailReferredController
 * @package App\Http\Controllers\API
 */

class AssetDisposalDetailReferredAPIController extends AppBaseController
{
    /** @var  AssetDisposalDetailReferredRepository */
    private $assetDisposalDetailReferredRepository;

    public function __construct(AssetDisposalDetailReferredRepository $assetDisposalDetailReferredRepo)
    {
        $this->assetDisposalDetailReferredRepository = $assetDisposalDetailReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetailReferreds",
     *      summary="Get a listing of the AssetDisposalDetailReferreds.",
     *      tags={"AssetDisposalDetailReferred"},
     *      description="Get all AssetDisposalDetailReferreds",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalDetailReferred")
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
        $this->assetDisposalDetailReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalDetailReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalDetailReferreds = $this->assetDisposalDetailReferredRepository->all();

        return $this->sendResponse($assetDisposalDetailReferreds->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
    }

    /**
     * @param CreateAssetDisposalDetailReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalDetailReferreds",
     *      summary="Store a newly created AssetDisposalDetailReferred in storage",
     *      tags={"AssetDisposalDetailReferred"},
     *      description="Store AssetDisposalDetailReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetailReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetailReferred")
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
     *                  ref="#/definitions/AssetDisposalDetailReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalDetailReferredAPIRequest $request)
    {
        $input = $request->all();

        $assetDisposalDetailReferreds = $this->assetDisposalDetailReferredRepository->create($input);

        return $this->sendResponse($assetDisposalDetailReferreds->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetailReferreds/{id}",
     *      summary="Display the specified AssetDisposalDetailReferred",
     *      tags={"AssetDisposalDetailReferred"},
     *      description="Get AssetDisposalDetailReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetailReferred",
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
     *                  ref="#/definitions/AssetDisposalDetailReferred"
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
        /** @var AssetDisposalDetailReferred $assetDisposalDetailReferred */
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
        }

        return $this->sendResponse($assetDisposalDetailReferred->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalDetailReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalDetailReferreds/{id}",
     *      summary="Update the specified AssetDisposalDetailReferred in storage",
     *      tags={"AssetDisposalDetailReferred"},
     *      description="Update AssetDisposalDetailReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetailReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetailReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetailReferred")
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
     *                  ref="#/definitions/AssetDisposalDetailReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalDetailReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetDisposalDetailReferred $assetDisposalDetailReferred */
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
        }

        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->update($input, $id);

        return $this->sendResponse($assetDisposalDetailReferred->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalDetailReferreds/{id}",
     *      summary="Remove the specified AssetDisposalDetailReferred from storage",
     *      tags={"AssetDisposalDetailReferred"},
     *      description="Delete AssetDisposalDetailReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetailReferred",
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
        /** @var AssetDisposalDetailReferred $assetDisposalDetailReferred */
        $assetDisposalDetailReferred = $this->assetDisposalDetailReferredRepository->findWithoutFail($id);

        if (empty($assetDisposalDetailReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
        }

        $assetDisposalDetailReferred->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_disposal_details_referreds')]));
    }

    function getAssetDisposalDetailHistory(Request $request)
    {
        $assetDisposalDetail = AssetDisposalDetailReferred::OfMaster($request->assetdisposalMasterAutoID)->where('timesReferred', $request->timesReferred)->with('segment_by', 'item_by')->get();
        if (empty($assetDisposalDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_details')]));
        }
        return $this->sendResponse($assetDisposalDetail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_details')]));
    }
}
