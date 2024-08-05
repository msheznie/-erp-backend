<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteTemporaryFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $path;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::useFiles(storage_path() . '/logs/file_delete.log');
        $path = $this->path;
        if($path){
            if ($exists = Storage::disk('local_public')->exists($path)) {
               // Storage::disk('local_public')->delete($path);
            }
        }
    }
}
