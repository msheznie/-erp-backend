<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepBudgetPlDetColumnAPIRequest;
use App\Http\Requests\API\UpdateDepBudgetPlDetColumnAPIRequest;
use App\Models\DepBudgetPlDetColumn;
use App\Models\DepBudgetPlDetEmpColumn;
use App\Repositories\DepBudgetPlDetColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DepBudgetPlDetColumnController
 * @package App\Http\Controllers\API
 */

class DepBudgetPlDetColumnAPIController extends AppBaseController
{
    /** @var  DepBudgetPlDetColumnRepository */
    private $depBudgetPlDetColumnRepository;

    public function __construct(DepBudgetPlDetColumnRepository $depBudgetPlDetColumnRepo)
    {
        $this->depBudgetPlDetColumnRepository = $depBudgetPlDetColumnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/depBudgetPlDetColumns",
     *      summary="getDepBudgetPlDetColumnList",
     *      tags={"DepBudgetPlDetColumn"},
     *      description="Get all DepBudgetPlDetColumns",
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
     *                  @OA\Items(ref="#/definitions/DepBudgetPlDetColumn")
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
        $this->depBudgetPlDetColumnRepository->pushCriteria(new RequestCriteria($request));
        $this->depBudgetPlDetColumnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $depBudgetPlDetColumns = $this->depBudgetPlDetColumnRepository->all();

        return $this->sendResponse($depBudgetPlDetColumns->toArray(), trans('custom.dep_budget_pl_det_columns_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/depBudgetPlDetColumns",
     *      summary="createDepBudgetPlDetColumn",
     *      tags={"DepBudgetPlDetColumn"},
     *      description="Create DepBudgetPlDetColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepBudgetPlDetColumnAPIRequest $request)
    {
        $input = $request->all();

        $depBudgetPlDetColumn = $this->depBudgetPlDetColumnRepository->create($input);

        return $this->sendResponse($depBudgetPlDetColumn->toArray(), trans('custom.dep_budget_pl_det_column_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/depBudgetPlDetColumns/{id}",
     *      summary="getDepBudgetPlDetColumnItem",
     *      tags={"DepBudgetPlDetColumn"},
     *      description="Get DepBudgetPlDetColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetColumn"
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
        /** @var DepBudgetPlDetColumn $depBudgetPlDetColumn */
        $depBudgetPlDetColumn = $this->depBudgetPlDetColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_column_not_found'));
        }

        return $this->sendResponse($depBudgetPlDetColumn->toArray(), trans('custom.dep_budget_pl_det_column_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/depBudgetPlDetColumns/{id}",
     *      summary="updateDepBudgetPlDetColumn",
     *      tags={"DepBudgetPlDetColumn"},
     *      description="Update DepBudgetPlDetColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetColumn",
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
     *                  ref="#/definitions/DepBudgetPlDetColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepBudgetPlDetColumnAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepBudgetPlDetColumn $depBudgetPlDetColumn */
        $depBudgetPlDetColumn = $this->depBudgetPlDetColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_column_not_found'));
        }

        $depBudgetPlDetColumn = $this->depBudgetPlDetColumnRepository->update($input, $id);

        return $this->sendResponse($depBudgetPlDetColumn->toArray(), trans('custom.depbudgetpldetcolumn_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/depBudgetPlDetColumns/{id}",
     *      summary="deleteDepBudgetPlDetColumn",
     *      tags={"DepBudgetPlDetColumn"},
     *      description="Delete DepBudgetPlDetColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepBudgetPlDetColumn",
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
        /** @var DepBudgetPlDetColumn $depBudgetPlDetColumn */
        $depBudgetPlDetColumn = $this->depBudgetPlDetColumnRepository->findWithoutFail($id);

        if (empty($depBudgetPlDetColumn)) {
            return $this->sendError(trans('custom.dep_budget_pl_det_column_not_found'));
        }

        $depBudgetPlDetColumn->delete();

        return $this->sendSuccess('Dep Budget Pl Det Column deleted successfully');
    }

    /**
     * Get available columns for budget planning
     *
     * @param Request $request
     * @return Response
     */
    public function getAllDeptBudgetPlDetColumns(Request $request)
    {
        $empID = Auth::user()->employee_id;

        if (!$empID) {
            return $this->sendError(trans('custom.employee_id_not_found'));
        }

        $empColumns = DepBudgetPlDetEmpColumn::where('empID', $empID)->where('companySystemID', $request->companySystemID)->get();
        $allColumns = DepBudgetPlDetColumn::whereNotIn('id', [1,4,6])->get();

        $data = [
            'empColumns' => $empColumns,
            'allColumns' => $allColumns
        ];
        
        return $this->sendResponse($data, trans('custom.available_columns_retrieved_successfully'));
    }
}
