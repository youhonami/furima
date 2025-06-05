<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerRatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $buyerName;
    public $itemName;

    public function __construct($buyerName, $itemName)
    {
        $this->buyerName = $buyerName;
        $this->itemName = $itemName;
    }

    public function build()
    {
        return $this->subject('商品評価のお知らせ')
            ->view('emails.seller_rated')
            ->with([
                'buyerName' => $this->buyerName,
                'itemName' => $this->itemName,
            ]);
    }
}
