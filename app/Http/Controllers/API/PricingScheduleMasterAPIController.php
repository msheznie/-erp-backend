<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleMasterAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleMasterAPIRequest;
use App\Models\PricingScheduleMaster;
use App\Models\ScheduleBidFormatDetails;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderMainWorks;
use App\Repositories\PricingScheduleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PricingScheduleDetail;
use App\Models\TenderBoqItems;

/**
 * Class PricingScheduleMasterController
 * @package App\Http\Controllers\API
 */

class PricingScheduleMasterAPIController extends AppBaseController
{
    /** @var  PricingScheduleMasterRepository */
    private $pricingScheduleMasterRepository;

    public function __construct(PricingScheduleMasterRepository $pricingScheduleMasterRepo)
    {
        $this->pricingScheduleMasterRepository = $pricingScheduleMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pricingScheduleMasters",
     *      summary="Get a listing of the PricingScheduleMasters.",
     *      tags={"PricingScheduleMaster"},
     *      description="Get all PricingScheduleMasters",
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
     *                  @SWG\Items(ref="#/definitions/PricingScheduleMaster")
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
        $this->pricingScheduleMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->pricingScheduleMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pricingScheduleMasters = $this->pricingScheduleMasterRepository->all();

        return $this->sendResponse($pricingScheduleMasters->toArray(), 'Pricing Schedule Masters retrieved successfully');
    }

    /**
     * @param CreatePricingScheduleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pricingScheduleMasters",
     *      summary="Store a newly created PricingScheduleMaster in storage",
     *      tags={"PricingScheduleMaster"},
     *      description="Store PricingScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PricingScheduleMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PricingScheduleMaster")
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
     *                  ref="#/definitions/PricingScheduleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePricingScheduleMasterAPIRequest $request)
    {
        $input = $request->all();

        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->create($input);

        return $this->sendResponse($pricingScheduleMaster->toArray(), 'Pricing Schedule Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pricingScheduleMasters/{id}",
     *      summary="Display the specified PricingScheduleMaster",
     *      tags={"PricingScheduleMaster"},
     *      description="Get PricingScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMaster",
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
     *                  ref="#/definitions/PricingScheduleMaster"
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
        /** @var PricingScheduleMaster $pricingScheduleMaster */
        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->findWithoutFail($id);

        if (empty($pricingScheduleMaster)) {
            return $this->sendError('Pricing Schedule Master not found');
        }

        return $this->sendResponse($pricingScheduleMaster->toArray(), 'Pricing Schedule Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePricingScheduleMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pricingScheduleMasters/{id}",
     *      summary="Update the specified PricingScheduleMaster in storage",
     *      tags={"PricingScheduleMaster"},
     *      description="Update PricingScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PricingScheduleMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PricingScheduleMaster")
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
     *                  ref="#/definitions/PricingScheduleMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePricingScheduleMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var PricingScheduleMaster $pricingScheduleMaster */
        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->findWithoutFail($id);

        if (empty($pricingScheduleMaster)) {
            return $this->sendError('Pricing Schedule Master not found');
        }

        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->update($input, $id);

        return $this->sendResponse($pricingScheduleMaster->toArray(), 'PricingScheduleMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pricingScheduleMasters/{id}",
     *      summary="Remove the specified PricingScheduleMaster from storage",
     *      tags={"PricingScheduleMaster"},
     *      description="Delete PricingScheduleMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PricingScheduleMaster",
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
        /** @var PricingScheduleMaster $pricingScheduleMaster */
        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->findWithoutFail($id);

        if (empty($pricingScheduleMaster)) {
            return $this->sendError('Pricing Schedule Master not found');
        }

        $pricingScheduleMaster->delete();

        return $this->sendSuccess('Pricing Schedule Master deleted successfully');
    }

    public function getPricingScheduleList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tender_id = $input['tender_id'];



        $tenderMaster = PricingScheduleMaster::with(['tender_master' => function($q){
            $q->with(['envelop_type']);
        },'tender_bid_format_master','pricing_shedule_details'=>function($q){
            $q->where('boq_applicable',true);
        },'pricing_shedule_details1'=>function($q){
            $q->where('is_disabled',true);
        }])->where('tender_id', $tender_id)->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhereHas('tender_bid_format_master', function ($query1) use ($search) {
                    $query1->where('tender_name', 'LIKE', "%{$search}%");
                });
                $query->orWhere('scheduler_name', 'LIKE', "%{$search}%");
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

    public function getPricingScheduleDropDowns(Request $request)
    {
        $input = $request->all();
        $data['priceBidFormatDrop'] = TenderBidFormatMaster::with(['tender_bid_format_detail'])->whereHas('tender_bid_format_detail')->where('company_id',$input['companySystemID'])->get();

        return $data;
    }

    public function addPricingSchedule(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('price_bid_format_id'));
        $schedule_mandatory = 0;
        $items_mandatory = 0;
        if(isset($input['schedule_mandatory'])){
            if($input['schedule_mandatory']){
                $schedule_mandatory = 1;
            }
        }

        if(isset($input['items_mandatory'])){
            if($input['items_mandatory']){
                $items_mandatory = 1;
            }
        }
        if(isset($input['id'])) {
            $exist = PricingScheduleMaster::where('id','!=',$input['id'])->where('tender_id', $input['tenderMasterId'])->where('scheduler_name', $input['scheduler_name'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Scheduler name can not be duplicated'];
            }
        }else{
            $exist = PricingScheduleMaster::where('scheduler_name', $input['scheduler_name'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Scheduler name can not be duplicated'];
            }
        }


        
        //check if formula is empty or not


        if(TenderBidFormatDetail::where('tender_id', $input['price_bid_format_id'])->where('field_type',4)->where('finalTotalYn', 0)->where('formula_string',"")->count() > 0)
        {
            return ['success' => false, 'message' => 'Pricing Bid format should have a defined formula'];
        }
        else if(TenderBidFormatDetail::where('tender_id', $input['price_bid_format_id'])->where('field_type',4)->where('finalTotalYn', 1)->where('formula_string',"")->count() > 0)
        {
            return ['success' => false, 'message' => 'Formula is required for the “Final Total” total line'];
        }


        if(isset($input['id'])) {
            $schedule = PricingScheduleMaster::where('id', $input['id'])->first();
        }
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['tender_id']=$input['tenderMasterId'];
            $data['scheduler_name']=$input['scheduler_name'];
            $data['price_bid_format_id']=$input['price_bid_format_id'];
            $data['schedule_mandatory']=$schedule_mandatory;
            $data['items_mandatory']=$items_mandatory;
            $data['company_id']=$input['companySystemID'];

            if(isset($input['id'])){
                $data['updated_by'] = $employee->employeeSystemID;
                $pricingSheduleMaster = PricingScheduleMaster::find($input['id']);
                $result = $pricingSheduleMaster->update($data);
                if($result){
                    if($schedule['price_bid_format_id'] != $input['price_bid_format_id']){
                        $master['status']=0;
                        PricingScheduleMaster::where('id',$input['id'])->update($master);
                        TenderMainWorks::where('schedule_id',$input['id'])->delete();
                        $priceBid = TenderBidFormatDetail::where('tender_id',$input['price_bid_format_id'])->where('is_disabled',0)->get();
                        foreach ($priceBid as $bid){
                            $dataBid['tender_id']=$input['tenderMasterId'];
                            $dataBid['schedule_id']=$input['id'];
                            $dataBid['bid_format_detail_id']=$bid['id'];
                            $dataBid['item']=$bid['label'];
                            $dataBid['company_id']=$input['companySystemID'];
                            $dataBid['created_by']=$employee->employeeSystemID;
                            TenderMainWorks::create($dataBid);
                        }
                    }
                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
                }
            }else{

              
                $data['created_by'] = $employee->employeeSystemID;
                $result = PricingScheduleMaster::create($data);
                if($result){
                    // $priceBid = TenderBidFormatDetail::where('tender_id',$input['price_bid_format_id'])->where('is_disabled',0)->get();
                    // foreach ($priceBid as $bid){
                    //     $dataBid['tender_id']=$input['tenderMasterId'];
                    //     $dataBid['schedule_id']=$result['id'];
                    //     $dataBid['bid_format_detail_id']=$bid['id'];
                    //     $dataBid['item']=$bid['label'];
                    //     $dataBid['company_id']=$input['companySystemID'];
                    //     $dataBid['created_by']=$employee->employeeSystemID;
                    //     TenderMainWorks::create($dataBid);


                    // }
               
                    $priceBidShe = TenderBidFormatDetail::where('tender_id',$input['price_bid_format_id'])->select('id','field_type','tender_id','label','is_disabled','boq_applicable','formula_string')->get();

                    $status_updated['status'] = true;
                    $status_updated['boq_status'] = true;

                    foreach ($priceBidShe as $bid){

                        if(($bid->is_disabled == 1) && $bid->field_type != 4)
                        {
                            $status_updated['status'] = false;
                        }

                        
                        if(($bid->boq_applicable == 1) && $bid->field_type != 4)
                        {
                            $status_updated['boq_status'] = false;
                        }
                        
                        $dataBidShed['tender_id']=$input['tenderMasterId'];
                        $dataBidShed['bid_format_id']=$bid['tender_id'];
                        $dataBidShed['bid_format_detail_id']=$bid['id'];
                        $dataBidShed['label']=$bid['label'];
                        $dataBidShed['field_type']=$bid['field_type'];
                        $dataBidShed['is_disabled']=$bid['is_disabled'];
                        $dataBidShed['boq_applicable']=$bid['boq_applicable'];
                        $dataBidShed['pricing_schedule_master_id']=$result['id'];
                        $dataBidShed['company_id']=$input['companySystemID'];
                        $dataBidShed['formula_string']=$bid['formula_string'];
                        $dataBidShed['created_by']=$employee->employeeSystemID;
                        PricingScheduleDetail::create($dataBidShed);

                    }
              

                    PricingScheduleMaster::where('id',$result['id'])->update($status_updated);

                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getPricingScheduleMaster(Request $request)
    {
        $input = $request->all();
        return PricingScheduleMaster::with(['tender_bid_format_master'])->where('id',$input['id'])->first();

    }

    public function deletePricingSchedule(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $sheduleMaster = PricingScheduleMaster::find($input['id']);
            $result = $sheduleMaster->delete();
            if($result){
                //TenderMainWorks::where('schedule_id',$input['id'])->delete();
        
                $sheduleDetails = $this->deleteSheduleDetails($input['id']);
                if (!$sheduleDetails['success']) {
                    return $this->sendError($sheduleDetails['message'], 500);
                }
                $boqItems = PricingScheduleDetail::select('id')->where('pricing_schedule_master_id',$input['id'])->where('boq_applicable',1)->get();
                PricingScheduleDetail::where('pricing_schedule_master_id',$input['id'])->delete();

                $boqDetails = $this->deleteBoqItems($boqItems);
                if (!$boqDetails['success']) {
                    return $this->sendError($boqDetails['message'], 500);
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

    public function getPriceBidFormatDetails(Request $request)
    {
        $input = $request->all();

        $price_bid_format_id=$input['price_bid_format_id'];
        $schedule_id=$input['schedule_id'];

    //     return DB::table('srm_pricing_schedule_detail')->where('bid_format_id',$price_bid_format_id)->where('pricing_schedule_master_id',$schedule_id) 
    //     ->leftJoin('srm_schedule_bid_format_details', 'srm_pricing_schedule_detail.id', '=', 'srm_schedule_bid_format_details.bid_format_detail_id')
    //     ->join('tender_field_type', 'srm_pricing_schedule_detail.field_type', '=', 'tender_field_type.id')
    //     ->leftJoin('srm_bid_main_work', 'srm_pricing_schedule_detail.id', '=', 'srm_bid_main_work.main_works_id')  
    //    ->select('srm_pricing_schedule_detail.id as id','srm_pricing_schedule_detail.tender_id','srm_pricing_schedule_detail.label','srm_pricing_schedule_detail.is_disabled','tender_field_type.type','srm_pricing_schedule_detail.field_type as typeId','srm_pricing_schedule_detail.formula_string','srm_pricing_schedule_detail.bid_format_detail_id'
    //             ,'srm_pricing_schedule_detail.bid_format_id','srm_pricing_schedule_detail.pricing_schedule_master_id','srm_pricing_schedule_detail.boq_applicable',
    //            DB::raw('(CASE WHEN srm_pricing_schedule_detail.field_type = 4 THEN srm_schedule_bid_format_details.value 
    //                           WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.is_disabled = 1) THEN srm_schedule_bid_format_details.value    
    //                           WHEN (srm_pricing_schedule_detail.field_type != 4 && srm_pricing_schedule_detail.boq_applicable = 1) THEN srm_bid_main_work.total_amount    
    //                           END) AS value'))  
    //    ->get();



       $val1 =  DB::select("SELECT
       srm_pricing_schedule_detail.id,
       tender_id,
       srm_pricing_schedule_detail.label,
       srm_pricing_schedule_detail.boq_applicable,
       is_disabled,
       tender_field_type.type,
       tender_field_type.id as typeId,
       srm_schedule_bid_format_details.`value` 
        FROM
       srm_pricing_schedule_detail
       INNER JOIN tender_field_type ON tender_field_type.id = srm_pricing_schedule_detail.field_type
       LEFT JOIN srm_schedule_bid_format_details ON srm_schedule_bid_format_details.bid_format_detail_id = srm_pricing_schedule_detail.id 
       AND srm_schedule_bid_format_details.schedule_id = $schedule_id 
        WHERE
            bid_format_id = $price_bid_format_id AND pricing_schedule_master_id = $schedule_id AND deleted_at IS NULL AND field_type != 4
        ORDER BY
            id ASC");


        $val2 =  DB::select("SELECT
        srm_pricing_schedule_detail.id,
        tender_id,
        srm_pricing_schedule_detail.label,
        srm_pricing_schedule_detail.boq_applicable,
        is_disabled,
        tender_field_type.type,
        tender_field_type.id as typeId 
        FROM
        srm_pricing_schedule_detail
        INNER JOIN tender_field_type ON tender_field_type.id = srm_pricing_schedule_detail.field_type
        WHERE
            bid_format_id = $price_bid_format_id AND pricing_schedule_master_id = $schedule_id AND deleted_at IS NULL AND field_type = 4
        ORDER BY
            id ASC");


    return array_merge($val1,$val2); 



    }

    public function addPriceBidDetails(Request $request)
    {
        $input = $request->all();
        
        $masterData = $input['masterData'];
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {

            

            ScheduleBidFormatDetails::where('schedule_id',$masterData['schedule_id'])->delete();
            
            if(isset($input['priceBidFormat'])){
                if(count($input['priceBidFormat'])>0){
                    $result = false;
                    $isComplete = true;
                    $isBoqComplete = true;
                    foreach ($input['priceBidFormat'] as $val){

                        if($val['boq_applicable'] == 1 && $val['typeId'] != 4)
                        {
                            $id = $val['id'];
                            $result1 = TenderBoqItems::where('main_work_id',$id)->count();
                            if(($result1) == 0)
                            {
                                $isBoqComplete = false;
                            }
                        }


                        if($val['is_disabled'] == 1 && $val['typeId'] != 4)
                        {
                            if(empty($val['value']) || $val['value'] == null)
                            {
                                $isComplete = false;
                            }
                        }
                     
                        
                        if($val['typeId'] != 4)
                            {
                                if(!empty($val['value']) || $val['value'] == "0")
                                
                                {
                                $data['bid_format_detail_id'] = $val['id'];
                                $data['schedule_id'] = $masterData['schedule_id'];
                                $data['value'] = $val['value'];
                                $data['created_by'] = $employee->employeeSystemID;
                                $data['company_id'] = $masterData['companySystemID'];
                                $result = ScheduleBidFormatDetails::create($data);
                                
                               }
                            
                           }
                        
                    }
                    
                    $exist = ScheduleBidFormatDetails::where('schedule_id',$masterData['schedule_id'])->select('id')->first();

                        
                    if($result){

                       
                        $master['status'] = ($isComplete) ? 1 : 0;
                  
                        $master['boq_status'] = ($isBoqComplete) ? 1 : 0;

                        PricingScheduleMaster::where('id',$masterData['schedule_id'])->update($master);
                        DB::commit();
                        return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
                    }else{
                        if(empty($exist)){
                            $master['status']=0;
                            PricingScheduleMaster::where('id',$masterData['schedule_id'])->update($master);
                        }
                        DB::commit();
                        return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
                    }
                }else{
                    return ['success' => false, 'message' => 'Price bid format does not exist'];
                }
            }else{
                return ['success' => false, 'message' => 'Price bid format does not exist'];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }

    public function getNotPulledPriceBidDetails(Request $request)
    {
        $input = $request->all();
        $priceSchedule = PricingScheduleMaster::where('id',$input['schedule_id'])->first();
        return $mainWorks = PricingScheduleDetail::where('pricing_schedule_master_id',$input['schedule_id'])->where('tender_id',$input['tender_id'])
        ->where(function($query){
            $query->where('boq_applicable',true);
            $query->orWhere('is_disabled',false);
        })->where('field_type','!=',4)
        ->get();
        
        // $bidDetailId = $mainWorks->pluck('bid_format_detail_id');

        // return TenderBidFormatDetail::where('tender_id',$priceSchedule['price_bid_format_id'])->where('is_disabled',0)->whereNotIn('id', $bidDetailId)->get();
    }

    public function deleteSheduleDetails($id)
    {

        DB::beginTransaction();
        try {
            $details = ScheduleBidFormatDetails::where('schedule_id',$id)->get();

            foreach ($details as $val) {
                $shedule = ScheduleBidFormatDetails::find($val->id);
                $shedule->delete();
            }

            DB::commit();
            return ['success' => true, 'message' => 'Successfully Deleted'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteBoqItems($items)
    {

        DB::beginTransaction();
        try {

            foreach($items as $item)
            {   
               $boqItems =  TenderBoqItems::select('id')->where('main_work_id',$item->id)->get();
               foreach($boqItems as $boqItem)
               {
                $boqItem = TenderBoqItems::find($boqItem->id);
                $boqItem->delete();
               }

      

            }
            DB::commit();
            return ['success' => true, 'message' => 'Successfully Deleted'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
}
