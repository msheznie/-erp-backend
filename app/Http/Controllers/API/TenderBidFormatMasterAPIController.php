<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderBidFormatMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderBidFormatMasterAPIRequest;
use App\Models\PricingScheduleMaster;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderFieldType;
use App\Repositories\TenderBidFormatMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderBidFormatMasterController
 * @package App\Http\Controllers\API
 */

class TenderBidFormatMasterAPIController extends AppBaseController
{
    /** @var  TenderBidFormatMasterRepository */
    private $tenderBidFormatMasterRepository;

    public function __construct(TenderBidFormatMasterRepository $tenderBidFormatMasterRepo)
    {
        $this->tenderBidFormatMasterRepository = $tenderBidFormatMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatMasters",
     *      summary="Get a listing of the TenderBidFormatMasters.",
     *      tags={"TenderBidFormatMaster"},
     *      description="Get all TenderBidFormatMasters",
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
     *                  @SWG\Items(ref="#/definitions/TenderBidFormatMaster")
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
        $this->tenderBidFormatMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBidFormatMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBidFormatMasters = $this->tenderBidFormatMasterRepository->all();

        return $this->sendResponse($tenderBidFormatMasters->toArray(), 'Tender Bid Format Masters retrieved successfully');
    }

    /**
     * @param CreateTenderBidFormatMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBidFormatMasters",
     *      summary="Store a newly created TenderBidFormatMaster in storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Store TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatMaster")
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
     *                  ref="#/definitions/TenderBidFormatMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBidFormatMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->create($input);

        return $this->sendResponse($tenderBidFormatMaster->toArray(), 'Tender Bid Format Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Display the specified TenderBidFormatMaster",
     *      tags={"TenderBidFormatMaster"},
     *      description="Get TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
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
     *                  ref="#/definitions/TenderBidFormatMaster"
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
        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError('Tender Bid Format Master not found');
        }

        return $this->sendResponse($tenderBidFormatMaster->toArray(), 'Tender Bid Format Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderBidFormatMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Update the specified TenderBidFormatMaster in storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Update TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidFormatMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidFormatMaster")
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
     *                  ref="#/definitions/TenderBidFormatMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBidFormatMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError('Tender Bid Format Master not found');
        }

        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->update($input, $id);

        return $this->sendResponse($tenderBidFormatMaster->toArray(), 'TenderBidFormatMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBidFormatMasters/{id}",
     *      summary="Remove the specified TenderBidFormatMaster from storage",
     *      tags={"TenderBidFormatMaster"},
     *      description="Delete TenderBidFormatMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidFormatMaster",
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
        /** @var TenderBidFormatMaster $tenderBidFormatMaster */
        $tenderBidFormatMaster = $this->tenderBidFormatMasterRepository->findWithoutFail($id);

        if (empty($tenderBidFormatMaster)) {
            return $this->sendError('Tender Bid Format Master not found');
        }

        $tenderBidFormatMaster->delete();

        return $this->sendSuccess('Tender Bid Format Master deleted successfully');
    }

    public function getTenderBidFormats(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];



        $tenderMaster = TenderBidFormatMaster::where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->where('tender_name', 'LIKE', "%{$search}%");
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

    public function storeBidFormat(Request $request)
    {
       $input = $request->all();
       $employee = \Helper::getEmployeeInfo();
       $boq_applicable = 0;
       if(isset($input['boq_applicable']) && $input['boq_applicable']){
           $boq_applicable = 1;
       }

       $exist = TenderBidFormatMaster::where('tender_name',$input['tender_name'])
           ->where('company_id',$input['companySystemID'])->first();

       if(!empty($exist)){
           return ['success' => false, 'message' => 'Description already exist'];
       }

        DB::beginTransaction();
        try {
           $data['boq_applicable']=$boq_applicable;
           $data['tender_name']=$input['tender_name'];
           $data['company_id']=$input['companySystemID'];
           $data['created_by'] = $employee->employeeSystemID;

           $result = TenderBidFormatMaster::create($data);

           if($result){
               DB::commit();
               return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
           }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function loadBidFormatMaster(Request $request)
    {
        $input = $request->all();

        $data['master'] = TenderBidFormatMaster::where('id',$input['id'])->where('company_id',$input['companySystemID'])->first();
        $data['detail'] = TenderBidFormatDetail::where('tender_id',$input['id'])->get();
        $data['tenderType'] = TenderFieldType::get();
        $pricebid = self::priceBidExistInTender($input['id']);
        if(!empty($pricebid)){
            $data['pricebid'] = 1;
        }else{
            $data['pricebid'] = 0;
        }

        return $data;
    }

    public function addPriceBidDetail(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $is_disabled = 0;
        if(!isset($input['label']) || empty($input['label'])){
            return ['success' => false, 'message' => 'Label is required'];
        }

        if(!isset($input['field_type']) || empty($input['field_type'])){
            return ['success' => false, 'message' => 'Field Type is required'];
        }

        if(isset($input['is_disabled']) && $input['is_disabled']){
            $is_disabled = 1;
        }

        $exist = TenderBidFormatDetail::where('label',$input['label'])
            ->where('tender_id',$input['tender_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Label already exist'];
        }

        DB::beginTransaction();
        try {
            $data['is_disabled']=$is_disabled;
            $data['tender_id']=$input['tender_id'];
            $data['label']=$input['label'];
            $data['field_type']=$input['field_type'];
            $data['created_by'] = $employee->employeeSystemID;

            $result = TenderBidFormatDetail::create($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved'];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function updatePriceBidDetail(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array('field_type'));
        $employee = \Helper::getEmployeeInfo();
        $is_disabled = 0;
        if(!isset($input['label']) || empty($input['label'])){
            return ['success' => false, 'message' => 'Label is required'];
        }

        if(!isset($input['field_type']) || empty($input['field_type'])){
            return ['success' => false, 'message' => 'Field Type is required'];
        }

        if(isset($input['is_disabled']) && $input['is_disabled']){
            $is_disabled = 1;
        }

        $exist = TenderBidFormatDetail::where('label',$input['label'])
            ->where('tender_id',$input['tender_id'])->where('id','!=',$input['id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Label already exist'];
        }

        DB::beginTransaction();
        try {
            $data['is_disabled']=$is_disabled;
            $data['label']=$input['label'];
            $data['field_type']=$input['field_type'];
            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderBidFormatDetail::where('id',$input['id'])->update($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function updateBidFormat(Request $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();
        $boq_applicable = 0;
        if(isset($input['boq_applicable']) && $input['boq_applicable']){
            $boq_applicable = 1;
        }

        $exist = TenderBidFormatMaster::where('tender_name',$input['tender_name'])
            ->where('id','!=',$input['id'])
            ->where('company_id',$input['companySystemID'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Description already exist'];
        }

        DB::beginTransaction();
        try {
            $pricebid = self::priceBidExistInTender($input['id']);
            if(empty($pricebid)) {
                $data['boq_applicable'] = $boq_applicable;
            }
            $data['tender_name']=$input['tender_name'];
            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderBidFormatMaster::where('id',$input['id'])->update($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function deletePriceBideDetail(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = TenderBidFormatDetail::where('id',$input['id'])->delete();
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

    public function deletePriceBidMaster(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $pricebid = self::priceBidExistInTender($input['id']);

            if(!empty($pricebid)){
                return ['success' => false, 'message' => 'Price bid format cannot be deleted it has been used in tenders'];
            }

            $data['deleted_by'] = $employee->employeeSystemID;
            $data['deleted_at'] = now();
            $result = TenderBidFormatMaster::where('id',$input['id'])->update($data);
            TenderBidFormatDetail::where('tender_id',$input['id'])->delete();
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

    function priceBidExistInTender($id){
       return PricingScheduleMaster::with(['tender_master' => function($q){
            $q->where('confirmed_yn',1);
        }])->whereHas('tender_master' , function($q){
            $q->where('confirmed_yn',1);
        })->where('price_bid_format_id',$id)->first();
    }

}
