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
        if(!isset($input['item_id']) || empty($input['item_id'])){
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

        $exist = TenderBoqItems::where('item_id',$input['item_id'])
            ->where('main_work_id',$input['main_work_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Item already exist'];
        }

        DB::beginTransaction();
        try {
            $data['main_work_id']=$input['main_work_id'];
            $data['item_id']=$input['item_id'];
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

        if(!isset($input['item_id']) || empty($input['item_id'])){
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

        $exist = TenderBoqItems::where('item_id',$input['item_id'])->where('id','!=',$input['id'])
            ->where('main_work_id',$input['main_work_id'])->first();

        if(!empty($exist)){
            return ['success' => false, 'message' => 'Item already exist'];
        }

        DB::beginTransaction();
        try {
            $data['item_id']=$input['item_id'];
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
        $disk = (isset($input['companySystemID'])) ?  Helper::policyWiseDisk($input['companySystemID'], 'public') : 'public';
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


                if ((isset($value['item']) && !is_null($value['item'])) || isset($value['uom']) && !is_null($value['uom']) || isset($value['qty']) && !is_null($value['qty'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateItem || !$validateUom || !$validateQty) {
                return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
            }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item', 'uom', 'qty'))->get()->toArray();

            $uploadSerialNumber = array_filter(collect($record)->toArray());

            if (count($record) > 0) {
                foreach ($record as $vl){

                    $getItem = ItemMaster::where('itemShortDescription','=',$vl['item'])->where('primaryCompanySystemID',$input['companySystemID'])->first();
                    $unit = Unit::where('UnitShortCode','=',$vl['uom'])->first();

                    if(empty($getItem)) {
                        return $this->sendError('Item '.$vl['item'].' can not be found in item master', 500);
                    }

                    if(empty($unit)) {
                        return $this->sendError('Uom '.$vl['uom'].' can not be found in unit of measure master', 500);
                    }

                    $exist = TenderBoqItems::where('item_id',$getItem['itemCodeSystem'])
                        ->where('main_work_id',$input['main_work_id'])->first();

                    if(!empty($exist)){
                        return $this->sendError('Item can not be duplicated', 500);
                    }
                }
                $employee = \Helper::getEmployeeInfo();
                foreach ($record as $vl){
                    $Itm = ItemMaster::where('itemShortDescription','=',$vl['item'])->where('primaryCompanySystemID',$input['companySystemID'])->first();
                    $units = Unit::where('UnitShortCode',$vl['uom'])->first();
                    $data['main_work_id']=$input['main_work_id'];
                    $data['item_id']=$Itm['itemCodeSystem'];
                    $data['uom']=$units['UnitID'];
                    $data['qty']=$vl['qty'];
                    $data['company_id']=$input['companySystemID'];
                    $data['created_by'] = $employee->employeeSystemID;
                    $result = TenderBoqItems::create($data);
                }
            } else {
                return $this->sendError('No Records found!', 500);
            }

            DB::commit();
            return $this->sendResponse([], 'Items uploaded Successfully!!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
