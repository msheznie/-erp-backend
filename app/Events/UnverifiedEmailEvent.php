<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
class UnverifiedEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $tenant;
    public $token;
    public $companyID;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        $url = request()->getHttpHost();
        $url_array = explode('.', $url);
        $subDomain = $url_array[0];
        $tenant = Cache::get('tenant_'.$subDomain)['uuid'];

        $token = request()->header('Authorization');
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $this->data = $data;
        $this->tenant = $tenant;
        $this->token = $token;
        $this->companyID = Auth::user()->employee->empCompanySystemID;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
