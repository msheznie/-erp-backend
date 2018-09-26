<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationDetailAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationDetailAPIRequest;
use App\Models\AssetCapitalizationDetail;
use App\Repositories\AssetCapitalizationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizationDetailController
 * @package App\Http\Controllers\API
 */

class AssetCapitalizationDetailAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationDetailRepository */
    private $assetCapitalizationDetailRepository;

    public function __construct(AssetCapitalizationDetailRepository $assetCapitalizationDetailRepo)
    {
        $this->assetCapitalizationDetailRepository = $assetCapitalizationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationDetails",
     *      summary="Get a listing of the AssetCapitalizationDetails.",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Get all AssetCapitalizationDetails",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalizationDetail")
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
        $this->assetCapitalizationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizationDetails = $this->assetCapitalizationDetailRepository->all();

        return $this->sendResponse($assetCapitalizationDetails->toArray(), 'Asset Capitalization Details retrieved successfully');
    }

    /**
     * @param CreateAssetCapitalizationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizationDetails",
     *      summary="Store a newly created AssetCapitalizationDetail in storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Store AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationDetail")
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationDetailAPIRequest $request)
    {
        $input = $request->all();

        $assetCapitalizationDetails = $this->assetCapitalizationDetailRepository->create($input);

        return $this->sendResponse($assetCapitalizationDetails->toArray(), 'Asset Capitalization Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Display the specified AssetCapitalizationDetail",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Get AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
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
        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            return $this->sendError('Asset Capitalization Detail not found');
        }

        return $this->sendResponse($assetCapitalizationDetail->toArray(), 'Asset Capitalization Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Update the specified AssetCapitalizationDetail in storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Update AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalizationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalizationDetail")
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
     *                  ref="#/definitions/AssetCapitalizationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            return $this->sendError('Asset Capitalization Detail not found');
        }

        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->update($input, $id);

        return $this->sendResponse($assetCapitalizationDetail->toArray(), 'AssetCapitalizationDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetCapitalizationDetails/{id}",
     *      summary="Remove the specified AssetCapitalizationDetail from storage",
     *      tags={"AssetCapitalizationDetail"},
     *      description="Delete AssetCapitalizationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalizationDetail",
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
        /** @var AssetCapitalizationDetail $assetCapitalizationDetail */
        $assetCapitalizationDetail = $this->assetCapitalizationDetailRepository->findWithoutFail($id);

        if (empty($assetCapitalizationDetail)) {
            return $this->sendError('Asset Capitalization Detail not found');
        }

        $assetCapitalizationDetail->delete();

        return $this->sendResponse($id, 'Asset Capitalization Detail deleted successfully');
    }
}
