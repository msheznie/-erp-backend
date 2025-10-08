<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationTemplateSectionFormulaAPIRequest;
use App\Http\Requests\API\UpdateEvaluationTemplateSectionFormulaAPIRequest;
use App\Models\EvaluationTemplateSectionFormula;
use App\Repositories\EvaluationTemplateSectionFormulaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationTemplateSectionFormulaController
 * @package App\Http\Controllers\API
 */

class EvaluationTemplateSectionFormulaAPIController extends AppBaseController
{
    /** @var  EvaluationTemplateSectionFormulaRepository */
    private $evaluationTemplateSectionFormulaRepository;

    public function __construct(EvaluationTemplateSectionFormulaRepository $evaluationTemplateSectionFormulaRepo)
    {
        $this->evaluationTemplateSectionFormulaRepository = $evaluationTemplateSectionFormulaRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationTemplateSectionFormulas",
     *      summary="getEvaluationTemplateSectionFormulaList",
     *      tags={"EvaluationTemplateSectionFormula"},
     *      description="Get all EvaluationTemplateSectionFormulas",
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
     *                  @OA\Items(ref="#/definitions/EvaluationTemplateSectionFormula")
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
        $this->evaluationTemplateSectionFormulaRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationTemplateSectionFormulaRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationTemplateSectionFormulas = $this->evaluationTemplateSectionFormulaRepository->all();

        return $this->sendResponse($evaluationTemplateSectionFormulas->toArray(), trans('custom.evaluation_template_section_formulas_retrieved_suc'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/evaluationTemplateSectionFormulas",
     *      summary="createEvaluationTemplateSectionFormula",
     *      tags={"EvaluationTemplateSectionFormula"},
     *      description="Create EvaluationTemplateSectionFormula",
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
     *                  ref="#/definitions/EvaluationTemplateSectionFormula"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationTemplateSectionFormulaAPIRequest $request)
    {
        $input = $request->all();

        $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->create($input);

        return $this->sendResponse($evaluationTemplateSectionFormula->toArray(), trans('custom.evaluation_template_section_formula_saved_successf'));
    }

    public function getTemplateSectionFormula(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $formula = EvaluationTemplateSectionFormula::with(['label', 'table'])
                                                    ->where('supplier_evaluation_template_id',$input['supplier_evaluation_template_id'] );

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $formula = $formula->where(function ($query) use ($search) {
                $query->where('formulaName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($formula)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationTemplateSectionFormulas/{id}",
     *      summary="getEvaluationTemplateSectionFormulaItem",
     *      tags={"EvaluationTemplateSectionFormula"},
     *      description="Get EvaluationTemplateSectionFormula",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionFormula",
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
     *                  ref="#/definitions/EvaluationTemplateSectionFormula"
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
        /** @var EvaluationTemplateSectionFormula $evaluationTemplateSectionFormula */
        $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionFormula)) {
            return $this->sendError(trans('custom.evaluation_template_section_formula_not_found'));
        }

        return $this->sendResponse($evaluationTemplateSectionFormula->toArray(), trans('custom.evaluation_template_section_formula_retrieved_succ'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/evaluationTemplateSectionFormulas/{id}",
     *      summary="updateEvaluationTemplateSectionFormula",
     *      tags={"EvaluationTemplateSectionFormula"},
     *      description="Update EvaluationTemplateSectionFormula",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionFormula",
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
     *                  ref="#/definitions/EvaluationTemplateSectionFormula"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationTemplateSectionFormulaAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationTemplateSectionFormula $evaluationTemplateSectionFormula */
        $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionFormula)) {
            return $this->sendError(trans('custom.evaluation_template_section_formula_not_found'));
        }

        $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->update($input, $id);

        return $this->sendResponse($evaluationTemplateSectionFormula->toArray(), trans('custom.evaluationtemplatesectionformula_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/evaluationTemplateSectionFormulas/{id}",
     *      summary="deleteEvaluationTemplateSectionFormula",
     *      tags={"EvaluationTemplateSectionFormula"},
     *      description="Delete EvaluationTemplateSectionFormula",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionFormula",
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
        /** @var EvaluationTemplateSectionFormula $evaluationTemplateSectionFormula */
        $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionFormula)) {
            return $this->sendError(trans('custom.evaluation_template_section_formula_not_found'));
        }

        $evaluationTemplateSectionFormula->delete();

        return $this->sendResponse($id,trans('custom.evaluation_template_section_formula_deleted_succes'));
    }
}
