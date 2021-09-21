<?php

namespace App\helper;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Config;

class CommonJobService
{
    public static function db_switch( $db ){
        Config::set("database.connections.mysql.database", $db);
        DB::reconnect('mysql');

        return true;
    }

    public static function get_specific_log_file($service){
        switch ($service){
            case 'leave-accrual':
                return storage_path() . '/logs/leave_accrual_service.log';

        }
    }

    public static function company_list(){
        return Company::selectRaw('companySystemID AS id, CompanyID AS code, CompanyName AS name')
            ->whereIn('companySystemID', [1])
            ->get();
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
        }
    }
}
