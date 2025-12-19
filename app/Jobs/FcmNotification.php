<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class FcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmService;
    protected $payLoadData;
    protected $description;
    protected $notificationTitle;
    protected $tokens;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tokens, $notificationTitle, $description, $payLoadData)
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

        $this->fcmService = new FcmService();
        $this->tokens = $tokens;
        $this->notificationTitle = $notificationTitle;
        $this->description = $description;
        $this->payLoadData = $payLoadData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $res = $this->fcmService->sendNotification($this->tokens, $this->notificationTitle, $this->description, $this->payLoadData);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
