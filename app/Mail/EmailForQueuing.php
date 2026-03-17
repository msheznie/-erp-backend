<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
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
    public $locale;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $attachment = '', $attachmentList = [],$color = '#C23C32',$text = 'GEARS', $fromName = 'GEARS', $locale = null)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->mailAttachment = $attachment;
        $this->mailAttachmentList = $attachmentList;
        $this->color = $color;
        $this->text = $text;
        $this->fromName = $fromName;
        $this->locale = $locale ?? app()->getLocale();
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

       $mail = $this->from(env('MAIL_FROM_ADDRESS'), $this->fromName)
                    ->view('email.default_email',['color' => $this->color,'text' => $this->text,'locale' => $this->locale])
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
        if ($this->mailAttachmentList && is_array($this->mailAttachmentList)) {
            foreach ($this->mailAttachmentList as $key => $attachment) {
                $mail->attach($attachment, ['as' => $key]);
            }
        }

        if ($this->mailAttachment) {
            if (file_exists($this->mailAttachment)) {
                $mail->attach($this->mailAttachment);
            } else {
                try {
                    $storage = Storage::disk('s3');
                    if ($storage->exists($this->mailAttachment)) {
                        $content = $storage->get($this->mailAttachment);
                        $filename = basename($this->mailAttachment);
                        $mail->attachData($content, $filename);
                    }
                } catch (\Throwable $e) {
                    sendError('EmailForQueuing: failed to attach from S3', [
                        'path' => $this->mailAttachment,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $mail;
    }
}
