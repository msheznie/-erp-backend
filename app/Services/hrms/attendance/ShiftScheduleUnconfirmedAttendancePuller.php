<?php

namespace App\Services\hrms\attendance;
use Illuminate\Support\Facades\DB;
use Exception;

class ShiftScheduleUnconfirmedAttendancePuller
{

    protected $attendanceData = [];
    protected $companyId;
    protected $shiftData;

    public function __construct($data){

        $this->attendanceData = $data['attendanceData'];
        $this->shiftData = $data['shiftData'];
        $dates = array_column($data['attendanceData'], 'date');

        $attendanceDataCount = count($this->attendanceData);

        if(empty($attendanceDataCount)){
            throw new Exception(__('custom.no_dates_found_for_shift_schedule_pulling'));
        }

        $this->companyId = $data["companyId"];

        if (empty($dates)){
            throw new Exception(__('custom.no_dates_found_for_shift_schedule_pulling'));
        }
    }

    public function pullData(){
        $this->updateShiftSchedule();
        $dateEmpMap = [];
        foreach ($this->attendanceData as $row){
            $dateEmpMap[$row['date']][] = $row['empId'];
        }

        foreach ($dateEmpMap as $date => $empIdList){
            try {

                $result = $this->deleteMultipleAttendance(
                    $date,
                    $empIdList
                );
                if (!$result['status']) {
                    throw new Exception($result['message']);
                }

                $serv = new SMAttendancePullingService(
                    $this->companyId,
                    $date,
                    false,
                    true,
                    $empIdList
                );
                $serv->execute();


            } catch (\Exception $e){
                throw new Exception(
                    __('custom.failed_to_process_attendance_for_date', ['date' => $date, 'employees' => implode(', ', $empIdList)]) . ': ' . $e->getMessage()
                );
            }
        }

        return ['status' => true, 'message' => __('custom.data_pulled_successfully')];
    }

    function updateShiftSchedule(){
        foreach ($this->shiftData as $shiftRecord){
            if (!empty($shiftRecord) && isset($shiftRecord['id'])) {
                $id = $shiftRecord['id'];
                unset($shiftRecord['id']);

                DB::table('hr_shift_schedule_details')
                    ->where('id', $id)
                    ->where('company_id', $this->companyId)
                    ->update($shiftRecord);
            }
        }
    }

    function deleteMultipleAttendance($date, $empIds){
        $this->processTempData($date, $empIds);
        $this->deleteAttendanceReviewData($date, $empIds);
        return ['status' => true, 'message' => 'Record/s deleted successfully'];
    }

    function processTempData($fromDate, $empIds){
        $tempIds =  DB::table('srp_erp_pay_empattendancetemptable as temp')
            ->join('srp_erp_pay_empattendancereview as review', function($join) {
                $join->on('review.attendanceDate', '=', 'temp.attDate')
                    ->on('review.empID', '=', 'temp.emp_id');
            })
            ->where('review.companyID', $this->companyId)
            ->where('review.attendanceDate', $fromDate)
            ->whereIn('review.empID', $empIds)
            ->where('review.confirmedYN', 0)
            ->pluck('temp.autoID')
            ->toArray();

        if(empty($tempIds)){
            throw new Exception(__('custom.no_temp_data_found'));
        }

        DB::table('srp_erp_pay_empattendancetemptable')
            ->whereIn('autoID', $tempIds)
            ->update(['isUpdated' => 0]);
    }

    function deleteAttendanceReviewData($date, $empIds){
        DB::table('srp_erp_pay_empattendancereview')
            ->where('companyID', $this->companyId)
            ->where('attendanceDate', $date)
            ->whereIn('empID', $empIds)
            ->where('confirmedYN', 0)
            ->delete();
    }
}
