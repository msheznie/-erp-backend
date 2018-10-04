<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDisposalDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalDetailAPIRequest;
use App\Models\AssetDisposalDetail;
use App\Repositories\AssetDisposalDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalDetailController
 * @package App\Http\Controllers\API
 */

class AssetDisposalDetailAPIController extends AppBaseController
{
    /** @var  AssetDisposalDetailRepository */
    private $assetDisposalDetailRepository;

    public function __construct(AssetDisposalDetailRepository $assetDisposalDetailRepo)
    {
        $this->assetDisposalDetailRepository = $assetDisposalDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetails",
     *      summary="Get a listing of the AssetDisposalDetails.",
     *      tags={"AssetDisposalDetail"},
     *      description="Get all AssetDisposalDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalDetail")
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
        $this->assetDisposalDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalDetails = $this->assetDisposalDetailRepository->all();

        return $this->sendResponse($assetDisposalDetails->toArray(), 'Asset Disposal Details retrieved successfully');
    }

    /**
     * @param CreateAssetDisposalDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalDetails",
     *      summary="Store a newly created AssetDisposalDetail in storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Store AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetail")
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
     *                  ref="#/definitions/AssetDisposalDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalDetailAPIRequest $request)
    {
        $input = $request->all();

        $assetDisposalDetails = $this->assetDisposalDetailRepository->create($input);

        return $this->sendResponse($assetDisposalDetails->toArray(), 'Asset Disposal Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Display the specified AssetDisposalDetail",
     *      tags={"AssetDisposalDetail"},
     *      description="Get AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
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
     *                  ref="#/definitions/AssetDisposalDetail"
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
        /** @var AssetDisposalDetail $assetDisposalDetail */
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }

        return $this->sendResponse($assetDisposalDetail->toArray(), 'Asset Disposal Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Update the specified AssetDisposalDetail in storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Update AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalDetail")
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
     *                  ref="#/definitions/AssetDisposalDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetDisposalDetail $assetDisposalDetail */
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }

        $assetDisposalDetail = $this->assetDisposalDetailRepository->update($input, $id);

        return $this->sendResponse($assetDisposalDetail->toArray(), 'AssetDisposalDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalDetails/{id}",
     *      summary="Remove the specified AssetDisposalDetail from storage",
     *      tags={"AssetDisposalDetail"},
     *      description="Delete AssetDisposalDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalDetail",
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
        /** @var AssetDisposalDetail $assetDisposalDetail */
        $assetDisposalDetail = $this->assetDisposalDetailRepository->findWithoutFail($id);

        if (empty($assetDisposalDetail)) {
            return $this->sendError('Asset Disposal Detail not found');
        }

        $assetDisposalDetail->delete();

        return $this->sendResponse($id, 'Asset Disposal Detail deleted successfully');
    }
}
