<?php

namespace App\Jobs\Telegram;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreMessagesFromBotInDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CHAT_KEYS         = 'message,chat,id';
    const FIRST_NAME_KEYS   = 'message,chat,first_name';
    const LAST_NAME_KEYS    = 'message,chat,last_name';
    const MESSAGE_ID_KEYS   = 'message,message_id';
    const MESSAGE_TEXT_KEYS = 'message,text';

    private $request;
    private $chatId;
    private $first_name;
    private $last_name;
    private $messageId;
    private $messageText;
    private $activeSession;

    public $action;
    
    public function __construct($request, $chatId)
    {
        $this->request       = $request;
        $this->chatId        = $chatId;
        $this->first_name    = getAttributesValueFromBot(self::FIRST_NAME_KEYS,   $request);
        $this->last_name     = getAttributesValueFromBot(self::LAST_NAME_KEYS,    $request);
        $this->messageId     = getAttributesValueFromBot(self::MESSAGE_ID_KEYS,   $request);
        $this->messageText   = getAttributesValueFromBot(self::MESSAGE_TEXT_KEYS, $request);
        $this->activeSession = $this->getActiveSession();
        $this->action        = __('Store messages from bot action');
    }

    /**
     * Execute the job.
     *
     * @return void
    */

    public function handle()
    {
        if ($this->activeSession != null) {
            $this->updateSession();
        }

        if ($this->activeSession == null) {
            $this->createNewSession();
        }
    }

    private function updateSession() : void
    {
        $actualMessages = $this->activeSession->messages;
        
        array_push($actualMessages,$this->getMessageFromBot());

        $this->activeSession->update(['messages' => $actualMessages]);
    }

    private function createNewSession() : void
    {
        Session::create(
            [
                'first_name'         => $this->first_name,
                'last_name'          => $this->last_name,
                'full_name'          => $this->last_name 
                                        ? $this->first_name. ' '.$this->last_name 
                                        : $this->first_name,
                'chat_id'            => $this->chatId,
                'messages'            => array(
                   $this->getMessageFromBot()
                )
            ]
        );
    }

    private function getMessageFromBot() : array
    {
        return [
            'message_id' => $this->messageId,
            'text'       => $this->messageText
        ];
    }

    private function getActiveSession() 
    {
        if(!$this->chatId) return collect();
        return Session::where('chat_id',$this->chatId)->first();  
    }
}
