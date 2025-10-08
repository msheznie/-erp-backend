<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaTypeAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaTypeAPIRequest;
use App\Models\EvaluationCriteriaType;
use App\Repositories\EvaluationCriteriaTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationCriteriaTypeController
 * @package App\Http\Controllers\API
 */

class EvaluationCriteriaTypeAPIController extends AppBaseController
{
    /** @var  EvaluationCriteriaTypeRepository */
    private $evaluationCriteriaTypeRepository;

    public function __construct(EvaluationCriteriaTypeRepository $evaluationCriteriaTypeRepo)
    {
        $this->evaluationCriteriaTypeRepository = $evaluationCriteriaTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaTypes",
     *      summary="Get a listing of the EvaluationCriteriaTypes.",
     *      tags={"EvaluationCriteriaType"},
     *      description="Get all EvaluationCriteriaTypes",
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
     *                  @SWG\Items(ref="#/definitions/EvaluationCriteriaType")
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
        $this->evaluationCriteriaTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationCriteriaTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationCriteriaTypes = $this->evaluationCriteriaTypeRepository->all();

        return $this->sendResponse($evaluationCriteriaTypes->toArray(), trans('custom.evaluation_criteria_types_retrieved_successfully'));
    }

    /**
     * @param CreateEvaluationCriteriaTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/evaluationCriteriaTypes",
     *      summary="Store a newly created EvaluationCriteriaType in storage",
     *      tags={"EvaluationCriteriaType"},
     *      description="Store EvaluationCriteriaType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaType")
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
     *                  ref="#/definitions/EvaluationCriteriaType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationCriteriaTypeAPIRequest $request)
    {
        $input = $request->all();

        $evaluationCriteriaType = $this->evaluationCriteriaTypeRepository->create($input);

        return $this->sendResponse($evaluationCriteriaType->toArray(), trans('custom.evaluation_criteria_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaTypes/{id}",
     *      summary="Display the specified EvaluationCriteriaType",
     *      tags={"EvaluationCriteriaType"},
     *      description="Get EvaluationCriteriaType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaType",
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
     *                  ref="#/definitions/EvaluationCriteriaType"
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
        /** @var EvaluationCriteriaType $evaluationCriteriaType */
        $evaluationCriteriaType = $this->evaluationCriteriaTypeRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaType)) {
            return $this->sendError(trans('custom.evaluation_criteria_type_not_found'));
        }

        return $this->sendResponse($evaluationCriteriaType->toArray(), trans('custom.evaluation_criteria_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEvaluationCriteriaTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/evaluationCriteriaTypes/{id}",
     *      summary="Update the specified EvaluationCriteriaType in storage",
     *      tags={"EvaluationCriteriaType"},
     *      description="Update EvaluationCriteriaType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaType")
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
     *                  ref="#/definitions/EvaluationCriteriaType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationCriteriaTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationCriteriaType $evaluationCriteriaType */
        $evaluationCriteriaType = $this->evaluationCriteriaTypeRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaType)) {
            return $this->sendError(trans('custom.evaluation_criteria_type_not_found'));
        }

        $evaluationCriteriaType = $this->evaluationCriteriaTypeRepository->update($input, $id);

        return $this->sendResponse($evaluationCriteriaType->toArray(), trans('custom.evaluationcriteriatype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/evaluationCriteriaTypes/{id}",
     *      summary="Remove the specified EvaluationCriteriaType from storage",
     *      tags={"EvaluationCriteriaType"},
     *      description="Delete EvaluationCriteriaType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaType",
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
        /** @var EvaluationCriteriaType $evaluationCriteriaType */
        $evaluationCriteriaType = $this->evaluationCriteriaTypeRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaType)) {
            return $this->sendError(trans('custom.evaluation_criteria_type_not_found'));
        }

        $evaluationCriteriaType->delete();

        return $this->sendSuccess('Evaluation Criteria Type deleted successfully');
    }
}
