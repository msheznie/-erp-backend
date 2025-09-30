<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetTypeAPIRequest;
use App\Http\Requests\API\UpdateAssetTypeAPIRequest;
use App\Models\AssetType;
use App\Repositories\AssetTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetTypeController
 * @package App\Http\Controllers\API
 */

class AssetTypeAPIController extends AppBaseController
{
    /** @var  AssetTypeRepository */
    private $assetTypeRepository;

    public function __construct(AssetTypeRepository $assetTypeRepo)
    {
        $this->assetTypeRepository = $assetTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetTypes",
     *      summary="Get a listing of the AssetTypes.",
     *      tags={"AssetType"},
     *      description="Get all AssetTypes",
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
     *                  @SWG\Items(ref="#/definitions/AssetType")
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
        $this->assetTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->assetTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetTypes = $this->assetTypeRepository->all();

        return $this->sendResponse($assetTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_types')]));
    }

    /**
     * @param CreateAssetTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetTypes",
     *      summary="Store a newly created AssetType in storage",
     *      tags={"AssetType"},
     *      description="Store AssetType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetType")
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
     *                  ref="#/definitions/AssetType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetTypeAPIRequest $request)
    {
        $input = $request->all();

        $assetTypes = $this->assetTypeRepository->create($input);

        return $this->sendResponse($assetTypes->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetTypes/{id}",
     *      summary="Display the specified AssetType",
     *      tags={"AssetType"},
     *      description="Get AssetType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetType",
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
     *                  ref="#/definitions/AssetType"
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
        /** @var AssetType $assetType */
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_types')]));
        }

        return $this->sendResponse($assetType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_types')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetTypes/{id}",
     *      summary="Update the specified AssetType in storage",
     *      tags={"AssetType"},
     *      description="Update AssetType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetType")
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
     *                  ref="#/definitions/AssetType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetType $assetType */
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            return $this->sendError(trans('custom.asset_type_not_found'));
        }

        $assetType = $this->assetTypeRepository->update($input, $id);

        return $this->sendResponse($assetType->toArray(), trans('custom.not_found', ['attribute' => trans('custom.asset_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetTypes/{id}",
     *      summary="Remove the specified AssetType from storage",
     *      tags={"AssetType"},
     *      description="Delete AssetType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetType",
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
        /** @var AssetType $assetType */
        $assetType = $this->assetTypeRepository->findWithoutFail($id);

        if (empty($assetType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_types')]));
        }

        $assetType->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_types')]));
    }
}
