<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\UserGroupAssign;
use App\Models\RoleRoute;

class UpdateRoleRouteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $dataBase;
    protected $userGroupID;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataBase, $userGroupID)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                 self::onConnection('database_main');
            }else{
                 self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->dataBase = $dataBase;
        $this->userGroupID = $userGroupID;
    }

    /**
     * Execute the job.
     * This job dispatches sub-jobs to process user group assignments in chunks
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);

        // Delete existing role routes for this user group
        RoleRoute::where('userGroupID', $this->userGroupID)->delete();

        // Process user group assignments in chunks of 50 and dispatch sub-jobs
        UserGroupAssign::where('userGroupID', $this->userGroupID)
            ->chunkById(50, function ($userGroupAssignments) {
                // Dispatch sub-job for each chunk of 50 records
                UpdateRoleRouteChunkJob::dispatch(
                    $this->dataBase,
                    $this->userGroupID,
                    $userGroupAssignments->pluck('id')->toArray()
                );
            });
    }
}
