<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveGroupDetailsAPIRequest;
use App\Http\Requests\API\UpdateLeaveGroupDetailsAPIRequest;
use App\Models\LeaveGroupDetails;
use App\Repositories\LeaveGroupDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveGroupDetailsController
 * @package App\Http\Controllers\API
 */

class LeaveGroupDetailsAPIController extends AppBaseController
{
    /** @var  LeaveGroupDetailsRepository */
    private $leaveGroupDetailsRepository;

    public function __construct(LeaveGroupDetailsRepository $leaveGroupDetailsRepo)
    {
        $this->leaveGroupDetailsRepository = $leaveGroupDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveGroupDetails",
     *      summary="Get a listing of the LeaveGroupDetails.",
     *      tags={"LeaveGroupDetails"},
     *      description="Get all LeaveGroupDetails",
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
     *                  @SWG\Items(ref="#/definitions/LeaveGroupDetails")
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
        $this->leaveGroupDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveGroupDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveGroupDetails = $this->leaveGroupDetailsRepository->all();

        return $this->sendResponse($leaveGroupDetails->toArray(), trans('custom.leave_group_details_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveGroupDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveGroupDetails",
     *      summary="Store a newly created LeaveGroupDetails in storage",
     *      tags={"LeaveGroupDetails"},
     *      description="Store LeaveGroupDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveGroupDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveGroupDetails")
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
     *                  ref="#/definitions/LeaveGroupDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveGroupDetailsAPIRequest $request)
    {
        $input = $request->all();

        $leaveGroupDetails = $this->leaveGroupDetailsRepository->create($input);

        return $this->sendResponse($leaveGroupDetails->toArray(), trans('custom.leave_group_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveGroupDetails/{id}",
     *      summary="Display the specified LeaveGroupDetails",
     *      tags={"LeaveGroupDetails"},
     *      description="Get LeaveGroupDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroupDetails",
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
     *                  ref="#/definitions/LeaveGroupDetails"
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
        /** @var LeaveGroupDetails $leaveGroupDetails */
        $leaveGroupDetails = $this->leaveGroupDetailsRepository->findWithoutFail($id);

        if (empty($leaveGroupDetails)) {
            return $this->sendError(trans('custom.leave_group_details_not_found'));
        }

        return $this->sendResponse($leaveGroupDetails->toArray(), trans('custom.leave_group_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveGroupDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveGroupDetails/{id}",
     *      summary="Update the specified LeaveGroupDetails in storage",
     *      tags={"LeaveGroupDetails"},
     *      description="Update LeaveGroupDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroupDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveGroupDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveGroupDetails")
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
     *                  ref="#/definitions/LeaveGroupDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveGroupDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveGroupDetails $leaveGroupDetails */
        $leaveGroupDetails = $this->leaveGroupDetailsRepository->findWithoutFail($id);

        if (empty($leaveGroupDetails)) {
            return $this->sendError(trans('custom.leave_group_details_not_found'));
        }

        $leaveGroupDetails = $this->leaveGroupDetailsRepository->update($input, $id);

        return $this->sendResponse($leaveGroupDetails->toArray(), trans('custom.leavegroupdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveGroupDetails/{id}",
     *      summary="Remove the specified LeaveGroupDetails from storage",
     *      tags={"LeaveGroupDetails"},
     *      description="Delete LeaveGroupDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveGroupDetails",
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
        /** @var LeaveGroupDetails $leaveGroupDetails */
        $leaveGroupDetails = $this->leaveGroupDetailsRepository->findWithoutFail($id);

        if (empty($leaveGroupDetails)) {
            return $this->sendError(trans('custom.leave_group_details_not_found'));
        }

        $leaveGroupDetails->delete();

        return $this->sendSuccess('Leave Group Details deleted successfully');
    }
}
