<?php

namespace Tests\Feature\Api\Telegram;

use App\Jobs\StoreMessagesFromBotInDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;

class TelegramControllerTest extends TestCase
{
    /** @return array  */
    public function getValuesFromToken() : array
    {
        return [
            [ 'token', Str::random(10) ],
            [ 'token', Str::random(10) ],
            [ 'token', Str::random(10) ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getValuesFromToken
     *
     * @param string $field
     * @param string $fieldValue
     *
     */
    public function it_should_validate_invalid_token($field, $fieldValue)
    {
        $this->post(route('api.updates-from-bot', $fieldValue))
             ->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY)
             ->assertExactJson([
                 $field  => [ 
                     __('validation.in', ['attribute' => $field]) 
                 ]
             ]);
    }

    /* 
    *  @test
    */
    public function test_it_should_queue_messages_from_bot()
    {
        Queue::fake();

        $this->post(route('api.updates-from-bot', config('telegram.token')))
             ->assertStatus(HttpResponse::HTTP_OK)
             ->assertExactJson([
                __('message') => __('Received Message')
            ]);

		Queue::assertPushed(StoreMessagesFromBotInDatabase::class, function ($job) {
            return $job->action === __('Store messages from bot action');
			// return $job->resource instanceof Model;
		});
    }
}
