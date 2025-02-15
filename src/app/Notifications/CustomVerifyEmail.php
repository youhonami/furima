<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('【重要】メールアドレス確認のお願い')
            ->greeting('こんにちは！')
            ->line('このメールアドレスを使用して登録が行われました。')
            ->line('メールアドレスを確認するには、以下のボタンをクリックしてください。')
            ->action('メールアドレスを確認する', $verificationUrl)
            ->line('もしこの操作に心当たりがない場合は、このメールを無視してください。');
    }
}
