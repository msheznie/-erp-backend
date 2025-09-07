<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeDesignationAPIRequest;
use App\Http\Requests\API\UpdateEmployeeDesignationAPIRequest;
use App\Models\EmployeeDesignation;
use App\Repositories\EmployeeDesignationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeDesignationController
 * @package App\Http\Controllers\API
 */

class EmployeeDesignationAPIController extends AppBaseController
{
    /** @var  EmployeeDesignationRepository */
    private $employeeDesignationRepository;

    public function __construct(EmployeeDesignationRepository $employeeDesignationRepo)
    {
        $this->employeeDesignationRepository = $employeeDesignationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDesignations",
     *      summary="Get a listing of the EmployeeDesignations.",
     *      tags={"EmployeeDesignation"},
     *      description="Get all EmployeeDesignations",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeDesignation")
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
        $this->employeeDesignationRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeDesignationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeDesignations = $this->employeeDesignationRepository->all();

        return $this->sendResponse($employeeDesignations->toArray(), trans('custom.employee_designations_retrieved_successfully'));
    }

    /**
     * @param CreateEmployeeDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeDesignations",
     *      summary="Store a newly created EmployeeDesignation in storage",
     *      tags={"EmployeeDesignation"},
     *      description="Store EmployeeDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeDesignation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeDesignation")
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
     *                  ref="#/definitions/EmployeeDesignation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeDesignationAPIRequest $request)
    {
        $input = $request->all();

        $employeeDesignation = $this->employeeDesignationRepository->create($input);

        return $this->sendResponse($employeeDesignation->toArray(), trans('custom.employee_designation_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDesignations/{id}",
     *      summary="Display the specified EmployeeDesignation",
     *      tags={"EmployeeDesignation"},
     *      description="Get EmployeeDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDesignation",
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
     *                  ref="#/definitions/EmployeeDesignation"
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
        /** @var EmployeeDesignation $employeeDesignation */
        $employeeDesignation = $this->employeeDesignationRepository->findWithoutFail($id);

        if (empty($employeeDesignation)) {
            return $this->sendError(trans('custom.employee_designation_not_found'));
        }

        return $this->sendResponse($employeeDesignation->toArray(), trans('custom.employee_designation_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeDesignationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeDesignations/{id}",
     *      summary="Update the specified EmployeeDesignation in storage",
     *      tags={"EmployeeDesignation"},
     *      description="Update EmployeeDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDesignation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeDesignation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeDesignation")
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
     *                  ref="#/definitions/EmployeeDesignation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeDesignationAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeDesignation $employeeDesignation */
        $employeeDesignation = $this->employeeDesignationRepository->findWithoutFail($id);

        if (empty($employeeDesignation)) {
            return $this->sendError(trans('custom.employee_designation_not_found'));
        }

        $employeeDesignation = $this->employeeDesignationRepository->update($input, $id);

        return $this->sendResponse($employeeDesignation->toArray(), trans('custom.employeedesignation_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeDesignations/{id}",
     *      summary="Remove the specified EmployeeDesignation from storage",
     *      tags={"EmployeeDesignation"},
     *      description="Delete EmployeeDesignation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDesignation",
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
        /** @var EmployeeDesignation $employeeDesignation */
        $employeeDesignation = $this->employeeDesignationRepository->findWithoutFail($id);

        if (empty($employeeDesignation)) {
            return $this->sendError(trans('custom.employee_designation_not_found'));
        }

        $employeeDesignation->delete();

        return $this->sendSuccess('Employee Designation deleted successfully');
    }
}
