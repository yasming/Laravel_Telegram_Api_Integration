<?php


namespace App\Services\Telegram\Messages;

use Illuminate\Support\Facades\Http;

class SendMessageToChat
{
    private $urlToSendMessageFromBot;

    public function __construct()
    {
        $urlToSendMessageFromBot       = config('telegram.send_message_url');
        $this->urlToSendMessageFromBot = str_replace(
            [
                '{botAndToken}',
                '{text_to_be_sent}'
            ],
            [
                config('telegram.bot_and_token'),
                __('Reply from bot')
            ] , $urlToSendMessageFromBot
        );    
    }

    public function setChatId($chatId) : self
    {
        $this->urlToSendMessageFromBot = str_replace('{chat_id}', $chatId, $this->urlToSendMessageFromBot);
        return $this;
    }

    public function sendMessage()
    {
        $response = Http::post($this->urlToSendMessageFromBot);
        return $response->json();
    }

}
