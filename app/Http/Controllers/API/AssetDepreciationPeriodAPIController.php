<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetDepreciationPeriodAPIRequest;
use App\Http\Requests\API\UpdateAssetDepreciationPeriodAPIRequest;
use App\Models\AssetDepreciationPeriod;
use App\Repositories\AssetDepreciationPeriodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDepreciationPeriodController
 * @package App\Http\Controllers\API
 */

class AssetDepreciationPeriodAPIController extends AppBaseController
{
    /** @var  AssetDepreciationPeriodRepository */
    private $assetDepreciationPeriodRepository;

    public function __construct(AssetDepreciationPeriodRepository $assetDepreciationPeriodRepo)
    {
        $this->assetDepreciationPeriodRepository = $assetDepreciationPeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDepreciationPeriods",
     *      summary="Get a listing of the AssetDepreciationPeriods.",
     *      tags={"AssetDepreciationPeriod"},
     *      description="Get all AssetDepreciationPeriods",
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
     *                  @SWG\Items(ref="#/definitions/AssetDepreciationPeriod")
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
        $this->assetDepreciationPeriodRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDepreciationPeriodRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDepreciationPeriods = $this->assetDepreciationPeriodRepository->all();

        return $this->sendResponse($assetDepreciationPeriods->toArray(), 'Asset Depreciation Periods retrieved successfully');
    }

    /**
     * @param CreateAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDepreciationPeriods",
     *      summary="Store a newly created AssetDepreciationPeriod in storage",
     *      tags={"AssetDepreciationPeriod"},
     *      description="Store AssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDepreciationPeriod that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDepreciationPeriod")
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
     *                  ref="#/definitions/AssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        $assetDepreciationPeriods = $this->assetDepreciationPeriodRepository->create($input);

        return $this->sendResponse($assetDepreciationPeriods->toArray(), 'Asset Depreciation Period saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDepreciationPeriods/{id}",
     *      summary="Display the specified AssetDepreciationPeriod",
     *      tags={"AssetDepreciationPeriod"},
     *      description="Get AssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDepreciationPeriod",
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
     *                  ref="#/definitions/AssetDepreciationPeriod"
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
        /** @var AssetDepreciationPeriod $assetDepreciationPeriod */
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            return $this->sendError('Asset Depreciation Period not found');
        }

        return $this->sendResponse($assetDepreciationPeriod->toArray(), 'Asset Depreciation Period retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDepreciationPeriods/{id}",
     *      summary="Update the specified AssetDepreciationPeriod in storage",
     *      tags={"AssetDepreciationPeriod"},
     *      description="Update AssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDepreciationPeriod",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDepreciationPeriod that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDepreciationPeriod")
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
     *                  ref="#/definitions/AssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetDepreciationPeriod $assetDepreciationPeriod */
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            return $this->sendError('Asset Depreciation Period not found');
        }

        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->update($input, $id);

        return $this->sendResponse($assetDepreciationPeriod->toArray(), 'AssetDepreciationPeriod updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDepreciationPeriods/{id}",
     *      summary="Remove the specified AssetDepreciationPeriod from storage",
     *      tags={"AssetDepreciationPeriod"},
     *      description="Delete AssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDepreciationPeriod",
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
        /** @var AssetDepreciationPeriod $assetDepreciationPeriod */
        $assetDepreciationPeriod = $this->assetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($assetDepreciationPeriod)) {
            return $this->sendError('Asset Depreciation Period not found');
        }

        $assetDepreciationPeriod->delete();

        return $this->sendResponse($id, 'Asset Depreciation Period deleted successfully');
    }
}
