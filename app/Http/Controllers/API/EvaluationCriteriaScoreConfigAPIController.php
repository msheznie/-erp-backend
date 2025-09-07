<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaScoreConfigAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaScoreConfigAPIRequest;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaMasterDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Repositories\EvaluationCriteriaScoreConfigRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EvaluationCriteriaScoreConfigController
 * @package App\Http\Controllers\API
 */

class EvaluationCriteriaScoreConfigAPIController extends AppBaseController
{
    /** @var  EvaluationCriteriaScoreConfigRepository */
    private $evaluationCriteriaScoreConfigRepository;

    public function __construct(EvaluationCriteriaScoreConfigRepository $evaluationCriteriaScoreConfigRepo)
    {
        $this->evaluationCriteriaScoreConfigRepository = $evaluationCriteriaScoreConfigRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaScoreConfigs",
     *      summary="Get a listing of the EvaluationCriteriaScoreConfigs.",
     *      tags={"EvaluationCriteriaScoreConfig"},
     *      description="Get all EvaluationCriteriaScoreConfigs",
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
     *                  @SWG\Items(ref="#/definitions/EvaluationCriteriaScoreConfig")
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
        $this->evaluationCriteriaScoreConfigRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationCriteriaScoreConfigRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationCriteriaScoreConfigs = $this->evaluationCriteriaScoreConfigRepository->all();

        return $this->sendResponse($evaluationCriteriaScoreConfigs->toArray(), trans('custom.evaluation_criteria_score_configs_retrieved_succes'));
    }

    /**
     * @param CreateEvaluationCriteriaScoreConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/evaluationCriteriaScoreConfigs",
     *      summary="Store a newly created EvaluationCriteriaScoreConfig in storage",
     *      tags={"EvaluationCriteriaScoreConfig"},
     *      description="Store EvaluationCriteriaScoreConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaScoreConfig that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaScoreConfig")
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
     *                  ref="#/definitions/EvaluationCriteriaScoreConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationCriteriaScoreConfigAPIRequest $request)
    {
        $input = $request->all();

        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->create($input);

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), trans('custom.evaluation_criteria_score_config_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaScoreConfigs/{id}",
     *      summary="Display the specified EvaluationCriteriaScoreConfig",
     *      tags={"EvaluationCriteriaScoreConfig"},
     *      description="Get EvaluationCriteriaScoreConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaScoreConfig",
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
     *                  ref="#/definitions/EvaluationCriteriaScoreConfig"
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
        /** @var EvaluationCriteriaScoreConfig $evaluationCriteriaScoreConfig */
        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaScoreConfig)) {
            return $this->sendError(trans('custom.evaluation_criteria_score_config_not_found'));
        }

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), trans('custom.evaluation_criteria_score_config_retrieved_success'));
    }

    /**
     * @param int $id
     * @param UpdateEvaluationCriteriaScoreConfigAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/evaluationCriteriaScoreConfigs/{id}",
     *      summary="Update the specified EvaluationCriteriaScoreConfig in storage",
     *      tags={"EvaluationCriteriaScoreConfig"},
     *      description="Update EvaluationCriteriaScoreConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaScoreConfig",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaScoreConfig that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaScoreConfig")
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
     *                  ref="#/definitions/EvaluationCriteriaScoreConfig"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationCriteriaScoreConfigAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationCriteriaScoreConfig $evaluationCriteriaScoreConfig */
        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaScoreConfig)) {
            return $this->sendError(trans('custom.evaluation_criteria_score_config_not_found'));
        }

        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->update($input, $id);

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), trans('custom.evaluationcriteriascoreconfig_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/evaluationCriteriaScoreConfigs/{id}",
     *      summary="Remove the specified EvaluationCriteriaScoreConfig from storage",
     *      tags={"EvaluationCriteriaScoreConfig"},
     *      description="Delete EvaluationCriteriaScoreConfig",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaScoreConfig",
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
        /** @var EvaluationCriteriaScoreConfig $evaluationCriteriaScoreConfig */
        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaScoreConfig)) {
            return $this->sendError(trans('custom.evaluation_criteria_score_config_not_found'));
        }

        $evaluationCriteriaScoreConfig->delete();

        return $this->sendSuccess('Evaluation Criteria Score Config deleted successfully');
    }

    public function removeCriteriaConfig(Request $request)
    {
        $input = $request->all();
        try{
            $deleteCriteriaScore = $this->evaluationCriteriaScoreConfigRepository->removeCriteriaConfig($input);
            if(!$deleteCriteriaScore['success']){
                return $this->sendError($deleteCriteriaScore['message']);
            }
            return $this->sendResponse([], trans('custom.score_configuration_deleted_successfully'));
        } catch (\Exception $ex){
            return $this->sendError(trans('custom.unexpected_error') . $ex->getMessage());
        }
    }

    public function addEvaluationCriteriaConfig(Request $request)
    {
        $input = $request->all();
        try {
            return $this->evaluationCriteriaScoreConfigRepository->addEvaluationCriteriaConfig($input);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateCriteriaScore(Request $request)
    {
        $input = $request->all();

        try {
            return $this->evaluationCriteriaScoreConfigRepository->updateCriteriaScore($input);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
