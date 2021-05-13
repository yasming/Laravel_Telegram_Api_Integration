<?php

namespace App\Http\Controllers\Api;

use App\Events\MessagesFromBotProcessed;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTokenRequest;
use App\Jobs\StoreMessagesFromBotInDatabase;
class TelegramController extends Controller
{
    public function getUpdatesFromBot(ValidateTokenRequest $request)
    {
        dd($request->all());
        StoreMessagesFromBotInDatabase::dispatch($request->all());
    }
}
