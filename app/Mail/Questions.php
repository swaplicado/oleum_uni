<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

class Questions extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($studentName, $studentQuestion)
    {
        $this->studentName = $studentName;
        $this->studentQuestion = $studentQuestion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                        ->subject('UVAETH. Duda de '.$this->studentName)
                        ->view('mails.uni.question')
                        ->with('studentName', $this->studentName)
                        ->with('studentQuestion', $this->studentQuestion);
    }
}
