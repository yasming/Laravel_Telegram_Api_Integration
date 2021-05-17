<?php

namespace Tests\Unit\Jobs;

use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\RefreshDatabase;

class SendMessageToTelegramChatJob extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_should_create_a_new_session_in_database()
    {
        $mock = $this->mockApiResponse();
        $job  = new SendMessageToTelegramChatJob($mock['message']['chat']['id']);
        $job->handle();
        $this->assertEquals($job->response,$this->mockApiResponse());
    }

    private function mockApiResponse()
    {
        return [ 
                    "ok" => true,
                    "result" => [
                        "message_id" => 120,
                        "from" => [
                            "id" => Str::random(10),
                            "is_bot" => true,
                            "first_name" => "mock",
                            "username" => "mock"
                        ],
                        "chat" => [
                            "id" => Str::random(10),
                            "first_name" => "mock",
                            "last_name" => "mock",
                            "type" => "private"
                        ],
                        "date" => Str::random(10),
                        "text" => "mock"
                    ]
               ];
    }
}
