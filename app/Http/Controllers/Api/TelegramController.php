<?php

namespace App\Http\Controllers\Api;

use App\Facades\Services\Telegram\Webhook\SetWebhookTelegramApi;
use App\Facades\Services\Telegram\Webhook\DeleteWebhookTelegramApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTokenRequest;
use App\Jobs\StoreMessagesFromBotInDatabase;
class TelegramController extends Controller
{
    public function getUpdatesFromBot(ValidateTokenRequest $request)
    {
        StoreMessagesFromBotInDatabase::dispatch($request->all());
        return response()->json([__('message') => __('Received Message')]);
    }

    public function setWebhook()
    {
        return response()->json(SetWebhookTelegramApi::setWebhook());
    }

    public function deleteWebhook()
    {
        return response()->json(DeleteWebhookTelegramApi::deleteWebhook());
    }
}
