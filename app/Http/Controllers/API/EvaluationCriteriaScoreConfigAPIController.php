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

        return $this->sendResponse($evaluationCriteriaScoreConfigs->toArray(), 'Evaluation Criteria Score Configs retrieved successfully');
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

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), 'Evaluation Criteria Score Config saved successfully');
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
            return $this->sendError('Evaluation Criteria Score Config not found');
        }

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), 'Evaluation Criteria Score Config retrieved successfully');
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
            return $this->sendError('Evaluation Criteria Score Config not found');
        }

        $evaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepository->update($input, $id);

        return $this->sendResponse($evaluationCriteriaScoreConfig->toArray(), 'EvaluationCriteriaScoreConfig updated successfully');
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
            return $this->sendError('Evaluation Criteria Score Config not found');
        }

        $evaluationCriteriaScoreConfig->delete();

        return $this->sendSuccess('Evaluation Criteria Score Config deleted successfully');
    }

    public function removeCriteriaConfig(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $min_value = 0;
        $max_value = 0;
        $x=1;
        $fromTender = $input['fromTender'] ?? false;
        $model = $fromTender ? EvaluationCriteriaDetails::class : EvaluationCriteriaMasterDetails::class;
        DB::beginTransaction();
        try {
            $result = EvaluationCriteriaScoreConfig::where('id',$input['id'])->delete();
            if($result) {
                $criteriaConfig = EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                ->where('criteria_detail_id',$input['criteria_detail_id'])
                    ->get();
                foreach ($criteriaConfig as $val){
                    if($x==1){
                        $min_value = $val['score'];
                    }

                    if($val['score']>$max_value){
                        $max_value = $val['score'];
                    }

                    if($val['score']<$min_value){
                        $min_value = $val['score'];
                    }

                    $ans['max_value'] = $max_value;
                    $ans['min_value'] = $min_value;
                    $model::where('id',$input['criteria_detail_id'])->update($ans);
                    $x++;
                }
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function addEvaluationCriteriaConfig(Request $request)
    {
        $input = $request->all();
        $fromTender = $input['fromTender'] ?? false;
        $employee = \Helper::getEmployeeInfo();
        $min_value = 0;
        $max_value = 0;
        $x=1;
        $model = $fromTender ? EvaluationCriteriaDetails::class : EvaluationCriteriaMasterDetails::class;
        DB::beginTransaction();
        try {
            $drop['criteria_detail_id'] = $input['criteria_detail_id'];
            $drop['label'] = $input['label'];
            $drop['score'] = $input['score'];
            $drop['fromTender'] = $fromTender;
            $drop['created_by'] = $employee->employeeSystemID;
            $result = EvaluationCriteriaScoreConfig::create($drop);
            if($result){
                $criteriaConfig = EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                ->where('criteria_detail_id',$input['criteria_detail_id'])->get();

                foreach ($criteriaConfig as $val){
                    if($x==1){
                        $min_value = $val['score'];
                    }

                    if($val['score']>$max_value){
                        $max_value = $val['score'];
                    }

                    if($val['score']<$min_value){
                        $min_value = $val['score'];
                    }

                    $ans['max_value'] = $max_value;
                    $ans['min_value'] = $min_value;

                    $model::where('id',$input['criteria_detail_id'])->update($ans);

                    $x++;
                }

                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function updateCriteriaScore(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $ScoreConfig = EvaluationCriteriaScoreConfig::where('id',$input['id'])->first();

        DB::beginTransaction();
        try {
            $data['score']=$input['score'];
            $data['updated_by'] = $employee->employeeSystemID;
            $result = EvaluationCriteriaScoreConfig::where('id',$input['id'])->update($data);
            if($result){
                $x=1;
                $min_value = 0;
                $max_value = 0;
                $criteriaConfig = EvaluationCriteriaScoreConfig::where('criteria_detail_id',$ScoreConfig['criteria_detail_id'])->get();
                foreach ($criteriaConfig as $val){
                    if($x==1){
                        $min_value = $val['score'];
                    }
                    if($val['score']>$max_value){
                        $max_value = $val['score'];
                    }
                    if($val['score']<$min_value){
                        $min_value = $val['score'];
                    }
                    $ans['max_value'] = $max_value;
                    $ans['min_value'] = $min_value;
                    EvaluationCriteriaMasterDetails::where('id',$ScoreConfig['criteria_detail_id'])->update($ans);
                    $x++;
                }

                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
}
