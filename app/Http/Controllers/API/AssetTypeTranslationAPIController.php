<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetTypeTranslationAPIRequest;
use App\Http\Requests\API\UpdateAssetTypeTranslationAPIRequest;
use App\Models\AssetTypeTranslation;
use App\Repositories\AssetTypeTranslationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetTypeTranslationController
 * @package App\Http\Controllers\API
 */

class AssetTypeTranslationAPIController extends AppBaseController
{
    /** @var  AssetTypeTranslationRepository */
    private $assetTypeTranslationRepository;

    public function __construct(AssetTypeTranslationRepository $assetTypeTranslationRepo)
    {
        $this->assetTypeTranslationRepository = $assetTypeTranslationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/assetTypeTranslations",
     *      summary="getAssetTypeTranslationList",
     *      tags={"AssetTypeTranslation"},
     *      description="Get all AssetTypeTranslations",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/AssetTypeTranslation")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->assetTypeTranslationRepository->pushCriteria(new RequestCriteria($request));
        $this->assetTypeTranslationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetTypeTranslations = $this->assetTypeTranslationRepository->all();

        return $this->sendResponse($assetTypeTranslations->toArray(), 'Asset Type Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/assetTypeTranslations",
     *      summary="createAssetTypeTranslation",
     *      tags={"AssetTypeTranslation"},
     *      description="Create AssetTypeTranslation",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/AssetTypeTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetTypeTranslationAPIRequest $request)
    {
        $input = $request->all();

        $assetTypeTranslation = $this->assetTypeTranslationRepository->create($input);

        return $this->sendResponse($assetTypeTranslation->toArray(), 'Asset Type Translation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/assetTypeTranslations/{id}",
     *      summary="getAssetTypeTranslationItem",
     *      tags={"AssetTypeTranslation"},
     *      description="Get AssetTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetTypeTranslation",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/AssetTypeTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var AssetTypeTranslation $assetTypeTranslation */
        $assetTypeTranslation = $this->assetTypeTranslationRepository->findWithoutFail($id);

        if (empty($assetTypeTranslation)) {
            return $this->sendError(trans('custom.asset_type_translation_not_found'));
        }

        return $this->sendResponse($assetTypeTranslation->toArray(), 'Asset Type Translation retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/assetTypeTranslations/{id}",
     *      summary="updateAssetTypeTranslation",
     *      tags={"AssetTypeTranslation"},
     *      description="Update AssetTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetTypeTranslation",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/AssetTypeTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetTypeTranslationAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetTypeTranslation $assetTypeTranslation */
        $assetTypeTranslation = $this->assetTypeTranslationRepository->findWithoutFail($id);

        if (empty($assetTypeTranslation)) {
            return $this->sendError(trans('custom.asset_type_translation_not_found'));
        }

        $assetTypeTranslation = $this->assetTypeTranslationRepository->update($input, $id);

        return $this->sendResponse($assetTypeTranslation->toArray(), 'AssetTypeTranslation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/assetTypeTranslations/{id}",
     *      summary="deleteAssetTypeTranslation",
     *      tags={"AssetTypeTranslation"},
     *      description="Delete AssetTypeTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetTypeTranslation",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var AssetTypeTranslation $assetTypeTranslation */
        $assetTypeTranslation = $this->assetTypeTranslationRepository->findWithoutFail($id);

        if (empty($assetTypeTranslation)) {
            return $this->sendError(trans('custom.asset_type_translation_not_found'));
        }

        $assetTypeTranslation->delete();

        return $this->sendSuccess('Asset Type Translation deleted successfully');
    }
}
