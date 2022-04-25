<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEvaluationCriteriaDetailsAPIRequest;
use App\Http\Requests\API\UpdateEvaluationCriteriaDetailsAPIRequest;
use App\Models\EvaluationCriteriaDetails;
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

        if($input['level'] == 1 || $input['level'] == 2){
            if($input['critera_type_id'] !=1) {
                if(!isset($input['weightage']) || empty($input['weightage'])){
                    return ['success' => false, 'message' => 'Weightage is required'];
                }

                if(!isset($input['passing_weightage']) || empty($input['passing_weightage'])){
                    return ['success' => false, 'message' => 'Passing weightage is required'];
                }
            }
        }

        DB::beginTransaction();
        try {
            $data['description'] = $input['description'];
            $data['tender_id'] = $input['tenderMasterId'];
            $data['parent_id'] = $input['parent_id'];
            $data['level'] = $input['level'];
            $data['critera_type_id'] = $input['critera_type_id'];
            $data['answer_type_id'] = $input['answer_type_id'];
            if(!empty($input['weightage'])){
                $data['weightage'] = $input['weightage'];
            }
            if(!empty($input['passing_weightage'])) {
                $data['passing_weightage'] = $input['passing_weightage'];
            }
            $data['is_final_level'] = $is_final_level;
            $data['sort_order'] = $sort_order;
            $data['created_by'] = $employee->employeeSystemID;

            $result = EvaluationCriteriaDetails::create($data);

            if($result){
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
        $data['criteriaDetail'] = EvaluationCriteriaDetails::with(['evaluation_criteria_type','tender_criteria_answer_type'])->where('tender_id',$input['tenderMasterId'])->get();
        return $data;
    }
}
