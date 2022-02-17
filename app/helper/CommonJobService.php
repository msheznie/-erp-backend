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
        Log::info("database Name in common service  start-".$db);
        if($db)
        {
            Log::info("database Name in common service -".$db);
            Config::set("database.connections.mysql.database", $db);
            // DB::reconnect('mysql');
            DB::purge('mysql');
        }

        Log::info("database Name in common service  end-".Config::get("database.connections.mysql.database"));

        return true;
    }

    public static function get_specific_log_file($service){
        switch ($service){
            case 'leave-accrual':
                return storage_path() . '/logs/leave_accrual_service.log';

            case 'attendance-clockIn':
            case 'attendance-clockOut':
                return storage_path() . '/logs/attendance_job_service.log';

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
