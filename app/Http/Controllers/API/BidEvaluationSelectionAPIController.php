<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidEvaluationSelectionAPIRequest;
use App\Http\Requests\API\UpdateBidEvaluationSelectionAPIRequest;
use App\Models\BidEvaluationSelection;
use App\Models\TenderBidNegotiation;
use App\Repositories\BidEvaluationSelectionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\BidSubmissionDetail;
use App\Repositories\BidSubmissionMasterRepository;
use App\Models\TenderMaster;
use App\Repositories\TenderMasterRepository;
use Illuminate\Support\Facades\DB;
use App\Models\BidSubmissionMaster;
use Carbon\Carbon;
/**
 * Class BidEvaluationSelectionController
 * @package App\Http\Controllers\API
 */

class BidEvaluationSelectionAPIController extends AppBaseController
{
    /** @var  BidEvaluationSelectionRepository */
    private $bidEvaluationSelectionRepository;
    private $bidSubmissionMasterRepository;
    private $tenderMasterRepository;

    public function __construct(TenderMasterRepository $tenderMasterRepo,BidSubmissionMasterRepository $bidSubmissionMasterRepo,BidEvaluationSelectionRepository $bidEvaluationSelectionRepo)
    {
        $this->bidEvaluationSelectionRepository = $bidEvaluationSelectionRepo;
        $this->bidSubmissionMasterRepository = $bidSubmissionMasterRepo;
        $this->tenderMasterRepository = $tenderMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bidEvaluationSelections",
     *      summary="getBidEvaluationSelectionList",
     *      tags={"BidEvaluationSelection"},
     *      description="Get all BidEvaluationSelections",
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
     *                  @OA\Items(ref="#/definitions/BidEvaluationSelection")
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
        $this->bidEvaluationSelectionRepository->pushCriteria(new RequestCriteria($request));
        $this->bidEvaluationSelectionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidEvaluationSelections = $this->bidEvaluationSelectionRepository->all();

        return $this->sendResponse($bidEvaluationSelections->toArray(), 'Bid Evaluation Selections retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bidEvaluationSelections",
     *      summary="createBidEvaluationSelection",
     *      tags={"BidEvaluationSelection"},
     *      description="Create BidEvaluationSelection",
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
     *                  ref="#/definitions/BidEvaluationSelection"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidEvaluationSelectionAPIRequest $request)
    {
        $input = $request->all();

        $bids_details = $input['bids'];
        $bids = collect($bids_details)->pluck('id')->toArray();

        $details['tender_id'] = $input['tender_id'];
        $details['description'] = $input['description'];
        $details['bids'] = json_encode($bids);
        $details['created_by'] = \Helper::getEmployeeSystemID();
        $details['is_negotiation'] = $input['isNegotiation'];

        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->create($details);

        return $this->sendResponse($bids, trans('srm_faq.bid_evaluation_selection_saved'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bidEvaluationSelections/{id}",
     *      summary="getBidEvaluationSelectionItem",
     *      tags={"BidEvaluationSelection"},
     *      description="Get BidEvaluationSelection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidEvaluationSelection",
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
     *                  ref="#/definitions/BidEvaluationSelection"
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
        /** @var BidEvaluationSelection $bidEvaluationSelection */
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            return $this->sendError('Bid Evaluation Selection not found');
        }

        return $this->sendResponse($bidEvaluationSelection->toArray(), 'Bid Evaluation Selection retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bidEvaluationSelections/{id}",
     *      summary="updateBidEvaluationSelection",
     *      tags={"BidEvaluationSelection"},
     *      description="Update BidEvaluationSelection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidEvaluationSelection",
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
     *                  ref="#/definitions/BidEvaluationSelection"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidEvaluationSelectionAPIRequest $request)
    {
        $input = $request->all();

      

            $type = $input['type'];
            $tender_id = $input['tender_id'];
            $bucket_id = $input['id'];
            unset($input['type']);
            unset($input['tender_id']);
            unset($input['id']);
    
           
            if($type == 2)
            {
                $bid_master_ids = json_decode(BidEvaluationSelection::where('id',$id)->pluck('bids')[0],true);
    
                $evaluation = BidSubmissionDetail::where('tender_id',$tender_id)->whereIn('bid_master_id',$bid_master_ids)->where('eval_result',null)->whereHas('srm_evaluation_criteria_details',function($q){
                    $q->where('critera_type_id',2);
                })->count();
    
             
                if($evaluation > 0)
                {
                    return $this->sendError('Please enter the remaining user values for the technical evaluation',500);
                }
         
            }

        

        /** @var BidEvaluationSelection $bidEvaluationSelection */
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            return $this->sendError('Bid Evaluation Selection not found');
        }

        if($type == 1){
            $input['updated_by'] = \Helper::getEmployeeSystemID();
            $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->update($input, $id);
        }



        if($type == 2)
        {
            // $bids_bucket = $this->bidSubmissionMasterRepository
            // ->where('bidSubmittedYN', 1)->where('tender_id', $tender_id)->where('doc_verifiy_status',1)->orderBy('id')->count();

            // $bids =  (BidEvaluationSelection::where('tender_id',$tender_id)->pluck('bids'));
            // $count = 0;
            // foreach($bids as $bid)
            // {
            //     $count += count(json_decode($bid,true));
            // }

            // $status =  BidEvaluationSelection::where('tender_id',$tender_id)->where('status',0)->count();

            

            // if(($bids_bucket == $count) && $status == 0)
            // {
            //     $update_status['technical_eval_status'] = 1;
            //     $this->tenderMasterRepository->update($update_status, $tender_id);

            // }

            $input['updated_by'] = \Helper::getEmployeeSystemID();
            $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->update($input, $id);

            BidSubmissionMaster::where('tender_id', $tender_id)->whereIn('id', $bid_master_ids)->update(
                ['technical_verify_status'=>1,
                'technical_verify_by'=>\Helper::getEmployeeSystemID(),
                'technical_verify_at'=>Carbon::now(),
                'technical_eval_remarks'=>$input['remarks']]
            );

            $query = BidSubmissionMaster::where('tender_id', $tender_id)->where('technical_verify_status','!=', 1)->where('bidSubmittedYN',1)->where('doc_verifiy_status',1)->where('status',1)->count();
            if($query == 0)
            {
                    $tenderMaster = $this->tenderMasterRepository->findWithoutFail($tender_id);
                    $tenderMaster->technical_eval_status = 1;;
                    $tenderMaster->save();
            }
        }

        if($type == 3){
                unset($input['status']);
                unset($input['updated_at']);
                $input['remarks'] = $input['remarks'];
                $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->update($input, $id);

                $bid_master_ids = json_decode(BidEvaluationSelection::where('id',$id)->pluck('bids')[0],true);
                BidSubmissionMaster::where('tender_id', $tender_id)->whereIn('id', $bid_master_ids)->update(
                    ['technical_eval_remarks'=>$input['remarks']]
                );


        }

        return $this->sendResponse($bidEvaluationSelection->toArray(), 'BidEvaluationSelection updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bidEvaluationSelections/{id}",
     *      summary="deleteBidEvaluationSelection",
     *      tags={"BidEvaluationSelection"},
     *      description="Delete BidEvaluationSelection",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidEvaluationSelection",
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
        /** @var BidEvaluationSelection $bidEvaluationSelection */
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            return $this->sendError('Bid Evaluation Selection not found');
        }

        $bidEvaluationSelection->delete();

        return $this->sendSuccess('Bid Evaluation Selection deleted successfully');
    }

    public function getBidSelection(Request $request)
    {
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $isNegotiation = $request['isNegotiation'];

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $query = BidEvaluationSelection::with('created_by')->where('tender_id', $tenderId)->where('is_negotiation', $isNegotiation);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
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

    public function removeBid(Request $request)
    {
  

        DB::beginTransaction();
        try {
            $input = $request->all();
            $id = $input['id'];
            $selection_id = $input['selection_id'];
            $bid_master_ids = json_decode(BidEvaluationSelection::where('id',$selection_id)->pluck('bids')[0],true);
    
            $newArray = array_diff($bid_master_ids, (array)$id);
    
            $result = (array_values($newArray));
    
            $temp['bids'] =  json_encode($result);
            $output = BidEvaluationSelection::where('id',$selection_id)->update($temp);

            if ($output) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $output];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }

    public function addBid(Request $request)
    {
  
        
        DB::beginTransaction();
        try {
            $input = $request->all();
            $bids_details = $input['bids'];
            $bids = collect($bids_details)->pluck('id')->toArray();

            $selection_id = $input['group_id'];
            $bid_master_ids = json_decode(BidEvaluationSelection::where('id',$selection_id)->pluck('bids')[0],true);
            

            $final_array = array_merge($bid_master_ids,$bids);

    
            $temp['bids'] =  json_encode($final_array);
            $output = BidEvaluationSelection::where('id',$selection_id)->update($temp);

            if ($output) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $output];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }


}
