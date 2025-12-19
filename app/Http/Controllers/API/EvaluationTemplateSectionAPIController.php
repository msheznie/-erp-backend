<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationTemplateSectionAPIRequest;
use App\Http\Requests\API\UpdateEvaluationTemplateSectionAPIRequest;
use App\Models\EvaluationTemplateSection;
use App\Repositories\EvaluationTemplateSectionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\EvaluationTemplateSectionFormula;
use App\Models\EvaluationTemplateSectionLabel;
use App\Models\SupplierEvaluationMasters;
use App\Models\SupplierEvaluationTemplateSectionTable;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationTemplateSectionController
 * @package App\Http\Controllers\API
 */

class EvaluationTemplateSectionAPIController extends AppBaseController
{
    /** @var  EvaluationTemplateSectionRepository */
    private $evaluationTemplateSectionRepository;

    public function __construct(EvaluationTemplateSectionRepository $evaluationTemplateSectionRepo)
    {
        $this->evaluationTemplateSectionRepository = $evaluationTemplateSectionRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationTemplateSections",
     *      summary="getEvaluationTemplateSectionList",
     *      tags={"EvaluationTemplateSection"},
     *      description="Get all EvaluationTemplateSections",
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
     *                  @OA\Items(ref="#/definitions/EvaluationTemplateSection")
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
        $this->evaluationTemplateSectionRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationTemplateSectionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationTemplateSections = $this->evaluationTemplateSectionRepository->all();

        return $this->sendResponse($evaluationTemplateSections->toArray(), trans('custom.evaluation_template_sections_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/evaluationTemplateSections",
     *      summary="createEvaluationTemplateSection",
     *      tags={"EvaluationTemplateSection"},
     *      description="Create EvaluationTemplateSection",
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
     *                  ref="#/definitions/EvaluationTemplateSection"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationTemplateSectionAPIRequest $request)
    {
        $input = $request->all();

        $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->create($input);

        return $this->sendResponse($evaluationTemplateSection->toArray(), trans('custom.evaluation_template_section_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/evaluationTemplateSections/{id}",
     *      summary="getEvaluationTemplateSectionItem",
     *      tags={"EvaluationTemplateSection"},
     *      description="Get EvaluationTemplateSection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSection",
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
     *                  ref="#/definitions/EvaluationTemplateSection"
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
        /** @var EvaluationTemplateSection $evaluationTemplateSection */
        $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSection)) {
            return $this->sendError(trans('custom.evaluation_template_section_not_found'));
        }

        return $this->sendResponse($evaluationTemplateSection->toArray(), trans('custom.evaluation_template_section_retrieved_successfully'));
    }

    public function getTemplateSectionFormData(Request $request)  {
        $input = $request->all();
        $templateId = $input['id'];

        $evaluationTemplateSection = EvaluationTemplateSection::where('supplier_evaluation_template_id',$templateId)->get();
        $evaluationTemplateSectionLabel = EvaluationTemplateSectionLabel::where('supplier_evaluation_template_id',$templateId)->get();
        $evaluationTemplateSectionFormula = EvaluationTemplateSectionFormula::where('supplier_evaluation_template_id',$templateId)->get();
        $evaluationTemplateSectionTable = SupplierEvaluationTemplateSectionTable::where('supplier_evaluation_template_id',$templateId)
                                                                                ->where('isConfirmed', 1)
                                                                                ->whereHas('column', function ($query) {
                                                                                    $query->where('column_type', 4);
                                                                                })
                                                                                ->get();

        $supplierEvaluationMasters = SupplierEvaluationMasters::where('is_active',1)->where('is_confirmed', 1)->get();
        $data = [
            'evaluationTemplateSection' => $evaluationTemplateSection,
            'evaluationTemplateSectionLabel' => $evaluationTemplateSectionLabel,
            'evaluationTemplateSectionFormula' => $evaluationTemplateSectionFormula,
            'evaluationTemplateSectionTable' => $evaluationTemplateSectionTable,
            'supplierEvaluationMasters' => $supplierEvaluationMasters,
        ];
        return $this->sendResponse($data, trans('custom.evaluation_template_section_form_data_retrieved_su'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/evaluationTemplateSections/{id}",
     *      summary="updateEvaluationTemplateSection",
     *      tags={"EvaluationTemplateSection"},
     *      description="Update EvaluationTemplateSection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSection",
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
     *                  ref="#/definitions/EvaluationTemplateSection"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationTemplateSectionAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationTemplateSection $evaluationTemplateSection */
        $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSection)) {
            return $this->sendError(trans('custom.evaluation_template_section_not_found'));
        }

        $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->update($input, $id);

        return $this->sendResponse($evaluationTemplateSection->toArray(), trans('custom.evaluationtemplatesection_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/evaluationTemplateSections/{id}",
     *      summary="deleteEvaluationTemplateSection",
     *      tags={"EvaluationTemplateSection"},
     *      description="Delete EvaluationTemplateSection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of EvaluationTemplateSection",
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
        /** @var EvaluationTemplateSection $evaluationTemplateSection */
        $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->findWithoutFail($id);

        if (empty($evaluationTemplateSection)) {
            return $this->sendError(trans('custom.evaluation_template_section_not_found'));
        }

        $evaluationTemplateSection->delete();

        return $this->sendSuccess('Evaluation Template Section deleted successfully');
    }
}
