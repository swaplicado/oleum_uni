<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Uni\Gift;

class GiftExchanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($gift, $points)
    {
        $this->oGift = Gift::find($gift);
        $this->dCurrentPoints = (float) $points;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('[UVAETH] Canje de premio UNIVAETH')
                    ->view('mails.adm.giftexchanged')
                    ->with('oGift', $this->oGift)
                    ->with('points', $this->dCurrentPoints);
    }
}
