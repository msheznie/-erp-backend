<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationTemplateSectionLabelAPIRequest;
use App\Http\Requests\API\UpdateEvaluationTemplateSectionLabelAPIRequest;
use App\Models\EvaluationTemplateSectionLabel;
use App\Repositories\EvaluationTemplateSectionLabelRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationTemplateSectionLabelController
 * @package App\Http\Controllers\API
 */

class EvaluationTemplateSectionLabelAPIController extends AppBaseController
{
    /** @var  EvaluationTemplateSectionLabelRepository */
    private $evaluationTemplateSectionLabelRepository;

    public function __construct(EvaluationTemplateSectionLabelRepository $evaluationTemplateSectionLabelRepo)
    {
        $this->evaluationTemplateSectionLabelRepository = $evaluationTemplateSectionLabelRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationTemplateSectionLabels",
     *      summary="getEvaluationTemplateSectionLabelList",
     *      tags={"EvaluationTemplateSectionLabel"},
     *      description="Get all EvaluationTemplateSectionLabels",
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
     *                  @OA\Items(ref="#/definitions/EvaluationTemplateSectionLabel")
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
        $this->evaluationTemplateSectionLabelRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationTemplateSectionLabelRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationTemplateSectionLabels = $this->evaluationTemplateSectionLabelRepository->all();

        return $this->sendResponse($evaluationTemplateSectionLabels->toArray(), 'Evaluation Template Section Labels retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/evaluationTemplateSectionLabels",
     *      summary="createEvaluationTemplateSectionLabel",
     *      tags={"EvaluationTemplateSectionLabel"},
     *      description="Create EvaluationTemplateSectionLabel",
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
     *                  ref="#/definitions/EvaluationTemplateSectionLabel"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationTemplateSectionLabelAPIRequest $request)
    {
        $input = $request->all();

        $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->create($input);

        return $this->sendResponse($evaluationTemplateSectionLabel->toArray(), 'Evaluation Template Section Label saved successfully');
    }

    public function getTemplateSectionLabel(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $validator = \Validator::make($input, [
            'supplier_evaluation_template_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $labels = EvaluationTemplateSectionLabel::where('supplier_evaluation_template_id',$input['supplier_evaluation_template_id'] );

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $labels = $labels->where(function ($query) use ($search) {
                $query->where('labelName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($labels)
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
     *      path="/evaluationTemplateSectionLabels/{id}",
     *      summary="getEvaluationTemplateSectionLabelItem",
     *      tags={"EvaluationTemplateSectionLabel"},
     *      description="Get EvaluationTemplateSectionLabel",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionLabel",
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
     *                  ref="#/definitions/EvaluationTemplateSectionLabel"
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
        /** @var EvaluationTemplateSectionLabel $evaluationTemplateSectionLabel */
        $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionLabel)) {
            return $this->sendError('Evaluation Template Section Label not found');
        }

        return $this->sendResponse($evaluationTemplateSectionLabel->toArray(), 'Evaluation Template Section Label retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/evaluationTemplateSectionLabels/{id}",
     *      summary="updateEvaluationTemplateSectionLabel",
     *      tags={"EvaluationTemplateSectionLabel"},
     *      description="Update EvaluationTemplateSectionLabel",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionLabel",
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
     *                  ref="#/definitions/EvaluationTemplateSectionLabel"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationTemplateSectionLabelAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationTemplateSectionLabel $evaluationTemplateSectionLabel */
        $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionLabel)) {
            return $this->sendError('Evaluation Template Section Label not found');
        }

        $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->update($input, $id);

        return $this->sendResponse($evaluationTemplateSectionLabel->toArray(), 'EvaluationTemplateSectionLabel updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/evaluationTemplateSectionLabels/{id}",
     *      summary="deleteEvaluationTemplateSectionLabel",
     *      tags={"EvaluationTemplateSectionLabel"},
     *      description="Delete EvaluationTemplateSectionLabel",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSectionLabel",
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
        /** @var EvaluationTemplateSectionLabel $evaluationTemplateSectionLabel */
        $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSectionLabel)) {
            return $this->sendError('Evaluation Template Section Label not found');
        }

        $evaluationTemplateSectionLabel->delete();

        return $this->sendResponse($id,'Evaluation Template Section Label deleted successfully');
    }
}
