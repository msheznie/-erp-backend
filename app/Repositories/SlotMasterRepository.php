<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\SlotDetails;
use App\Models\SlotMaster;
use App\Models\SlotMasterWeekDays;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class SlotMasterRepository
 * @package App\Repositories
 * @version November 10, 2021, 12:34 pm +04
 *
 * @method SlotMaster findWithoutFail($id, $columns = ['*'])
 * @method SlotMaster find($id, $columns = ['*'])
 * @method SlotMaster first($columns = ['*'])
 */
class SlotMasterRepository extends BaseRepository
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
        $resValidate = $this->validateCalanderSlots($input);
        if (!$resValidate['status']) {
            return $resValidate;
        }
        $hoursTimesFrom = (isset($input['hoursFrom']) ? ($input['hoursFrom']) : 0);
        $minutesTimesFrom = (isset($input['minutesFrom']) ? ($input['minutesFrom']) : 0);
        $timeFrom = (($hoursTimesFrom) * 60 + $minutesTimesFrom) * 60;

        $hoursTimesTo = (isset($input['hoursTo']) ? ($input['hoursTo']) : 0);
        $minutesTimesTo = (isset($input['minutesTo']) ? ($input['minutesTo']) : 0);
        $timeTo = (($hoursTimesTo) * 60 + $minutesTimesTo) * 60;
        $weekDaysActive = $input['weekDaysActive'];
        DB::beginTransaction();

        $data['warehouse_id'] = $input['wareHouse'];
        $data['from_date'] = new Carbon($input['fromDate']);
        $data['to_date'] = new Carbon($input['toDate']);
        $data['time_from'] = $timeFrom;
        $data['time_to'] = $timeTo;

        $data['no_of_deliveries'] = $input['noofdeliveries'];
        $data['company_id'] = $input['companyID'];
        $data['created_by'] = Helper::getEmployeeSystemID();

        try {
            $insertResp = $this->model->create($data);
            if ($insertResp) {
                $this->insertCalanderScheduleDays($insertResp->id, $weekDaysActive, $input['companyID'], $data['from_date'], $data['to_date'],$data['no_of_deliveries']);
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
            'wareHouse.required' => 'Type is required.',
            'fromDate.required' => 'Customer is required.',
            'toDate.required' => 'Segment is required.'
        ];

        $validator = \Validator::make($input, [
            'wareHouse' => 'required',
            'fromDate' => 'required',
            'toDate' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return ['status' => false, 'code' => 422, 'message' => $validator->messages()];
        }
        return ['status' => true, 'message' => "success"];
    }
    public function insertCalanderScheduleDays($id, $weekDaysActive, $companyID, $fromDate, $toDate,$noOfDeliveries)
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
            $this->insertCalanderSlotDetails($id, $fromDate, $toDate, $companyID,$noOfDeliveries);
            return ['status' => true, 'message' => "Successfully Saved."];
        } else {
            return ['status' => false, 'message' => "Not Successfull"];
        }
    }
    public function insertCalanderSlotDetails($id, $fromDate, $toDate, $companyID,$noOfDeliveries)
    {
        $begin = new DateTime($fromDate);
        $end = clone $begin;
        $end->modify($toDate);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
        $slotWeekDays = SlotMasterWeekDays::with(['week_days'])->where('slot_master_id', $id)->get();
        foreach ($slotWeekDays as $val) {
            for($i=0;$i<$noOfDeliveries;$i++){ 
                foreach ($daterange as $date) {
                    if ($val['week_days']['description'] == $date->format("l")) {
                        $data['slot_master_id'] = $id;
                        $data['date'] = $date->format("Y-m-d");
                        $data['time_from'] = 3600;
                        $data['time_to'] = 3700;
                        $data['status'] = 0;
                        $data['company_id'] = $companyID;
                        $data['created_by'] = Helper::getEmployeeSystemID();
                        $insertCalanderDetails = SlotDetails::create($data);
                    }
                }
            }
        }
        if ($insertCalanderDetails) {
            return ['status' => true, 'message' => "Successfully Saved."];
        } else {
            return ['status' => false, 'message' => "Not Successfull"];
        }
    }
}
