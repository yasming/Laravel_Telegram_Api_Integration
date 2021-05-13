<?php

namespace App\Facades\Services\Telegram\Webhook;

use App\Services\Telegram\Webhook\SetWebhookTelegramApi as Action;
use Illuminate\Support\Facades\Facade;

class SetWebhookTelegramApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Action::class;
    }
}
