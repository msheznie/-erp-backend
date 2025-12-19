<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UsersLogHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if(!empty($event)){
            $logHistory = new UsersLogHistory();
            $user = User::with(['employee'])->find($event->user->id);
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
