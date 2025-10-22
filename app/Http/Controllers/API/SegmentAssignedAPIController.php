<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSegmentAssignedAPIRequest;
use App\Http\Requests\API\UpdateSegmentAssignedAPIRequest;
use App\Models\SegmentAssigned;
use App\Repositories\SegmentAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SegmentAssignedController
 * @package App\Http\Controllers\API
 */

class SegmentAssignedAPIController extends AppBaseController
{
    /** @var  SegmentAssignedRepository */
    private $segmentAssignedRepository;

    public function __construct(SegmentAssignedRepository $segmentAssignedRepo)
    {
        $this->segmentAssignedRepository = $segmentAssignedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/segmentAssigneds",
     *      summary="getSegmentAssignedList",
     *      tags={"SegmentAssigned"},
     *      description="Get all SegmentAssigneds",
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
     *                  @OA\Items(ref="#/definitions/SegmentAssigned")
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
        $this->segmentAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->segmentAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $segmentAssigneds = $this->segmentAssignedRepository->all();

        return $this->sendResponse($segmentAssigneds->toArray(), trans('custom.segment_assigneds_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/segmentAssigneds",
     *      summary="createSegmentAssigned",
     *      tags={"SegmentAssigned"},
     *      description="Create SegmentAssigned",
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
     *                  ref="#/definitions/SegmentAssigned"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSegmentAssignedAPIRequest $request)
    {
        $input = $request->all();

        $segmentAssigned = $this->segmentAssignedRepository->create($input);

        return $this->sendResponse($segmentAssigned->toArray(), trans('custom.segment_assigned_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/segmentAssigneds/{id}",
     *      summary="getSegmentAssignedItem",
     *      tags={"SegmentAssigned"},
     *      description="Get SegmentAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SegmentAssigned",
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
     *                  ref="#/definitions/SegmentAssigned"
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
        /** @var SegmentAssigned $segmentAssigned */
        $segmentAssigned = $this->segmentAssignedRepository->findWithoutFail($id);

        if (empty($segmentAssigned)) {
            return $this->sendError(trans('custom.segment_assigned_not_found'));
        }

        return $this->sendResponse($segmentAssigned->toArray(), trans('custom.segment_assigned_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/segmentAssigneds/{id}",
     *      summary="updateSegmentAssigned",
     *      tags={"SegmentAssigned"},
     *      description="Update SegmentAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SegmentAssigned",
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
     *                  ref="#/definitions/SegmentAssigned"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSegmentAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var SegmentAssigned $segmentAssigned */
        $segmentAssigned = $this->segmentAssignedRepository->findWithoutFail($id);

        if (empty($segmentAssigned)) {
            return $this->sendError(trans('custom.segment_assigned_not_found'));
        }

        $segmentAssigned = $this->segmentAssignedRepository->update($input, $id);

        return $this->sendResponse($segmentAssigned->toArray(), trans('custom.segmentassigned_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/segmentAssigneds/{id}",
     *      summary="deleteSegmentAssigned",
     *      tags={"SegmentAssigned"},
     *      description="Delete SegmentAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SegmentAssigned",
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
        /** @var SegmentAssigned $segmentAssigned */
        $segmentAssigned = $this->segmentAssignedRepository->findWithoutFail($id);

        if (empty($segmentAssigned)) {
            return $this->sendError(trans('custom.segment_assigned_not_found'));
        }

        $segmentAssigned->delete();

        return $this->sendSuccess('Segment Assigned deleted successfully');
    }
}
