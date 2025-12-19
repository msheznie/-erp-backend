<?php
/**
=============================================
-- File Name : HRMSLeaveAccrualDetailAPIController.php
-- Project Name : ERP
-- Module Name :  LEAVE
-- Author : Mohamed Rilwan
-- Create date : 19 - November 2019
-- Description :
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSLeaveAccrualDetailAPIRequest;
use App\Http\Requests\API\UpdateHRMSLeaveAccrualDetailAPIRequest;
use App\Models\HRMSLeaveAccrualDetail;
use App\Repositories\HRMSLeaveAccrualDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSLeaveAccrualDetailController
 * @package App\Http\Controllers\API
 */

class HRMSLeaveAccrualDetailAPIController extends AppBaseController
{
    /** @var  HRMSLeaveAccrualDetailRepository */
    private $hRMSLeaveAccrualDetailRepository;

    public function __construct(HRMSLeaveAccrualDetailRepository $hRMSLeaveAccrualDetailRepo)
    {
        $this->hRMSLeaveAccrualDetailRepository = $hRMSLeaveAccrualDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualDetails",
     *      summary="Get a listing of the HRMSLeaveAccrualDetails.",
     *      tags={"HRMSLeaveAccrualDetail"},
     *      description="Get all HRMSLeaveAccrualDetails",
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
     *                  @SWG\Items(ref="#/definitions/HRMSLeaveAccrualDetail")
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
        $this->hRMSLeaveAccrualDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSLeaveAccrualDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSLeaveAccrualDetails = $this->hRMSLeaveAccrualDetailRepository->all();

        return $this->sendResponse($hRMSLeaveAccrualDetails->toArray(), trans('custom.h_r_m_s_leave_accrual_details_retrieved_successful'));
    }

    /**
     * @param CreateHRMSLeaveAccrualDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSLeaveAccrualDetails",
     *      summary="Store a newly created HRMSLeaveAccrualDetail in storage",
     *      tags={"HRMSLeaveAccrualDetail"},
     *      description="Store HRMSLeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualDetail")
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
     *                  ref="#/definitions/HRMSLeaveAccrualDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSLeaveAccrualDetailAPIRequest $request)
    {
        $input = $request->all();

        $hRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepository->create($input);

        return $this->sendResponse($hRMSLeaveAccrualDetail->toArray(), trans('custom.h_r_m_s_leave_accrual_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualDetails/{id}",
     *      summary="Display the specified HRMSLeaveAccrualDetail",
     *      tags={"HRMSLeaveAccrualDetail"},
     *      description="Get HRMSLeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualDetail",
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
     *                  ref="#/definitions/HRMSLeaveAccrualDetail"
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
        /** @var HRMSLeaveAccrualDetail $hRMSLeaveAccrualDetail */
        $hRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualDetail)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_detail_not_found'));
        }

        return $this->sendResponse($hRMSLeaveAccrualDetail->toArray(), trans('custom.h_r_m_s_leave_accrual_detail_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSLeaveAccrualDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSLeaveAccrualDetails/{id}",
     *      summary="Update the specified HRMSLeaveAccrualDetail in storage",
     *      tags={"HRMSLeaveAccrualDetail"},
     *      description="Update HRMSLeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualDetail")
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
     *                  ref="#/definitions/HRMSLeaveAccrualDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSLeaveAccrualDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSLeaveAccrualDetail $hRMSLeaveAccrualDetail */
        $hRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualDetail)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_detail_not_found'));
        }

        $hRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepository->update($input, $id);

        return $this->sendResponse($hRMSLeaveAccrualDetail->toArray(), trans('custom.hrmsleaveaccrualdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSLeaveAccrualDetails/{id}",
     *      summary="Remove the specified HRMSLeaveAccrualDetail from storage",
     *      tags={"HRMSLeaveAccrualDetail"},
     *      description="Delete HRMSLeaveAccrualDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualDetail",
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
        /** @var HRMSLeaveAccrualDetail $hRMSLeaveAccrualDetail */
        $hRMSLeaveAccrualDetail = $this->hRMSLeaveAccrualDetailRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualDetail)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_detail_not_found'));
        }

        $hRMSLeaveAccrualDetail->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_leave_accrual_detail_deleted_successfully'));
    }
}
