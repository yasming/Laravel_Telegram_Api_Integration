<?php


namespace App\Services\Telegram\Webhook;

use Illuminate\Support\Facades\Http;

class SetWebhookTelegramApi
{
    private $urlToSetWebhook;
    private $urlWebhookApi;

    public function __construct()
    {
        $urlToSetWebhook       = config('telegram.set_webhook_url');
        $this->urlToSetWebhook = str_replace('{botAndToken}', config('telegram.bot_and_token'), $urlToSetWebhook);
        $this->urlWebhookApi   = config('telegram.webhook_api_url');
    }

    public function setWebhook()
    {
        $response = Http::post($this->urlToSetWebhook, [
            'url' => $this->urlWebhookApi,
        ]);
        return $response->json();
    }

}