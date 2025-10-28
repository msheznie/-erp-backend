<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeDetailsAPIRequest;
use App\Http\Requests\API\UpdateEmployeeDetailsAPIRequest;
use App\Models\EmployeeDetails;
use App\Repositories\EmployeeDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeDetailsController
 * @package App\Http\Controllers\API
 */

class EmployeeDetailsAPIController extends AppBaseController
{
    /** @var  EmployeeDetailsRepository */
    private $employeeDetailsRepository;

    public function __construct(EmployeeDetailsRepository $employeeDetailsRepo)
    {
        $this->employeeDetailsRepository = $employeeDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDetails",
     *      summary="Get a listing of the EmployeeDetails.",
     *      tags={"EmployeeDetails"},
     *      description="Get all EmployeeDetails",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeDetails")
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
        $this->employeeDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeDetails = $this->employeeDetailsRepository->all();

        return $this->sendResponse($employeeDetails->toArray(), trans('custom.employee_details_retrieved_successfully'));
    }

    /**
     * @param CreateEmployeeDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeDetails",
     *      summary="Store a newly created EmployeeDetails in storage",
     *      tags={"EmployeeDetails"},
     *      description="Store EmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeDetails")
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
     *                  ref="#/definitions/EmployeeDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeDetailsAPIRequest $request)
    {
        $input = $request->all();

        $employeeDetails = $this->employeeDetailsRepository->create($input);

        return $this->sendResponse($employeeDetails->toArray(), trans('custom.employee_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeDetails/{id}",
     *      summary="Display the specified EmployeeDetails",
     *      tags={"EmployeeDetails"},
     *      description="Get EmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDetails",
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
     *                  ref="#/definitions/EmployeeDetails"
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
        /** @var EmployeeDetails $employeeDetails */
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            return $this->sendError(trans('custom.employee_details_not_found'));
        }

        return $this->sendResponse($employeeDetails->toArray(), trans('custom.employee_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeDetails/{id}",
     *      summary="Update the specified EmployeeDetails in storage",
     *      tags={"EmployeeDetails"},
     *      description="Update EmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeDetails")
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
     *                  ref="#/definitions/EmployeeDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeDetails $employeeDetails */
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            return $this->sendError(trans('custom.employee_details_not_found'));
        }

        $employeeDetails = $this->employeeDetailsRepository->update($input, $id);

        return $this->sendResponse($employeeDetails->toArray(), trans('custom.employeedetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeDetails/{id}",
     *      summary="Remove the specified EmployeeDetails from storage",
     *      tags={"EmployeeDetails"},
     *      description="Delete EmployeeDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeDetails",
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
        /** @var EmployeeDetails $employeeDetails */
        $employeeDetails = $this->employeeDetailsRepository->findWithoutFail($id);

        if (empty($employeeDetails)) {
            return $this->sendError(trans('custom.employee_details_not_found'));
        }

        $employeeDetails->delete();

        return $this->sendResponse($id, trans('custom.employee_details_deleted_successfully'));
    }
}
