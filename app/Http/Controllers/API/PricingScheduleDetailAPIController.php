<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleDetailAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleDetailAPIRequest;
use App\Models\PricingScheduleDetail;
use App\Repositories\PricingScheduleDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PricingScheduleDetailController
 * @package App\Http\Controllers\API
 */

class PricingScheduleDetailAPIController extends AppBaseController
{
    /** @var  PricingScheduleDetailRepository */
    private $pricingScheduleDetailRepository;

    public function __construct(PricingScheduleDetailRepository $pricingScheduleDetailRepo)
    {
        $this->pricingScheduleDetailRepository = $pricingScheduleDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleDetails",
     *      summary="getPricingScheduleDetailList",
     *      tags={"PricingScheduleDetail"},
     *      description="Get all PricingScheduleDetails",
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
     *                  @OA\Items(ref="#/definitions/PricingScheduleDetail")
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
        $this->pricingScheduleDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pricingScheduleDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pricingScheduleDetails = $this->pricingScheduleDetailRepository->all();

        return $this->sendResponse($pricingScheduleDetails->toArray(), trans('custom.pricing_schedule_details_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/pricingScheduleDetails",
     *      summary="createPricingScheduleDetail",
     *      tags={"PricingScheduleDetail"},
     *      description="Create PricingScheduleDetail",
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
     *                  ref="#/definitions/PricingScheduleDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePricingScheduleDetailAPIRequest $request)
    {
        $input = $request->all();

        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->create($input);

        return $this->sendResponse($pricingScheduleDetail->toArray(), trans('custom.pricing_schedule_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/pricingScheduleDetails/{id}",
     *      summary="getPricingScheduleDetailItem",
     *      tags={"PricingScheduleDetail"},
     *      description="Get PricingScheduleDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetail",
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
     *                  ref="#/definitions/PricingScheduleDetail"
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
        /** @var PricingScheduleDetail $pricingScheduleDetail */
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            return $this->sendError(trans('custom.pricing_schedule_detail_not_found'));
        }

        return $this->sendResponse($pricingScheduleDetail->toArray(), trans('custom.pricing_schedule_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/pricingScheduleDetails/{id}",
     *      summary="updatePricingScheduleDetail",
     *      tags={"PricingScheduleDetail"},
     *      description="Update PricingScheduleDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetail",
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
     *                  ref="#/definitions/PricingScheduleDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePricingScheduleDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var PricingScheduleDetail $pricingScheduleDetail */
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            return $this->sendError(trans('custom.pricing_schedule_detail_not_found'));
        }

        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->update($input, $id);

        return $this->sendResponse($pricingScheduleDetail->toArray(), trans('custom.pricingscheduledetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/pricingScheduleDetails/{id}",
     *      summary="deletePricingScheduleDetail",
     *      tags={"PricingScheduleDetail"},
     *      description="Delete PricingScheduleDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PricingScheduleDetail",
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
        /** @var PricingScheduleDetail $pricingScheduleDetail */
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            return $this->sendError(trans('custom.pricing_schedule_detail_not_found'));
        }

        $pricingScheduleDetail->delete();

        return $this->sendSuccess('Pricing Schedule Detail deleted successfully');
    }
}
