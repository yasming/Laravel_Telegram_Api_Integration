<?php

namespace App\Facades\Services\Telegram\Webhook;

use App\Services\Telegram\Webhook\DeleteWebhookTelegramApi as Action;
use Illuminate\Support\Facades\Facade;

class DeleteWebhookTelegramApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Action::class;
    }
}
