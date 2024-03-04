<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Sichikawa\LaravelSendgridDriver\SendGrid;
use App\Models\AppearanceSettings;

class EmailForQueuing extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    use SendGrid;

    public $content;
    public $subject;
    public $to;
    public $mailAttachment;
    public $mailAttachmentList;
    public $color;
    public $text;
    public $fromName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $attachment = '', $attachmentList = [],$color = '#C23C32',$text = 'GEARS', $fromName = 'GEARS')
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->mailAttachment = $attachment;
        $this->mailAttachmentList = $attachmentList;
        $this->color = $color;
        $this->text = $text;
        $this->fromName = $fromName;
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

       $mail = $this->from('noreply@gears-int.com', $this->fromName)
                    ->view('email.default_email',['color' => $this->color,'text' => $this->text])
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
            foreach ($this->mailAttachmentList as  $key => $attachment) {
                $mail->attach($attachment, array('as' => $key));
            }
        }
        if($this->mailAttachment){
           $mail->attach($this->mailAttachment);
       }

       return $mail;
    }
}
