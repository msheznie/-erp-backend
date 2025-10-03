<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ScheduleBidFormatDetails;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\TenderBidFormatDetail;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\TenderMaster;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use App\Services\SrmDocumentModifyService;

/**
 * Class PricingScheduleMasterRepository
 * @package App\Repositories
 * @version March 20, 2022, 12:57 pm +04
 *
 * @method PricingScheduleMaster findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleMaster find($id, $columns = ['*'])
 * @method PricingScheduleMaster first($columns = ['*'])
 */
class PricingScheduleMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'scheduler_name',
        'price_bid_format_id',
        'schedule_mandatory',
        'items_mandatory',
        'status',
        'created_by',
        'updated_by',
        'company_id'
    ];

    private $srmDocumentModifyService;
    public function __construct(Application $app, SrmDocumentModifyService $srmDocumentModifyService)
    {
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleMaster::class;
    }
    public function checkScheduleNameValidation($id, $amdID, $tenderMasterId, $scheduler_name, $companySystemID, $editOrAmend, $versionID){
        if($id > 0){
            $existData = $editOrAmend ?
                PricingScheduleMasterEditLog::checkScheduleNameExists($id, $amdID, $tenderMasterId, $scheduler_name, $companySystemID, $versionID) :
                PricingScheduleMaster::checkScheduleNameExists($id, $tenderMasterId, $scheduler_name, $companySystemID);
        } else{
            $existData = $editOrAmend ?
                PricingScheduleMasterEditLog::checkScheduleNameExists($id, $amdID, $tenderMasterId, $scheduler_name, $companySystemID, $versionID) :
                PricingScheduleMaster::checkScheduleNameExists($id, $tenderMasterId, $scheduler_name, $companySystemID);
        }
        if(!empty($existData)){
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.scheduler_name_cannot_be_duplicated')
            ];
        }
        return ['success' => true, 'message' => trans('srm_tender_rfx.success')];
    }
    public function checkTenderBidFormatFormulaExists($price_bid_format_id){
        if(TenderBidFormatDetail::checkTenderBidFormatFormulaExists($price_bid_format_id, 4, 0) > 0)
        {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.pricing_bid_format_formula_required')
            ];
        }
        else if(TenderBidFormatDetail::checkTenderBidFormatFormulaExists($price_bid_format_id, 4, 1) > 0)
        {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.final_total_formula_required')
            ];
        }
        return ['success' => true, 'message' => trans('srm_tender_rfx.success')];
    }
    public function getPricingScheduleMasterRecord($id, $amd_id, $editOrAmend){
        $scheduleMaster = $editOrAmend ?
            PricingScheduleMasterEditLog::find($amd_id) :
            PricingScheduleMaster::find($id);
        if(empty($scheduleMaster)) {
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.pricing_schedule_master_not_found'),
                'data' => null
            ];
        }
        return [
            'success' => true,
            'message' => trans('srm_tender_rfx.pricing_schedule_master_retrieved'),
            'data' => $scheduleMaster
        ];
    }

    public function updateTenderPricingScheduleDetail($price_bid_format_id, $tenderMasterId, $pricing_schedule_master, $companySystemID, $employee, $editOrAmend, $versionID){
        try{
            return DB::transaction(function($q) use ($price_bid_format_id, $tenderMasterId, $pricing_schedule_master, $companySystemID, $employee, $editOrAmend, $versionID){
                $priceBidShe = TenderBidFormatDetail::getPricingBidFormatDetails($price_bid_format_id);
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
                    $dataBidShed['tender_id'] = $tenderMasterId;
                    $dataBidShed['bid_format_id'] = $bid['tender_id'];
                    $dataBidShed['bid_format_detail_id'] = $bid['id'];
                    $dataBidShed['label'] = $bid['label'];
                    $dataBidShed['field_type'] = $bid['field_type'];
                    $dataBidShed['is_disabled'] = $bid['is_disabled'];
                    $dataBidShed['boq_applicable'] = $bid['boq_applicable'];
                    $dataBidShed['company_id'] = $companySystemID;
                    $dataBidShed['formula_string'] = $bid['formula_string'];
                    $dataBidShed['created_by'] = $employee->employeeSystemID;
                    if($editOrAmend){
                        $dataBidShed['id'] = null;
                        $dataBidShed['level_no'] = 1;
                        $dataBidShed['tender_edit_version_id'] = $versionID;
                        $dataBidShed['pricing_schedule_master_id'] = $pricing_schedule_master['amd_id'];
                        $dataBidShed['amd_pricing_schedule_master_id'] = $pricing_schedule_master['amd_id'];
                        PricingScheduleDetailEditLog::create($dataBidShed);
                    } else {
                        $dataBidShed['pricing_schedule_master_id'] = $pricing_schedule_master['id'];
                        PricingScheduleDetail::create($dataBidShed);
                    }
                }
                $editOrAmend ?
                    PricingScheduleMasterEditLog::where('amd_id', $pricing_schedule_master['amd_id'])->update($status_updated) :
                    PricingScheduleMaster::where('id',$pricing_schedule_master['id'])->update($status_updated);
                return ['success' => true, 'message' => trans('srm_tender_rfx.created_successfully')];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
    public function getPricingScheduleList(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $tender_id = $input['tender_id'];

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tender_id);
        $tenderMaster = $requestData['enableRequestChange'] ?
            PricingScheduleMasterEditLog::getPricingScheduleMasterListQry($tender_id, $companyId, $requestData['versionID']) :
            PricingScheduleMaster::getPricingScheduleMasterListQry($tender_id, $companyId);

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
    public function getPricingScheduleMasterEditData($input)
    {
        $versionID = $input['versionID'] ?? 0;
        $enableChangeRequest = $versionID > 0;
        $id = $input['id'];
        if($enableChangeRequest){
            return PricingScheduleMasterEditLog::getScheduleMasterData($id);
        } else {
            return PricingScheduleMaster::getScheduleMasterData($id);
        }
    }
    public function updatePricingScheduleMaster($updateData, $id, $editOrEnable, $amd_id){
        try{
            return DB::transaction(function () use ($updateData, $id, $editOrEnable, $amd_id) {
                if($editOrEnable){
                    $data = PricingScheduleMasterEditLog::where('amd_id', $amd_id)->update($updateData);
                } else {
                    $data = PricingScheduleMaster::where('id', $id)->update($updateData);
                }
                return ['success' => true , 'message' => trans('srm_tender_rfx.updated_successfully'), 'data' => $data];
            });
        } catch (\Exception $exception){
            return ['success' => false , 'message' => $exception->getMessage()];
        }
    }
    public function getPricingScheduleDetails($schedule_id, $price_bid_format_id, $editOrAmend){
        return PricingScheduleDetail::getPricingScheduleDetails($schedule_id, $price_bid_format_id, $editOrAmend);
    }
    public function addPricingScheduleDetails($input){
        try{
            return DB::transaction(function () use ($input) {
                $masterData = $input['masterData'];
                $priceBidFormat = $input['priceBidFormat'] ?? [];
                $employee = Helper::getEmployeeInfo();
                $versionID = $masterData['versionID'] ?? 0;
                $editOrAmend = $versionID > 0;
                $scheduleID = $masterData['schedule_id'] ?? 0;
                $price_bid_format_id = $masterData['price_bid_format_id'] ?? 0;
                $companySystemID = $masterData['companySystemID'] ?? 0;

                if(!$editOrAmend){
                    $deleteExistsScheduleBidFormat = self::deleteExistingScheduleBidFormat($scheduleID, $editOrAmend, $versionID);
                    if(!$deleteExistsScheduleBidFormat['success']){
                        return $deleteExistsScheduleBidFormat;
                    }
                }

                if(!empty($priceBidFormat)){
                    $result = false;
                    $isComplete = true;
                    $isBoqComplete = true;
                    foreach($priceBidFormat as $val){
                        if($val['boq_applicable'] == 1 && $val['typeId'] != 4)
                        {
                            $id = $val['id'];
                            $result1 = $editOrAmend ?
                                TenderBoqItemsEditLog::checkExistsBoqItem($id, $versionID) :
                                TenderBoqItems::checkExistsBoqItem($id);

                            $isBoqComplete = (bool)$result1;
                        }
                        if($val['is_disabled'] == 1 && $val['typeId'] != 4)
                        {
                            if(empty($val['value']))
                            {
                                $isComplete = false;
                            }
                        }
                        if($val['typeId'] != 4)
                        {
                            if(!empty($val['value']) || $val['value'] == "0")

                            {
                                $data = [
                                    'bid_format_detail_id' => $val['id'],
                                    'schedule_id' => $scheduleID,
                                    'value' => $val['value'],
                                    'created_by' => $employee->employeeSystemID,
                                    'company_id' => $masterData['companySystemID']
                                ];

                                if($editOrAmend){
                                    $checkScheduleExists = ScheduleBidFormatDetailsLog::checkScheduleBidFormatDetailExists($scheduleID, $val['id'], $versionID);
                                    if($checkScheduleExists){
                                        $result = ScheduleBidFormatDetailsLog::where('amd_pricing_schedule_master_id', $scheduleID)
                                            ->where('amd_bid_format_detail_id', $val['id'])
                                            ->where('tender_edit_version_id', $versionID)
                                            ->where('is_deleted', 0)
                                            ->update(['value' => $val['value']]);
                                    } else {
                                        $data_log = [
                                            'id' => null,
                                            'level_no' => 1,
                                            'amd_bid_format_detail_id' => $val['id'],
                                            'amd_pricing_schedule_master_id' => $scheduleID,
                                            'tender_edit_version_id' => $versionID,
                                        ];

                                        $result = ScheduleBidFormatDetailsLog::create(array_merge($data, $data_log));
                                    }

                                } else {
                                    $result = ScheduleBidFormatDetails::create($data);
                                }

                            }

                        }

                    }
                    $exist = $editOrAmend ?
                        ScheduleBidFormatDetailsLog::checkScheduleBidFormatExists($scheduleID) :
                        ScheduleBidFormatDetails::checkScheduleBidFormatExists($scheduleID);
                    if($result){
                        $master['status'] = ($isComplete) ? 1 : 0;
                        $master['boq_status'] = ($isBoqComplete) ? 1 : 0;

                        $editOrAmend ?
                            PricingScheduleMasterEditLog::where('amd_id', $scheduleID)->update($master) :
                            PricingScheduleMaster::where('id',$masterData['schedule_id'])->update($master);
                        return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_saved'), 'data' => $result];
                    } else {
                        if(empty($exist)){
                            $master['status']=0;
                            $editOrAmend ?
                                PricingScheduleMasterEditLog::where('amd_id', $scheduleID)->update($master) :
                                PricingScheduleMaster::where('id',$masterData['schedule_id'])->update($master);
                        }
                        return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_saved'), 'data' => $result];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => trans('srm_tender_rfx.price_bid_format_not_exist')
                    ];
                }
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    private function deleteExistingScheduleBidFormat($scheduleID, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($scheduleID, $editOrAmend, $versionID) {
                if($editOrAmend){
                    ScheduleBidFormatDetailsLog::where('amd_pricing_schedule_master_id', $scheduleID)
                        ->where('tender_edit_version_id', $versionID)->where('is_deleted', 0)
                        ->update(['is_deleted' => 1]);
                } else {
                    ScheduleBidFormatDetails::where('schedule_id',$scheduleID)->delete();
                }

                return [
                    'success' => true,
                    'message' => trans('srm_tender_rfx.schedule_bid_format_deleted_successfully')
                ];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function deletePricingSchedule(Request $request){
        $input = $request->all();
        try{
            return DB::transaction(function () use ($input) {
                $versionID = $input['tender_edit_version_id'] ?? 0;
                $tender_id = $input['tender_id'];
                $enableRequestChange = $versionID > 0;
                $id = $enableRequestChange ? (int) $input['amd_id'] ?? 0 : (int)$input['id'];

                $tenderMaster = TenderMaster::find($tender_id);
                if(empty($tenderMaster)){
                    return [
                        'success' => false,
                        'message' => trans('srm_tender_rfx.tender_master_not_found')
                    ];
                }

                $scheduleMaster = $enableRequestChange ?
                    PricingScheduleDetailEditLog::find($id) :
                    PricingScheduleMaster::find($id);
                if($scheduleMaster) {

                    $scheduleDetail = self::deleteScheduleBidFormat($id, $enableRequestChange, $versionID);
                    if(!$scheduleDetail['success']){
                        return $scheduleDetail;
                    }

                    $boqItems = $enableRequestChange ?
                        PricingScheduleDetailEditLog::getPricingScheduleMainWork($tender_id, $id, $versionID, 'get') :
                        PricingScheduleDetail::getPricingScheduleMainWork($tender_id, $id);

                    $enableRequestChange ?
                        PricingScheduleDetailEditLog::where('amd_pricing_schedule_master_id', $id)->update(['is_deleted' => 1]) :
                        PricingScheduleDetail::where('pricing_schedule_master_id', $id)->delete();

                    $boqDetails = self::deleteBoqItems($boqItems, $versionID, $enableRequestChange);
                    if (!$boqDetails['success']) {
                        return ['success' => false, 'message' => $boqDetails['message']];
                    }
                    $enableRequestChange ?
                        PricingScheduleMasterEditLog::where('amd_id', $id)->update(['is_deleted' => 1]) :
                        PricingScheduleMaster::where('id', $id)->delete();

                    return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
                } else {
                    return ['success' => false, 'message' => trans('srm_tender_rfx.pricing_schedule_master_not_found')];
                }

            });
        } catch(\Exception $exception){
            return [
                'success' => false,
                'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $exception->getMessage()])
            ];
        }
    }
    public function deleteScheduleBidFormat($scheduleID, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($scheduleID, $editOrAmend, $versionID) {
                $details = $editOrAmend ?
                    ScheduleBidFormatDetailsLog::getScheduleBidFormat($scheduleID, $versionID) :
                    ScheduleBidFormatDetails::getScheduleBidFormat($scheduleID);
                foreach($details as $val){
                    $schedule = $editOrAmend ?
                        ScheduleBidFormatDetailsLog::find($val->amd_id) :
                        ScheduleBidFormatDetails::find($val->id);
                    if($editOrAmend){
                        $schedule->is_deleted = 1;
                        $schedule->update();
                    } else {
                        $schedule->delete();
                    }
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => trans('srm_masters.unexpected_error') . $exception->getMessage()];
        }
    }
    public function deleteBoqItems($boqItems, $versionID, $enableRequestChange){
        try {
            return DB::transaction(function () use ($boqItems, $versionID, $enableRequestChange) {
                foreach($boqItems as $item)
                {
                    $boqItems =  $enableRequestChange ?
                        TenderBoqItemsEditLog::getTenderBoqItemList($item->amd_id, $versionID) :
                        TenderBoqItems::getTenderBoqItemList($item->id);
                    foreach($boqItems as $boqItem)
                    {
                        $boqItem = TenderBoqItems::find($boqItem->id);
                        $boqItem->delete();
                    }
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
            });
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
