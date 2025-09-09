<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeProfileAPIRequest;
use App\Http\Requests\API\UpdateEmployeeProfileAPIRequest;
use App\Models\EmployeeProfile;
use App\Repositories\EmployeeProfileRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeProfileController
 * @package App\Http\Controllers\API
 */

class EmployeeProfileAPIController extends AppBaseController
{
    /** @var  EmployeeProfileRepository */
    private $employeeProfileRepository;

    public function __construct(EmployeeProfileRepository $employeeProfileRepo)
    {
        $this->employeeProfileRepository = $employeeProfileRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeProfiles",
     *      summary="Get a listing of the EmployeeProfiles.",
     *      tags={"EmployeeProfile"},
     *      description="Get all EmployeeProfiles",
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
     *                  @SWG\Items(ref="#/definitions/EmployeeProfile")
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
        $this->employeeProfileRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeProfileRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeeProfiles = $this->employeeProfileRepository->all();

        return $this->sendResponse($employeeProfiles->toArray(), trans('custom.employee_profiles_retrieved_successfully'));
    }

    /**
     * @param CreateEmployeeProfileAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/employeeProfiles",
     *      summary="Store a newly created EmployeeProfile in storage",
     *      tags={"EmployeeProfile"},
     *      description="Store EmployeeProfile",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeProfile that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeProfile")
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
     *                  ref="#/definitions/EmployeeProfile"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEmployeeProfileAPIRequest $request)
    {
        $input = $request->all();

        $employeeProfiles = $this->employeeProfileRepository->create($input);

        return $this->sendResponse($employeeProfiles->toArray(), trans('custom.employee_profile_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/employeeProfiles/{id}",
     *      summary="Display the specified EmployeeProfile",
     *      tags={"EmployeeProfile"},
     *      description="Get EmployeeProfile",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeProfile",
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
     *                  ref="#/definitions/EmployeeProfile"
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
        /** @var EmployeeProfile $employeeProfile */
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            return $this->sendError(trans('custom.employee_profile_not_found'));
        }

        return $this->sendResponse($employeeProfile->toArray(), trans('custom.employee_profile_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEmployeeProfileAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/employeeProfiles/{id}",
     *      summary="Update the specified EmployeeProfile in storage",
     *      tags={"EmployeeProfile"},
     *      description="Update EmployeeProfile",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeProfile",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EmployeeProfile that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EmployeeProfile")
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
     *                  ref="#/definitions/EmployeeProfile"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEmployeeProfileAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeeProfile $employeeProfile */
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            return $this->sendError(trans('custom.employee_profile_not_found'));
        }

        $employeeProfile = $this->employeeProfileRepository->update($input, $id);

        return $this->sendResponse($employeeProfile->toArray(), trans('custom.employeeprofile_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/employeeProfiles/{id}",
     *      summary="Remove the specified EmployeeProfile from storage",
     *      tags={"EmployeeProfile"},
     *      description="Delete EmployeeProfile",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EmployeeProfile",
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
        /** @var EmployeeProfile $employeeProfile */
        $employeeProfile = $this->employeeProfileRepository->findWithoutFail($id);

        if (empty($employeeProfile)) {
            return $this->sendError(trans('custom.employee_profile_not_found'));
        }

        $employeeProfile->delete();

        return $this->sendResponse($id, trans('custom.employee_profile_deleted_successfully'));
    }
}
