<?php

namespace App\Jobs;

use App\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreMessagesFromBotInDatabase implements ShouldQueue
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

    public function __construct($request)
    {
        $this->request       = $request;
        $this->chatId        = $this->getAttributesValueFromBot(self::CHAT_KEYS);
        $this->first_name    = $this->getAttributesValueFromBot(self::FIRST_NAME_KEYS);
        $this->last_name     = $this->getAttributesValueFromBot(self::LAST_NAME_KEYS);
        $this->messageId     = $this->getAttributesValueFromBot(self::MESSAGE_ID_KEYS);
        $this->messageText   = $this->getAttributesValueFromBot(self::MESSAGE_TEXT_KEYS);
        $this->activeSession = $this->getActiveSession($this->chatId);
    }

    /**
     * Execute the job.
     *
     * @return void
    */
    public function handle()
    {
        if ($this->activeSession->count() > 0) {
            $this->updateSession();
        }

        if ($this->activeSession->count() == 0) {
            $this->createNewSession();
        }
    }

    private function createNewSession() : void
    {
        Session::create(
            [
                'first_name'         => $this->first_name,
                'last_name'          => $this->last_name,
                'chat_id'            => $this->chatId,
                'message'            => array(
                    [
                        'message_id' => $this->messageId,
                        'text'       => $this->messageText
                    ]
                )
            ]
        );
    }

    private function getAttributesValueFromBot($attributes)
    {
        $arrayOfAttributes = explode(',', $attributes);
        $value           = $this->request;
        foreach($arrayOfAttributes as $attribute) {
            if(!isset($value[$attribute])) return null;
            $value = $value[$attribute];
        }

        return $value;
    }

    private function getActiveSession($chatId) 
    {
        if(!$chatId) return collect();
        return Session::whereChatId('chat_id',$chatId)->get();  
    }
}
