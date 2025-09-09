<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveGroupAPIRequest;
use App\Http\Requests\API\UpdateLeaveGroupAPIRequest;
use App\Models\LeaveGroup;
use App\Repositories\LeaveGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveGroupController
 * @package App\Http\Controllers\API
 */

class LeaveGroupAPIController extends AppBaseController
{
    /** @var  LeaveGroupRepository */
    private $leaveGroupRepository;

    public function __construct(LeaveGroupRepository $leaveGroupRepo)
    {
        $this->leaveGroupRepository = $leaveGroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveGroups",
     *      summary="Get a listing of the LeaveGroups.",
     *      tags={"LeaveGroup"},
     *      description="Get all LeaveGroups",
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
     *                  @SWG\Items(ref="#/definitions/LeaveGroup")
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
        $this->leaveGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveGroups = $this->leaveGroupRepository->all();

        return $this->sendResponse($leaveGroups->toArray(), trans('custom.leave_groups_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveGroups",
     *      summary="Store a newly created LeaveGroup in storage",
     *      tags={"LeaveGroup"},
     *      description="Store LeaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveGroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveGroup")
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
     *                  ref="#/definitions/LeaveGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveGroupAPIRequest $request)
    {
        $input = $request->all();

        $leaveGroup = $this->leaveGroupRepository->create($input);

        return $this->sendResponse($leaveGroup->toArray(), trans('custom.leave_group_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveGroups/{id}",
     *      summary="Display the specified LeaveGroup",
     *      tags={"LeaveGroup"},
     *      description="Get LeaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroup",
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
     *                  ref="#/definitions/LeaveGroup"
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
        /** @var LeaveGroup $leaveGroup */
        $leaveGroup = $this->leaveGroupRepository->findWithoutFail($id);

        if (empty($leaveGroup)) {
            return $this->sendError(trans('custom.leave_group_not_found'));
        }

        return $this->sendResponse($leaveGroup->toArray(), trans('custom.leave_group_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveGroups/{id}",
     *      summary="Update the specified LeaveGroup in storage",
     *      tags={"LeaveGroup"},
     *      description="Update LeaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveGroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveGroup")
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
     *                  ref="#/definitions/LeaveGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveGroup $leaveGroup */
        $leaveGroup = $this->leaveGroupRepository->findWithoutFail($id);

        if (empty($leaveGroup)) {
            return $this->sendError(trans('custom.leave_group_not_found'));
        }

        $leaveGroup = $this->leaveGroupRepository->update($input, $id);

        return $this->sendResponse($leaveGroup->toArray(), trans('custom.leavegroup_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveGroups/{id}",
     *      summary="Remove the specified LeaveGroup from storage",
     *      tags={"LeaveGroup"},
     *      description="Delete LeaveGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroup",
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
        /** @var LeaveGroup $leaveGroup */
        $leaveGroup = $this->leaveGroupRepository->findWithoutFail($id);

        if (empty($leaveGroup)) {
            return $this->sendError(trans('custom.leave_group_not_found'));
        }

        $leaveGroup->delete();

        return $this->sendSuccess('Leave Group deleted successfully');
    }
}
