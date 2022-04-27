<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForgotToPunchInService{
    private $companyId;
    private $date;
    private $time;

    private $dayId;
    private $shiftMasters;
    
    public function __construct($companyId, $date, $time)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->time = $time;
    }

    public function run(){
        $this->getDayId();
        $this->getShiftMasters();
        
        if($this->shiftMasters->count() == 0){
            return;
        }

        $this->processOverShifts();
    }

    public function getShiftMasters(){ 
        //TODO:  validate the on duty time with the job processing time
        $this->shiftMasters = DB::table('srp_erp_pay_shiftmaster AS m')
            ->selectRaw("m.shiftID, m.Description, d.dayID, d.onDutyTime, d.offDutyTime, d.dayID")
            ->join('srp_erp_pay_shiftdetails AS d', 'd.shiftID', '=', 'm.shiftID')
            ->where('d.dayID', $this->dayId)
            //->where('d.onDutyTime', 0)
            ->where('d.isWeekend', 0)
            ->where('m.shiftID', 2)
            ->where('m.companyID', $this->companyId)
            ->get();
    }

    public function processOverShifts(){
        foreach ($this->shiftMasters as $key => $shift) {
            //TODO: validate whether this job processed for this shift master


            $this->empShifts($shift->shiftID);
        }
    }
    
    public function empShifts($shiftId){
        $empArr = DB::table('srp_erp_pay_shiftemployees')
            ->select('empID') 
            ->where('shiftID', $shiftId) 
            ->whereRaw("('{$this->date}' BETWEEN startDate and endDate )")
            ->where('companyID', $this->companyId)
            ->get();

        if($empArr->count() == 0){
            return [];
        }
        
        $empArr = $empArr->pluck('empID')->toArray();
        //dd($empArr);
    }
    
    public function check2(){
        $q = "SELECT t.autoID, l.empID, t.device_id, t.empMachineID, l.floorID, t.attDate, t.attTime, t.uploadType
        FROM srp_erp_pay_empattendancetemptable AS t
        JOIN srp_erp_empattendancelocation AS l ON l.deviceID = t.device_id AND t.empMachineID = l.empMachineID               
        WHERE t.companyID = {$this->companyId} AND t.attDate = '{$this->date}'          
        ORDER BY l.empID, t.attTime ASC";

        $this->tempData = DB::select($q);
    }

    public function getDayId(){
        $dayName = Carbon::parse($this->date)->format('l');        
        $this->dayId = DB::table('srp_weekdays')
            ->select('DayID')
            ->where('DayDesc', $dayName)
            ->value('DayID');        
    }

    /* TODO:
     - check employee shift date and get shift start time
     - check employee leave ( half day leave concern)
    */
}