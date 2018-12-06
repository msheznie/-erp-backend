<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationReferredAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationReferredAPIRequest;
use App\Models\AssetCapitalizationReferred;
use App\Repositories\AssetCapitalizationReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizationReferredController
 * @package App\Http\Controllers\API
 */

class AssetCapitalizationReferredAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationReferredRepository */
    private $assetCapitalizationReferredRepository;

    public function __construct(AssetCapitalizationReferredRepository $assetCapitalizationReferredRepo)
    {
        $this->assetCapitalizationReferredRepository = $assetCapitalizationReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationReferreds",
     *      summary="Get a listing of the AssetCapitalizationReferreds.",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Get all AssetCapitalizationReferreds",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalizationReferred")
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
        $this->assetCapitalizationReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizationReferreds = $this->assetCapitalizationReferredRepository->all();

        return $this->sendResponse($assetCapitalizationReferreds->toArray(), 'Asset Capitalization Referreds retrieved successfully');
    }

    /**
     * @param CreateAssetCapitalizationReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizationReferreds",
     *      summary="Store a newly created AssetCapitalizationReferred in storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Store AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationReferred")
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationReferredAPIRequest $request)
    {
        $input = $request->all();

        $assetCapitalizationReferreds = $this->assetCapitalizationReferredRepository->create($input);

        return $this->sendResponse($assetCapitalizationReferreds->toArray(), 'Asset Capitalization Referred saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Display the specified AssetCapitalizationReferred",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Get AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
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
        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError('Asset Capitalization Referred not found');
        }

        return $this->sendResponse($assetCapitalizationReferred->toArray(), 'Asset Capitalization Referred retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Update the specified AssetCapitalizationReferred in storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Update AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationReferred")
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
     *                  ref="#/definitions/AssetCapitalizationReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError('Asset Capitalization Referred not found');
        }

        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->update($input, $id);

        return $this->sendResponse($assetCapitalizationReferred->toArray(), 'AssetCapitalizationReferred updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizationReferreds/{id}",
     *      summary="Remove the specified AssetCapitalizationReferred from storage",
     *      tags={"AssetCapitalizationReferred"},
     *      description="Delete AssetCapitalizationReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationReferred",
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
        /** @var AssetCapitalizationReferred $assetCapitalizationReferred */
        $assetCapitalizationReferred = $this->assetCapitalizationReferredRepository->findWithoutFail($id);

        if (empty($assetCapitalizationReferred)) {
            return $this->sendError('Asset Capitalization Referred not found');
        }

        $assetCapitalizationReferred->delete();

        return $this->sendResponse($id, 'Asset Capitalization Referred deleted successfully');
    }
}
