<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidSubmissionMasterAPIRequest;
use App\Http\Requests\API\UpdateBidSubmissionMasterAPIRequest;
use App\Models\BidMainWork;
use App\Models\BidSubmissionDetail;
use App\Models\BidSubmissionMaster;
use App\Models\DocumentAttachments;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleMaster;
use App\Models\TenderMaster;
use App\Repositories\BidSubmissionMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\BidEvaluationSelection;
use function Clue\StreamFilter\fun;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\BidBoq;
use App\Models\SupplierRegistrationLink;
use App\Repositories\TenderMasterRepository;
/**
 * Class BidSubmissionMasterController
 * @package App\Http\Controllers\API
 */

class BidSubmissionMasterAPIController extends AppBaseController
{
    /** @var  BidSubmissionMasterRepository */
    private $bidSubmissionMasterRepository;

    public function __construct(TenderMasterRepository $tenderMasterRepo,BidSubmissionMasterRepository $bidSubmissionMasterRepo)
    {
        $this->bidSubmissionMasterRepository = $bidSubmissionMasterRepo;
        $this->tenderMasterRepository = $tenderMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionMasters",
     *      summary="Get a listing of the BidSubmissionMasters.",
     *      tags={"BidSubmissionMaster"},
     *      description="Get all BidSubmissionMasters",
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
     *                  @SWG\Items(ref="#/definitions/BidSubmissionMaster")
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
        $this->bidSubmissionMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bidSubmissionMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidSubmissionMasters = $this->bidSubmissionMasterRepository->all();

        return $this->sendResponse($bidSubmissionMasters->toArray(), 'Bid Submission Masters retrieved successfully');
    }

    /**
     * @param CreateBidSubmissionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bidSubmissionMasters",
     *      summary="Store a newly created BidSubmissionMaster in storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Store BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionMaster")
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
     *                  ref="#/definitions/BidSubmissionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidSubmissionMasterAPIRequest $request)
    {
        $input = $request->all();

        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->create($input);

        return $this->sendResponse($bidSubmissionMaster->toArray(), 'Bid Submission Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Display the specified BidSubmissionMaster",
     *      tags={"BidSubmissionMaster"},
     *      description="Get BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
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
     *                  ref="#/definitions/BidSubmissionMaster"
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
        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->with(['SupplierRegistrationLink','tender'])->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        return $this->sendResponse($bidSubmissionMaster->toArray(), 'Bid Submission Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBidSubmissionMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Update the specified BidSubmissionMaster in storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Update BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BidSubmissionMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BidSubmissionMaster")
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
     *                  ref="#/definitions/BidSubmissionMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidSubmissionMasterAPIRequest $request)
    {
        $input = $request->all();
        
        
            
        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        if(isset($input['meth']))
        {
            $meth = $input['meth'];
            $tender_id = $input['tender_id'];

            if($meth == 1)
            {
    
                $input['technical_verify_by'] = \Helper::getEmployeeSystemID();
                $input['technical_verify_at'] = Carbon::now();
                $input['technical_eval_remarks'] = $input['technical_eval_remarks'];
        
                $bidSubmissionMaster = $this->bidSubmissionMasterRepository->update($input, $id);
    
                $query = BidSubmissionMaster::where('tender_id', $tender_id)->where('technical_verify_status','!=', 1)->where('bidSubmittedYN',1)->where('status',1)->count();
                if($query == 0)
                {
                        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tender_id);
                        $tenderMaster->technical_eval_status = 1;;
                        $tenderMaster->save();
                }
            }
            else if($meth == 2)
            {
                $input['commercial_verify_by'] = \Helper::getEmployeeSystemID();
                $input['commercial_verify_at'] = Carbon::now();
        
                $bidSubmissionMaster = $this->bidSubmissionMasterRepository->update($input, $id);
        
                //$query = BidSubmissionMaster::where('tender_id', $tender_id)->where('commercial_verify_status','!=', 1)->where('bidSubmittedYN',1)->where('status',1)->count();

                $query = BidSubmissionMaster::selectRaw("SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage) as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,srm_tender_master.technical_passing_weightage as passing_weightage,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
                ->join('srm_evaluation_criteria_details', 'srm_evaluation_criteria_details.id', '=', 'srm_bid_submission_detail.evaluation_detail_id')
                ->havingRaw('weightage >= passing_weightage')
                ->groupBy('srm_bid_submission_master.id')
                ->where('srm_evaluation_criteria_details.critera_type_id', 2)->where('srm_bid_submission_master.commercial_verify_status','!=', 1)->where('srm_bid_submission_master.status', 1)->where('srm_bid_submission_master.bidSubmittedYN', 1)->where('srm_bid_submission_master.tender_id', $tender_id)
                ->get();
                $count = count($query);

                if($count == 0)
                {
                        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tender_id);
                        $tenderMaster->commercial_verify_status = 1;
                        $tenderMaster->commercial_verify_by = \Helper::getEmployeeSystemID();
                        $tenderMaster->commercial_verify_at = Carbon::now();
                        $tenderMaster->save();
                }
            }
        }
        else
        {
            $bidSubmissionMaster = $this->bidSubmissionMasterRepository->update($input, $id);
        }





        return $this->sendResponse($bidSubmissionMaster->toArray(), 'BidSubmissionMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bidSubmissionMasters/{id}",
     *      summary="Remove the specified BidSubmissionMaster from storage",
     *      tags={"BidSubmissionMaster"},
     *      description="Delete BidSubmissionMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BidSubmissionMaster",
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
        /** @var BidSubmissionMaster $bidSubmissionMaster */
        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->findWithoutFail($id);

        if (empty($bidSubmissionMaster)) {
            return $this->sendError('Bid Submission Master not found');
        }

        $bidSubmissionMaster->delete();

        return $this->sendSuccess('Bid Submission Master deleted successfully');
    }

    public function getTenderBits(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $sort = 'asc';
        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];
        $type = $request['type'];

        if($type == 1)
        {
            $query = BidSubmissionMaster::with(['SupplierRegistrationLink','bidSubmissionDetail' => function($query){
                $query->whereHas('srm_evaluation_criteria_details.evaluation_criteria_type', function ($query) {
                    $query->where('id', 1);
                });
            }])->where('status', 1)->where('bidSubmittedYN', 1)->where('tender_id', $tenderId);
        }
        else if($type == 2)
        {
            $query = BidSubmissionMaster::with(['SupplierRegistrationLink','bidSubmissionDetail' => function($query){
                $query->whereHas('srm_evaluation_criteria_details.evaluation_criteria_type', function ($query) {
                    $query->where('id', 1);
                });
            }])->where('status', 1)->where('bidSubmittedYN', 1)->where('doc_verifiy_status',1)->where('tender_id', $tenderId);
        }
      

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->WhereHas('SupplierRegistrationLink', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            });
        }

        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input,$sort) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $sort);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getTenderBidGoNoGoResponse(Request $request){
        $input = $request->all();

        if(request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];

        $query = BidSubmissionDetail::with(['srm_evaluation_criteria_details','srm_bid_submission_master',
            'srm_evaluation_criteria_details.evaluation_criteria_type',
            'srm_evaluation_criteria_details.tender_criteria_answer_type', 'srm_tender_master', 'supplier_registration_link'])
            ->whereHas('srm_evaluation_criteria_details.evaluation_criteria_type', function ($query) {
                $query->where('id', 1);
            })->whereHas('srm_tender_master', function ($query) use($companyId) {
                $query->where('company_id', $companyId);
            })->where('bid_master_id', $tenderId);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->whereHas('srm_evaluation_criteria_details', function ($q) use ($search) {
                        return $q->where('description', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', 'asc');
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function updateTenderBidGoNoGoResponse(Request $request){
        $input = $request->all();

        DB::beginTransaction();
        try {
            $att['go_no_go_criteria_result'] = $input['value'];
            $att['updated_at'] = Carbon::now();
            $att['updated_by'] = \Helper::getEmployeeSystemID();
            $result = BidSubmissionDetail::where('id', $input['id'])->update($att);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function bidGoNoGoCommentAndStatus(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            if (isset($input['status'])){
                $att['go_no_go_criteria_status'] = $input['status'];
            }

            if (isset($input['value'])){
                $att['go_no_go_criteria_comment'] = $input['value'];
            }

            $att['updated_at'] = Carbon::now();
            $att['updated_by'] = \Helper::getEmployeeSystemID();
            $result = BidSubmissionMaster::where('id', $input['id'])->update($att);

            $details = BidSubmissionMaster::where('id', $input['id'])->first();
            $tenderId = $details->tender_id;

            $query = BidSubmissionMaster::where('tender_id', $tenderId)->where('go_no_go_criteria_status','=', null)->where('bidSubmittedYN',1)->where('status',1)->count();
            if($query == 0)
            {
                    $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tenderId);
                    $tenderMaster->go_no_go_status = 1;
                    $tenderMaster->save();
            }


            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }
    }

    public function getBidVerificationStatus(Request $request)
    {
        $input = $request->all();
        $tenderId = $request['tenderMasterId'];
        $is_verified = true;

        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tenderId);

        if($tenderMaster->is_active_go_no_go == 0)
        {
            $tenderMaster->go_no_go_status = 1;
            $tenderMaster->save();
        }

        $query = BidSubmissionMaster::where('tender_id', $tenderId)->where('doc_verifiy_status', 0)->where('bidSubmittedYN',1)->where('status',1)->count();


        if($query > 0)
        {
            $is_verified = false;
        }

        return $this->sendResponse($is_verified, 'Data retrived successfully');
    }

    public function getVerifieddBids(Request $request)
    {
        $input = $request->all();
        $details = $input['extraParams'];
        $tenderId = $details['tenderId'];



        $bid_master_ids = BidEvaluationSelection::where('tender_id',$tenderId)->pluck('bids');
        $temp = [];

        foreach($bid_master_ids as $bid)
        {
            foreach(json_decode($bid,true) as $val)
            {
                array_push($temp,$val);
            }

        }


        $query = $this->bidSubmissionMasterRepository
        ->join('srm_supplier_registration_link', 'srm_bid_submission_master.supplier_registration_id', '=', 'srm_supplier_registration_link.id')
        ->select('srm_bid_submission_master.id as id','srm_bid_submission_master.bidSubmittedDatetime as submitted_date','srm_supplier_registration_link.name as supplier_name','srm_bid_submission_master.bidSubmissionCode')
        ->where('bidSubmittedYN', 1)->where('tender_id', $tenderId)->where('doc_verifiy_status',1)->orderBy('id')->get()->toArray();

        foreach($query as $key=>$val)
        {
            $id = $val['id'];
            if(in_array($id,$temp))
            {
                unset($query[$key]);
            }


        }
        $result =  array_values($query);
        return $this->sendResponse($result, 'Data retrived successfully');
    }


    public function saveTechnicalEvalBidSubmissionLine(Request $request)
    {
        $tenderId = $request->input('extraParams.tenderMasterId');
        $bidMasterId = $request->input('extraParams.bid_id');
        $val = $request->input('extraParams.val');
        $id = $request->input('extraParams.id');
        $row_id = $request->input('extraParams.row_id');
        $criteriaDetail = $request->input('extraParams.criteriaDetail');

        DB::beginTransaction();
        try {
            if ($criteriaDetail['answer_type_id'] == 4 || $criteriaDetail['answer_type_id'] == 2) {
                if ($val['value'] > 0 && $val['value'] != null) {
                    $score = EvaluationCriteriaScoreConfig::where('id', $val['value'])->first();
;
                    $score_id = $val['value'];
                    $val = $score['score'];
                    $result = round(($val/$criteriaDetail['max_value'])*$criteriaDetail['weightage'],3);

                } else {
                    $result = null;
                    $val = null;
                    $score_id = null;
                }
            }

            if ($criteriaDetail['answer_type_id'] == 1 || $criteriaDetail['answer_type_id'] == 3) {
                if (!is_null($val)) {

                    $result = round(($val/$criteriaDetail['max_value'])*$criteriaDetail['weightage'],3);

                } else {
                    $result = null;
                    $val = null;

                }
                $score_id = null;
            }


            $att['eval_score'] = $val;
            $att['eval_result'] = $result;
            $att['evaluate_by'] = \Helper::getEmployeeSystemID();
            $att['evaluate_at'] =Carbon::now();
            $att['bid_selection_id'] = $id;
            $att['eval_score_id'] = $score_id;
            $result = BidSubmissionDetail::where('id', $row_id)->update($att);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Successfully Saved',
                'data' => $result
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => '', 'message' => $e];
        }

    }

    public function BidSummaryExportReport(Request $request)
    {
        $tenderId = $request->get('id');

        $bidData = TenderMaster::with(['srm_bid_submission_master' => function($query) use($tenderId){
            $query->where('status', 1);
        }, 'srm_bid_submission_master.SupplierRegistrationLink', 'srm_bid_submission_master.BidDocumentVerification',
            'DocumentAttachments' => function($query) use($tenderId){
            $query->with(['bid_verify'])->where('documentSystemCode', $tenderId)->where('documentSystemID', 108)
                ->where('attachmentType', 2)->where('envelopType',3);
        }])->where('id', $tenderId)
            ->get();

        $resultTable = BidSubmissionMaster::select('id')->where('tender_id', $tenderId)
            ->where('status', 1)
            ->where('doc_verifiy_comment', '!=', null)
            ->get()
            ->toArray();

        $i = 0;
        foreach ($resultTable as $a){
            $arr[$i] = DocumentAttachments::with(['bid_verify'])
                ->whereIn('documentSystemCode', [$a['id']])
                ->where('documentSystemID', 108)
                ->where('attachmentType',0)
                ->where('envelopType',3)
                ->get();
            $i++;
        }

        $time = strtotime("now");
        $fileName = 'Bid_Opening_Summary' . $time . '.pdf';
        $order = array('bidData' => $bidData, 'attachments' => $arr);
        $html = view('print.bid_summary_print', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);

    }


    public function getTenderCommercialBids(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];



        $query = BidSubmissionMaster::selectRaw("SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage) as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,srm_tender_master.technical_passing_weightage as passing_weightage,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode")
        ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
        ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
        ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
        ->join('srm_evaluation_criteria_details', 'srm_evaluation_criteria_details.id', '=', 'srm_bid_submission_detail.evaluation_detail_id')
        ->havingRaw('weightage >= passing_weightage')
        ->groupBy('srm_bid_submission_master.id')
        ->where('srm_evaluation_criteria_details.critera_type_id', 2)->where('srm_bid_submission_master.status', 1)->where('srm_bid_submission_master.bidSubmittedYN', 1)->where('srm_bid_submission_master.tender_id', $tenderId)
        ;


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }

        return \DataTables::eloquent($query)
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

    public function SupplierItemWiseExportReport(Request $request)
    {
        $tenderId = $request['tenderMasterId'];
        $tenderId = $request->get('tenderMasterId');
        $bidSubmission = $request['bidSubmission'];
        $itemList = $request['itemList'];

        $bidMasterId = [];
        if(isset($bidSubmission)){
            foreach ($bidSubmission as $bid){
                $bidMasterId[] = $bid['id'];
            }
        }

        $notBoqitems = [];
        if(isset($itemList)){
            foreach ($itemList as $item){
                $notBoqitems[] = $item['id'];
            }
        }

        $queryResult = PricingScheduleMaster::with(['tender_master.srm_bid_submission_master' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with('SupplierRegistrationLink')->whereIn('id', $bidMasterId);
        }, 'bid_schedules.SupplierRegistrationLink', 'pricing_shedule_details' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with('tender_boq_items')->where('is_disabled', 0)->whereNotIn('field_type', [3,4]);
            if(sizeof($notBoqitems) > 0 ){
                $q->whereIn('id', $notBoqitems);
            }
        }])->where('tender_id', $tenderId)->get();

        $data = PricingScheduleMaster::with(['tender_bid_format_master', 'bid_schedule' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }, 'pricing_shedule_details' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with(['tender_boq_items' => function ($q) use ($bidMasterId) {
                $q->with(['bid_boqs' => function ($q) use ($bidMasterId) {
                    $q->whereIn('bid_master_id', $bidMasterId);
                }]);
            }, 'bid_main_works' => function ($q) use ($bidMasterId) {
                $q->with('srm_bid_submission_master.SupplierRegistrationLink')->whereIn('bid_master_id', $bidMasterId);
            }])->whereNotIn('field_type', [3,4])->where('is_disabled', 0);
            if($notBoqitems){
                $q->whereIn('id', $notBoqitems);
            }
        }])->where('tender_id', $tenderId)->get();

        $itemsArrayCount = array();
        $arr = array();
        foreach ($bidSubmission as $bid) {
            $totalItemsCount = 0;
            $totalAmount = PricingScheduleMaster::with(['pricing_shedule_details' => function ($q) use ($bid, $notBoqitems) {
                $q->with(['bid_main_works' => function ($q) use ($bid) {
                    $q->where('bid_master_id', $bid)->sum('total_amount');
                }])->whereNotIn('field_type', [3, 4])->where('is_disabled', 0);
                if ($notBoqitems) {
                    $q->whereIn('id', $notBoqitems);
                }
            }])->where('tender_id', $tenderId)->get()->toArray();

            foreach ($totalAmount as $pricing_shedule_details) {
                foreach ($pricing_shedule_details['pricing_shedule_details'] as $item) {
                    $totalItemsCount = $totalItemsCount + $item['bid_main_works'][0]['total_amount'];
                }
            }

            $arr['id'] = $bid['id'];
            $arr['value'] = $totalItemsCount;
            $itemsArrayCount[] = $arr;
        }
        $time = strtotime("now");
        $fileName = 'supplier_item_summary' . $time . '.pdf';
        $order = array(
            'supplier_list' => $queryResult,
            'srm_bid_submission_master' => $queryResult[0]['tender_master']['srm_bid_submission_master'],
            'item_list' => $data,
            'totalItemsCount' => $itemsArrayCount
        );

        $html = view('print.bid_supplier_item_print', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }

    public function getSupplierItemList(Request $request)
    {
        $tenderId = $request['tenderMasterId'];
        $queryResult = BidSubmissionMaster::selectRaw("
        SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage) as weightage, 
        srm_bid_submission_master.id, 
        srm_supplier_registration_link.name,
        srm_tender_master.technical_passing_weightage as passing_weightage,
        srm_bid_submission_master.bidSubmissionCode")
            ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
            ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
            ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
            ->join('srm_evaluation_criteria_details', 'srm_evaluation_criteria_details.id', '=', 'srm_bid_submission_detail.evaluation_detail_id')
            ->havingRaw('weightage >= passing_weightage')
            ->groupBy('srm_bid_submission_master.id')
            ->where('srm_evaluation_criteria_details.critera_type_id', 2)
            ->where('srm_bid_submission_master.status', 1)
            ->where('srm_bid_submission_master.bidSubmittedYN', 1)
            ->where('srm_bid_submission_master.tender_id', $tenderId)
            ->orderBy('srm_bid_submission_master.id', 'desc')
            ->get();

        $itemListIsEnableFalse = PricingScheduleDetail::select('id', 'label')
            ->where('tender_id', $tenderId)
            ->whereNotIn('field_type', [3,4])
            ->where('is_disabled', 0)
            ->get()
            ->toArray();

        return $this->sendResponse(['supplierList'=> $queryResult, 'itemList' => $itemListIsEnableFalse], 'Data retrieved successfully');
    }

    public function generateSupplierItemReportTableView(Request $request)
    {
        $tenderId = $request['tenderMasterId'];
        $bidSubmission = $request['bidSubmission'];
        $itemList = $request['itemList'];

        $bidMasterId = [];
        if(isset($bidSubmission)){
            foreach ($bidSubmission as $bid){
                $bidMasterId[] = $bid['id'];
            }
        }

        $notBoqitems = [];
        if(isset($itemList)){
            foreach ($itemList as $item){
                $notBoqitems[] = $item['id'];
            }
        }

        $queryResult = PricingScheduleMaster::with(['tender_master.srm_bid_submission_master' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with('SupplierRegistrationLink')->whereIn('id', $bidMasterId);
        }, 'pricing_shedule_details' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with('tender_boq_items')->where('is_disabled', 0)->whereNotIn('field_type', [3,4]);
                if(sizeof($notBoqitems) > 0 ){
                    $q->whereIn('id', $notBoqitems);
                }
        }])->where('tender_id', $tenderId)->get();

        $supplierNameCode = array();
        $supplierArr = array();
        foreach ($queryResult as $query) {
            foreach ($query['tender_master']['srm_bid_submission_master'] as $item) {
                $supplierArr['id'] = $item['id'];
                $supplierArr['name'] = $item['SupplierRegistrationLink']['name'] ." - " . $item['bidSubmissionCode'];
                $supplierNameCode[] = $supplierArr;
            }
        }

        $data = PricingScheduleMaster::with(['tender_bid_format_master', 'bid_schedule' => function ($q) use ($bidMasterId) {
            $q->where('bid_master_id', $bidMasterId);
        }, 'pricing_shedule_details' => function ($q) use ($bidMasterId, $notBoqitems) {
            $q->with(['tender_boq_items' => function ($q) use ($bidMasterId) {
                $q->with(['bid_boqs' => function ($q) use ($bidMasterId) {
                    $q->whereIn('bid_master_id', $bidMasterId);
                }]);
            }, 'bid_main_works' => function ($q) use ($bidMasterId) {
                $q->with('srm_bid_submission_master.SupplierRegistrationLink')->whereIn('bid_master_id', $bidMasterId);
            }])->whereNotIn('field_type', [3,4])->where('is_disabled', 0);
                if($notBoqitems){
                   $q->whereIn('id', $notBoqitems);
                }
        }])->where('tender_id', $tenderId)->get();

        $itemsArrayCount = array();
        $arr = array();
        foreach ($bidSubmission as $bid) {
            $totalItemsCount = 0;
            $totalAmount = PricingScheduleMaster::with(['pricing_shedule_details' => function ($q) use ($bid, $notBoqitems) {
                $q->with(['bid_main_works' => function ($q) use ($bid) {
                    $q->where('bid_master_id', $bid)->sum('total_amount');
                }])->whereNotIn('field_type', [3, 4])->where('is_disabled', 0);
                if ($notBoqitems) {
                    $q->whereIn('id', $notBoqitems);
                }
            }])->where('tender_id', $tenderId)->get()->toArray();

            foreach ($totalAmount as $pricing_shedule_details) {
                foreach ($pricing_shedule_details['pricing_shedule_details'] as $item) {
                    if(isset($item['bid_main_works'][0])){
                        $totalItemsCount = $totalItemsCount + $item['bid_main_works'][0]['total_amount'];
                    }
                }
            }

            $arr['id'] = $bid['id'];
            $arr['value'] = $totalItemsCount;
            $itemsArrayCount[] = $arr;
        }

        $result = ['supplier_list' => $supplierNameCode,
            'item_list' => $data,
            'totalItemsCount' => $itemsArrayCount];

        return $this->sendResponse($result, 'Data retrieved successfully');

    }

    public function SupplierSheduleWiseReport(Request $request) {
        $queryResult = [];
        
        $tenderId = $request->input('tenderMasterId');
        $bidSubmissionIDs = $request->input('bidSubmissionID');
        $bidSubmissionIDs = collect($bidSubmissionIDs)->pluck('id')->toArray();
        $tenderMaster = TenderMaster::find($tenderId);

        $suppliers = BidSubmissionMaster::where('tender_id',$tenderId)->where('status',1)->where('bidSubmittedYN',1)->groupBy('supplier_registration_id')->pluck('supplier_registration_id')->toArray();

        $pring_schedul_master_datas =  PricingScheduleMaster::with(['tender_main_works' => function ($q1) use ($tenderId) {
            $q1->where('tender_id', $tenderId);
            $q1->with(['bid_main_work' => function ($q2) use ($tenderId) {
                $q2->where('tender_id', $tenderId);
            }]);
        }])
            ->where('tender_id', $tenderId)
            ->where('status',1)->get();

        $data = Array();
        $x=0;
        $total = 0;



        foreach($suppliers as $supplier) {

            foreach($pring_schedul_master_datas as $pring_schedul_master_data) {

                $pricing_shedule_details = $pring_schedul_master_data->pricing_shedule_details()->where('is_disabled',0)->get();
                
                foreach($pricing_shedule_details as $pricing_shedule_detail) {
    
                    if($pricing_shedule_detail->boq_applicable) {
                        $items = $pricing_shedule_detail->tender_boq_items;
    
                        foreach ($items as $item) {
                           $bid_boq =  BidBoq::where('boq_id', $item->id)->where('main_works_id',$item->main_work_id);
                            if($supplier) {
                                $bid_boq->where('created_by',$supplier);
                            }

                            if($bidSubmissionIDs) {
                                $bid_boq->whereIn('bid_master_id',$bidSubmissionIDs);
                            }
    
                            $bid_boq = $bid_boq->orderBy("id","desc")->first();
    
                           if($bid_boq) 
                                $total += $bid_boq->total_amount;
    
                        }
    
    
                    }else {
                        if($pricing_shedule_detail->field_type != 4 || $pricing_shedule_detail->field_type != 3) { 
                            //BidMainWork
                            $dataBidBoq = BidMainWork::where('tender_id',$tenderId)->where('main_works_id',$pricing_shedule_detail->id);
                            if($supplier) 
                                $dataBidBoq->where('created_by',$supplier);
                            
                            if($bidSubmissionIDs) 
                                $dataBidBoq->whereIn('bid_master_id',$bidSubmissionIDs);

                            $dataBidBoq = $dataBidBoq->orderBy("id","desc")->first();
                            if($dataBidBoq)
                                $total += $dataBidBoq->total_amount;
                        }
                    }
    
                }
                $pring_schedul_master_data['total'] = $total;
            }


            $supplier_data = SupplierRegistrationLink::find($supplier);

            $data[$x]['pring_schedul_master_data'] = $pring_schedul_master_data;
            $data[$x]['supplier'] = ($supplier_data) ? $supplier_data->name : "";
            $data[$x]['ranking'] = "L".($x+1);
            $x++;
            
        }

        


        return $this->sendResponse($data, 'Data retrieved successfully');
 
    }
}
