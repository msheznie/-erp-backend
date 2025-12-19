<?php

namespace App\Jobs;

use App\helper\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ResetFinaceSubCategoryValuesInAllDocuments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $records;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db,$records)
    {
        // if(env('IS_MULTI_TENANCY',false)){
        //     self::onConnection('database_main');
        // }else{
        //     self::onConnection('database');
        // }

        $this->dispatch_db = $dispatch_db;
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // self::db_switch( $this->dispatch_db );
        $records = $this->records;

        foreach($records as $record) {
            $table = $record['table'];
            $master =  $record['master'];
            $key =  $record['key'];
            $gParent = $record['gParent'];
            $gParentKey = $record['gParentKey'];
            $columns = $record['columns'];
            if(isset($record['masterKey'])) {
                $masterKey = $record['masterKey'];
            }
            $query = "
                UPDATE ".$table."
                INNER JOIN financeitemcategorysub ON ".$table.".itemFinanceCategorySubID = financeitemcategorysub.itemCategorySubID
                
            ";

            if(!isset($record['masterKey'])) {
                $query .= " INNER JOIN  ".$master." ON ".$master.".".$key." = ".$table.".".$key."";
            }
            
            if(isset($record['masterKey']) && !isset($record["gConfirm"])){
                $query .= " INNER JOIN  ".$master." ON ".$master.".".$masterKey." = ".$table.".".$key."";
            }

            if(isset($record['masterKey']) && isset($record["gConfirm"])){
                $query .= " INNER JOIN  ".$master." ON ".$master.".".$key." = ".$table.".".$key."";
            }

            if(!$record['confirm'] && $record["gConfirm"] && !isset($record['masterKey'])) {
                $query = $query." INNER JOIN ".$gParent." ON ".$gParent.".".$gParentKey." = ".$master.".".$gParentKey."";
            }
            
            if(!$record['confirm'] && $record["gConfirm"] && isset($record['masterKey'])){
                $query = $query." INNER JOIN ".$gParent." ON ".$gParent.".".$masterKey." = ".$master.".".$gParentKey."";
            }

            // if(isset($record['masterKey']) && $record["confirm"]) {
            //     $query = $query."INNER JOIN ".$table." ON ".$table.".".$key." = ".$master.".".$masterKey."";
            // }
                    //  ".$table.".financeGLcodeRevenueSystemID = financeitemcategorysub.financeGLcodeRevenueSystemID,
                    //  ".$table.".financeGLcodeRevenue = financeitemcategorysub.financeGLcodeRevenue,
//  ".$table.".financeGLcodebBSSystemID = financeitemcategorysub.financeGLcodebBSSystemID,
                    //  ".$table.".financeGLcodePLSystemID = financeitemcategorysub.financeGLcodePLSystemID,

            $query = $query." SET ";
                    

            foreach($columns as $column) {
                  $query .= $table.".".$column." = financeitemcategorysub.".$column;
                  $query .= ($column == end($columns)) ? "" : ",";
            }
               

            if($record['confirm']) {
              $query =  $query." WHERE ".$master.".".$record['confirm']." = 0";
              \DB::statement($query);

            }
            
            if($record['gConfirm']){
              $query =  $query." WHERE ".$gParent.".".$record['gConfirm']." = 0";
             \DB::statement($query);

            }

        }
        
    }

    public static function db_switch( $db ){
        Config::set("database.connections.mysql.database", $db);
        DB::reconnect('mysql');
        return true;
    }
}
