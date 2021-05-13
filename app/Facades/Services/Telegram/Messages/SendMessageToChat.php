<?php

namespace App\Facades\Services\Telegram\Messages;

use App\Services\Telegram\Messages\SendMessageToChat as Action;
use Illuminate\Support\Facades\Facade;

class SendMessageToChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Action::class;
    }
}
