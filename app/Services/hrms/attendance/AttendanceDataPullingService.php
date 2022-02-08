<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Collator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceDataPullingService{
    private $companyId;
    private $pullingDate;
    private $tempData;
    private $uniqueKey;
    private $isClockInPulling;
    


    public function __construct($companyId, $pullingDate, $isClockInPulling)
    {
        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->isClockInPulling = $isClockInPulling;

        $this->uniqueKey = "{$this->companyId}" . rand(2, 500) . '' . Carbon::now()->timestamp;
    }

    function execute(){
        if(!$this->is_all_data_pulled()){  return false; }
                   
        if(!$this->step1()){  return false; }
        
        if(!$this->isClockInPulling){
            if(!$this->step2()){  return false; }
        }
        
        if(!$this->step3()){  return false; }

        $data = $this->step4($attData);
        $this->step5($data);

        Log::info('Data pulled successfully'.$this->log_suffix(__LINE__));
        return true;        
    }

    function is_all_data_pulled(){
        $pending = DB::table('srp_erp_pay_empattendancetemptable')
            ->selectRaw('COUNT(autoID) AS pendingCount')
            ->where('companyID', $this->companyId)
            ->where('isUpdated', 0)
            ->whereDate('attDate', $this->pullingDate)
            ->value('pendingCount');       
            
        if($pending == 0){            
            Log::error('No records found for pulling'.$this->log_suffix(__LINE__));
            return false;
        }  
        return false;
    }

    function step1(){
        $q = "SELECT t.autoID, l.empID, t.device_id, t.empMachineID, l.floorID, t.attDate, t.attTime, t.uploadType
        FROM srp_erp_pay_empattendancetemptable AS t
        LEFT JOIN srp_erp_empattendancelocation AS l ON l.deviceID = t.device_id AND t.empMachineID = l.empMachineID               
        WHERE t.companyID = {$this->companyId} AND t.attDate = '{$this->pullingDate}' AND t.isUpdated = 0  
        AND NOT EXISTS (
            SELECT * FROM srp_erp_pay_empattendancereview AS r
            WHERE r.companyID = {$this->companyId} AND r.attendanceDate = t.attDate AND r.empID = l.empID 
        )
        ORDER BY l.empID, t.attTime ASC";

        $this->tempData = DB::select($q);

        if(empty($this->tempData)){
            Log::error('No records found for pulling step-1'.$this->log_suffix(__LINE__));
            return false;
        }

        $this->insert_to_temp_tb();

        return true;        
    }    

    function insert_to_temp_tb(){ 
        $temp = collect($this->tempData);
        unset($this->tempData);
        $temp = $temp->groupBy('empID');
        
        $data = []; $autoIdArr = [];
        foreach ($temp as $empId=> $row) {
            $thisData = [];
            $autoIdArr[] = $row[0]['autoID'];

            $thisData['emp_id'] = $empId;            
            $thisData['att_date'] = $row[0]['attDate'];
            $thisData['location_in'] = $row[0]['floorID'];
            $thisData['clock_in'] = $row[0]['attTime'];
            $thisData['device_id_in'] = $row[0]['device_id'];
            $thisData['machine_id_in'] = $row[0]['empMachineID'];
            $thisData['upload_type'] = $row[0]['uploadType'];
            $thisData['uniqueID'] = $this->uniqueKey;

            $clockOut = $location_out = $clockOutDevice = $clockOutMachine = $uploadType = null;

            $occurrences = count($row);
            if( $occurrences > 1 ){
                $lastIndex = $occurrences - 1;

                $autoIdArr[] = $row[$lastIndex]['autoID'];
                $clockOut = $row[$lastIndex]['attTime'];
                $location_out = $row[$lastIndex]['floorID'];
                $clockOutDevice = $row[$lastIndex]['device_id'];
                $clockOutMachine = $row[$lastIndex]['empMachineID'];
                $uploadType = $row[$lastIndex]['uploadType'];

                if($occurrences > 2){
                    $this->multipleOccurrence[] = $empId;
                }
            }

            $thisData['clock_out'] = $clockOut; 
            $thisData['location_out'] = $location_out;
            $thisData['device_id_out'] = $clockOutDevice;
            $thisData['machine_id_out'] = $clockOutMachine;
            $thisData['company_id'] = $this->companyId;
            $thisData['upload_type'] = $uploadType;

            $data[] = $thisData;
        }
        
        unset($temp);
        DB::table('attendance_temporary_tbl')->insert($data);
        DB::table('srp_erp_pay_empattendancetemptable')->whereIn('autoID', $autoIdArr)->update(['isUpdated'=> 1]);
    }

    function step2(){
        $q = "SELECT EIdNo, ECode, Ename2
        FROM srp_employeesdetails AS e         
        WHERE e.Erp_companyID = {$this->companyId} AND isSystemAdmin = 0 AND isDischarged = 0 AND empConfirmedYN = 1   
        AND NOT EXISTS (
            SELECT * FROM srp_erp_pay_empattendancereview AS r
            WHERE r.companyID = {$this->companyId} AND r.attendanceDate = '{$this->pullingDate}' AND r.empID = e.EIdNo 
        )  
        AND NOT EXISTS (
            SELECT * FROM attendance_temporary_tbl AS att
            WHERE att.company_id = {$this->companyId} AND att.att_date = '{$this->pullingDate}' AND att.emp_id = e.EIdNo
        )";

        $this->tempData = DB::select($q);

        if(empty($temp)){
            return true;
        } 
        
        $data = [];
        foreach ($temp as $row) {
            $thisData = [];

            $thisData['emp_id'] = $row['EIdNo'];          
            $thisData['att_date'] = $this->pullingDate; 
            $thisData['company_id'] = $this->companyId;
            $thisData['uniqueID'] = $this->uniqueKey;

            $data[] = $thisData;
        }

        unset($temp);
        DB::table('attendance_temporary_tbl')->insert($data);
    }

    function step3(){
        $q = "SELECT t.emp_id, e.ECode, e.Ename2, t.att_date, t.clock_in, t.clock_out, t.location_in, t.location_out, 
        t.upload_type, t.device_id_in, t.machine_id_in, t.machine_id_out, lm.leaveMasterID, lm.leaveHalfDay, 
        shd.onDutyTime, shd.offDutyTime, shd.weekDayNo, IF (IFNULL(shd.isHalfDay, 0), 1, 0) AS isHalfDay, 
        IF(IFNULL(calenders.holiday_flag, 0), 1, 0) AS isHoliday, shd.isWeekend, shd.gracePeriod, shd.isFlexyHour, 
        shd.flexyHrFrom, shd.flexyHrTo, e.isCheckInMust
        FROM attendance_temporary_tbl AS t
        JOIN (
            SELECT EIdNo, ECode, Ename2, isCheckin AS isCheckInMust
            FROM srp_employeesdetails WHERE Erp_companyID = {$this->companyId}
        ) AS e ON e.EIdNo = t.emp_id 
        LEFT JOIN srp_erp_pay_shiftemployees AS she ON she.empID = t.emp_id AND she.companyID = {$this->companyId}
        LEFT JOIN (
            SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sd.isHalfDay, sd.weekDayNo, sd.isWeekend, 
            sd.gracePeriod, sm.isFlexyHour, sd.flexyHrFrom, sd.flexyHrTo 
            FROM srp_erp_pay_shiftdetails AS sd 
            JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = sd.shiftID 
            WHERE sm.companyID = {$this->companyId}
        ) AS shd ON shd.shiftID = she.shiftID AND shd.weekDayNo = WEEKDAY(t.att_date) 
        LEFT JOIN ( 
            SELECT leaveMasterID, empID, startDate, endDate, ishalfDay as leaveHalfDay
            FROM srp_erp_leavemaster WHERE companyID = {$this->companyId} AND approvedYN = 1
        ) AS lm ON lm.empID = t.emp_id AND t.att_date BETWEEN lm.startDate AND lm.endDate 
        LEFT JOIN ( 
            SELECT * FROM srp_erp_calender WHERE companyID = {$this->companyId} 
            AND fulldate BETWEEN '{$this->fromDate}' AND '{$this->toDate}'
        ) AS calenders ON fulldate = t.att_date
        WHERE t.company_id = {$this->companyId} AND uniqueID = '{$this->uniqueKey}'";
        //echo '<pre>'; print_r($q); echo '</pre><br/><br/>'; 
        DB::select($q);
    }

    function log_suffix($line_no) : string
    {
        return " | companyId: $this->companyId \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
    }
}