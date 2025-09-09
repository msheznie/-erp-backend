<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepBudgetPlDetEmpColumnAPIRequest;
use App\Http\Requests\API\UpdateDepBudgetPlDetEmpColumnAPIRequest;
use App\Models\DepBudgetPlDetColumn;
use App\Models\DepBudgetPlDetEmpColumn;
use App\Repositories\DepBudgetPlDetEmpColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DepBudgetPlDetEmpColumnController
 * @package App\Http\Controllers\API
 */

class DepBudgetPlDetEmpColumnAPIController extends AppBaseController
{
    /** @var  DepBudgetPlDetEmpColumnRepository */
    private $depBudgetPlDetEmpColumnRepository;

    public function __construct(DepBudgetPlDetEmpColumnRepository $depBudgetPlDetEmpColumnRepo)
    {
        $this->depBudgetPlDetEmpColumnRepository = $depBudgetPlDetEmpColumnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/depBudgetPlDetEmpColumns",
     *      summary="getDepBudgetPlDetEmpColumnList",
     *      tags={"DepBudgetPlDetEmpColumn"},
     *      description="Get all DepBudgetPlDetEmpColumns",
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
     *                  @OA\Items(ref="#/definitions/DepBudgetPlDetEmpColumn")
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
        $this->depBudgetPlDetEmpColumnRepository->pushCriteria(new RequestCriteria($request));
        $this->depBudgetPlDetEmpColumnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $depBudgetPlDetEmpColumns = $this->depBudgetPlDetEmpColumnRepository->all();

        return $this->sendResponse($depBudgetPlDetEmpColumns->toArray(), trans('custom.dep_budget_pl_det_emp_columns_retrieved_successful'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/depBudgetPlDetEmpColumns",
     *      summary="createDepBudgetPlDetEmpColumn",
     *      tags={"DepBudgetPlDetEmpColumn"},
     *      description="Create DepBudgetPlDetEmpColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetEmpColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepBudgetPlDetEmpColumnAPIRequest $request)
    {
        $input = $request->all();

        $depBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepository->create($input);

        return $this->sendResponse($depBudgetPlDetEmpColumn->toArray(), trans('custom.dep_budget_pl_det_emp_column_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/depBudgetPlDetEmpColumns/{id}",
     *      summary="getDepBudgetPlDetEmpColumnItem",
     *      tags={"DepBudgetPlDetEmpColumn"},
     *      description="Get DepBudgetPlDetEmpColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetEmpColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetEmpColumn"
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
        /** @var DepBudgetPlDetEmpColumn $depBudgetPlDetEmpColumn */
        $depBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetEmpColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_emp_column_not_found'));
        }

        return $this->sendResponse($depBudgetPlDetEmpColumn->toArray(), trans('custom.dep_budget_pl_det_emp_column_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/depBudgetPlDetEmpColumns/{id}",
     *      summary="updateDepBudgetPlDetEmpColumn",
     *      tags={"DepBudgetPlDetEmpColumn"},
     *      description="Update DepBudgetPlDetEmpColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetEmpColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetEmpColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepBudgetPlDetEmpColumnAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepBudgetPlDetEmpColumn $depBudgetPlDetEmpColumn */
        $depBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetEmpColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_emp_column_not_found'));
        }

        $depBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepository->update($input, $id);

        return $this->sendResponse($depBudgetPlDetEmpColumn->toArray(), trans('custom.depbudgetpldetempcolumn_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/depBudgetPlDetEmpColumns/{id}",
     *      summary="deleteDepBudgetPlDetEmpColumn",
     *      tags={"DepBudgetPlDetEmpColumn"},
     *      description="Delete DepBudgetPlDetEmpColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetEmpColumn",
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
        /** @var DepBudgetPlDetEmpColumn $depBudgetPlDetEmpColumn */
        $depBudgetPlDetEmpColumn = $this->depBudgetPlDetEmpColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetEmpColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_emp_column_not_found'));
        }

        $depBudgetPlDetEmpColumn->delete();

        return $this->sendSuccess('Dep Budget Pl Det Emp Column deleted successfully');
    }

    /**
     * Save multiple employee column assignments for current authenticated employee
     *
     * @param Request $request
     * @return Response
     */
    public function saveDepBudgetPlEmpColumns(Request $request)
    {
        $data = $request->all();

        if (!isset($data['selectedColumns'])) {
            return $this->sendError(trans('custom.selected_column_list_is_required'));
        }

        $empID = Auth::user()->employee_id;
        
        if (!$empID) {
            return $this->sendError(trans('custom.employee_id_not_found'));
        }

        DepBudgetPlDetEmpColumn::where('empID', $empID)->where('companySystemID', $data['companySystemID'])->delete();

        $selectedColumns = collect($data['selectedColumns'])->pluck('id')->toArray();
        array_push($selectedColumns, ...[1,4,6]);
        sort($selectedColumns);

        $dataset = [];

        foreach ($selectedColumns as $selectedColumn) {
            $dataset[] = [
                'companySystemID' => $data['companySystemID'],
                'empID' => $empID,
                'columnID' => $selectedColumn
            ];
        }

        if (count($dataset) > 0) {
            DepBudgetPlDetEmpColumn::insert($dataset);
        }

        $empColumns = DepBudgetPlDetEmpColumn::with(['column'])->where('empID', $empID)->where('companySystemID', $data['companySystemID'])->get();

        return $this->sendResponse($empColumns, trans('custom.employee_columns_saved_successfully'));
    }

    public function getDepBudgetPlDetEmpColumns(Request $request)
    {
        $data = $request->all();

        $empID = Auth::user()->employee_id;

        if (!$empID) {
            return $this->sendError(trans('custom.employee_id_not_found'));
        }

        $empColumns = DepBudgetPlDetEmpColumn::with(['column'])->where('empID', $empID)->where('companySystemID', $data['companySystemID'])->get();

        return $this->sendResponse($empColumns, trans('custom.available_columns_retrieved_successfully'));
    }
}
