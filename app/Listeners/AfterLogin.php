<?php

namespace App\Listeners;

use App\Models\AccessTokens;
use App\Models\User;
use App\Models\UsersLogHistory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Events\logHistory;

class AfterLogin
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(logHistory $event)
    {
        //Log::info($event->accessToken);

        //$accessToken = $event->accessToken;

        if(!empty($accessToken)){
            $logHistory = new UsersLogHistory();
            $user = User::with(['employee'])->find($event->userId);
            if($user){
                if($user->employee){
                    $logHistory->employee_id = $user->employee['employeeSystemID'];
                    $logHistory->empID = $user->employee['empID'];
                    $logHistory->loginPCId = gethostname();
                    $logHistory->save();
                }
            }
        }
    }
}
