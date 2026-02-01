<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\UserGroupAssign;
use App\Models\NavigationRoute;
use App\Models\RoleRoute;

class UpdateRoleRouteChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $dataBase;
    protected $userGroupID;
    protected $userGroupAssignmentIds;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;
    
    /**
     * Create a new job instance.
     *
     * @param string $dataBase
     * @param int $userGroupID
     * @param array $userGroupAssignmentIds Array of UserGroupAssign IDs to process
     * @return void
     */
    public function __construct($dataBase, $userGroupID, $userGroupAssignmentIds)
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
        $this->userGroupAssignmentIds = $userGroupAssignmentIds;
    }

    /**
     * Execute the job.
     * Processes a chunk of user group assignments and creates role routes
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);

        // Retrieve the user group assignments for this chunk
        $userGroupAssignedData = UserGroupAssign::whereIn('id', $this->userGroupAssignmentIds)
            ->where('userGroupID', $this->userGroupID)
            ->get();

        foreach ($userGroupAssignedData as $value) {
            // Process readonly permission (action 1)
            if ($value->readonly == 1) {
                $this->processPermission($value, 1);
            }

            // Process create permission (action 2)
            if ($value->create == 1) {
                $this->processPermission($value, 2);
            }

            // Process update permission (action 3)
            if ($value->update == 1) {
                $this->processPermission($value, 3);
            }

            // Process delete permission (action 4)
            if ($value->delete == 1) {
                $this->processPermission($value, 4);
            }
            
            // Process print permission (action 5)
            if ($value->print == 1) {
                $this->processPermission($value, 5);
            }

            // Process export permission (action 6)
            if ($value->export == 1) {
                $this->processPermission($value, 6);
            }
        }
    }

    /**
     * Process a specific permission and create role routes
     *
     * @param UserGroupAssign $userGroupAssignment
     * @param int $action Action type (1=readonly, 2=create, 3=update, 4=delete, 5=print, 6=export)
     * @return void
     */
    private function processPermission($userGroupAssignment, $action)
    {
        $navigationRoutes = NavigationRoute::where('navigationID', $userGroupAssignment->navigationMenuID)
            ->where('action', $action)
            ->get();

        // Bulk insert role routes for better performance
        $roleRoutesData = [];
        
        foreach ($navigationRoutes as $navigationRoute) {
            $roleRoutesData[] = [
                'routeName' => $navigationRoute->routeName,
                'userGroupID' => $this->userGroupID,
                'companySystemID' => $userGroupAssignment->companyID,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Bulk insert if there are routes to insert
        if (!empty($roleRoutesData)) {
            RoleRoute::insert($roleRoutesData);
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        \Log::error('UpdateRoleRouteChunkJob failed', [
            'userGroupID' => $this->userGroupID,
            'assignmentIds' => $this->userGroupAssignmentIds,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
