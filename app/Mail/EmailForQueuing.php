<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class EmailForQueuing extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    use SendGrid;

    public $content;
    public $subject;
    public $to;
    public $mailAttachment;
    public $mailAttachmentList;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $attachment = '', $attachmentList = [])
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->mailAttachment = $attachment;
        $this->mailAttachmentList = $attachmentList;
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       $mail = $this->view('email.default_email')
                    ->subject($this->subject)
                    ->sendgrid([
                        'personalizations' => [
                            [
                                'substitutions' => [
                                    ':myname' => 's-ichikawa',
                                ],
                            ],
                        ],
                    ]);
        Log::info('mailAttachment path');
        Log::info($this->mailAttachment);
        if($this->mailAttachmentList && is_array($this->mailAttachmentList)) {
            foreach ($this->mailAttachmentList as $attachment) {
                $mail->attach($attachment);
            }
        }
        if($this->mailAttachment){
           $mail->attach($this->mailAttachment);
       }

       return $mail;
    }
}
