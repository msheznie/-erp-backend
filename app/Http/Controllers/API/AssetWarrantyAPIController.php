<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetWarrantyAPIRequest;
use App\Http\Requests\API\UpdateAssetWarrantyAPIRequest;
use App\Models\AssetWarranty;
use App\Repositories\AssetWarrantyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetWarrantyController
 * @package App\Http\Controllers\API
 */

class AssetWarrantyAPIController extends AppBaseController
{
    /** @var  AssetWarrantyRepository */
    private $assetWarrantyRepository;

    public function __construct(AssetWarrantyRepository $assetWarrantyRepo)
    {
        $this->assetWarrantyRepository = $assetWarrantyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/assetWarranties",
     *      summary="getAssetWarrantyList",
     *      tags={"AssetWarranty"},
     *      description="Get all AssetWarranties",
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
     *                  @OA\Items(ref="#/definitions/AssetWarranty")
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
        $this->assetWarrantyRepository->pushCriteria(new RequestCriteria($request));
        $this->assetWarrantyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetWarranties = $this->assetWarrantyRepository->all();

        return $this->sendResponse($assetWarranties->toArray(), 'Asset Warranties retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/assetWarranties",
     *      summary="createAssetWarranty",
     *      tags={"AssetWarranty"},
     *      description="Create AssetWarranty",
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
     *                  ref="#/definitions/AssetWarranty"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetWarrantyAPIRequest $request)
    {
        $input = $request->all();

        $assetWarranty = $this->assetWarrantyRepository->create($input);

        return $this->sendResponse($assetWarranty->toArray(), 'Asset Warranty saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/assetWarranties/{id}",
     *      summary="getAssetWarrantyItem",
     *      tags={"AssetWarranty"},
     *      description="Get AssetWarranty",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetWarranty",
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
     *                  ref="#/definitions/AssetWarranty"
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
        /** @var AssetWarranty $assetWarranty */
        $assetWarranty = $this->assetWarrantyRepository->findWithoutFail($id);

        if (empty($assetWarranty)) {
            return $this->sendError('Asset Warranty not found');
        }

        return $this->sendResponse($assetWarranty->toArray(), 'Asset Warranty retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/assetWarranties/{id}",
     *      summary="updateAssetWarranty",
     *      tags={"AssetWarranty"},
     *      description="Update AssetWarranty",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetWarranty",
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
     *                  ref="#/definitions/AssetWarranty"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetWarrantyAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetWarranty $assetWarranty */
        $assetWarranty = $this->assetWarrantyRepository->findWithoutFail($id);

        if (empty($assetWarranty)) {
            return $this->sendError('Asset Warranty not found');
        }

        $assetWarranty = $this->assetWarrantyRepository->update($input, $id);

        return $this->sendResponse($assetWarranty->toArray(), 'AssetWarranty updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/assetWarranties/{id}",
     *      summary="deleteAssetWarranty",
     *      tags={"AssetWarranty"},
     *      description="Delete AssetWarranty",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of AssetWarranty",
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
        /** @var AssetWarranty $assetWarranty */
        $assetWarranty = $this->assetWarrantyRepository->findWithoutFail($id);

        if (empty($assetWarranty)) {
            return $this->sendError('Asset Warranty not found');
        }

        $assetWarranty->delete();

        return $this->sendSuccess('Asset Warranty deleted successfully');
    }
}
