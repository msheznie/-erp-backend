<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidSubmissionMasterAPIRequest;
use App\Http\Requests\API\UpdateBidSubmissionMasterAPIRequest;
use App\Models\BidSubmissionDetail;
use App\Models\BidSubmissionMaster;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Repositories\BidSubmissionMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidSubmissionMasterController
 * @package App\Http\Controllers\API
 */

class BidSubmissionMasterAPIController extends AppBaseController
{
    /** @var  BidSubmissionMasterRepository */
    private $bidSubmissionMasterRepository;

    public function __construct(BidSubmissionMasterRepository $bidSubmissionMasterRepo)
    {
        $this->bidSubmissionMasterRepository = $bidSubmissionMasterRepo;
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

        $bidSubmissionMaster = $this->bidSubmissionMasterRepository->update($input, $id);

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

        $companyId = $request['companyId'];
        $tenderId = $request['tenderId'];



        $query = BidSubmissionMaster::with(['SupplierRegistrationLink'])->where('status', 1)->where('bidSubmittedYN', 1)->where('tender_id', $tenderId);

       // return $this->sendResponse($query, 'Tender Masters retrieved successfully');

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
                    $query->where('description', 'like', "%{$search}%")
                          ->orWhere('name', 'LIKE', "%{$search}%");
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
            $att['go_no_go_criteria_status'] = 1;
            $att['go_no_go_criteria_comment'] = $input['value'];
            $att['updated_at'] = Carbon::now();
            $att['updated_by'] = \Helper::getEmployeeSystemID();
            $result = BidSubmissionMaster::where('id', $input['id'])->update($att);

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

        $query = BidSubmissionMaster::where('tender_id', $tenderId)->where('doc_verifiy_status', 0)->count();


        if($query > 0)
        {
            $is_verified = false;
        }

        return $this->sendResponse($is_verified, 'Data retrived successfully');
    }
}
