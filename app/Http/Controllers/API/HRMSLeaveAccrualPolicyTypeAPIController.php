<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRMSLeaveAccrualPolicyTypeAPIRequest;
use App\Http\Requests\API\UpdateHRMSLeaveAccrualPolicyTypeAPIRequest;
use App\Models\HRMSLeaveAccrualPolicyType;
use App\Repositories\HRMSLeaveAccrualPolicyTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRMSLeaveAccrualPolicyTypeController
 * @package App\Http\Controllers\API
 */

class HRMSLeaveAccrualPolicyTypeAPIController extends AppBaseController
{
    /** @var  HRMSLeaveAccrualPolicyTypeRepository */
    private $hRMSLeaveAccrualPolicyTypeRepository;

    public function __construct(HRMSLeaveAccrualPolicyTypeRepository $hRMSLeaveAccrualPolicyTypeRepo)
    {
        $this->hRMSLeaveAccrualPolicyTypeRepository = $hRMSLeaveAccrualPolicyTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualPolicyTypes",
     *      summary="Get a listing of the HRMSLeaveAccrualPolicyTypes.",
     *      tags={"HRMSLeaveAccrualPolicyType"},
     *      description="Get all HRMSLeaveAccrualPolicyTypes",
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
     *                  @SWG\Items(ref="#/definitions/HRMSLeaveAccrualPolicyType")
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
        $this->hRMSLeaveAccrualPolicyTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->hRMSLeaveAccrualPolicyTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRMSLeaveAccrualPolicyTypes = $this->hRMSLeaveAccrualPolicyTypeRepository->all();

        return $this->sendResponse($hRMSLeaveAccrualPolicyTypes->toArray(), trans('custom.h_r_m_s_leave_accrual_policy_types_retrieved_succe'));
    }

    /**
     * @param CreateHRMSLeaveAccrualPolicyTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hRMSLeaveAccrualPolicyTypes",
     *      summary="Store a newly created HRMSLeaveAccrualPolicyType in storage",
     *      tags={"HRMSLeaveAccrualPolicyType"},
     *      description="Store HRMSLeaveAccrualPolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualPolicyType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualPolicyType")
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
     *                  ref="#/definitions/HRMSLeaveAccrualPolicyType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRMSLeaveAccrualPolicyTypeAPIRequest $request)
    {
        $input = $request->all();

        $hRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepository->create($input);

        return $this->sendResponse($hRMSLeaveAccrualPolicyType->toArray(), trans('custom.h_r_m_s_leave_accrual_policy_type_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hRMSLeaveAccrualPolicyTypes/{id}",
     *      summary="Display the specified HRMSLeaveAccrualPolicyType",
     *      tags={"HRMSLeaveAccrualPolicyType"},
     *      description="Get HRMSLeaveAccrualPolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualPolicyType",
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
     *                  ref="#/definitions/HRMSLeaveAccrualPolicyType"
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
        /** @var HRMSLeaveAccrualPolicyType $hRMSLeaveAccrualPolicyType */
        $hRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualPolicyType)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_policy_type_not_found'));
        }

        return $this->sendResponse($hRMSLeaveAccrualPolicyType->toArray(), trans('custom.h_r_m_s_leave_accrual_policy_type_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateHRMSLeaveAccrualPolicyTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hRMSLeaveAccrualPolicyTypes/{id}",
     *      summary="Update the specified HRMSLeaveAccrualPolicyType in storage",
     *      tags={"HRMSLeaveAccrualPolicyType"},
     *      description="Update HRMSLeaveAccrualPolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualPolicyType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HRMSLeaveAccrualPolicyType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HRMSLeaveAccrualPolicyType")
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
     *                  ref="#/definitions/HRMSLeaveAccrualPolicyType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRMSLeaveAccrualPolicyTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRMSLeaveAccrualPolicyType $hRMSLeaveAccrualPolicyType */
        $hRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualPolicyType)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_policy_type_not_found'));
        }

        $hRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepository->update($input, $id);

        return $this->sendResponse($hRMSLeaveAccrualPolicyType->toArray(), trans('custom.hrmsleaveaccrualpolicytype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hRMSLeaveAccrualPolicyTypes/{id}",
     *      summary="Remove the specified HRMSLeaveAccrualPolicyType from storage",
     *      tags={"HRMSLeaveAccrualPolicyType"},
     *      description="Delete HRMSLeaveAccrualPolicyType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HRMSLeaveAccrualPolicyType",
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
        /** @var HRMSLeaveAccrualPolicyType $hRMSLeaveAccrualPolicyType */
        $hRMSLeaveAccrualPolicyType = $this->hRMSLeaveAccrualPolicyTypeRepository->findWithoutFail($id);

        if (empty($hRMSLeaveAccrualPolicyType)) {
            return $this->sendError(trans('custom.h_r_m_s_leave_accrual_policy_type_not_found'));
        }

        $hRMSLeaveAccrualPolicyType->delete();

        return $this->sendResponse($id, trans('custom.h_r_m_s_leave_accrual_policy_type_deleted_successf'));
    }
}
