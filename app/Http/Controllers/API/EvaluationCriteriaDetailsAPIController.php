<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaDetailsAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaDetailsAPIRequest;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaMaster;
use App\Models\EvaluationCriteriaMasterDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\EvaluationCriteriaType;
use App\Models\TenderCriteriaAnswerType;
use App\Models\TenderMaster;
use App\Repositories\EvaluationCriteriaDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use function foo\func;

/**
 * Class EvaluationCriteriaDetailsController
 * @package App\Http\Controllers\API
 */

class EvaluationCriteriaDetailsAPIController extends AppBaseController
{
    /** @var  EvaluationCriteriaDetailsRepository */
    private $evaluationCriteriaDetailsRepository;

    public function __construct(EvaluationCriteriaDetailsRepository $evaluationCriteriaDetailsRepo)
    {
        $this->evaluationCriteriaDetailsRepository = $evaluationCriteriaDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaDetails",
     *      summary="Get a listing of the EvaluationCriteriaDetails.",
     *      tags={"EvaluationCriteriaDetails"},
     *      description="Get all EvaluationCriteriaDetails",
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
     *                  @SWG\Items(ref="#/definitions/EvaluationCriteriaDetails")
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
        $this->evaluationCriteriaDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->evaluationCriteriaDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->all();

        return $this->sendResponse($evaluationCriteriaDetails->toArray(), 'Evaluation Criteria Details retrieved successfully');
    }

    /**
     * @param CreateEvaluationCriteriaDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/evaluationCriteriaDetails",
     *      summary="Store a newly created EvaluationCriteriaDetails in storage",
     *      tags={"EvaluationCriteriaDetails"},
     *      description="Store EvaluationCriteriaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaDetails")
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
     *                  ref="#/definitions/EvaluationCriteriaDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEvaluationCriteriaDetailsAPIRequest $request)
    {
        $input = $request->all();

        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->create($input);

        return $this->sendResponse($evaluationCriteriaDetails->toArray(), 'Evaluation Criteria Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/evaluationCriteriaDetails/{id}",
     *      summary="Display the specified EvaluationCriteriaDetails",
     *      tags={"EvaluationCriteriaDetails"},
     *      description="Get EvaluationCriteriaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetails",
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
     *                  ref="#/definitions/EvaluationCriteriaDetails"
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
        /** @var EvaluationCriteriaDetails $evaluationCriteriaDetails */
        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetails)) {
            return $this->sendError('Evaluation Criteria Details not found');
        }

        return $this->sendResponse($evaluationCriteriaDetails->toArray(), 'Evaluation Criteria Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateEvaluationCriteriaDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/evaluationCriteriaDetails/{id}",
     *      summary="Update the specified EvaluationCriteriaDetails in storage",
     *      tags={"EvaluationCriteriaDetails"},
     *      description="Update EvaluationCriteriaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EvaluationCriteriaDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EvaluationCriteriaDetails")
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
     *                  ref="#/definitions/EvaluationCriteriaDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEvaluationCriteriaDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var EvaluationCriteriaDetails $evaluationCriteriaDetails */
        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetails)) {
            return $this->sendError('Evaluation Criteria Details not found');
        }

        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->update($input, $id);

        return $this->sendResponse($evaluationCriteriaDetails->toArray(), 'EvaluationCriteriaDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/evaluationCriteriaDetails/{id}",
     *      summary="Remove the specified EvaluationCriteriaDetails from storage",
     *      tags={"EvaluationCriteriaDetails"},
     *      description="Delete EvaluationCriteriaDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EvaluationCriteriaDetails",
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
        /** @var EvaluationCriteriaDetails $evaluationCriteriaDetails */
        $evaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepository->findWithoutFail($id);

        if (empty($evaluationCriteriaDetails)) {
            return $this->sendError('Evaluation Criteria Details not found');
        }

        $evaluationCriteriaDetails->delete();

        return $this->sendSuccess('Evaluation Criteria Details deleted successfully');
    }

    public function getEvaluationCriteriaDropDowns(Request $request)
    {
        $input = $request->all();
        $data['criteriaType'] = EvaluationCriteriaType::where('id','!=',3)->get();
        $data['answerType'] = TenderCriteriaAnswerType::get();
        return $data;
    }

    public function addEvaluationCriteria(Request $request)
    {

        $input = $this->convertArrayToSelectedValue($request->all(), array('critera_type_id', 'answer_type_id'));
        $employee = \Helper::getEmployeeInfo();
        $is_final_level = 0;
        $sort_order = 1;
        $fromTender = $input['fromTender'] ?? false;
        $model = $fromTender ? EvaluationCriteriaDetails::class : EvaluationCriteriaMasterDetails::class;

        if($input['tenderMasterId'] == null && isset($input['level']) && $input['level'] !== 0){
            return $this->addMasterEvaluationCriteriaDetail($request);
        } else if(isset($input['level']) && $input['level'] === 0) {
            return $this->addMasterEvaluationCriteria($request);
        } else if(isset($input['pullFromMaster']) && $input['pullFromMaster'] == true){
            $idArray = array_map(function ($item) {
                return $item['id'];
            }, $input['selectedData']);

            return $this->pullFromMasterEvaluationCriteria($request, $idArray, $input['tenderMasterId'], $fromTender);

        } else {
            $sort = EvaluationCriteriaDetails::where('tender_id',$input['tenderMasterId'])->where('level',$input['level'])->where('parent_id',$input['parent_id'])->orderBy('sort_order', 'desc')->first();
            if(!empty($sort)){
                $sort_order = $sort['sort_order'] + 1;
            }
            if(isset($input['is_final_level'])){
                if($input['is_final_level']){
                    $is_final_level = 1;
                }
            }

            if($input['level'] == 1){
                if($input['critera_type_id'] !=1) {
                    if(!isset($input['weightage']) || empty($input['weightage']) || $input['weightage']<= 0){
                        return ['success' => false, 'message' => 'Weightage is required'];
                    }

                    if(!isset($input['passing_weightage']) || empty($input['passing_weightage']) || $input['passing_weightage']<= 0){
                        return ['success' => false, 'message' => 'Passing weightage is required'];
                    }
                }
            }

            if($is_final_level == 1){
                if(!isset($input['answer_type_id']) || empty($input['answer_type_id'])){
                    return ['success' => false, 'message' => 'Answer Type is required'];
                }
            }

            $chkDuplicate =  EvaluationCriteriaDetails::where('tender_id',$input['tenderMasterId'])->where('description',$input['description'])->where('level',$input['level'])->first();

            if(!empty($chkDuplicate)){
                return ['success' => false, 'message' => 'Description cannot be duplicated'];
            }

            DB::beginTransaction();
            try {
                $data['description'] = $input['description'];
                $data['tender_id'] = $input['tenderMasterId'];
                $data['parent_id'] = $input['parent_id'];
                $data['level'] = $input['level'];
                $data['critera_type_id'] = $input['critera_type_id'];
                if(isset($input['answer_type_id'])){
                    $data['answer_type_id'] = $input['answer_type_id'];
                }
                if(!empty($input['weightage'])){
                    $data['weightage'] = $input['weightage'];
                }
                if(!empty($input['passing_weightage'])) {
                    $data['passing_weightage'] = $input['passing_weightage'];
                }
                $data['is_final_level'] = $is_final_level;
                $data['sort_order'] = $sort_order;
                $data['created_by'] = $employee->employeeSystemID;

                if($is_final_level == 1 && $input['critera_type_id'] == 2 && $input['answer_type_id'] == 2 ){
                    if($input['yes_value'] > $input['no_value']){
                        $data['max_value'] = $input['yes_value'];
                        $data['min_value'] = $input['no_value'];
                    }else{
                        $data['max_value'] = $input['no_value'];
                        $data['min_value'] = $input['yes_value'];
                    }
                }

                if($is_final_level == 1 && $input['critera_type_id'] == 2  && ($input['answer_type_id'] == 1 || $input['answer_type_id'] == 3)){
                    $data['max_value'] = $input['max_value'];
                }
                $result = $model::create($data);

                if($result){
                    if($is_final_level == 1 && $input['critera_type_id'] == 2 && $input['answer_type_id'] == 2 ){
                        $datayes['criteria_detail_id'] = $result['id'];
                        $datayes['label'] = $input['yes_label'];
                        $datayes['score'] = $input['yes_value'];
                        $datayes['created_by'] = $employee->employeeSystemID;
                        $datayes['fromTender'] = $fromTender;
                        EvaluationCriteriaScoreConfig::create($datayes);

                        $datano['criteria_detail_id'] = $result['id'];
                        $datano['label'] = $input['no_label'];
                        $datano['score'] = $input['no_value'];
                        $datano['created_by'] = $employee->employeeSystemID;
                        $datano['fromTender'] = $fromTender;
                        EvaluationCriteriaScoreConfig::create($datano);
                    }

                    if($is_final_level == 1 && $input['critera_type_id'] == 2 && ($input['answer_type_id'] == 4 || $input['answer_type_id'] == 5) ){
                        if(count($input['selectedData'])>0){
                            $minAns = 0;
                            $maxAns = 0;
                            $x=1;
                            foreach ($input['selectedData'] as $vl){
                                if($x==1){
                                    $minAns = $vl['drop_value'];
                                }

                                if($vl['drop_value']>$maxAns){
                                    $maxAns = $vl['drop_value'];
                                }

                                if($vl['drop_value']<$minAns){
                                    $minAns = $vl['drop_value'];
                                }

                                $drop['criteria_detail_id'] = $result['id'];
                                $drop['label'] = $vl['drop_label'];
                                $drop['score'] = $vl['drop_value'];
                                $drop['created_by'] = $employee->employeeSystemID;
                                $drop['fromTender'] = $fromTender;
                                EvaluationCriteriaScoreConfig::create($drop);

                                $ans['max_value'] = $maxAns;
                                $ans['min_value'] = $minAns;
                                $model::where('id',$result['id'])->update($ans);
                                $x++;
                            }
                        }else{
                            return ['success' => false, 'message' => 'At least one score configuration is required'];
                        }
                    }


                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully created'];
                }
            }catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
        }

    }

    public function pullFromMasterEvaluationCriteria($request, $ids, $tenderId, $fromTender)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), ['critera_type_id', 'answer_type_id']);
        $results = EvaluationCriteriaMasterDetails::whereIn('evaluation_criteria_master_id', $ids)->get();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();

        // Initialize the parent-child mapping
        $parentMap = [];
        try {
            foreach ($results as $result) {
                $this->insertCriteriaDetail($result, $input, $employee, $tenderId, $parentMap, $fromTender);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Successfully created'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function insertCriteriaDetail($result, $input, $employee, $tenderId, &$parentMap, $fromTender)
    {
        $evaluationCriteriaMasterId = $result->evaluation_criteria_master_id;
        $level = $result->level;

        $parentId = $result->parent_id;

        if ($level > 1) {
            $parentId = $parentMap[$parentId];
        }

        $data = [
            'description' => $result->description,
            'tender_id' => $tenderId,
            'parent_id' => $parentId,
            'level' => $level,
            'critera_type_id' => $result->critera_type_id,
            'max_value' => $result->max_value,
            'sort_order' => $result->sort_order,
            'evaluation_criteria_master_id' => $evaluationCriteriaMasterId,
        ];

        if (isset($result->answer_type_id)) {
            $data['answer_type_id'] = $result->answer_type_id;
        }

        if (!empty($result->weightage)) {
            $data['weightage'] = $result->weightage;
        }

        if (!empty($result->passing_weightage)) {
            $data['passing_weightage'] = $result->passing_weightage;
        }

        $data['is_final_level'] = $result->is_final_level;

        $criteriaDetail = EvaluationCriteriaDetails::create($data);


        if ($result->is_final_level == 1 && $result->critera_type_id == 2)
        {
            if (in_array($result->answer_type_id, [2, 4])) {
                $this->createScoreConfig($result, $criteriaDetail, $employee, $fromTender);
            }
        }

        /*if ($result->is_final_level == 1 && $result->critera_type_id == 2) {
            if($result->answer_type_id == 2 ){
               // EvaluationCriteriaScoreConfig::where('criteria_detail_id', $result['id'])->delete();
                $datayes['criteria_detail_id'] = $criteriaDetail->id;
                $datayes['label'] = 'Yes';
                $datayes['score'] = $result->max_value;
                $datayes['created_by'] = $employee->employeeSystemID;
                EvaluationCriteriaScoreConfig::create($datayes);

                $datano['criteria_detail_id'] = $criteriaDetail->id;
                $datano['label'] = 'No';
                $datano['score'] = $result->min_value;
                $datano['created_by'] = $employee->employeeSystemID;
                EvaluationCriteriaScoreConfig::create($datano);
            }

            if($result->answer_type_id == 4)
            {
                $getEvalCriteriaScore = EvaluationCriteriaScoreConfig::getEvalScore($result['id']);

                if(!empty($getEvalCriteriaScore))
                {
                    foreach ($getEvalCriteriaScore as $key => $value)
                    {
                        $datano['criteria_detail_id'] = $criteriaDetail->id;
                        $datano['label'] = $value['label'];
                        $datano['score']= $value['score'];
                        $datano['created_by'] = $employee->employeeSystemID;
                        EvaluationCriteriaScoreConfig::create($datano);
                    }
                }


            }

            //EvaluationCriteriaScoreConfig::where('criteria_detail_id', null)->update(['criteria_detail_id' => $criteriaDetail->id]);
        }*/

        if ($criteriaDetail) {
            $parentMap[$result->id] = $criteriaDetail->id;
        }
    }

    private function addMasterEvaluationCriteriaDetail(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array('critera_type_id', 'answer_type_id'));
        $employee = \Helper::getEmployeeInfo();
        $is_final_level = 0;
        $sort_order = 1;
        $sort = EvaluationCriteriaMasterDetails::where('level',$input['level'])->where('parent_id',$input['parent_id'])->orderBy('sort_order', 'desc')->first();
        if(!empty($sort)){
            $sort_order = $sort['sort_order'] + 1;
        }
        if(isset($input['is_final_level'])){
            if($input['is_final_level']){
                $is_final_level = 1;
            }
        }

        if($input['level'] == 1){
            if($input['critera_type_id'] !=1) {
                if(!isset($input['weightage']) || empty($input['weightage']) || $input['weightage']<= 0){
                    return ['success' => false, 'message' => 'Weightage is required'];
                }

                if(!isset($input['passing_weightage']) || empty($input['passing_weightage']) || $input['passing_weightage']<= 0){
                    return ['success' => false, 'message' => 'Passing weightage is required'];
                }
            }
        }

        if($is_final_level == 1){
            if(!isset($input['answer_type_id']) || empty($input['answer_type_id'])){
                return ['success' => false, 'message' => 'Answer Type is required'];
            }
        }

        if($input['level'] == 1){
            $chkDuplicate =  EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id',$input['evaluationCriteriaMasterId'])->where('description',$input['description'])->where('level',$input['level'])->first();

            if(!empty($chkDuplicate)){
                return ['success' => false, 'message' => 'Description cannot be duplicated'];
            }
        }


        DB::beginTransaction();
        try {
            $evaluationCriteriaMasterId = $input['evaluationCriteriaMasterId'];
            $data['description'] = $input['description'];
            $data['evaluation_criteria_master_id'] = $evaluationCriteriaMasterId;
            $data['parent_id'] = $input['parent_id'];
            $data['level'] = $input['level'];
            $data['critera_type_id'] = $input['critera_type_id'];
            if(isset($input['answer_type_id'])){
                $data['answer_type_id'] = $input['answer_type_id'];
            }
            if(!empty($input['weightage'])){
                $data['weightage'] = $input['weightage'];
            }
            if(!empty($input['passing_weightage'])) {
                $data['passing_weightage'] = $input['passing_weightage'];
            }
            $data['is_final_level'] = $is_final_level;
            $data['sort_order'] = $sort_order;
            $data['created_by'] = $employee->employeeSystemID;

            if($is_final_level == 1 && $input['critera_type_id'] == 2 && $input['answer_type_id'] == 2 ){
                if($input['yes_value'] > $input['no_value']){
                    $data['max_value'] = $input['yes_value'];
                    $data['min_value'] = $input['no_value'];
                }else{
                    $data['max_value'] = $input['no_value'];
                    $data['min_value'] = $input['yes_value'];
                }
            }

            if($is_final_level == 1 && $input['critera_type_id'] == 2  && ($input['answer_type_id'] == 1 || $input['answer_type_id'] == 3)){
                $data['max_value'] = $input['max_value'];
            }

            $result = EvaluationCriteriaMasterDetails::create($data);

            if($result){
                if($is_final_level == 1 && $input['critera_type_id'] == 2 && $input['answer_type_id'] == 2 ){
                    $datayes['criteria_detail_id'] = $result['id'];
                    $datayes['label'] = $input['yes_label'];
                    $datayes['score'] = $input['yes_value'];
                    $datayes['created_by'] = $employee->employeeSystemID;
                    EvaluationCriteriaScoreConfig::create($datayes);

                    $datano['criteria_detail_id'] = $result['id'];
                    $datano['label'] = $input['no_label'];
                    $datano['score'] = $input['no_value'];
                    $datano['created_by'] = $employee->employeeSystemID;
                    EvaluationCriteriaScoreConfig::create($datano);
                }

                if($is_final_level == 1 && $input['critera_type_id'] == 2 && ($input['answer_type_id'] == 4 || $input['answer_type_id'] == 5) ){

                    if(count($input['selectedData'])>0){
                        $minAns = 0;
                        $maxAns = 0;
                        $x=1;
                        foreach ($input['selectedData'] as $vl){
                            if($x==1){
                                $minAns = $vl['drop_value'];
                            }

                            if($vl['drop_value']>$maxAns){
                                $maxAns = $vl['drop_value'];
                            }

                            if($vl['drop_value']<$minAns){
                                $minAns = $vl['drop_value'];
                            }

                            $drop['criteria_detail_id'] = $result['id'];
                            $drop['label'] = $vl['drop_label'];
                            $drop['score'] = $vl['drop_value'];
                            $drop['created_by'] = $employee->employeeSystemID;
                            EvaluationCriteriaScoreConfig::create($drop);

                            $ans['max_value'] = $maxAns;
                            $ans['min_value'] = $minAns;
                            EvaluationCriteriaMasterDetails::where('id',$result['id'])->update($ans);
                            $x++;
                        }
                    }else{
                        return ['success' => false, 'message' => 'At least one score configuration is required'];
                    }
                }


                DB::commit();
                return ['success' => true, 'message' => 'Successfully created'];
            }
        }catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function addMasterEvaluationCriteria(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array('critera_type_id', 'answer_type_id'));
        $employee = \Helper::getEmployeeInfo();

        if($input['level'] == 0){
            $chkDuplicateName =  EvaluationCriteriaMaster::where('name',$input['name'])->first();

            if(!empty($chkDuplicateName)){
                return ['success' => false, 'message' => 'Name cannot be duplicated'];
            }
        }

        DB::beginTransaction();
        try {
            $data_master['name'] = $input['name'];
            $data_master['is_active'] = '1';
            $data_master['company_id'] = $input['companySystemID'];
            $data_master['created_by'] = $employee->employeeSystemID;
            EvaluationCriteriaMaster::create($data_master);
            DB::commit();
            return ['success' => true, 'message' => 'Successfully created'];
        }catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getEvaluationCriteriaDetails(Request $request)
    {
        $input = $request->all();
        if(isset($request['tenderMasterId']) && $request['tenderMasterId'] == null) {
            if(isset($request['loadMasterCriteria']) &&  $request['loadMasterCriteria'] == true){
                return $this->getEvaluationCriteriaMaster($request);
            } else if(isset($request['loadMasterCriteriaDetails']) && $request['loadMasterCriteriaDetails'] == true){
                return $this->getEvaluationCriteriaMasterDetails($request);
            }
        }

        $data['criteriaDetail'] = EvaluationCriteriaDetails::with(['evaluation_criteria_type','tender_criteria_answer_type','child'=> function($q){
            $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                    $q->with(['evaluation_criteria_type','tender_criteria_answer_type']);
                }]);
            }]);
        }])->where('tender_id',$input['tenderMasterId'])->where('level',1)->where('critera_type_id',$input['critera_type_id'])->get();

        $data['criteriaMaster'] = EvaluationCriteriaMaster::select('id', 'name', 'is_active')
            ->where('is_active', 1)
            ->whereDoesntHave('evaluation_criteria_details', function ($query) use($request) {
                $query->where('tender_id', '=', $request['tenderMasterId']);
            })
            ->get();


        $parentsWithoutSubLevels = DB::table('srm_evaluation_criteria_master_details as parent')
            ->leftJoin('srm_evaluation_criteria_master_details as child', 'parent.id', '=', 'child.parent_id')
            ->where('parent.parent_id', 0)
            ->whereNull('child.id')
            ->where('parent.is_final_level', 0)
            ->select('parent.*')
            ->get();


        $subLevelsWithoutFurtherSubLevels = DB::table('srm_evaluation_criteria_master_details as sub')
            ->leftJoin('srm_evaluation_criteria_master_details as child', 'sub.id', '=', 'child.parent_id')
            ->where('sub.parent_id', '!=', 0)
            ->whereNull('child.id')
            ->where('sub.is_final_level', 0)
            ->select('sub.*')
            ->get();



        $uniqueIds = null;
        if ($parentsWithoutSubLevels->isEmpty() && $subLevelsWithoutFurtherSubLevels->isEmpty()) {
            $data['uniqueEvaluationCriteriaMasterIds'] = "Validation passed: All parent and sub-level entries are correctly marked.";
        } else {
            $parents = $parentsWithoutSubLevels->pluck('evaluation_criteria_master_id')->toArray();
            $subLevels = $subLevelsWithoutFurtherSubLevels->pluck('evaluation_criteria_master_id')->toArray();

            // Merge and make unique
            $uniqueIds = array_unique(array_merge($parents, $subLevels));

            $data['uniqueEvaluationCriteriaMasterIds'] = $uniqueIds;

            $data['criteriaMaster'] = EvaluationCriteriaMaster::select('id', 'name', 'is_active')
                ->where('is_active', 1)
                ->whereNotIn('id', $uniqueIds)
                ->whereDoesntHave('evaluation_criteria_details', function ($query) use($request) {
                    $query->where('tender_id', '=', $request['tenderMasterId']);
                })
                ->get();
        }



        return $data;
    }

    public function getEvaluationCriteriaMasterDetails(Request $request)
    {
        $input = $request->all();
        $data['criteriaDetail'] = EvaluationCriteriaMasterDetails::with(['evaluation_criteria_master',
            'evaluation_criteria_master.evaluation_criteria_details.tender_master' => function ($query) {
                $query->where('published_yn', 0);
            }, 'evaluation_criteria_type','tender_criteria_answer_type','child'=> function($q){
                $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                    $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                        $q->with(['evaluation_criteria_type','tender_criteria_answer_type']);
                    }]);
                }]);
            }])->where('level',1)->where('critera_type_id',$input['critera_type_id'])->where('evaluation_criteria_master_id', $input['evaluationCriteriaMasterId'])->get();
        return $data;
    }

    public function getEvaluationCriteriaMaster(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $tenderMaster = EvaluationCriteriaMaster::with(['evaluation_criteria_details.tender_master' => function ($query) {
            $query->where('confirmed_yn', 0);
        }])->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        return \DataTables::eloquent($tenderMaster)
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

    public function deleteEvaluationCriteria(Request $request)
    {
        $input = $request->all();
        $fromTender = $input['fromTender'] ?? false;
        if(isset($request['isMasterCriteria']) && $request['isMasterCriteria']){
            return $this->deleteEvaluationCriteriaMaster($request);
        }

        if(isset($request['isMasterCriteriaDetails']) && $request['isMasterCriteriaDetails']){
            return $this->deleteEvaluationCriteriaMasterDetails($request);
        }

        DB::beginTransaction();
        try {
            $evaluationDetails = EvaluationCriteriaDetails::find($input['id']);
            $result = $evaluationDetails->delete();
            EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                ->where('criteria_detail_id',$input['id'])->delete();
            $levelTwo = EvaluationCriteriaDetails::where('parent_id',$input['id'])->get();
            if(!empty($levelTwo)){
                foreach ($levelTwo as $val2){
                    $levelThree = EvaluationCriteriaDetails::where('parent_id',$val2['id'])->get();
                    if(!empty($levelThree)){
                        foreach ($levelThree as $val3){
                            $levelfour = EvaluationCriteriaDetails::where('parent_id',$val3['id'])->get();
                            if(!empty($levelfour)){
                                foreach ($levelfour as $val4){
                                    EvaluationCriteriaDetails::where('id',$val4['id'])->delete();
                                    EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                                        ->where('criteria_detail_id',$val4['id'])->delete();
                                }
                            }
                            EvaluationCriteriaDetails::where('id',$val3['id'])->delete();
                            EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                                ->where('criteria_detail_id',$val3['id'])->delete();
                        }
                    }
                    EvaluationCriteriaDetails::where('id',$val2['id'])->delete();
                    EvaluationCriteriaScoreConfig::where('fromTender',$fromTender)
                        ->where('criteria_detail_id',$val2['id'])->delete();
                }
            }
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteEvaluationCriteriaMasterDetails(Request $request)
    {
        DB::beginTransaction();
        try {
            $evaluationDetails = EvaluationCriteriaMasterDetails::find($request['id']);
            $result = $evaluationDetails->delete();
            EvaluationCriteriaScoreConfig::where('criteria_detail_id',$request['id'])->delete();
            $levelTwo = EvaluationCriteriaMasterDetails::where('parent_id',$request['id'])->get();
            if(!empty($levelTwo)){
                foreach ($levelTwo as $val2){
                    $levelThree = EvaluationCriteriaMasterDetails::where('parent_id',$val2['id'])->get();
                    if(!empty($levelThree)){
                        foreach ($levelThree as $val3){
                            $levelfour = EvaluationCriteriaMasterDetails::where('parent_id',$val3['id'])->get();
                            if(!empty($levelfour)){
                                foreach ($levelfour as $val4){
                                    EvaluationCriteriaMasterDetails::where('id',$val4['id'])->delete();
                                    EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val4['id'])->delete();
                                }
                            }
                            EvaluationCriteriaMasterDetails::where('id',$val3['id'])->delete();
                            EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val3['id'])->delete();
                        }
                    }
                    EvaluationCriteriaMasterDetails::where('id',$val2['id'])->delete();
                    EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val2['id'])->delete();
                }
            }
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteEvaluationCriteriaMaster(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $evaluationMaster = EvaluationCriteriaMaster::find($input['id']);
            $evaluationMaster->delete();

            $result = EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id', $input['id']);
            $result->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getEvaluationDetailById(Request $request)
    {
        $input = $request->all();
        if(isset($input['isMasterCriteria']) && $input['isMasterCriteria'] == 1)
        {
            return EvaluationCriteriaMasterDetails::with(['evaluation_criteria_master','evaluation_criteria_score_config' => function ($q) {
                $q->where('fromTender', 0);
            }
            ])
                ->where('id',$input['evaluationId'])
                ->first();
        }
        else
        {
            return EvaluationCriteriaDetails::with(['evaluation_criteria_score_config' => function ($q) {
                $q->where('fromTender', 1);
            }])
                ->where('id',$input['evaluationId'])
                ->first();
        }
    }

    public function editEvaluationCriteria(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array( 'answer_type_id'));
        $employee = \Helper::getEmployeeInfo();

        if(isset($input['eidtMasterCriteria'])&& isset($input['evaluation_criteria_master_id']) && $input['evaluation_criteria_master_id'] != 0){
            return $this->editEvaluationMasterCriteria($request);
        }

        if(isset($input['criteriaMasterStatusEdit']) && $input['criteriaMasterStatusEdit'] == true && !isset($input['isDeleteMaster'])){
            return $this->criteriaMasterStatusChange($input['isChecked'], $input['masterCriteriaId']);
        }

        if(isset($input['criteriaMasterStatusEdit']) && $input['criteriaMasterStatusEdit'] == true && $input['isDeleteMaster'] == true){
            return $this->criteriaDelete($input['masterCriteriaId']);
        }

        if($input['level'] == 1){
            if($input['critera_type_id'] !=1) {
                if(!isset($input['weightage']) || empty($input['weightage']) || $input['weightage']<= 0){
                    return ['success' => false, 'message' => 'Weightage is required'];
                }

                if(!isset($input['passing_weightage']) || empty($input['passing_weightage']) || $input['passing_weightage'] <= 0){
                    return ['success' => false, 'message' => 'Passing weightage is required'];
                }
            }
        }

        if($input['is_final_level'] == 1){
            if(!isset($input['answer_type_id']) || empty($input['answer_type_id'])){
                return ['success' => false, 'message' => 'Answer Type is required'];
            }
        }

        $chkDuplicate =  EvaluationCriteriaDetails::where('tender_id',$input['tender_id'])->where('id','!=',$input['id'])->where('description',$input['description'])->where('level',$input['level'])->first();

        if(!empty($chkDuplicate)){
            return ['success' => false, 'message' => 'Description cannot be duplicated'];
        }

        DB::beginTransaction();
        try {
            if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2  && ($input['answer_type_id'] == 1 || $input['answer_type_id'] == 3)){
                $data['max_value'] = $input['max_value'];
            }

            $data['description'] = $input['description'];
            if(isset($input['answer_type_id'])){
                $data['answer_type_id'] = $input['answer_type_id'];
            }
            if(!empty($input['weightage'])){
                $data['weightage'] = $input['weightage'];
            }
            if(!empty($input['passing_weightage'])) {
                $data['passing_weightage'] = $input['passing_weightage'];
            }
            $data['updated_by'] = $employee->employeeSystemID;

            $evaluationDetails = EvaluationCriteriaDetails::find($input['id']);
            $result = $evaluationDetails->update($data);

            if($result){

                if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2 && ($input['answer_type_id'] == 4 || $input['answer_type_id'] == 5) ){
                    $config = EvaluationCriteriaScoreConfig::where('criteria_detail_id',$input['id'])->first();
                    if(empty($config)){
                        return ['success' => false, 'message' => 'At least one score configuration is required'];
                    }
                }


                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }
        }catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function criteriaMasterStatusChange($isChecked, $masterCriteriaId){
        try {
            $evaluationCriteriaMasterDetails = EvaluationCriteriaDetails::where('evaluation_criteria_master_id', $masterCriteriaId)
                ->pluck('tender_id')
                ->toArray();

            $tenderMasterPublishedCount = TenderMaster::select('id')->whereIn('id', $evaluationCriteriaMasterDetails)->where('confirmed_yn', 0)->count();

            if ($tenderMasterPublishedCount > 0) {
                return ['success' => false, 'message' => 'Technical evaluation criteria already used'];
            }

            $dataMaster['is_active'] = $isChecked;
            $evaluationMaster = EvaluationCriteriaMaster::find($masterCriteriaId);
            $result = $evaluationMaster->update($dataMaster);
            if($result){
                return ['success' => true, 'message' => 'Successfully updated'];
            } else {
                return ['success' => false, 'message' => 'Unexpected Error'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }

    }

    private function criteriaDelete($masterCriteriaId){
        $evaluationCriteriaMasterDetails = EvaluationCriteriaDetails::where('evaluation_criteria_master_id', $masterCriteriaId)
            ->pluck('tender_id')
            ->toArray();

        $tenderMasterPublishedCount = TenderMaster::select('id')->whereIn('id', $evaluationCriteriaMasterDetails)->where('confirmed_yn', 0)->count();

        if ($tenderMasterPublishedCount > 0) {
            return ['success' => false, 'message' => 'Technical evaluation criteria already used'];
        } else {
            return ['success' => true, 'message' => 'Technical evaluation criteria not used'];
        }
    }

    public function editEvaluationMasterCriteria(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array( 'answer_type_id'));
        $employee = \Helper::getEmployeeInfo();

        if($input['level'] == 1){
            if($input['critera_type_id'] !=1) {
                if(!isset($input['weightage']) || empty($input['weightage']) || $input['weightage']<= 0){
                    return ['success' => false, 'message' => 'Weightage is required'];
                }

                if(!isset($input['passing_weightage']) || empty($input['passing_weightage']) || $input['passing_weightage'] <= 0){
                    return ['success' => false, 'message' => 'Passing weightage is required'];
                }
            }
        }

        if($input['is_final_level'] == 1){
            if(!isset($input['answer_type_id']) || empty($input['answer_type_id'])){
                return ['success' => false, 'message' => 'Answer Type is required'];
            }
        }

        $chkDuplicateName =  EvaluationCriteriaMaster::where('id','!=',$input['evaluation_criteria_master']['id'])->where('name',$input['evaluation_criteria_master']['name'])->first();

        if(!empty($chkDuplicateName)){
            return ['success' => false, 'message' => 'Name cannot be duplicated'];
        }

        $chkDuplicate =  EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id',$input['evaluation_criteria_master_id'])->where('id','!=',$input['id'])->where('description',$input['description'])->where('level',$input['level'])->first();

        if(!empty($chkDuplicate)){
            return ['success' => false, 'message' => 'Description cannot be duplicated'];
        }

        DB::beginTransaction();
        try {
            if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2  && ($input['answer_type_id'] == 1 || $input['answer_type_id'] == 3)){
                $data['max_value'] = $input['max_value'];
            }

            $data['description'] = $input['description'];
            if(isset($input['answer_type_id'])){
                $data['answer_type_id'] = $input['answer_type_id'];
            }
            if(!empty($input['weightage'])){
                $data['weightage'] = $input['weightage'];
            }
            if(!empty($input['passing_weightage'])) {
                $data['passing_weightage'] = $input['passing_weightage'];
            }
            $data['updated_by'] = $employee->employeeSystemID;

            if(isset($input['evaluation_criteria_master']['name'])){
                $dataMaster['name'] = $input['evaluation_criteria_master']['name'];
                $dataMaster['is_active'] = $input['evaluation_criteria_master']['is_active'];
                $evaluationMaster = EvaluationCriteriaMaster::find($input['evaluation_criteria_master_id']);
                $evaluationMaster->update($dataMaster);
            }

            $evaluationDetails = EvaluationCriteriaMasterDetails::find($input['id']);
            $result = $evaluationDetails->update($data);

            if($result){

                if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2 && ($input['answer_type_id'] == 4 || $input['answer_type_id'] == 5) ){
                    $config = EvaluationCriteriaScoreConfig::where('criteria_detail_id',$input['id'])->first();
                    if(empty($config)){
                        return ['success' => false, 'message' => 'At least one score configuration is required'];
                    }
                }


                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }
        }catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function validateWeightage(Request $request)
    {
        $input = $request->all();
        $weightage = $input['weightage'];
        $tenderMasterId = $input['tenderMasterId'];
        if($input['tenderMasterId'] == null){
            return $this->validateWeightageMaster($request);
        }

        $level  = isset($input['level']) ? $input['level'] : null;

        $parentId = $input['parentId'];
        if($level == 1){
            $result = EvaluationCriteriaDetails::where('tender_id',$input['tenderMasterId'])->where('level',1)->sum('weightage');
            $total = $result + $weightage;
            if($total>100){
                return ['success' => false, 'message' => 'Total weightage cannot exceed 100 percent'];
            } else {
                return ['success' => true, 'message' => 'Success'];
            }
        } else {
            $result = EvaluationCriteriaDetails::where('tender_id',$input['tenderMasterId'])
                ->where('parent_id',$parentId)->where('level',$level)->sum('weightage');
            $parent = EvaluationCriteriaDetails::where('id',$parentId)->first();

            $total = $result + $weightage;

            if($total>$parent['weightage']){
                return ['success' => false, 'message' => 'Total Child Weightage cannot exceed '.$parent['weightage']];
            }else{
                return ['success' => true, 'message' => 'Success'];
            }
        }


    }

    private function validateWeightageMaster(Request $request)
    {
        $input = $request->all();
        $weightage = $input['weightage'];
        $level  = isset($input['level']) ? $input['level'] : null;
        $parentId = $input['parentId'];

        if($level == 1){
            $result = EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id',$input['evaluationCriteriaMasterId'])->where('level',1)->sum('weightage');
            $total = $result + $weightage;
            if($total>100){
                return ['success' => false, 'message' => 'Total weightage cannot exceed 100 percent'];
            } else {
                return ['success' => true, 'message' => 'Success'];
            }
        } else {
            $result = EvaluationCriteriaMasterDetails::where('parent_id',$parentId)->where('level',$level)->sum('weightage');
            $parent = EvaluationCriteriaMasterDetails::where('id',$parentId)->first();

            $total = $result + $weightage;

            if($total>$parent['weightage']){
                return ['success' => false, 'message' => 'Total Child Weightage cannot exceed '.$parent['weightage']];
            }else{
                return ['success' => true, 'message' => 'Success'];
            }
        }


    }

    public function validateWeightageEdit(Request $request)
    {
        $input = $request->all();

        if(isset($input['eidtMasterCriteria']) && isset($input['evaluation_criteria_master_id']) && $input['evaluation_criteria_master_id'] != 0){
            return $this->validateWeightageMasterEdit($request);
        }

        if($input['level'] != 1){
            $result = EvaluationCriteriaDetails::where('tender_id',$input['tender_id'])
                ->where('parent_id',$input['parent_id'])->where('level',$input['level'])
                ->where('id','!=',$input['id'])
                ->sum('weightage');

            $parent = EvaluationCriteriaDetails::where('id',$input['parent_id'])->first();

            $total = $result + $input['weightage'];

            if($total>$parent['weightage']){
                return ['success' => false, 'message' => 'Total Child Weightage cannot exceed '.$parent['weightage']];
            } else{
                return ['success' => true, 'message' => 'Success'];
            }
        } else {
            return ['success' => true, 'message' => 'Success'];
        }

    }

    private function validateWeightageMasterEdit(Request $request)
    {
        $input = $request->all();
        if($input['level'] == 1){
            $result = EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id',$input['evaluation_criteria_master_id'])->where('level',1)->where('id','!=',$input['id'])->sum('weightage');
            $total = $result + $input['weightage'];
            if($total>100){
                return ['success' => false, 'message' => 'Total weightage cannot exceed 100 percent'];
            } else {
                return ['success' => true, 'message' => 'Success'];
            }
        } else {
            $result = EvaluationCriteriaMasterDetails::where('evaluation_criteria_master_id',$input['evaluation_criteria_master_id'])
                ->where('parent_id',$input['parent_id'])->where('level',$input['level'])
                ->where('id','!=',$input['id'])
                ->sum('weightage');

            $parent = EvaluationCriteriaMasterDetails::where('id',$input['parent_id'])->first();

            $total = $result + $input['weightage'];

            if($total>$parent['weightage']){
                return ['success' => false, 'message' => 'Total Child Weightage cannot exceed '.$parent['weightage']];
            }else{
                return ['success' => true, 'message' => 'Success'];
            }
        }

    }


    private function createScoreConfig($result, $criteriaDetail, $employee, $fromTender)
    {
        if ($result->answer_type_id == 2) {
            $scores = [
                ['label' => 'Yes', 'score' => $result->max_value],
                ['label' => 'No', 'score' => $result->min_value],
            ];
        }
        elseif ($result->answer_type_id == 4)
        {
            $scores = EvaluationCriteriaScoreConfig::getEvalScore($result['id'], $fromTender);
        }
        else
        {
            return;
        }

        foreach ($scores as $score) {
            EvaluationCriteriaScoreConfig::create([
                'criteria_detail_id' => $criteriaDetail->id,
                'label' => $score['label'],
                'score' => $score['score'],
                'fromTender' => $fromTender,
                'created_by' => $employee->employeeSystemID,
            ]);
        }
    }
}
