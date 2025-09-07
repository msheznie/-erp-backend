<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateemployeeDepartmentDelegationAPIRequest;
use App\Http\Requests\API\UpdateemployeeDepartmentDelegationAPIRequest;
use App\Models\employeeDepartmentDelegation;
use App\Repositories\employeeDepartmentDelegationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class employeeDepartmentDelegationController
 * @package App\Http\Controllers\API
 */

class employeeDepartmentDelegationAPIController extends AppBaseController
{
    /** @var  employeeDepartmentDelegationRepository */
    private $employeeDepartmentDelegationRepository;

    public function __construct(employeeDepartmentDelegationRepository $employeeDepartmentDelegationRepo)
    {
        $this->employeeDepartmentDelegationRepository = $employeeDepartmentDelegationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDepartmentDelegations",
     *      summary="Get a listing of the employeeDepartmentDelegations.",
     *      tags={"employeeDepartmentDelegation"},
     *      description="Get all employeeDepartmentDelegations",
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
     *                  @SWG\Items(ref="#/definitions/employeeDepartmentDelegation")
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
        $this->employeeDepartmentDelegationRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeDepartmentDelegationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeDepartmentDelegations = $this->employeeDepartmentDelegationRepository->all();

        return $this->sendResponse($employeeDepartmentDelegations->toArray(), trans('custom.employee_department_delegations_retrieved_successf'));
    }

    /**
     * @param CreateemployeeDepartmentDelegationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeDepartmentDelegations",
     *      summary="Store a newly created employeeDepartmentDelegation in storage",
     *      tags={"employeeDepartmentDelegation"},
     *      description="Store employeeDepartmentDelegation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="employeeDepartmentDelegation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/employeeDepartmentDelegation")
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
     *                  ref="#/definitions/employeeDepartmentDelegation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateemployeeDepartmentDelegationAPIRequest $request)
    {
        $input = $request->all();

        $employeeDepartmentDelegation = $this->employeeDepartmentDelegationRepository->create($input);

        return $this->sendResponse($employeeDepartmentDelegation->toArray(), trans('custom.employee_department_delegation_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDepartmentDelegations/{id}",
     *      summary="Display the specified employeeDepartmentDelegation",
     *      tags={"employeeDepartmentDelegation"},
     *      description="Get employeeDepartmentDelegation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of employeeDepartmentDelegation",
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
     *                  ref="#/definitions/employeeDepartmentDelegation"
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
        /** @var employeeDepartmentDelegation $employeeDepartmentDelegation */
        $employeeDepartmentDelegation = $this->employeeDepartmentDelegationRepository->findWithoutFail($id);

        if (empty($employeeDepartmentDelegation)) {
            return $this->sendError(trans('custom.employee_department_delegation_not_found'));
        }

        return $this->sendResponse($employeeDepartmentDelegation->toArray(), trans('custom.employee_department_delegation_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateemployeeDepartmentDelegationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeDepartmentDelegations/{id}",
     *      summary="Update the specified employeeDepartmentDelegation in storage",
     *      tags={"employeeDepartmentDelegation"},
     *      description="Update employeeDepartmentDelegation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of employeeDepartmentDelegation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="employeeDepartmentDelegation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/employeeDepartmentDelegation")
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
     *                  ref="#/definitions/employeeDepartmentDelegation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateemployeeDepartmentDelegationAPIRequest $request)
    {
        $input = $request->all();

        /** @var employeeDepartmentDelegation $employeeDepartmentDelegation */
        $employeeDepartmentDelegation = $this->employeeDepartmentDelegationRepository->findWithoutFail($id);

        if (empty($employeeDepartmentDelegation)) {
            return $this->sendError(trans('custom.employee_department_delegation_not_found'));
        }

        $employeeDepartmentDelegation = $this->employeeDepartmentDelegationRepository->update($input, $id);

        return $this->sendResponse($employeeDepartmentDelegation->toArray(), trans('custom.employeedepartmentdelegation_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeDepartmentDelegations/{id}",
     *      summary="Remove the specified employeeDepartmentDelegation from storage",
     *      tags={"employeeDepartmentDelegation"},
     *      description="Delete employeeDepartmentDelegation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of employeeDepartmentDelegation",
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
        /** @var employeeDepartmentDelegation $employeeDepartmentDelegation */
        $employeeDepartmentDelegation = $this->employeeDepartmentDelegationRepository->findWithoutFail($id);

        if (empty($employeeDepartmentDelegation)) {
            return $this->sendError(trans('custom.employee_department_delegation_not_found'));
        }

        $employeeDepartmentDelegation->delete();

        return $this->sendResponse($id, trans('custom.employee_department_delegation_deleted_successfull'));
    }
}
