<?php

namespace App\Listeners;

use App\Events\logHistory;
use App\Models\AccessTokens;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

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
    public function handle(logHistory $event)
    {

        $accessToken = $event->accessToken;

        $allAccessTokens = AccessTokens::where('id','!=',$accessToken->id)
                                       ->where('user_id', $accessToken->user_id)
                                       ->where('client_id', $accessToken->client_id)
                                       ->where('revoked', 0)
                                       ->get();

        Log::info('Start revoked');
        //Log::info($allAccessTokens->count());

        foreach ($allAccessTokens as $token){
            Log::info($token['id']);

            $temToken = AccessTokens::find($token['id']);
            $temToken->revoked = 1;
            $temToken->save();
        }
        Log::info('End  revoked');


       /* DB::table('oauth_access_tokens')
            ->where('id', '<>', $event->tokenId)
            ->where('user_id', $event->userId)
            ->where('client_id', $event->clientId)
            ->update(['revoked' => 1]);*/
    }
}
