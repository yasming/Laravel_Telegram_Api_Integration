<?php

namespace App\Jobs\Telegram;

use App\Facades\Services\Telegram\Messages\SendMessageToChat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageToTelegramChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $chatId;

    public $action;
    
    public function __construct($chatId)
    {
        $this->chatId        = $chatId;
        $this->action        = __('Send message to user chat action');
    }

    public function handle()
    {
        SendMessageToChat::setChatId($this->chatId)->sendMessage();
    }
}
