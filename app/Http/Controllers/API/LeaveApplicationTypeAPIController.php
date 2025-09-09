<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLeaveApplicationTypeAPIRequest;
use App\Http\Requests\API\UpdateLeaveApplicationTypeAPIRequest;
use App\Models\LeaveApplicationType;
use App\Repositories\LeaveApplicationTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveApplicationTypeController
 * @package App\Http\Controllers\API
 */

class LeaveApplicationTypeAPIController extends AppBaseController
{
    /** @var  LeaveApplicationTypeRepository */
    private $leaveApplicationTypeRepository;

    public function __construct(LeaveApplicationTypeRepository $leaveApplicationTypeRepo)
    {
        $this->leaveApplicationTypeRepository = $leaveApplicationTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveApplicationTypes",
     *      summary="Get a listing of the LeaveApplicationTypes.",
     *      tags={"LeaveApplicationType"},
     *      description="Get all LeaveApplicationTypes",
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
     *                  @SWG\Items(ref="#/definitions/LeaveApplicationType")
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
        $this->leaveApplicationTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveApplicationTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveApplicationTypes = $this->leaveApplicationTypeRepository->all();

        return $this->sendResponse($leaveApplicationTypes->toArray(), trans('custom.leave_application_types_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveApplicationTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveApplicationTypes",
     *      summary="Store a newly created LeaveApplicationType in storage",
     *      tags={"LeaveApplicationType"},
     *      description="Store LeaveApplicationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveApplicationType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveApplicationType")
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
     *                  ref="#/definitions/LeaveApplicationType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveApplicationTypeAPIRequest $request)
    {
        $input = $request->all();

        $leaveApplicationType = $this->leaveApplicationTypeRepository->create($input);

        return $this->sendResponse($leaveApplicationType->toArray(), trans('custom.leave_application_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveApplicationTypes/{id}",
     *      summary="Display the specified LeaveApplicationType",
     *      tags={"LeaveApplicationType"},
     *      description="Get LeaveApplicationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveApplicationType",
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
     *                  ref="#/definitions/LeaveApplicationType"
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
        /** @var LeaveApplicationType $leaveApplicationType */
        $leaveApplicationType = $this->leaveApplicationTypeRepository->findWithoutFail($id);

        if (empty($leaveApplicationType)) {
            return $this->sendError(trans('custom.leave_application_type_not_found'));
        }

        return $this->sendResponse($leaveApplicationType->toArray(), trans('custom.leave_application_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveApplicationTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveApplicationTypes/{id}",
     *      summary="Update the specified LeaveApplicationType in storage",
     *      tags={"LeaveApplicationType"},
     *      description="Update LeaveApplicationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveApplicationType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveApplicationType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveApplicationType")
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
     *                  ref="#/definitions/LeaveApplicationType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveApplicationTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveApplicationType $leaveApplicationType */
        $leaveApplicationType = $this->leaveApplicationTypeRepository->findWithoutFail($id);

        if (empty($leaveApplicationType)) {
            return $this->sendError(trans('custom.leave_application_type_not_found'));
        }

        $leaveApplicationType = $this->leaveApplicationTypeRepository->update($input, $id);

        return $this->sendResponse($leaveApplicationType->toArray(), trans('custom.leaveapplicationtype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveApplicationTypes/{id}",
     *      summary="Remove the specified LeaveApplicationType from storage",
     *      tags={"LeaveApplicationType"},
     *      description="Delete LeaveApplicationType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveApplicationType",
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
        /** @var LeaveApplicationType $leaveApplicationType */
        $leaveApplicationType = $this->leaveApplicationTypeRepository->findWithoutFail($id);

        if (empty($leaveApplicationType)) {
            return $this->sendError(trans('custom.leave_application_type_not_found'));
        }

        $leaveApplicationType->delete();

        return $this->sendResponse($id, trans('custom.leave_application_type_deleted_successfully'));
    }
}
