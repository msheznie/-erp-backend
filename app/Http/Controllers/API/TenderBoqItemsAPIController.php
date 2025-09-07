<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderBoqItemsAPIRequest;
use App\Http\Requests\API\UpdateTenderBoqItemsAPIRequest;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\Unit;
use App\Repositories\TenderBoqItemsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleMaster;
use App\Models\ScheduleBidFormatDetails;
use App\Services\SrmDocumentModifyService;

/**
 * Class TenderBoqItemsController
 * @package App\Http\Controllers\API
 */

class TenderBoqItemsAPIController extends AppBaseController
{
    /** @var  TenderBoqItemsRepository */
    private $tenderBoqItemsRepository;
    private $srmDocumentModifyService;

    public function __construct(
        TenderBoqItemsRepository $tenderBoqItemsRepo,
        SrmDocumentModifyService $documentModifyService
    )
    {
        $this->tenderBoqItemsRepository = $tenderBoqItemsRepo;
        $this->srmDocumentModifyService = $documentModifyService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBoqItems",
     *      summary="Get a listing of the TenderBoqItems.",
     *      tags={"TenderBoqItems"},
     *      description="Get all TenderBoqItems",
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
     *                  @SWG\Items(ref="#/definitions/TenderBoqItems")
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
        $this->tenderBoqItemsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBoqItemsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBoqItems = $this->tenderBoqItemsRepository->all();

        return $this->sendResponse($tenderBoqItems->toArray(), trans('custom.tender_boq_items_retrieved_successfully'));
    }

    /**
     * @param CreateTenderBoqItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBoqItems",
     *      summary="Store a newly created TenderBoqItems in storage",
     *      tags={"TenderBoqItems"},
     *      description="Store TenderBoqItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBoqItems that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBoqItems")
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
     *                  ref="#/definitions/TenderBoqItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBoqItemsAPIRequest $request)
    {
        $input = $request->all();

        $tenderBoqItems = $this->tenderBoqItemsRepository->create($input);

        return $this->sendResponse($tenderBoqItems->toArray(), trans('custom.tender_boq_items_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBoqItems/{id}",
     *      summary="Display the specified TenderBoqItems",
     *      tags={"TenderBoqItems"},
     *      description="Get TenderBoqItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBoqItems",
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
     *                  ref="#/definitions/TenderBoqItems"
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
        /** @var TenderBoqItems $tenderBoqItems */
        $tenderBoqItems = $this->tenderBoqItemsRepository->findWithoutFail($id);

        if (empty($tenderBoqItems)) {
            return $this->sendError(trans('custom.tender_boq_items_not_found'));
        }

        return $this->sendResponse($tenderBoqItems->toArray(), trans('custom.tender_boq_items_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderBoqItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBoqItems/{id}",
     *      summary="Update the specified TenderBoqItems in storage",
     *      tags={"TenderBoqItems"},
     *      description="Update TenderBoqItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBoqItems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBoqItems that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBoqItems")
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
     *                  ref="#/definitions/TenderBoqItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBoqItemsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBoqItems $tenderBoqItems */
        $tenderBoqItems = $this->tenderBoqItemsRepository->findWithoutFail($id);

        if (empty($tenderBoqItems)) {
            return $this->sendError(trans('custom.tender_boq_items_not_found'));
        }

        $tenderBoqItems = $this->tenderBoqItemsRepository->update($input, $id);

        return $this->sendResponse($tenderBoqItems->toArray(), trans('custom.tenderboqitems_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBoqItems/{id}",
     *      summary="Remove the specified TenderBoqItems from storage",
     *      tags={"TenderBoqItems"},
     *      description="Delete TenderBoqItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBoqItems",
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
        /** @var TenderBoqItems $tenderBoqItems */
        $tenderBoqItems = $this->tenderBoqItemsRepository->findWithoutFail($id);

        if (empty($tenderBoqItems)) {
            return $this->sendError(trans('custom.tender_boq_items_not_found'));
        }

        $tenderBoqItems->delete();

        return $this->sendSuccess('Tender Boq Items deleted successfully');
    }

    public function loadTenderBoqItems(Request $request)
    {
        return $this->tenderBoqItemsRepository->getTenderBoqItems($request);
    }

    public function addTenderBoqItems(Request $request)
    {
        $input = $request->all();

        $validator = $this->tenderBoqItemsRepository->checkValidBoqItemRequestParams($input);
        if(!$validator['success']){
            return $this->sendError($validator['message']);
        }

        $employee = Helper::getEmployeeInfo();
        $is_disabled = 0;
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tender_id']);
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $versionID = $requestData['versionID'] ?? 0;
        $main_work_id = $input['main_work_id'];

        if($input['qty'] <=0 ){
            return $this->sendError(trans('custom.qty_cannot_be_less_than_or_equal_to_zero'));
        }

        $exist = $this->tenderBoqItemsRepository->checkBoqItemsExists($input, $editOrAmend, $input['item_name'], $main_work_id);
        if(!$exist['success']){
            return $exist;
        }
        $d['purchase_request_id'] = $exist['data'];

        DB::beginTransaction();
        try {
            $data['main_work_id']=$input['main_work_id'];
            $data['item_name']=$input['item_name'];
            if(isset($input['description'])){
                $data['description']=$input['description'];
            }
            $data['uom']=$input['uom'];
            $data['qty']=$input['qty'];
            $data['tender_id']=$input['tender_id'];
            $data['created_by'] = $employee->employeeSystemID;
            if($d['purchase_request_id'] != ''){
                $data['purchase_request_id'] = $d['purchase_request_id'];
            } else {
                $data['purchase_request_id'] = isset($input['purchaseRequestID']) ? $input['purchaseRequestID'] : '';
            }

            $data['item_primary_code'] = isset($input['itemPrimaryCode']) ? $input['itemPrimaryCode'] : '';
            $data['origin'] = isset($input['origin']) ? $input['origin'] : '';

            if($editOrAmend){
                $data['id'] = null;
                $data['level_no'] = 1;
                $data['main_work_id'] = $main_work_id;
                $data['amd_main_work_id'] = $main_work_id;
                $data['tender_edit_version_id'] = $versionID;
                $result = TenderBoqItemsEditLog::create($data);
            } else {
                $result = TenderBoqItems::create($data);
            }
            if($result){

                $mainwork = $editOrAmend ?
                    PricingScheduleDetailEditLog::find($main_work_id) :
                    PricingScheduleDetail::find($main_work_id);

                $mainwork_items = $editOrAmend ?
                    PricingScheduleDetailEditLog::getPricingScheduleMainWork($mainwork->tender_id, $mainwork->amd_pricing_schedule_master_id, $versionID) :
                    PricingScheduleDetail::getPricingScheduleMainWork($mainwork->tender_id, $mainwork->pricing_schedule_master_id);

                $is_main_works_complete = true;

                if($mainwork_items->count() > 0)
                {
                    $details = $mainwork_items->get();
                    foreach($details as $main)
                    {
                        if(count($main->tender_boq_items) == 0)
                        {
                            $is_main_works_complete = false;
                            break;
                        }
                    }
                }
                if($is_main_works_complete)
                {
                    $master['boq_status']= 1;
                    $editOrAmend ?
                        PricingScheduleMasterEditLog::where('amd_id', $mainwork->amd_pricing_schedule_master_id)->update($master) :
                        PricingScheduleMaster::where('id',$mainwork->pricing_schedule_master_id)->update($master);
                }

                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved'];
            }
            return ['success' => false, 'message' => 'Unable to create'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function updateTenderBoqItem(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array('item_id','uom'));
        $employee = \Helper::getEmployeeInfo();

        if(!isset($input['item_name']) || empty($input['item_name'])){
            return ['success' => false, 'message' => 'Item is required'];
        }

        if(!isset($input['uom']) || empty($input['uom'])){
            return ['success' => false, 'message' => 'UOM is required'];
        }

        if(!isset($input['qty']) || empty($input['qty'])){
            return ['success' => false, 'message' => 'QTY is required'];
        }else{
            if($input['qty'] <=0 ){
                return ['success' => false, 'message' => trans('custom.qty_cannot_be_less_than_or_equal_to_zero')];
            }
        }
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;
        $id = $editOrAmend ? $input['amd_id'] : $input['id'];
        $mainWorkID = $editOrAmend ? $input['amd_main_work_id'] : $input['main_work_id'];

        $exist = $editOrAmend ?
            TenderBoqItemsEditLog::checkItemNameExists($input['item_name'], $mainWorkID, $id) :
            TenderBoqItems::checkItemNameExists($input['item_name'], $mainWorkID);

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Item already exist'];
        }

        DB::beginTransaction();
        try {

            $model = $editOrAmend ? TenderBoqItemsEditLog::find($id) : TenderBoqItems::find($id);
            $data['item_name']=$input['item_name'];
            $data['description']=$input['description'];
            $data['uom']=$input['uom'];
            $data['qty']=$input['qty'];
            $data['updated_by'] = $employee->employeeSystemID;
            $result = $model->update($data);
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

    public function deleteTenderBoqItem(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;
            $id = $editOrAmend ? $input['amd_id'] : $input['id'];
            $mainWorkID = $editOrAmend ? $input['amd_main_work_id'] : $input['main_work_id'];

            $tenderBoqItems = $editOrAmend ? TenderBoqItemsEditLog::find($id) : TenderBoqItems::find($input['id']);
            if($editOrAmend){
                $tenderBoqItems->is_deleted = 1;
                $result = $tenderBoqItems->save();
            } else {
                $result = $tenderBoqItems->delete();
            }

            if($result){
                $mainwork = $this->getMainwork($mainWorkID, $editOrAmend);
                $mainworkItems = $this->getMainworkItems($mainwork, $editOrAmend, $versionID);
                $isMainWorksComplete = true;
                if($mainworkItems->count() > 0)
                {
                    $details = $mainworkItems->get();
                    foreach($details as $main)
                    {
                        if(count($main->tender_boq_items) == 0)
                        {   
                            $isMainWorksComplete = false;
                            break;
                        }
                       
                    }
                   
                }

                $master['boq_status'] = ($isMainWorksComplete) ? 1 : 0;
                $editOrAmend ? PricingScheduleMasterEditLog::where('amd_id', $mainwork->amd_pricing_schedule_master_id)->update($master) :
                PricingScheduleMaster::where('id',$mainwork->pricing_schedule_master_id)->update($master);
            
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function downloadTenderBoqItemUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('tender_boq_item_upload_template/tender_boq_item_upload_template.xlsx')) {
            return Storage::disk($disk)->download('tender_boq_item_upload_template/tender_boq_item_upload_template.xlsx', 'tender_boq_item_upload_template.xlsx');
        } else {
            return $this->sendError(trans('custom.attachments_not_found'), 500);
        }
    }

    public function tenderBoqItemsUpload(request $request)
    {

        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['itemExcelUpload'];
            $input = array_except($request->all(), 'itemExcelUpload');
            $input = $this->convertArrayToValue($input);

            $validation = $this->tenderBoqItemsRepository->checkValidUploadRequestParams($input);
            if(!$validation['success']) {
                return $this->sendError($validation['message']);
            }

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];

            $allowedExtensions = ['xlsx','xls'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->get()->toArray();

            $uniqueData = array_filter(collect($formatChk)->toArray());

            $validateItem = false;
            $validateUom = false;
            $validateQty = false;
            $totalItemCount = 0;
            $existData = [];

            $allowItemToTypePolicy = false;
            $itemNotound = false;

            foreach ($uniqueData as $key => $value) {
                if (isset($value['item'])) {
                    $validateItem = true;
                }

                if (isset($value['uom'])) {
                    $validateUom = true;
                }

                if (isset($value['qty'])) {
                    $validateQty = true;
                }


                if (!$validateItem || !$validateQty) {
                    return $this->sendError(trans('custom.items_cannot_be_uploaded_as_there_are_null_values_'), 500);
                }
            }



            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item', 'description', 'uom', 'qty'))->get()->toArray();

            $uploadSerialNumber = array_filter(collect($record)->toArray());

            if (count($record) > 0) {
                $dataToUpload = array_unique(array_column($record, 'item'));

                $excelUploadDataN = (array_intersect_key($record, $dataToUpload));

                $diff = array_diff(array_map('json_encode', $record), array_map('json_encode', $excelUploadDataN));

                $duplicates = array_map('json_decode', $diff);

                $duplicateEntries = [];
                $success = 0;
                $skipRecords = [];
                $employee = \Helper::getEmployeeInfo();
                foreach ($excelUploadDataN as $vl){
                    $exist = $this->tenderBoqItemsRepository->checkItemExistsForUpload($input, $vl['item']);

                    if(empty($exist)){
                        $units = Unit::where('UnitShortCode',$vl['uom'])->first();
                        $data['main_work_id']=$input['main_work_id'];
                        $data['item_name']=$vl['item'];
                        if(isset($vl['description'])){
                            $data['description']=$vl['description'];
                        }else{
                            $data['description']= '';
                        }
                        if(!empty($units)){
                            $data['uom']=$units['UnitID'];
                        }else{
                            $data['uom']= '';
                        }
                        $data['qty']=$vl['qty'];
                        $data['company_id']=$input['companySystemID'];
                        $data['created_by'] = $employee->employeeSystemID;
                        $this->tenderBoqItemsRepository->saveBoqItemsUpload($data, $input);
                        $success +=1;
                    }else{
                        array_push($duplicateEntries,$vl);
                    }
                }
            } else {
                return $this->sendError('No Records found!', 500);
            }

            if (!empty($duplicateEntries)) {
                foreach ($duplicateEntries as $key => $dupl) {
                    $dataItm['err'] = 'Item ' . $dupl['item'] . ' already exist and has been skipped';
                    $dataItm['type'] = 'error';
                    array_push($skipRecords, $dataItm);
                }
            }

            if (!empty($duplicates)) {
                foreach ($duplicates as $duple) {
                    $dataItm['err'] = 'Item ' . $duple->item . ' duplicated and has been skipped';
                    $dataItm['type'] = 'error';
                    array_push($skipRecords, $dataItm);
                }
            }
            DB::commit();
            if (!empty($skipRecords)) {
                $dataItm['err'] = $success.' Items successfully uploaded out of '.count($record);
                $dataItm['type'] = 'success';
                array_push($skipRecords, $dataItm);
                return $this->sendError($skipRecords, 200);
            }

            return $this->sendResponse([], trans('custom.items_uploaded_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getMainwork($id, $editOrAmend)
    {
        $output = $editOrAmend ?
            PricingScheduleDetailEditLog::getPricingScheduleByID($id) :
            PricingScheduleDetail::getPricingScheduleByID($id);
        return $output;
    }

    public function getMainworkItems($mainwork, $editOrAmend, $versionID)
    {
        $output = $editOrAmend ?
            PricingScheduleDetailEditLog::getPricingScheduleMainWork($mainwork->tender_id, $mainwork->amd_pricing_schedule_master_id, $versionID) :
            PricingScheduleDetail::getPricingScheduleMainWork($mainwork->tender_id, $mainwork->pricing_schedule_master_id);

         return $output;                               
    }
}
