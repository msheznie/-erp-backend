<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrEmpDepartmentsAPIRequest;
use App\Http\Requests\API\UpdateHrEmpDepartmentsAPIRequest;
use App\Models\HrEmpDepartments;
use App\Repositories\HrEmpDepartmentsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrEmpDepartmentsController
 * @package App\Http\Controllers\API
 */

class HrEmpDepartmentsAPIController extends AppBaseController
{
    /** @var  HrEmpDepartmentsRepository */
    private $hrEmpDepartmentsRepository;

    public function __construct(HrEmpDepartmentsRepository $hrEmpDepartmentsRepo)
    {
        $this->hrEmpDepartmentsRepository = $hrEmpDepartmentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hrEmpDepartments",
     *      summary="getHrEmpDepartmentsList",
     *      tags={"HrEmpDepartments"},
     *      description="Get all HrEmpDepartments",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/HrEmpDepartments")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->hrEmpDepartmentsRepository->pushCriteria(new RequestCriteria($request));
        $this->hrEmpDepartmentsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->all();

        return $this->sendResponse($hrEmpDepartments->toArray(), trans('custom.hr_emp_departments_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hrEmpDepartments",
     *      summary="createHrEmpDepartments",
     *      tags={"HrEmpDepartments"},
     *      description="Create HrEmpDepartments",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrEmpDepartments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrEmpDepartmentsAPIRequest $request)
    {
        $input = $request->all();

        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->create($input);

        return $this->sendResponse($hrEmpDepartments->toArray(), trans('custom.hr_emp_departments_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hrEmpDepartments/{id}",
     *      summary="getHrEmpDepartmentsItem",
     *      tags={"HrEmpDepartments"},
     *      description="Get HrEmpDepartments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrEmpDepartments",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrEmpDepartments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var HrEmpDepartments $hrEmpDepartments */
        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->findWithoutFail($id);

        if (empty($hrEmpDepartments)) {
            return $this->sendError(trans('custom.hr_emp_departments_not_found'));
        }

        return $this->sendResponse($hrEmpDepartments->toArray(), trans('custom.hr_emp_departments_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hrEmpDepartments/{id}",
     *      summary="updateHrEmpDepartments",
     *      tags={"HrEmpDepartments"},
     *      description="Update HrEmpDepartments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrEmpDepartments",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrEmpDepartments"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrEmpDepartmentsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrEmpDepartments $hrEmpDepartments */
        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->findWithoutFail($id);

        if (empty($hrEmpDepartments)) {
            return $this->sendError(trans('custom.hr_emp_departments_not_found'));
        }

        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->update($input, $id);

        return $this->sendResponse($hrEmpDepartments->toArray(), trans('custom.hrempdepartments_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hrEmpDepartments/{id}",
     *      summary="deleteHrEmpDepartments",
     *      tags={"HrEmpDepartments"},
     *      description="Delete HrEmpDepartments",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrEmpDepartments",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var HrEmpDepartments $hrEmpDepartments */
        $hrEmpDepartments = $this->hrEmpDepartmentsRepository->findWithoutFail($id);

        if (empty($hrEmpDepartments)) {
            return $this->sendError(trans('custom.hr_emp_departments_not_found'));
        }

        $hrEmpDepartments->delete();

        return $this->sendSuccess('Hr Emp Departments deleted successfully');
    }
}
