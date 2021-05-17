<?php

namespace Tests\Unit\Jobs;

use App\Jobs\Telegram\StoreMessagesFromBotInDatabaseJob;
use App\Models\Session;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\RefreshDatabase;

class StoreMessagesFromBotInDatabaseJobTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_it_should_create_a_new_session_in_database()
    {
        $mock = $this->mockApiResponse();
        $job  = new StoreMessagesFromBotInDatabaseJob($mock,$mock['message']['chat']['id']);
        $job->handle();
        $this->assertEquals(Session::count(),1);
        $job->handle();
        $this->assertEquals(Session::count(),2);
    }

    private function mockApiResponse()
    {
        return [ 
                    "update_id" => Str::random(10),
                    "message"   => 
                    [
                        "message_id" => Str::random(4),
                        "from" => [
                            "id"            => Str::random(10),
                            "is_bot"        => false,
                            "first_name"    => "Mock",
                            "language_code" => "pt-br",
                        ],
                        "chat" => [ 
                            "id"         => Str::random(10),
                            "first_name" => "Mock",
                            "type"       => "private",
                        ],
                        "date" => Str::random(10),
                        "text" => "Mock",
                    ],
                    "token" => "Mock"
               ];
    }
}
