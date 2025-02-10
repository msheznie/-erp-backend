<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderPaymentDetailAPIRequest;
use App\Http\Requests\API\UpdateTenderPaymentDetailAPIRequest;
use App\Models\TenderPaymentDetail;
use App\Repositories\TenderPaymentDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderPaymentDetailController
 * @package App\Http\Controllers\API
 */

class TenderPaymentDetailAPIController extends AppBaseController
{
    /** @var  TenderPaymentDetailRepository */
    private $tenderPaymentDetailRepository;

    public function __construct(TenderPaymentDetailRepository $tenderPaymentDetailRepo)
    {
        $this->tenderPaymentDetailRepository = $tenderPaymentDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderPaymentDetails",
     *      summary="getTenderPaymentDetailList",
     *      tags={"TenderPaymentDetail"},
     *      description="Get all TenderPaymentDetails",
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
     *                  @OA\Items(ref="#/definitions/TenderPaymentDetail")
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
        $this->tenderPaymentDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderPaymentDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderPaymentDetails = $this->tenderPaymentDetailRepository->all();

        return $this->sendResponse($tenderPaymentDetails->toArray(), 'Tender Payment Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderPaymentDetails",
     *      summary="createTenderPaymentDetail",
     *      tags={"TenderPaymentDetail"},
     *      description="Create TenderPaymentDetail",
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
     *                  ref="#/definitions/TenderPaymentDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderPaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        $tenderPaymentDetail = $this->tenderPaymentDetailRepository->create($input);

        return $this->sendResponse($tenderPaymentDetail->toArray(), 'Tender Payment Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderPaymentDetails/{id}",
     *      summary="getTenderPaymentDetailItem",
     *      tags={"TenderPaymentDetail"},
     *      description="Get TenderPaymentDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPaymentDetail",
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
     *                  ref="#/definitions/TenderPaymentDetail"
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
        /** @var TenderPaymentDetail $tenderPaymentDetail */
        $tenderPaymentDetail = $this->tenderPaymentDetailRepository->findWithoutFail($id);

        if (empty($tenderPaymentDetail)) {
            return $this->sendError('Tender Payment Detail not found');
        }

        return $this->sendResponse($tenderPaymentDetail->toArray(), 'Tender Payment Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderPaymentDetails/{id}",
     *      summary="updateTenderPaymentDetail",
     *      tags={"TenderPaymentDetail"},
     *      description="Update TenderPaymentDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPaymentDetail",
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
     *                  ref="#/definitions/TenderPaymentDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderPaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderPaymentDetail $tenderPaymentDetail */
        $tenderPaymentDetail = $this->tenderPaymentDetailRepository->findWithoutFail($id);

        if (empty($tenderPaymentDetail)) {
            return $this->sendError('Tender Payment Detail not found');
        }

        $tenderPaymentDetail = $this->tenderPaymentDetailRepository->update($input, $id);

        return $this->sendResponse($tenderPaymentDetail->toArray(), 'TenderPaymentDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderPaymentDetails/{id}",
     *      summary="deleteTenderPaymentDetail",
     *      tags={"TenderPaymentDetail"},
     *      description="Delete TenderPaymentDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderPaymentDetail",
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
        /** @var TenderPaymentDetail $tenderPaymentDetail */
        $tenderPaymentDetail = $this->tenderPaymentDetailRepository->findWithoutFail($id);

        if (empty($tenderPaymentDetail)) {
            return $this->sendError('Tender Payment Detail not found');
        }

        $tenderPaymentDetail->delete();

        return $this->sendSuccess('Tender Payment Detail deleted successfully');
    }
}
