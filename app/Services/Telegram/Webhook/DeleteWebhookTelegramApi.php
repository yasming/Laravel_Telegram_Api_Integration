<?php


namespace App\Services\Telegram\Webhook;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeleteWebhookTelegramApi
{
    private $urlToDeleteWebhook;

    public function __construct()
    {
        $urlToDeleteWebhook       = config('telegram.delete_webhook_url');
        $this->urlToDeleteWebhook = Str::replace('{botAndToken}', config('telegram.bot_and_token'), $urlToDeleteWebhook);
    }

    public function deleteWebhook()
    {
        $response = Http::post($this->urlToDeleteWebhook);
        return $response->json();
    }

}
