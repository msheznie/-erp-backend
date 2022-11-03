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
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);

        RoleRoute::where('userGroupID', $this->userGroupID)->delete();

        $userGroupAssignedData = UserGroupAssign::where('userGroupID', $this->userGroupID)->get();

        foreach ($userGroupAssignedData as $key => $value) {
            if ($value->readonly == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 1)
                                                     ->get();
                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }

            if ($value->create == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 2)
                                                     ->get();

                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }

            if ($value->update == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 3)
                                                     ->get();

                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }

            if ($value->delete == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 4)
                                                     ->get();

                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }
            
            if ($value->print == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 5)
                                                     ->get();

                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }

            if ($value->export == 1) {
                $navigationroutes = NavigationRoute::where('navigationID', $value->navigationMenuID)
                                                     ->where('action', 6)
                                                     ->get();

                foreach ($navigationroutes as $k => $val) {
                    $data = [
                        'routeName' => $val->routeName,
                        'userGroupID' => $this->userGroupID,
                        'companySystemID' => $value->companyID
                    ];

                    RoleRoute::create($data);
                }
            }

        }
    }
}
