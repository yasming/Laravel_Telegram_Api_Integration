<?php

namespace App\Facades\Services\Telegram;

use App\Services\Telegram\SetWebhookTelegramApi as Action;
use Illuminate\Support\Facades\Facade;

class SetWebhookTelegramApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Action::class;
    }
}
