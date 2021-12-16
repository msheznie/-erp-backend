<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\SlotMasterWeekDays;
use App\Models\WeekDays;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AppBaseController;

/**
 * Class SlotMasterRepository
 * @package App\Repositories
 * @version November 10, 2021, 12:34 pm +04
 *
 * @method SlotMaster findWithoutFail($id, $columns = ['*'])
 * @method SlotMaster find($id, $columns = ['*'])
 * @method SlotMaster first($columns = ['*'])
 */
class SlotMasterRepository extends AppBaseController
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'from_date',
        'no_of_deliveries',
        'time_from',
        'time_to',
        'to_date',
        'warehouse_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SlotMaster::class;
    }

    public function saveCalanderSlots(Request $request)
    {
        $input = $request->all();
        $slotMaster = new SlotMaster();
        $dt = Carbon::now();


        $slotMasterID = $input['slotMasterID'];
        $resValidate = $this->validateCalanderSlots($input);
        if (!$resValidate['status']) {
            return $resValidate;
        }
        $fromTime = date_format(new Carbon($input['dateFromTime']), 'H:i:s');
        $fromDate = new Carbon($input['dateFrom']);
        $fromDateTime = $fromDate->toDateString().' '.$fromTime ;

        $toTime = date_format(new Carbon($input['dateToTime']), 'H:i:s');
        $toDate = new Carbon($input['dateTo']);
        $toDateTime = $toDate->toDateString().' '.$toTime ;

        $weekDaysActive = $slotMaster->checkDaySelectedDate($input);
        $weekDayCount = array_filter($weekDaysActive, function ($item) {
            if (isset($item['isActive'])) {
                return ($item['isActive']) == true;
            }
        });

        if($toTime <= $fromTime){ 
            return ['status' => false, 'message' => 'Time To field is invalid'];
        }

        if( $fromDate <= $dt->toDateString()){
            return ['status' => false, 'message' => 'From Date is invalid'];
        }

        if($fromDate->toDateString() === $dt->toDateString() && $fromTime <= $dt->toTimeString()){
            return ['status' => false, 'message' => 'Time From field is invalid'];
        }

        if (count($weekDayCount) == 0) {
            return ['status' => false, 'message' => 'Please select at least one day to proceed'];
        }

        $input = $this->convertArrayToValue($input);
        $fromDate = $fromDate->format('Y-m-d') . ' ' . $fromTime;
        $toDate = $toDate->format('Y-m-d') . ' ' . $toTime; 
        $dateRangeExist = '';
        $limitYN = (isset($input['limit_deliveries'])&&$input['limit_deliveries']==true)?1:0;
        if($limitYN == 1){
                if(!isset($input['noofdeliveries'])){
                    return ['status' => false, 'message' => 'Invalid No of deliveries'];
                }
                if( isset($input['noofdeliveries']) && $input['noofdeliveries'] <=0){
                    return ['status' => false, 'message' => 'No of deliveries cannot be less than or equal to 0'];
                } 
        }

        DB::beginTransaction();
        $data['warehouse_id'] = $input['wareHouse'];
        $data['from_date'] = $fromDate;
        $data['to_date'] = $toDate;
        $data['limit_deliveries'] = $limitYN;
        $data['no_of_deliveries'] = isset($input['noofdeliveries']) ? $input['noofdeliveries'] : 0;
        $data['company_id'] = $input['companyId'];
        $data['created_by'] = Helper::getEmployeeSystemID();
        try {
            if ($slotMasterID > 0) {
                $this->deleteSlot($slotMasterID);
            } 
            if ($slotMasterID > 0) {
                $dateRangeExist = DB::table('slot_master')
                ->selectRaw('id')
                ->whereRaw("(from_date >= '$fromDateTime' AND to_date <= '$toDateTime')")
                //->orWhereRaw("(to_date >= '$fromDateTime' AND to_date <= '$toDateTime')")
                ->where('warehouse_id', '=', $input['wareHouse'])
                ->where('id', '!=', $input['slotMasterID'])
                ->first();
            }
    
            if($slotMasterID == 0){ 
                $dateRangeExist = DB::table('slot_master')
                ->selectRaw('id')
                ->whereRaw("(from_date >= '$fromDateTime' AND to_date <= '$toDateTime')")
                //->orWhereRaw("(to_date >= '$fromDateTime' AND to_date <= '$toDateTime')")
                ->where('warehouse_id', '=', $input['wareHouse'])
                ->first();
            }
            if (!empty($dateRangeExist)) {
                return ['status' => false, 'message' => 'Slot is available for selected date range'];
            } 
            $insertResp = $slotMaster->create($data);
            if ($insertResp) {
                $this->insertCalanderScheduleDays(
                    $insertResp->id,
                    $weekDaysActive,
                    $input['companyId'],
                    $data['from_date'],
                    $data['to_date'],
                    $data['no_of_deliveries'],
                    $fromTime,
                    $toTime
                );
                DB::commit();
                return ['status' => true, 'message' => "Successfully Saved."];
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => false, 'message' => $exception->getMessage()];
        }
    }
    public function validateCalanderSlots($input)
    {
        $messages = [
            'wareHouse.required' => 'Warehouse is required.',
            'dateFrom.required' => 'From Date is required.',
            'dateTo.required' => 'To is required.',
            'dateFromTime.required' => 'Time From is required.',
            'dateToTime.required' => 'Time To is required.',
        ];

        $validator = \Validator::make($input, [
            'wareHouse' => 'required',
            'dateFrom' => 'required',
            'dateTo' => 'required',
            'dateFromTime' => 'required',
            'dateToTime' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return ['status' => false, 'code' => 422, 'message' => $validator->messages()];
        }
        return ['status' => true, 'message' => "success"];
    }
    public function insertCalanderScheduleDays($id, $weekDaysActive, $companyID, $fromDate, $toDate, $noOfDeliveries, $fromTime, $toTime)
    {

        foreach ($weekDaysActive as $val) {
            if ((isset($val['isActive']) && $val['isActive'] == true)) {
                $data['slot_master_id'] = $id;
                $data['day_id'] = $val['id'];
                $data['company_id'] = $companyID;
                $data['created_by'] = Helper::getEmployeeSystemID();
                $insertCalanderDays = SlotMasterWeekDays::create($data);
            }
        }
        if ($insertCalanderDays) {
            $this->insertCalanderSlotDetails($id, $fromDate, $toDate, $companyID, $noOfDeliveries, $fromTime, $toTime);
            return ['status' => true, 'message' => "Successfully Saved."];
        } else {
            return ['status' => false, 'message' => "Not Successfull"];
        }
    }
    public function insertCalanderSlotDetails($id, $fromDate, $toDate, $companyID, $noOfDeliveries, $fromTime, $toTime)
    {
        $begin = new DateTime($fromDate);
        $end = clone $begin;
        $end->modify($toDate);
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
        $slotWeekDays = SlotMasterWeekDays::with(['week_days'])->where('slot_master_id', $id)->get();
        foreach ($slotWeekDays as $val) { 
                foreach ($daterange as $date) {
                    if ($val['week_days']['description'] == $date->format("l")) {
                        $data['slot_master_id'] = $id;
                        $data['start_date'] = $date->format("Y-m-d") . ' ' . $fromTime;
                        $data['end_date'] = $date->format("Y-m-d") . ' ' . $toTime;
                        $data['status'] = 0;
                        $data['company_id'] = $companyID;
                        $data['created_by'] = Helper::getEmployeeSystemID();
                        $insertCalanderDetails = SlotDetails::create($data);
                    }
                } 
        }
        if ($insertCalanderDetails) {
            return ['status' => true, 'message' => "Successfully Saved."];
        } else {
            return ['status' => false, 'message' => "Not Successfull"];
        }
    }
    public function deleteSlot($slotMasterID)
    {
        $slotMaster =  SlotMaster::where('id', $slotMasterID)->delete();
        $slotdetail =  SlotDetails::where('slot_master_id', $slotMasterID)->delete();
        if ($slotMaster && $slotdetail) {
            return ['status' => true, 'message' => "Successfully Deleted."];
        } else {
            return ['status' => false, 'message' => "Not Successfull"];
        }
    }
}
