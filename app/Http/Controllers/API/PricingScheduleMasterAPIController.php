<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleMasterAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleMasterAPIRequest;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ScheduleBidFormatDetails;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Models\TenderMainWorks;
use App\Repositories\PricingScheduleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PricingScheduleDetail;
use App\Models\TenderBoqItems;
use App\Services\SrmDocumentModifyService;

/**
 * Class PricingScheduleMasterController
 * @package App\Http\Controllers\API
 */

class PricingScheduleMasterAPIController extends AppBaseController
{
    /** @var  PricingScheduleMasterRepository */
    private $pricingScheduleMasterRepository;
    private $srmDocumentModifyService;

    public function __construct(
        PricingScheduleMasterRepository $pricingScheduleMasterRepo,
        SrmDocumentModifyService $srmDocumentModifyService
    )
    {
        $this->pricingScheduleMasterRepository = $pricingScheduleMasterRepo;
        $this->srmDocumentModifyService = $srmDocumentModifyService;
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

        return $this->sendResponse($pricingScheduleMasters->toArray(), trans('custom.pricing_schedule_masters_retrieved_successfully'));
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

        return $this->sendResponse($pricingScheduleMaster->toArray(), trans('custom.pricing_schedule_master_saved_successfully'));
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
            return $this->sendError(trans('custom.pricing_schedule_master_not_found'));
        }

        return $this->sendResponse($pricingScheduleMaster->toArray(), trans('custom.pricing_schedule_master_retrieved_successfully'));
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
            return $this->sendError(trans('custom.pricing_schedule_master_not_found'));
        }

        $pricingScheduleMaster = $this->pricingScheduleMasterRepository->update($input, $id);

        return $this->sendResponse($pricingScheduleMaster->toArray(), trans('custom.pricingschedulemaster_updated_successfully'));
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
            return $this->sendError(trans('custom.pricing_schedule_master_not_found'));
        }

        $pricingScheduleMaster->delete();

        return $this->sendSuccess('Pricing Schedule Master deleted successfully');
    }

    public function getPricingScheduleList(Request $request)
    {
        return $this->pricingScheduleMasterRepository->getPricingScheduleList($request);
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
        $tenderMasterId = $input['tenderMasterId'];
        $scheduler_name = $input['scheduler_name'];
        $companySystemID = $input['companySystemID'];
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
        $id = $input['id'] ?? 0;
        $amdID = $input['amd_id'] ?? 0;

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $exist = $this->pricingScheduleMasterRepository->checkScheduleNameValidation(
            $id, $amdID, $tenderMasterId, $scheduler_name, $companySystemID, $editOrAmend, $requestData['versionID']
        );

        if(!$exist['success']){
            return $exist;
        }

        //check if formula is empty or not
        $formulaExists = $this->pricingScheduleMasterRepository->checkTenderBidFormatFormulaExists($input['price_bid_format_id']);
        if(!$formulaExists['success']){
            return $formulaExists;
        }

        if($id > 0 || $amdID > 0) {
            $scheduleResp = $this->pricingScheduleMasterRepository->getPricingScheduleMasterRecord($id, $amdID, $editOrAmend);
            if(!$scheduleResp['success']){
                return $scheduleResp;
            }
            $schedule = $scheduleResp['data'];
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

            if($id > 0 || $amdID > 0){
                $data['updated_by'] = $employee->employeeSystemID;
                $update = $this->pricingScheduleMasterRepository->updatePricingScheduleMaster($data, $id, $editOrAmend, $amdID);
                if(!$update['success']){
                    DB::rollback();
                    return $update;
                }
                $result = $update['data'];
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
                if($editOrAmend){
                    $data['level_no'] = 1;
                    $data['id'] = null;
                    $data['tender_edit_version_id'] = $requestData['versionID'];
                }
                $result = $editOrAmend ? PricingScheduleMasterEditLog::create($data) : PricingScheduleMaster::create($data);
                if($result){

                    $detailUpdate = $this->pricingScheduleMasterRepository->updateTenderPricingScheduleDetail(
                        $input['price_bid_format_id'], $tenderMasterId, $result, $companySystemID, $employee, $editOrAmend, $requestData['versionID']
                    );
                    if(!$detailUpdate['success']){
                        DB::rollback();
                        return ['success' => false, 'message' => $detailUpdate['message']];
                    }
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
        $response = $this->pricingScheduleMasterRepository->getPricingScheduleMasterEditData($input);
        return $this->sendResponse($response, trans('custom.pricing_schedule_master_record_retrieved_successfu'));
    }

    public function deletePricingSchedule(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'tender_id' => 'required'
            ],[
                'tender_id.required' => 'Tender Master ID is required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first('tender_id'));
            }

            $data = $this->pricingScheduleMasterRepository->deletePricingSchedule($request);
            if(!$data['success']){
                return $this->sendError($data['message'], 500);
            }
            return $this->sendResponse([], $data['message']);
        } catch (\Exception $exception){
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function getPriceBidFormatDetails(Request $request)
    {
        $input = $request->all();

        $price_bid_format_id=$input['price_bid_format_id'];
        $schedule_id=$input['schedule_id'];
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        list($val1, $val2) = $this->pricingScheduleMasterRepository->getPricingScheduleDetails($schedule_id, $price_bid_format_id, $editOrAmend);
        return array_merge($val1,$val2);
    }

    public function addPriceBidDetails(Request $request)
    {
        $input = $request->all();
        try{
            return $this->pricingScheduleMasterRepository->addPricingScheduleDetails($input);
        } catch(\Exception $ex){
            return ['success' => false, 'message' => $ex];
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
