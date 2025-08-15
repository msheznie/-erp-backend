<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetPlanningAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetPlanningAPIRequest;
use App\Models\DepartmentBudgetPlanning;
use App\Repositories\DepartmentBudgetPlanningRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DepartmentBudgetPlanningController
 * @package App\Http\Controllers\API
 */

class DepartmentBudgetPlanningAPIController extends AppBaseController
{
    /** @var  DepartmentBudgetPlanningRepository */
    private $departmentBudgetPlanningRepository;

    public function __construct(DepartmentBudgetPlanningRepository $departmentBudgetPlanningRepo)
    {
        $this->departmentBudgetPlanningRepository = $departmentBudgetPlanningRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentBudgetPlannings",
     *      summary="getDepartmentBudgetPlanningList",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Get all DepartmentBudgetPlannings",
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
     *                  @OA\Items(ref="#/definitions/DepartmentBudgetPlanning")
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
        $this->departmentBudgetPlanningRepository->pushCriteria(new RequestCriteria($request));
        $this->departmentBudgetPlanningRepository->pushCriteria(new LimitOffsetCriteria($request));
        $departmentBudgetPlannings = $this->departmentBudgetPlanningRepository->all();

        return $this->sendResponse($departmentBudgetPlannings->toArray(), 'Department Budget Plannings retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/departmentBudgetPlannings",
     *      summary="createDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Create DepartmentBudgetPlanning",
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
     *                  ref="#/definitions/DepartmentBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepartmentBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();

        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->create($input);

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'Department Budget Planning saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="getDepartmentBudgetPlanningItem",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Get DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
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
     *                  ref="#/definitions/DepartmentBudgetPlanning"
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
        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'Department Budget Planning retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="updateDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Update DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
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
     *                  ref="#/definitions/DepartmentBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepartmentBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->update($input, $id);

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'DepartmentBudgetPlanning updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="deleteDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Delete DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
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
        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        $departmentBudgetPlanning->delete();

        return $this->sendSuccess('Department Budget Planning deleted successfully');
    }
}
