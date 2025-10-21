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
        DB::beginTransaction();
        $this->updateShiftSchedule();
        $dateEmpMap = [];
        foreach ($this->attendanceData as $row){
            $dateEmpMap[$row['date']][] = $row['empId'];
        }

        foreach ($dateEmpMap as $date => $empIdList){
            try {

                $serv = new SMAttendancePullingService(
                    $this->companyId,
                    $date,
                    true,
                    true,
                    $empIdList
                );
                $serv->execute();
                DB::commit();

            } catch (\Exception $e){
                DB::rollBack();
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
}
