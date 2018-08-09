<?php

namespace App\Listeners;

use App\Events\logHistory;
use App\Models\AccessTokens;
use App\Models\User;
use App\Models\UsersLogHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use Laravel\Passport\Events\AccessTokenCreated;

class RevokeOldTokens
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
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {

      DB::table('oauth_access_tokens')
            ->where('id', '<>', $event->tokenId)
            ->where('user_id', $event->userId)
            ->where('client_id', $event->clientId)
            ->update(['revoked' => true]);

       if(!empty($event->tokenId)){
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
