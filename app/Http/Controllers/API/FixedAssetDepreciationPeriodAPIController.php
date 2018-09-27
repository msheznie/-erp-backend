<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetDepreciationPeriodAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetDepreciationPeriodAPIRequest;
use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\FixedAssetDepreciationPeriodRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetDepreciationPeriodController
 * @package App\Http\Controllers\API
 */

class FixedAssetDepreciationPeriodAPIController extends AppBaseController
{
    /** @var  FixedAssetDepreciationPeriodRepository */
    private $fixedAssetDepreciationPeriodRepository;

    public function __construct(FixedAssetDepreciationPeriodRepository $fixedAssetDepreciationPeriodRepo)
    {
        $this->fixedAssetDepreciationPeriodRepository = $fixedAssetDepreciationPeriodRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationPeriods",
     *      summary="Get a listing of the FixedAssetDepreciationPeriods.",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Get all FixedAssetDepreciationPeriods",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetDepreciationPeriod")
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
        $this->fixedAssetDepreciationPeriodRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetDepreciationPeriodRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetDepreciationPeriods = $this->fixedAssetDepreciationPeriodRepository->all();

        return $this->sendResponse($fixedAssetDepreciationPeriods->toArray(), 'Fixed Asset Depreciation Periods retrieved successfully');
    }

    /**
     * @param CreateFixedAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetDepreciationPeriods",
     *      summary="Store a newly created FixedAssetDepreciationPeriod in storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Store FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationPeriod that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationPeriod")
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetDepreciationPeriods = $this->fixedAssetDepreciationPeriodRepository->create($input);

        return $this->sendResponse($fixedAssetDepreciationPeriods->toArray(), 'Fixed Asset Depreciation Period saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Display the specified FixedAssetDepreciationPeriod",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Get FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
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
        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError('Fixed Asset Depreciation Period not found');
        }

        return $this->sendResponse($fixedAssetDepreciationPeriod->toArray(), 'Fixed Asset Depreciation Period retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetDepreciationPeriodAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Update the specified FixedAssetDepreciationPeriod in storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Update FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationPeriod that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationPeriod")
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
     *                  ref="#/definitions/FixedAssetDepreciationPeriod"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetDepreciationPeriodAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError('Fixed Asset Depreciation Period not found');
        }

        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->update($input, $id);

        return $this->sendResponse($fixedAssetDepreciationPeriod->toArray(), 'FixedAssetDepreciationPeriod updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetDepreciationPeriods/{id}",
     *      summary="Remove the specified FixedAssetDepreciationPeriod from storage",
     *      tags={"FixedAssetDepreciationPeriod"},
     *      description="Delete FixedAssetDepreciationPeriod",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationPeriod",
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
        /** @var FixedAssetDepreciationPeriod $fixedAssetDepreciationPeriod */
        $fixedAssetDepreciationPeriod = $this->fixedAssetDepreciationPeriodRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationPeriod)) {
            return $this->sendError('Fixed Asset Depreciation Period not found');
        }

        $fixedAssetDepreciationPeriod->delete();

        return $this->sendResponse($id, 'Fixed Asset Depreciation Period deleted successfully');
    }
}
