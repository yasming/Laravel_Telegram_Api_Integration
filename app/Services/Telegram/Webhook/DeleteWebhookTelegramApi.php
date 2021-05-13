<?php


namespace App\Services\Telegram\Webhook;

use Illuminate\Support\Facades\Http;

class DeleteWebhookTelegramApi
{
    private $urlToDeleteWebhook;

    public function __construct()
    {
        $urlToDeleteWebhook       = config('telegram.delete_webhook_url');
        $this->urlToDeleteWebhook = str_replace('{bot}', config('telegram.bot'), $urlToDeleteWebhook);
    }

    public function deleteWebhook()
    {
        $response = Http::post($this->urlToDeleteWebhook);
        return $response->json();
    }

}
