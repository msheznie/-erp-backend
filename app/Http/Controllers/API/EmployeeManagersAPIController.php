<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeManagersAPIRequest;
use App\Http\Requests\API\UpdateEmployeeManagersAPIRequest;
use App\Models\EmployeeManagers;
use App\Repositories\EmployeeManagersRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeManagersController
 * @package App\Http\Controllers\API
 */

class EmployeeManagersAPIController extends AppBaseController
{
    /** @var  EmployeeManagersRepository */
    private $employeeManagersRepository;

    public function __construct(EmployeeManagersRepository $employeeManagersRepo)
    {
        $this->employeeManagersRepository = $employeeManagersRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeManagers",
     *      summary="Get a listing of the EmployeeManagers.",
     *      tags={"EmployeeManagers"},
     *      description="Get all EmployeeManagers",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeManagers")
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
        $this->employeeManagersRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeManagersRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeManagers = $this->employeeManagersRepository->all();

        return $this->sendResponse($employeeManagers->toArray(), trans('custom.employee_managers_retrieved_successfully'));
    }

    /**
     * @param CreateEmployeeManagersAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeManagers",
     *      summary="Store a newly created EmployeeManagers in storage",
     *      tags={"EmployeeManagers"},
     *      description="Store EmployeeManagers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeManagers that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeManagers")
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
     *                  ref="#/definitions/EmployeeManagers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeManagersAPIRequest $request)
    {
        $input = $request->all();

        $employeeManagers = $this->employeeManagersRepository->create($input);

        return $this->sendResponse($employeeManagers->toArray(), trans('custom.employee_managers_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeManagers/{id}",
     *      summary="Display the specified EmployeeManagers",
     *      tags={"EmployeeManagers"},
     *      description="Get EmployeeManagers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeManagers",
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
     *                  ref="#/definitions/EmployeeManagers"
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
        /** @var EmployeeManagers $employeeManagers */
        $employeeManagers = $this->employeeManagersRepository->findWithoutFail($id);

        if (empty($employeeManagers)) {
            return $this->sendError(trans('custom.employee_managers_not_found'));
        }

        return $this->sendResponse($employeeManagers->toArray(), trans('custom.employee_managers_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeManagersAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeManagers/{id}",
     *      summary="Update the specified EmployeeManagers in storage",
     *      tags={"EmployeeManagers"},
     *      description="Update EmployeeManagers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeManagers",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeManagers that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeManagers")
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
     *                  ref="#/definitions/EmployeeManagers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeManagersAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeManagers $employeeManagers */
        $employeeManagers = $this->employeeManagersRepository->findWithoutFail($id);

        if (empty($employeeManagers)) {
            return $this->sendError(trans('custom.employee_managers_not_found'));
        }

        $employeeManagers = $this->employeeManagersRepository->update($input, $id);

        return $this->sendResponse($employeeManagers->toArray(), trans('custom.employeemanagers_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeManagers/{id}",
     *      summary="Remove the specified EmployeeManagers from storage",
     *      tags={"EmployeeManagers"},
     *      description="Delete EmployeeManagers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeManagers",
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
        /** @var EmployeeManagers $employeeManagers */
        $employeeManagers = $this->employeeManagersRepository->findWithoutFail($id);

        if (empty($employeeManagers)) {
            return $this->sendError(trans('custom.employee_managers_not_found'));
        }

        $employeeManagers->delete();

        return $this->sendResponse($id, trans('custom.employee_managers_deleted_successfully'));
    }
}
