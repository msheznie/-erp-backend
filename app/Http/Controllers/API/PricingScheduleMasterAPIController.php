<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePricingScheduleMasterAPIRequest;
use App\Http\Requests\API\UpdatePricingScheduleMasterAPIRequest;
use App\Models\PricingScheduleMaster;
use App\Models\ScheduleBidFormatDetails;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBidFormatMaster;
use App\Repositories\PricingScheduleMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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
        },'tender_bid_format_master'])->where('tender_id', $tender_id)->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
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
        $data['priceBidFormatDrop'] = TenderBidFormatMaster::where('company_id',$input['companySystemID'])->get();

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
            $exist = PricingScheduleMaster::where('id','!=',$input['id'])->where('scheduler_name', $input['scheduler_name'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Scheduler name can not be duplicated'];
            }
        }else{
            $exist = PricingScheduleMaster::where('scheduler_name', $input['scheduler_name'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Scheduler name can not be duplicated'];
            }
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
            $data['created_by'] = $employee->employeeSystemID;

            if(isset($input['id'])){
                $data['updated_by'] = $employee->employeeSystemID;
                $result = PricingScheduleMaster::where('id',$input['id'])->update($data);
                if($result){
                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
                }
            }else{
                $result = PricingScheduleMaster::create($data);
                if($result){
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
        return PricingScheduleMaster::where('id',$input['id'])->first();

    }

    public function deletePricingSchedule(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = PricingScheduleMaster::where('id',$input['id'])->delete();
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

    public function getPriceBidFormatDetails(Request $request)
    {
        $input = $request->all();

        $price_bid_format_id=$input['price_bid_format_id'];
        $schedule_id=$input['schedule_id'];
        return DB::select("SELECT
	tender_bid_format_detail.id,
	tender_id,
	label,
	is_disabled,
	tender_field_type.type,
	srm_schedule_bid_format_details.`value` 
FROM
	tender_bid_format_detail
	INNER JOIN tender_field_type ON tender_field_type.id = tender_bid_format_detail.field_type
	LEFT JOIN srm_schedule_bid_format_details ON srm_schedule_bid_format_details.bid_format_detail_id = tender_bid_format_detail.id 
	AND srm_schedule_bid_format_details.schedule_id = $schedule_id 
WHERE
	tender_id = $price_bid_format_id 
ORDER BY
	id ASC");
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
                    foreach ($input['priceBidFormat'] as $val){
                        if($val['value']>0){
                            $data['bid_format_detail_id'] = $val['id'];
                            $data['schedule_id'] = $masterData['schedule_id'];
                            $data['value'] = $val['value'];
                            $data['created_by'] = $employee->employeeSystemID;
                            $data['company_id'] = $masterData['companySystemID'];
                            $result = ScheduleBidFormatDetails::create($data);
                        }
                    }
                    $exist = ScheduleBidFormatDetails::where('schedule_id',$masterData['schedule_id'])->first();
                    if($result){
                        if(!empty($exist)){
                            $master['status']=1;
                            PricingScheduleMaster::where('id',$masterData['schedule_id'])->update($master);
                        }
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
}
