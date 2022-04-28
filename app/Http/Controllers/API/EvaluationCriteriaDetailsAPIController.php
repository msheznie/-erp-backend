<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaDetailsAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaDetailsAPIRequest;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\EvaluationCriteriaType;
use App\Models\TenderCriteriaAnswerType;
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

            $result = EvaluationCriteriaDetails::create($data);

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
                DB::commit();
                return ['success' => true, 'message' => 'Successfully created'];
            }
        }catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getEvaluationCriteriaDetails(Request $request)
    {
        $input = $request->all();
        $data['criteriaDetail'] = EvaluationCriteriaDetails::with(['evaluation_criteria_type','tender_criteria_answer_type','child'=> function($q){
                                $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                                    $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                                        $q->with(['evaluation_criteria_type','tender_criteria_answer_type']);
                                    }]);
                                }]);
        }])->where('tender_id',$input['tenderMasterId'])->where('level',1)->where('critera_type_id',$input['critera_type_id'])->get();
        return $data;
    }

    public function deleteEvaluationCriteria(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = EvaluationCriteriaDetails::where('id',$input['id'])->delete();
            EvaluationCriteriaScoreConfig::where('criteria_detail_id',$input['id'])->delete();
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
                                    EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val4['id'])->delete();
                                }
                            }
                            EvaluationCriteriaDetails::where('id',$val3['id'])->delete();
                            EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val3['id'])->delete();
                        }
                    }
                    EvaluationCriteriaDetails::where('id',$val2['id'])->delete();
                    EvaluationCriteriaScoreConfig::where('criteria_detail_id',$val2['id'])->delete();
                }
            }
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
        return EvaluationCriteriaDetails::where('id',$input['evaluationId'])->first();

    }

    public function editEvaluationCriteria(Request $request)
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

        $chkDuplicate =  EvaluationCriteriaDetails::where('tender_id',$input['tender_id'])->where('id','!=',$input['id'])->where('description',$input['description'])->where('level',$input['level'])->first();

        if(!empty($chkDuplicate)){
            return ['success' => false, 'message' => 'Description cannot be duplicated'];
        }

        DB::beginTransaction();
        try {
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

            $result = EvaluationCriteriaDetails::where('id',$input['id'])->update($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }
        }catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function validateWeightage(Request $request)
    {
        $input = $request->all();
        return EvaluationCriteriaDetails::where('tender_id',$input['tenderMasterId'])->where('level',1)->sum('weightage');

    }

    public function validateWeightageEdit(Request $request)
    {
        $input = $request->all();
        return EvaluationCriteriaDetails::where('tender_id',$input['tender_id'])->where('level',1)->where('id','!=',$input['id'])->sum('weightage');

    }
}
