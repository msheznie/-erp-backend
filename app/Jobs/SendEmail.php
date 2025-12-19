<?php

namespace App\Jobs;

use App\Mail\EmailForQueuing;
use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $to;
    public $subject;
    public $content;
    public $alertID;
    public $attachments;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
//    public $tries = 5;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $subject, $content, $alertID = 0, $attachmentArr = '')
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->content = $content;
        $this->alertID = $alertID;
        $this->attachments = $attachmentArr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/send_email_jobs.log');
        Mail::to($this->to)->send(new EmailForQueuing($this->subject, $this->content, '', [], '#C23C32', 'GEARS', 'GEARS', app()->getLocale()));
    }
}
