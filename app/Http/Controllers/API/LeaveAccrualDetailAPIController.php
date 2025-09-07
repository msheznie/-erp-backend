<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveAccrualDetailAPIRequest;
use App\Http\Requests\API\UpdateLeaveAccrualDetailAPIRequest;
use App\Models\LeaveAccrualDetail;
use App\Repositories\LeaveAccrualDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveAccrualDetailController
 * @package App\Http\Controllers\API
 */

class LeaveAccrualDetailAPIController extends AppBaseController
{
    /** @var  LeaveAccrualDetailRepository */
    private $leaveAccrualDetailRepository;

    public function __construct(LeaveAccrualDetailRepository $leaveAccrualDetailRepo)
    {
        $this->leaveAccrualDetailRepository = $leaveAccrualDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveAccrualDetails",
     *      summary="Get a listing of the LeaveAccrualDetails.",
     *      tags={"LeaveAccrualDetail"},
     *      description="Get all LeaveAccrualDetails",
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
     *                  @SWG\Items(ref="#/definitions/LeaveAccrualDetail")
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
        $this->leaveAccrualDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveAccrualDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveAccrualDetails = $this->leaveAccrualDetailRepository->all();

        return $this->sendResponse($leaveAccrualDetails->toArray(), trans('custom.leave_accrual_details_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveAccrualDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveAccrualDetails",
     *      summary="Store a newly created LeaveAccrualDetail in storage",
     *      tags={"LeaveAccrualDetail"},
     *      description="Store LeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveAccrualDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveAccrualDetail")
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
     *                  ref="#/definitions/LeaveAccrualDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveAccrualDetailAPIRequest $request)
    {
        $input = $request->all();

        $leaveAccrualDetail = $this->leaveAccrualDetailRepository->create($input);

        return $this->sendResponse($leaveAccrualDetail->toArray(), trans('custom.leave_accrual_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveAccrualDetails/{id}",
     *      summary="Display the specified LeaveAccrualDetail",
     *      tags={"LeaveAccrualDetail"},
     *      description="Get LeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualDetail",
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
     *                  ref="#/definitions/LeaveAccrualDetail"
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
        /** @var LeaveAccrualDetail $leaveAccrualDetail */
        $leaveAccrualDetail = $this->leaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($leaveAccrualDetail)) {
            return $this->sendError(trans('custom.leave_accrual_detail_not_found'));
        }

        return $this->sendResponse($leaveAccrualDetail->toArray(), trans('custom.leave_accrual_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveAccrualDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveAccrualDetails/{id}",
     *      summary="Update the specified LeaveAccrualDetail in storage",
     *      tags={"LeaveAccrualDetail"},
     *      description="Update LeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveAccrualDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveAccrualDetail")
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
     *                  ref="#/definitions/LeaveAccrualDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveAccrualDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveAccrualDetail $leaveAccrualDetail */
        $leaveAccrualDetail = $this->leaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($leaveAccrualDetail)) {
            return $this->sendError(trans('custom.leave_accrual_detail_not_found'));
        }

        $leaveAccrualDetail = $this->leaveAccrualDetailRepository->update($input, $id);

        return $this->sendResponse($leaveAccrualDetail->toArray(), trans('custom.leaveaccrualdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveAccrualDetails/{id}",
     *      summary="Remove the specified LeaveAccrualDetail from storage",
     *      tags={"LeaveAccrualDetail"},
     *      description="Delete LeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveAccrualDetail",
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
        /** @var LeaveAccrualDetail $leaveAccrualDetail */
        $leaveAccrualDetail = $this->leaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($leaveAccrualDetail)) {
            return $this->sendError(trans('custom.leave_accrual_detail_not_found'));
        }

        $leaveAccrualDetail->delete();

        return $this->sendSuccess('Leave Accrual Detail deleted successfully');
    }
}
