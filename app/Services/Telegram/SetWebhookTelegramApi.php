<?php


namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Spatie\Multitenancy\Models\Tenant;

class SetWebhookTelegramApi
{
    private $urlToSetWebhook;
    private $urlWebhookApi;

    public function __construct()
    {
        $urlToSetWebhook       = config('telegram.set_webhook_url');
        $this->urlToSetWebhook = str_replace('{bot}', config('telegram.bot'), $urlToSetWebhook);
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
