<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizatioDetReferredAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizatioDetReferredAPIRequest;
use App\Models\AssetCapitalizatioDetReferred;
use App\Repositories\AssetCapitalizatioDetReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizatioDetReferredController
 * @package App\Http\Controllers\API
 */

class AssetCapitalizatioDetReferredAPIController extends AppBaseController
{
    /** @var  AssetCapitalizatioDetReferredRepository */
    private $assetCapitalizatioDetReferredRepository;

    public function __construct(AssetCapitalizatioDetReferredRepository $assetCapitalizatioDetReferredRepo)
    {
        $this->assetCapitalizatioDetReferredRepository = $assetCapitalizatioDetReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizatioDetReferreds",
     *      summary="Get a listing of the AssetCapitalizatioDetReferreds.",
     *      tags={"AssetCapitalizatioDetReferred"},
     *      description="Get all AssetCapitalizatioDetReferreds",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalizatioDetReferred")
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
        $this->assetCapitalizatioDetReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizatioDetReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizatioDetReferreds = $this->assetCapitalizatioDetReferredRepository->all();

        return $this->sendResponse($assetCapitalizatioDetReferreds->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
    }

    /**
     * @param CreateAssetCapitalizatioDetReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizatioDetReferreds",
     *      summary="Store a newly created AssetCapitalizatioDetReferred in storage",
     *      tags={"AssetCapitalizatioDetReferred"},
     *      description="Store AssetCapitalizatioDetReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizatioDetReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizatioDetReferred")
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
     *                  ref="#/definitions/AssetCapitalizatioDetReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizatioDetReferredAPIRequest $request)
    {
        $input = $request->all();

        $assetCapitalizatioDetReferreds = $this->assetCapitalizatioDetReferredRepository->create($input);

        return $this->sendResponse($assetCapitalizatioDetReferreds->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizatioDetReferreds/{id}",
     *      summary="Display the specified AssetCapitalizatioDetReferred",
     *      tags={"AssetCapitalizatioDetReferred"},
     *      description="Get AssetCapitalizatioDetReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizatioDetReferred",
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
     *                  ref="#/definitions/AssetCapitalizatioDetReferred"
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
        /** @var AssetCapitalizatioDetReferred $assetCapitalizatioDetReferred */
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
        }

        return $this->sendResponse($assetCapitalizatioDetReferred->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizatioDetReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizatioDetReferreds/{id}",
     *      summary="Update the specified AssetCapitalizatioDetReferred in storage",
     *      tags={"AssetCapitalizatioDetReferred"},
     *      description="Update AssetCapitalizatioDetReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizatioDetReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizatioDetReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizatioDetReferred")
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
     *                  ref="#/definitions/AssetCapitalizatioDetReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizatioDetReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalizatioDetReferred $assetCapitalizatioDetReferred */
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
        }

        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->update($input, $id);

        return $this->sendResponse($assetCapitalizatioDetReferred->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizatioDetReferreds/{id}",
     *      summary="Remove the specified AssetCapitalizatioDetReferred from storage",
     *      tags={"AssetCapitalizatioDetReferred"},
     *      description="Delete AssetCapitalizatioDetReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizatioDetReferred",
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
        /** @var AssetCapitalizatioDetReferred $assetCapitalizatioDetReferred */
        $assetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizatioDetReferred)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
        }

        $assetCapitalizatioDetReferred->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_capitalizatio_det_referreds')]));
    }

    public function getCapitalizationDetailsHistory(Request $request)
    {
        $id = $request->capitalizationID;
        $assetCapitalizationDetail = $this->assetCapitalizatioDetReferredRepository->with(['segment'])->findWhere(['capitalizationID' => $id,'timesReferred' => $request->timesReferred]);
        return $this->sendResponse($assetCapitalizationDetail, trans('custom.retrieve', ['attribute' => trans('custom.details')]));
    }
}
