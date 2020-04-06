<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;
    use SendGrid;

    public $content;
    public $subject;
    public $to;
    public $mailAttachments;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $attachments = '')
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->mailAttachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.default_email')
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
    }
}
