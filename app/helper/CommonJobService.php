<?php

namespace App\helper;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\CompanyJobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CommonJobService
{
    public static function db_switch( $db ){
        if(!$db || $db == ''){ 
            Log::info("db name is empty");
            return;
        }

        Config::set("database.connections.mysql.database", $db);
        DB::reconnect('mysql');
        
        //DB::purge('mysql'); 
        /*            
            As discussed with Fayas DB::purge('mysql'); not working properly on ubuntu machine so we have decide 
            to use the DB::reconnect('mysql');
        */ 

        return true;
    }

    public static function get_specific_log_file($service){
        switch ($service){
            case 'leave-accrual':
                return storage_path() . '/logs/leave_accrual_service.log';

            case 'attendance-clockIn':
            case 'attendance-clockOut':
                return storage_path() . '/logs/attendance_job_service.log';
            case 'attendance-cross-day-clockOut':
                return storage_path() . '/logs/attendance_cross_day_job_service.log';
            case 'attendance-notification':
                return storage_path() . '/logs/attendance_notification_service.log';
            case 'birthday-wishes':
                return storage_path() . '/logs/birthday_wishes_service.log';
            case 'leave-carry-forward':
                return storage_path() . '/logs/leave_carry_forward_service.log';
            case 'hr-document':
                return storage_path() . '/logs/hr_document_service.log';
            case 'travel-request':
                    return storage_path() . '/logs/travel_request_service.log';
            case 'return-to-work':
                return storage_path() . '/logs/return_to_work_service.log';      
            case 'emp_create_profile':
                return storage_path() . '/logs/emp_create_profile_service.log';
            case 'item-wac-amount':
                return storage_path() . '/logs/item_wac_amount_service.log';
            case 'recurring-voucher':
                return storage_path() . '/logs/recurring_voucher_service.log';
            case 'delegation':
                return storage_path() . '/logs/delegation.log';  
            case 'absent-notification':
                return storage_path() . '/logs/absent-notification.log';
            case 'emp-designation-update-notification':
                return storage_path() . '/logs/emp-designation-update-notification.log';
        }
    }

    public static function tenant_list(){
        return Tenant::where('is_active', 1)->groupBy('database')->get();
    }

    public static function get_tenant_db($tenantId){
        return Tenant::where('id', $tenantId)->value('database');
    }

    public static function company_list(){
        return Company::selectRaw('companySystemID AS id, CompanyID AS code, CompanyName AS name')
            ->get();
    }

    public static function leave_accrual_service_types(){
        return [
            ['policy'=> 1, 'dailyBasis'=> false, 'description'=> 'Annual accrual'],
            ['policy'=> 1, 'dailyBasis'=> true, 'description'=> 'Annual daily basis accrual'],
            ['policy'=> 3, 'dailyBasis'=> false, 'description'=> 'Monthly accrual'],
        ];
    }

    public static function get_active_companies($signature){
        $companies = CompanyJobs::getActiveCompanies($signature);

        if($companies->count() == 0){
            return [];
        }

        $companies = $companies->toArray();
        return array_column($companies, 'company_id');
    }

    public static function job_check(){

        $log = DB::table('jobs')->get(); //failed_jobs | jobs
        //echo '<pre>'; print_r($log); echo '</pre>'; exit;

        foreach ($log as $row){
            $payload = json_decode($row->payload);
            $command = $payload->data->command;

            $command = str_replace('\u0000', ' ', $command);
            $command = unserialize($command, ['allowed_classes' => false]);

            $row->payload = $payload;
            $row->command = $command;

            echo '<pre>'; print_r($row); echo '</pre>';
            echo '<hr/>';
        }
    }
}
