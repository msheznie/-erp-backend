<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateB2BSubmissionFileDetailAPIRequest;
use App\Http\Requests\API\UpdateB2BSubmissionFileDetailAPIRequest;
use App\Models\B2BSubmissionFileDetail;
use App\Repositories\B2BSubmissionFileDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class B2BSubmissionFileDetailController
 * @package App\Http\Controllers\API
 */

class B2BSubmissionFileDetailAPIController extends AppBaseController
{
    /** @var  B2BSubmissionFileDetailRepository */
    private $b2BSubmissionFileDetailRepository;

    public function __construct(B2BSubmissionFileDetailRepository $b2BSubmissionFileDetailRepo)
    {
        $this->b2BSubmissionFileDetailRepository = $b2BSubmissionFileDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/b2BSubmissionFileDetails",
     *      summary="getB2BSubmissionFileDetailList",
     *      tags={"B2BSubmissionFileDetail"},
     *      description="Get all B2BSubmissionFileDetails",
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
     *                  @OA\Items(ref="#/definitions/B2BSubmissionFileDetail")
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
        $this->b2BSubmissionFileDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->b2BSubmissionFileDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $b2BSubmissionFileDetails = $this->b2BSubmissionFileDetailRepository->all();

        return $this->sendResponse($b2BSubmissionFileDetails->toArray(), 'B2 B Submission File Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/b2BSubmissionFileDetails",
     *      summary="createB2BSubmissionFileDetail",
     *      tags={"B2BSubmissionFileDetail"},
     *      description="Create B2BSubmissionFileDetail",
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
     *                  ref="#/definitions/B2BSubmissionFileDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateB2BSubmissionFileDetailAPIRequest $request)
    {
        $input = $request->all();

        $b2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepository->create($input);

        return $this->sendResponse($b2BSubmissionFileDetail->toArray(), 'B2 B Submission File Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/b2BSubmissionFileDetails/{id}",
     *      summary="getB2BSubmissionFileDetailItem",
     *      tags={"B2BSubmissionFileDetail"},
     *      description="Get B2BSubmissionFileDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of B2BSubmissionFileDetail",
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
     *                  ref="#/definitions/B2BSubmissionFileDetail"
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
        /** @var B2BSubmissionFileDetail $b2BSubmissionFileDetail */
        $b2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepository->findWithoutFail($id);

        if (empty($b2BSubmissionFileDetail)) {
            return $this->sendError('B2 B Submission File Detail not found');
        }

        return $this->sendResponse($b2BSubmissionFileDetail->toArray(), 'B2 B Submission File Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/b2BSubmissionFileDetails/{id}",
     *      summary="updateB2BSubmissionFileDetail",
     *      tags={"B2BSubmissionFileDetail"},
     *      description="Update B2BSubmissionFileDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of B2BSubmissionFileDetail",
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
     *                  ref="#/definitions/B2BSubmissionFileDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateB2BSubmissionFileDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var B2BSubmissionFileDetail $b2BSubmissionFileDetail */
        $b2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepository->findWithoutFail($id);

        if (empty($b2BSubmissionFileDetail)) {
            return $this->sendError('B2 B Submission File Detail not found');
        }

        $b2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepository->update($input, $id);

        return $this->sendResponse($b2BSubmissionFileDetail->toArray(), 'B2BSubmissionFileDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/b2BSubmissionFileDetails/{id}",
     *      summary="deleteB2BSubmissionFileDetail",
     *      tags={"B2BSubmissionFileDetail"},
     *      description="Delete B2BSubmissionFileDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of B2BSubmissionFileDetail",
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
        /** @var B2BSubmissionFileDetail $b2BSubmissionFileDetail */
        $b2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepository->findWithoutFail($id);

        if (empty($b2BSubmissionFileDetail)) {
            return $this->sendError('B2 B Submission File Detail not found');
        }

        $b2BSubmissionFileDetail->delete();

        return $this->sendSuccess('B2 B Submission File Detail deleted successfully');
    }
}
