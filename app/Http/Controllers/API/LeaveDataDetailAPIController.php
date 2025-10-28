<?php
/**
 * =============================================
 * -- File Name : LeaveDataMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 01 - September 2019
 * -- Description : This file contains the all related functions for leave appliation
 * -- REVISION HISTORY
 * -- Date: 01- September 2019 By: Rilwan Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveDataDetailAPIRequest;
use App\Http\Requests\API\UpdateLeaveDataDetailAPIRequest;
use App\Models\LeaveDataDetail;
use App\Repositories\LeaveDataDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveDataDetailController
 * @package App\Http\Controllers\API
 */

class LeaveDataDetailAPIController extends AppBaseController
{
    /** @var  LeaveDataDetailRepository */
    private $leaveDataDetailRepository;

    public function __construct(LeaveDataDetailRepository $leaveDataDetailRepo)
    {
        $this->leaveDataDetailRepository = $leaveDataDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataDetails",
     *      summary="Get a listing of the LeaveDataDetails.",
     *      tags={"LeaveDataDetail"},
     *      description="Get all LeaveDataDetails",
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
     *                  @SWG\Items(ref="#/definitions/LeaveDataDetail")
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
        $this->leaveDataDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDataDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDataDetails = $this->leaveDataDetailRepository->all();

        return $this->sendResponse($leaveDataDetails->toArray(), trans('custom.leave_data_details_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveDataDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDataDetails",
     *      summary="Store a newly created LeaveDataDetail in storage",
     *      tags={"LeaveDataDetail"},
     *      description="Store LeaveDataDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataDetail")
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
     *                  ref="#/definitions/LeaveDataDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDataDetailAPIRequest $request)
    {
        $input = $request->all();

        $leaveDataDetail = $this->leaveDataDetailRepository->create($input);

        return $this->sendResponse($leaveDataDetail->toArray(), trans('custom.leave_data_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataDetails/{id}",
     *      summary="Display the specified LeaveDataDetail",
     *      tags={"LeaveDataDetail"},
     *      description="Get LeaveDataDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataDetail",
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
     *                  ref="#/definitions/LeaveDataDetail"
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
        /** @var LeaveDataDetail $leaveDataDetail */
        $leaveDataDetail = $this->leaveDataDetailRepository->findWithoutFail($id);

        if (empty($leaveDataDetail)) {
            return $this->sendError(trans('custom.leave_data_detail_not_found'));
        }

        return $this->sendResponse($leaveDataDetail->toArray(), trans('custom.leave_data_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveDataDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDataDetails/{id}",
     *      summary="Update the specified LeaveDataDetail in storage",
     *      tags={"LeaveDataDetail"},
     *      description="Update LeaveDataDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataDetail")
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
     *                  ref="#/definitions/LeaveDataDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDataDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDataDetail $leaveDataDetail */
        $leaveDataDetail = $this->leaveDataDetailRepository->findWithoutFail($id);

        if (empty($leaveDataDetail)) {
            return $this->sendError(trans('custom.leave_data_detail_not_found'));
        }

        $leaveDataDetail = $this->leaveDataDetailRepository->update($input, $id);

        return $this->sendResponse($leaveDataDetail->toArray(), trans('custom.leavedatadetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDataDetails/{id}",
     *      summary="Remove the specified LeaveDataDetail from storage",
     *      tags={"LeaveDataDetail"},
     *      description="Delete LeaveDataDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataDetail",
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
        /** @var LeaveDataDetail $leaveDataDetail */
        $leaveDataDetail = $this->leaveDataDetailRepository->findWithoutFail($id);

        if (empty($leaveDataDetail)) {
            return $this->sendError(trans('custom.leave_data_detail_not_found'));
        }

        $leaveDataDetail->delete();

        return $this->sendResponse($id, trans('custom.leave_data_detail_deleted_successfully'));
    }
}
