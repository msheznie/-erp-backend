<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderFinalBidsAPIRequest;
use App\Http\Requests\API\UpdateTenderFinalBidsAPIRequest;
use App\Models\BidSubmissionMaster;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\Employee;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\TenderBidNegotiation;
use App\Models\TenderFinalBids;
use App\Models\TenderNegotiation;
use App\Repositories\TenderFinalBidsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TenderMaster;
/**
 * Class TenderFinalBidsController
 * @package App\Http\Controllers\API
 */

class TenderFinalBidsAPIController extends AppBaseController
{
    /** @var  TenderFinalBidsRepository */
    private $tenderFinalBidsRepository;

    public function __construct(TenderFinalBidsRepository $tenderFinalBidsRepo)
    {
        $this->tenderFinalBidsRepository = $tenderFinalBidsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderFinalBids",
     *      summary="getTenderFinalBidsList",
     *      tags={"TenderFinalBids"},
     *      description="Get all TenderFinalBids",
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
     *                  @OA\Items(ref="#/definitions/TenderFinalBids")
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
        $this->tenderFinalBidsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderFinalBidsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderFinalBids = $this->tenderFinalBidsRepository->all();

        return $this->sendResponse($tenderFinalBids->toArray(), 'Tender Final Bids retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderFinalBids",
     *      summary="createTenderFinalBids",
     *      tags={"TenderFinalBids"},
     *      description="Create TenderFinalBids",
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
     *                  ref="#/definitions/TenderFinalBids"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderFinalBidsAPIRequest $request)
    {
        $input = $request->all();

        $tenderFinalBids = $this->tenderFinalBidsRepository->create($input);

        return $this->sendResponse($tenderFinalBids->toArray(), 'Tender Final Bids saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderFinalBids/{id}",
     *      summary="getTenderFinalBidsItem",
     *      tags={"TenderFinalBids"},
     *      description="Get TenderFinalBids",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderFinalBids",
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
     *                  ref="#/definitions/TenderFinalBids"
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
        /** @var TenderFinalBids $tenderFinalBids */
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            return $this->sendError('Tender Final Bids not found');
        }

        return $this->sendResponse($tenderFinalBids->toArray(), 'Tender Final Bids retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderFinalBids/{id}",
     *      summary="updateTenderFinalBids",
     *      tags={"TenderFinalBids"},
     *      description="Update TenderFinalBids",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderFinalBids",
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
     *                  ref="#/definitions/TenderFinalBids"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderFinalBidsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderFinalBids $tenderFinalBids */
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            return $this->sendError('Tender Final Bids not found');
        }

        $tenderFinalBids = $this->tenderFinalBidsRepository->update($input, $id);

        return $this->sendResponse($tenderFinalBids->toArray(), 'TenderFinalBids updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderFinalBids/{id}",
     *      summary="deleteTenderFinalBids",
     *      tags={"TenderFinalBids"},
     *      description="Delete TenderFinalBids",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderFinalBids",
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
        /** @var TenderFinalBids $tenderFinalBids */
        $tenderFinalBids = $this->tenderFinalBidsRepository->findWithoutFail($id);

        if (empty($tenderFinalBids)) {
            return $this->sendError('Tender Final Bids not found');
        }

        $tenderFinalBids->delete();

        return $this->sendSuccess('Tender Final Bids deleted successfully');
    }

    public function getFinalBids(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $tenderId = $request['tenderId'];

        $isNegotiation = $request['isNegotiation'];
        $latestNegotiation = [];
        if($isNegotiation){
            $latestNegotiation = TenderNegotiation::getTenderLatestNegotiations($tenderId);
        }

        $tenderBidNegotiations = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation);

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        $query = TenderFinalBids::selectRaw('srm_tender_final_bids.id,srm_tender_final_bids.status,srm_tender_final_bids.supplier_id,srm_tender_final_bids.com_weightage,srm_tender_final_bids.tech_weightage,srm_tender_final_bids.total_weightage,srm_tender_final_bids.bid_id,srm_bid_submission_master.bidSubmittedDatetime,srm_supplier_registration_link.name,srm_bid_submission_master.bidSubmissionCode,srm_bid_submission_master.line_item_total,srm_tender_final_bids.award, srm_tender_final_bids.combined_ranking')
        ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
        ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
        ->where('srm_tender_final_bids.status',1)
        ->where('srm_tender_final_bids.tender_id', $tenderId);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        }

        $query = $query->orderBy('srm_tender_final_bids.total_weightage','desc');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('bidSubmissionCode', 'LIKE', "%{$search}%");
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
            ->addColumn('latestNegotiationVersion', function () use ($isNegotiation, $latestNegotiation) {
                return $isNegotiation ? $latestNegotiation->version : null;
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFinalBidsReport(Request $request)
    {
        $tenderId = $request->get('id');
        $employeeID = $request->get('userID');
        $tenderMaster= TenderMaster::select('title', 'tender_code', 'stage', 'negotiation_code', 'bid_opening_date', 'technical_bid_opening_date', 'commerical_bid_opening_date', 'award_comment', 'negotiation_award_comment', 'negotiation_code')
            ->where('id', $tenderId)
            ->first();

        $tenderCompany = TenderMaster::with('company')->where('id', $tenderId)->first();

        $isNegotiation = 0;
        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId)
            ->get();

        $bidSubmissionMasterIds = [];

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        }

        $query = TenderFinalBids::selectRaw('srm_tender_final_bids.id,srm_tender_final_bids.status,srm_tender_final_bids.supplier_id,srm_tender_final_bids.com_weightage,srm_tender_final_bids.tech_weightage,srm_tender_final_bids.total_weightage,srm_tender_final_bids.bid_id,srm_bid_submission_master.bidSubmittedDatetime,srm_supplier_registration_link.name,srm_bid_submission_master.bidSubmissionCode,srm_bid_submission_master.line_item_total,srm_tender_final_bids.award, srm_tender_final_bids.combined_ranking')
            ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
            ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
            ->where('srm_tender_final_bids.status',1)
            ->where('srm_tender_final_bids.tender_id', $tenderId);

        if ($tenderMaster->negotiation_code != null || $tenderMaster->negotiation_code != '') {
            $isNegotiation = 1;
            $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        }

        $employeeData = Employee::where('employeeSystemID',$employeeID)->first();

        $awardSummary = $query->orderBy('srm_tender_final_bids.total_weightage','desc')->get();
        $time = strtotime("now");
        $fileName = 'Supplier_Ranking_Summary' . $time . '.pdf';
        $order = array('awardSummary' => $awardSummary, 'tenderMaster' => $tenderMaster, 'isNegotiation' => $isNegotiation, 'tenderCompany' => $tenderCompany, 'employeeData' => $employeeData);
        $html = view('print.final_bid_summary_print', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
    }

    public function getTenderAwardingReport(Request $request)
    {
        $tenderId = $request->get('id');
        $employeeID = $request->get('userID');

        $tenderBidNegotiations = TenderBidNegotiation::select('bid_submission_master_id_new')
            ->where('tender_id', $tenderId)
            ->get();

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        $getNegotiationCode = TenderMaster::select('negotiation_code')->where('id', $tenderId)->first();

        $tenderMaster = TenderMaster::where('id', $tenderId)->with(['ranking_supplier' => function ($q) use($bidSubmissionMasterIds, $getNegotiationCode) {
            if($getNegotiationCode->negotiation_code != '' OR $getNegotiationCode->negotiation_code != null){
                $q->whereIn('bid_id', $bidSubmissionMasterIds);
            }
            $q->where('award', 1)->with('supplier');
        }])->first();


        $employeeDetails = SrmTenderBidEmployeeDetails::where('tender_id', $tenderId)->with('employee')->get();

        $company = Company::where('companySystemID', $tenderMaster->company_id)->first();

        $employeeData = Employee::where('employeeSystemID',$employeeID)->first();

        $time = strtotime("now");
        $fileName = 'Minutes_of_Tender_Awarding' . $time . '.pdf';
        $order = array('tenderMaster' => $tenderMaster, 'employeeDetails' => $employeeDetails, 'company' => $company, 'employeeData' => $employeeData);
        $html = view('print.minutes_of_tender_awarding_print', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);

    }


    public function confirmFinalBid(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = $request->all();
            $tenderId = $inputs['tenderMasterId']; 
            $isNegotiation = $inputs['isNegotiation'];
            $comment = $inputs['comment'];
            $id = $inputs['id'][0];

            $existingAwardedTender = TenderFinalBids::where('tender_id', $tenderId)
                ->where('award', true)
                ->first();

            if ($existingAwardedTender) {
                $existingAwardedTender->update(['award' => false]);
            }

            TenderFinalBids::where('id',$id)->update(['award'=>true]);

            if($isNegotiation == 0) {
                $update = ['is_awarded'=>true,'award_comment'=>$comment];
            } else if($isNegotiation == 1) {
                $update = ['negotiation_is_awarded'=>true,'negotiation_award_comment'=>$comment];
            }

            TenderMaster::where('id',$tenderId)->update($update);

            DB::commit();
            return ['success' => true, 'message' => 'Successfully updated', 'data' => true];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
}
