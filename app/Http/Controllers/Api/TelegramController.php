<?php

namespace App\Http\Controllers\Api;

use App\Facades\Services\Telegram\SetWebhookTelegramApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTokenRequest;
use App\Jobs\StoreMessagesFromBotInDatabase;
class TelegramController extends Controller
{
    public function getUpdatesFromBot(ValidateTokenRequest $request)
    {
        StoreMessagesFromBotInDatabase::dispatch($request->all());
    }

    public function setWebhook()
    {
        return response()->json(SetWebhookTelegramApi::setWebhook());
    }
}
