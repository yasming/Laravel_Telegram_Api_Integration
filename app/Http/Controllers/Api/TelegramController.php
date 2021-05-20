<?php

namespace App\Http\Controllers\Api;

use App\Facades\Services\Telegram\Webhook\SetWebhookTelegramApi;
use App\Facades\Services\Telegram\Webhook\DeleteWebhookTelegramApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTokenRequest;
use App\Http\Resources\Session\SessionCollection;
use App\Jobs\Telegram\SendMessageToTelegramChatJob;
use App\Jobs\Telegram\StoreMessagesFromBotInDatabaseJob;
use App\Models\Session;

class TelegramController extends Controller
{
    public function getUpdatesFromBot(ValidateTokenRequest $request)
    {
        $this->storeMessagesFromBotInDatabase($request->all());
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

    public function getActiveSessions()
    {
        return response()->json(new SessionCollection(Session::filterByName(request()->query('name'))->get()));
    }

    private function storeMessagesFromBotInDatabase($request) : void
    {
        $chatId  = getAttributesValueFromBot(StoreMessagesFromBotInDatabaseJob::CHAT_KEYS, $request);
        StoreMessagesFromBotInDatabaseJob::withChain([
            new SendMessageToTelegramChatJob($chatId)
        ])->dispatch(
            $request,
            $chatId
        );
    }
}
