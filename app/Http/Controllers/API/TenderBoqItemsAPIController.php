<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderBoqItemsAPIRequest;
use App\Http\Requests\API\UpdateTenderBoqItemsAPIRequest;
use App\Models\ItemAssigned;
use App\Models\ItemMaster;
use App\Models\TenderBoqItems;
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

/**
 * Class TenderBoqItemsController
 * @package App\Http\Controllers\API
 */

class TenderBoqItemsAPIController extends AppBaseController
{
    /** @var  TenderBoqItemsRepository */
    private $tenderBoqItemsRepository;

    public function __construct(TenderBoqItemsRepository $tenderBoqItemsRepo)
    {
        $this->tenderBoqItemsRepository = $tenderBoqItemsRepo;
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

        return $this->sendResponse($tenderBoqItems->toArray(), 'Tender Boq Items retrieved successfully');
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

        return $this->sendResponse($tenderBoqItems->toArray(), 'Tender Boq Items saved successfully');
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
            return $this->sendError('Tender Boq Items not found');
        }

        return $this->sendResponse($tenderBoqItems->toArray(), 'Tender Boq Items retrieved successfully');
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
            return $this->sendError('Tender Boq Items not found');
        }

        $tenderBoqItems = $this->tenderBoqItemsRepository->update($input, $id);

        return $this->sendResponse($tenderBoqItems->toArray(), 'TenderBoqItems updated successfully');
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
            return $this->sendError('Tender Boq Items not found');
        }

        $tenderBoqItems->delete();

        return $this->sendSuccess('Tender Boq Items deleted successfully');
    }

    public function loadTenderBoqItems(Request $request)
    {
        $input = $request->all();

        $data['detail'] = TenderBoqItems::where('main_work_id',$input['main_work_id'])->get();
        $data['uomDrop'] = Unit::get();
        $itemDrop = ItemAssigned::with(['item_master'])->where('companySystemID',$input['companySystemID'])->get();

        $items =array();
        foreach($itemDrop as $key => $val){
            $items[$key]['id'] = $val['itemCodeSystem'];
            $items[$key]['label'] = $val['item_master']['itemShortDescription'];
        }
        $data['itemDrop'] = $items;

        return $data;
    }

    public function addTenderBoqItems(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $is_disabled = 0;
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
                return ['success' => false, 'message' => 'QTY cannot be less than or equal to zero'];
            }
        }

        $exist = TenderBoqItems::where('item_name',$input['item_name'])
            ->where('main_work_id',$input['main_work_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Item already exist'];
        }

        DB::beginTransaction();
        try {
            $data['main_work_id']=$input['main_work_id'];
            $data['item_name']=$input['item_name'];
            if(isset($input['description'])){
                $data['description']=$input['description'];
            }
            $data['uom']=$input['uom'];
            $data['qty']=$input['qty'];
            $data['created_by'] = $employee->employeeSystemID;

            $result = TenderBoqItems::create($data);

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
                return ['success' => false, 'message' => 'QTY cannot be less than or equal to zero'];
            }
        }

        $exist = TenderBoqItems::where('item_name',$input['item_name'])->where('id','!=',$input['id'])
            ->where('main_work_id',$input['main_work_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Item already exist'];
        }

        DB::beginTransaction();
        try {
            $data['item_name']=$input['item_name'];
            $data['description']=$input['description'];
            $data['uom']=$input['uom'];
            $data['qty']=$input['qty'];
            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderBoqItems::where('id',$input['id'])->update($data);

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
            $result = TenderBoqItems::where('id',$input['id'])->delete();
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

    public function downloadTenderBoqItemUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('tender_boq_item_upload_template/tender_boq_item_upload_template.xlsx')) {
            return Storage::disk($disk)->download('tender_boq_item_upload_template/tender_boq_item_upload_template.xlsx', 'tender_boq_item_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
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
                    return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
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
                    $exist = TenderBoqItems::where('item_name',$vl['item'])
                        ->where('main_work_id',$input['main_work_id'])->first();

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
                        $result = TenderBoqItems::create($data);
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

            return $this->sendResponse([], 'Items uploaded successfully!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
