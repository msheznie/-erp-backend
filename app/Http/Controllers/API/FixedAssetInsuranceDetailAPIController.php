<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetInsuranceDetailAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetInsuranceDetailAPIRequest;
use App\Models\FixedAssetInsuranceDetail;
use App\Repositories\FixedAssetInsuranceDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetInsuranceDetailController
 * @package App\Http\Controllers\API
 */

class FixedAssetInsuranceDetailAPIController extends AppBaseController
{
    /** @var  FixedAssetInsuranceDetailRepository */
    private $fixedAssetInsuranceDetailRepository;

    public function __construct(FixedAssetInsuranceDetailRepository $fixedAssetInsuranceDetailRepo)
    {
        $this->fixedAssetInsuranceDetailRepository = $fixedAssetInsuranceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetInsuranceDetails",
     *      summary="Get a listing of the FixedAssetInsuranceDetails.",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Get all FixedAssetInsuranceDetails",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetInsuranceDetail")
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
        $this->fixedAssetInsuranceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetInsuranceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetInsuranceDetails = $this->fixedAssetInsuranceDetailRepository->all();

        return $this->sendResponse($fixedAssetInsuranceDetails->toArray(), 'Fixed Asset Insurance Details retrieved successfully');
    }

    /**
     * @param CreateFixedAssetInsuranceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetInsuranceDetails",
     *      summary="Store a newly created FixedAssetInsuranceDetail in storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Store FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetInsuranceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetInsuranceDetail")
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetInsuranceDetailAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetInsuranceDetails = $this->fixedAssetInsuranceDetailRepository->create($input);

        return $this->sendResponse($fixedAssetInsuranceDetails->toArray(), 'Fixed Asset Insurance Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Display the specified FixedAssetInsuranceDetail",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Get FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
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
        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError('Fixed Asset Insurance Detail not found');
        }

        return $this->sendResponse($fixedAssetInsuranceDetail->toArray(), 'Fixed Asset Insurance Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetInsuranceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Update the specified FixedAssetInsuranceDetail in storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Update FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetInsuranceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetInsuranceDetail")
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
     *                  ref="#/definitions/FixedAssetInsuranceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetInsuranceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError('Fixed Asset Insurance Detail not found');
        }

        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->update($input, $id);

        return $this->sendResponse($fixedAssetInsuranceDetail->toArray(), 'FixedAssetInsuranceDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetInsuranceDetails/{id}",
     *      summary="Remove the specified FixedAssetInsuranceDetail from storage",
     *      tags={"FixedAssetInsuranceDetail"},
     *      description="Delete FixedAssetInsuranceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetInsuranceDetail",
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
        /** @var FixedAssetInsuranceDetail $fixedAssetInsuranceDetail */
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            return $this->sendError('Fixed Asset Insurance Detail not found');
        }

        $fixedAssetInsuranceDetail->delete();

        return $this->sendResponse($id, 'Fixed Asset Insurance Detail deleted successfully');
    }
}
