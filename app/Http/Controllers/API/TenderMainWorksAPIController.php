<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderMainWorksAPIRequest;
use App\Http\Requests\API\UpdateTenderMainWorksAPIRequest;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderMainWorks;
use App\Repositories\TenderMainWorksRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\PricingScheduleDetail;

/**
 * Class TenderMainWorksController
 * @package App\Http\Controllers\API
 */

class TenderMainWorksAPIController extends AppBaseController
{
    /** @var  TenderMainWorksRepository */
    private $tenderMainWorksRepository;

    public function __construct(TenderMainWorksRepository $tenderMainWorksRepo)
    {
        $this->tenderMainWorksRepository = $tenderMainWorksRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMainWorks",
     *      summary="Get a listing of the TenderMainWorks.",
     *      tags={"TenderMainWorks"},
     *      description="Get all TenderMainWorks",
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
     *                  @SWG\Items(ref="#/definitions/TenderMainWorks")
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
        $this->tenderMainWorksRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMainWorksRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMainWorks = $this->tenderMainWorksRepository->all();

        return $this->sendResponse($tenderMainWorks->toArray(), 'Tender Main Works retrieved successfully');
    }

    /**
     * @param CreateTenderMainWorksAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderMainWorks",
     *      summary="Store a newly created TenderMainWorks in storage",
     *      tags={"TenderMainWorks"},
     *      description="Store TenderMainWorks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMainWorks that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMainWorks")
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
     *                  ref="#/definitions/TenderMainWorks"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMainWorksAPIRequest $request)
    {
        $input = $request->all();

        $tenderMainWorks = $this->tenderMainWorksRepository->create($input);

        return $this->sendResponse($tenderMainWorks->toArray(), 'Tender Main Works saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMainWorks/{id}",
     *      summary="Display the specified TenderMainWorks",
     *      tags={"TenderMainWorks"},
     *      description="Get TenderMainWorks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMainWorks",
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
     *                  ref="#/definitions/TenderMainWorks"
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
        /** @var TenderMainWorks $tenderMainWorks */
        $tenderMainWorks = $this->tenderMainWorksRepository->findWithoutFail($id);

        if (empty($tenderMainWorks)) {
            return $this->sendError('Tender Main Works not found');
        }

        return $this->sendResponse($tenderMainWorks->toArray(), 'Tender Main Works retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderMainWorksAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderMainWorks/{id}",
     *      summary="Update the specified TenderMainWorks in storage",
     *      tags={"TenderMainWorks"},
     *      description="Update TenderMainWorks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMainWorks",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMainWorks that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMainWorks")
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
     *                  ref="#/definitions/TenderMainWorks"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMainWorksAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMainWorks $tenderMainWorks */
        $tenderMainWorks = $this->tenderMainWorksRepository->findWithoutFail($id);

        if (empty($tenderMainWorks)) {
            return $this->sendError('Tender Main Works not found');
        }

        $tenderMainWorks = $this->tenderMainWorksRepository->update($input, $id);

        return $this->sendResponse($tenderMainWorks->toArray(), 'TenderMainWorks updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderMainWorks/{id}",
     *      summary="Remove the specified TenderMainWorks from storage",
     *      tags={"TenderMainWorks"},
     *      description="Delete TenderMainWorks",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMainWorks",
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
        /** @var TenderMainWorks $tenderMainWorks */
        $tenderMainWorks = $this->tenderMainWorksRepository->findWithoutFail($id);

        if (empty($tenderMainWorks)) {
            return $this->sendError('Tender Main Works not found');
        }

        $tenderMainWorks->delete();

        return $this->sendSuccess('Tender Main Works deleted successfully');
    }

    public function getMainWorksList(Request $request)
    {
        $input = $request->all();
        return $this->tenderMainWorksRepository->getMainWorkList($request);

    }

    public function addMainWorks(Request $request)
    {
        $input = $request->all();
      
        $input = $this->convertArrayToSelectedValue($request->all(), array('item'));
        $employee = \Helper::getEmployeeInfo();
        //$priceBidDetail = TenderBidFormatDetail::where('id',$input['item'])->first();
        $priceBidDetail = PricingScheduleDetail::where('id',$input['item'])->first();


        DB::beginTransaction();
        try {
            // $data['tender_id']=$input['tender_id'];
            // $data['schedule_id']=$input['schedule_id'];
            // $data['bid_format_detail_id']=$input['item'];
            // $data['item']=$priceBidDetail['label'];
            // $data['description']=$input['description'];
            // $data['company_id']=$input['companySystemID'];
            // $data['created_by'] = $employee->employeeSystemID;

            // $result = TenderMainWorks::create($data);

            $dataBidShed['tender_id']=$input['tender_id'];
            $dataBidShed['bid_format_id']=$priceBidDetail['bid_format_id'];
            $dataBidShed['bid_format_detail_id']=$priceBidDetail['bid_format_detail_id'];
            $dataBidShed['label']=$priceBidDetail['label'];
            $dataBidShed['field_type']=$priceBidDetail['field_type'];
            $dataBidShed['is_disabled']=$priceBidDetail['is_disabled'];
            $dataBidShed['boq_applicable']=$priceBidDetail['boq_applicable'];
            $dataBidShed['pricing_schedule_master_id']=$input['schedule_id'];
            $dataBidShed['company_id']=$input['companySystemID'];
            $dataBidShed['description']=$input['description'];
            $dataBidShed['created_by']=$employee->employeeSystemID;

            $result = PricingScheduleDetail::create($dataBidShed);


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

    public function downloadMainWorksUploadTemplate(Request $request)
    {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('main_works_item_upload_template/main_works_item_upload_template.xlsx')) {
            return Storage::disk($disk)->download('main_works_item_upload_template/main_works_item_upload_template.xlsx', 'main_works_item_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    public function mainWorksItemsUpload(request $request)
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
            $validateDescription = false;
            $totalItemCount = 0;

            $allowItemToTypePolicy = false;
            $itemNotound = false;


            foreach ($uniqueData as $key => $value) {
                if (isset($value['item'])) {
                    $validateItem = true;
                }

                if (isset($value['description'])) {
                    $validateDescription = true;
                }


                if ((isset($value['item']) && !is_null($value['item'])) || isset($value['description']) && !is_null($value['description'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateItem || !$validateDescription) {
                return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
            }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item', 'description'))->get()->toArray();


            $uploadSerialNumber = array_filter(collect($record)->toArray());



            if (count($record) > 0) {
                foreach ($record as $vl){
                    $exist = TenderMainWorks::where('item', $vl['item'])->where('tender_id', $input['tender_id'])->where('schedule_id', $input['schedule_id'])->where('company_id', $input['companySystemID'])->first();

                    if(!empty($exist)){
                        return $this->sendError('Item can not be duplicated', 500);
                    }
                }
                $employee = \Helper::getEmployeeInfo();
                foreach ($record as $vl){
                    $data['tender_id']=$input['tender_id'];
                    $data['schedule_id']=$input['schedule_id'];
                    $data['item']=$vl['item'];
                    $data['description']=$vl['description'];
                    $data['company_id']=$input['companySystemID'];
                    $data['created_by'] = $employee->employeeSystemID;
                    $result = TenderMainWorks::create($data);
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

    public function deleteMainWorks(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = PricingScheduleDetail::where('id',$input['id'])->delete();
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

    public function updateWorkOrderDescription(Request $request)
    {

        $input = $request->all();
        try{
            return $this->tenderMainWorksRepository->updateWorkOrderDescription($input);
        } catch(\Exception $ex) {
            return ['success' => false, 'message' => 'Unexpected Error: ' . $ex->getMessage()];
        }
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $pricingShedulrDetail = PricingScheduleDetail::find($input['id']);
            $data['description']=$input['description'];
            $data['updated_by'] = $employee->employeeSystemID;
            $result = $pricingShedulrDetail->update($data);
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
}
