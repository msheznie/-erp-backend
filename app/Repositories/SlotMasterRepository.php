<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\SlotMasterWeekDays;
use App\Models\WarehouseMaster;
use App\Models\WarehouseRights;
use App\Models\WeekDays;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
            return ['status' => false, 'message' => 'Time To cannot be less than or equal to Time From'];
        }

        if( $fromDate <= $dt->toDateString()){
            return ['status' => false, 'message' => 'Invalid From Date is selected'];
        }

        if($fromDate->toDateString() === $dt->toDateString() && $fromTime <= $dt->toTimeString()){
            return ['status' => false, 'message' => 'Invalid Time From is selected'];
        }

        if (count($weekDayCount) == 0) {
            return ['status' => false, 'message' => 'Please select at least one day to proceed'];
        }

        $input = $this->convertArrayToValue($input);
        $fromDate = $fromDate->format('Y-m-d') . ' ' . $fromTime;
        $toDate = $toDate->format('Y-m-d') . ' ' . $toTime;
        $dateRangeExist = [];
        $limitYN = (isset($input['limit_deliveries'])&&$input['limit_deliveries']==true)?1:0;
        if($limitYN == 1){
            if(!isset($input['noofdeliveries'])){
                return ['status' => false, 'message' => 'No of deliveries is required'];
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

            $frmDateOnly=Carbon::parse($fromDate);
            $toDateOnly=Carbon::parse($toDate);

            $diff = $frmDateOnly->diffInDays($toDateOnly);

            for ($x = 0; $x <= $diff; $x++) {
                $frmDateOnly=Carbon::parse($fromDate);
                $wareHouse=$input['wareHouse'];
                $addedDays = $frmDateOnly->addDays($x);
                $dateFrm = $addedDays->format('Y-m-d');


                $fTim = new Carbon($fromTime);
                $fTimF = $fTim->addSeconds(1)->format('H-i-s');

                $tTim = new Carbon($toTime);
                $tTimF = $tTim->subSeconds(1)->format('H-i-s');


                $dateFrmTime = $dateFrm.' '.$fTimF;
                $dateFrmToTime = $dateFrm.' '.$tTimF;

                /*$dateRangeExist =  DB::select("SELECT * FROM slot_master WHERE warehouse_id = $wareHouse AND (
            ( ( '$dateFrmTime' BETWEEN from_date AND to_date ) OR ( '$dateFrmToTime' BETWEEN from_date AND to_date ) )
                OR
                ( ( from_date BETWEEN '$dateFrmTime' AND '$dateFrmToTime' ) OR ( to_date BETWEEN '$dateFrmTime' AND '$dateFrmToTime' ) ))");*/


                if ($slotMasterID > 0) {

                    $dateRangeExist = SlotDetails::getSlotDetails($frmDateOnly, $toDateOnly, $input['companyId'],
                        $input['wareHouse'], $input['slotMasterID']);
                }

                if($slotMasterID == 0){
                    $dateRangeExist = SlotDetails::getSlotDetails($frmDateOnly, $toDateOnly, $input['companyId'],
                        $input['wareHouse'], 0);
                }
                if (count($dateRangeExist) > 0) {
                    return ['status' => false, 'message' => 'The slot is available for selected date range'];
                }
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
            'dateTo.required' => 'To Date is required.',
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

    public function getFormDataCalendar(Request $request) {

        $companyID = $request['companyID'];
        $validator = $this->companyIdValidator($companyID);
        if(!$validator['success']){
            return $validator;
        }
        $subCompanies = $this->getCompanyHierarchy($companyID);
        $wareHouseLocation = WarehouseMaster::getAllCompanyWarehouseList($subCompanies);

        $user = Helper::getEmployeeSystemID();
        $assignedWareHouseIds = WarehouseRights::getAssignedWarehouses($user, $subCompanies);

        $filteredWarehouses = $wareHouseLocation->filter(function ($warehouse) use ($assignedWareHouseIds) {
            return in_array($warehouse->wareHouseSystemCode, $assignedWareHouseIds);
        })->values();

        $weekDays = WeekDays::all();

        $output = array(
            'wareHouseLocation' => $filteredWarehouses,
            'weekDays' => $weekDays
        );

        return $output;
    }

    public function getCalendarSlotData(Request $request)
    {
        $input = $request->all();
        $slot = new SlotMaster();

        $companyID  = $input['companyID'];
        $validator = $this->companyIdValidator($companyID);
        if(!$validator['success']){
            return $validator;
        }

        $subCompanies = $this->getCompanyHierarchy($companyID);

        $user = Helper::getEmployeeSystemID();
        $assignedWareHouseIds = WarehouseRights::getAssignedWarehouses($user, $subCompanies);

        $data = $slot->getSlotData($subCompanies, 0, $assignedWareHouseIds);
        $arr = [];
        $x = 0;
        if (!empty($data)) {
            foreach ($data as $row) {
                foreach ($row['slot_details'] as $slotDetail) {
                    $status = collect($slotDetail->appointment)->contains('confirmed_yn', true) ? 1 : 0;

                    $arr[$x]['id'] = $slotDetail->id;
                    $arr[$x]['slot_master_id'] = $row->id;
                    $arr[$x]['title'] =  Carbon::parse($slotDetail->start_date)->format('h:i A'). '-'. Carbon::parse($slotDetail->end_date)->format('h:i A'). ' '.$row->ware_house->wareHouseDescription;
                    $arr[$x]['start'] = $slotDetail->start_date;
                    $arr[$x]['end'] = $slotDetail->end_date;
                    $arr[$x]['fullDay'] = 0;
                    $arr[$x]['color'] =($status == 1?'#cf3000ba':'#ffc107');
                    $arr[$x]['status'] = $status;
                    $x++;
                }
            }
        }
        return $arr;
    }

    private function getCompanyHierarchy($companyID) {

        return \Helper::checkIsCompanyGroup($companyID)
            ? \Helper::getGroupCompany($companyID)
            : [$companyID];
    }

    private function companyIdValidator($companyID)
    {
        $validator = Validator::make(
            ['companyID' => $companyID],
            ['companyID' => 'required'],
            [
                'companyID.required' => 'Company ID is required.',
            ]
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first('name')
            ];
        }

        return ['success' => true];
    }
}
