<?php

namespace Tests\Feature\Api\Telegram;

use App\Jobs\Telegram\StoreMessagesFromBotInDatabaseJob;
use App\Jobs\Telegram\SendMessageToTelegramChatJob;
use App\Facades\Services\Telegram\Webhook\DeleteWebhookTelegramApi;
use App\Facades\Services\Telegram\Webhook\SetWebhookTelegramApi;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;

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

		Queue::assertPushed(StoreMessagesFromBotInDatabaseJob::class, function ($job) {
            return $job->action === __('Store messages from bot action');
		});

        Queue::assertPushedWithChain(StoreMessagesFromBotInDatabaseJob::class, [
            SendMessageToTelegramChatJob::class,
        ]);

        Queue::assertPushed(StoreMessagesFromBotInDatabaseJob::class, 1);
    }

    public function test_it_should_delete_webhook()
    {
        $url = DeleteWebhookTelegramApi::getUrlToDeleteWebhook();
        Http::fake([
            $url => Http::response($this->mockDeleteWebhook(), HttpResponse::HTTP_OK, ['Headers']),
        ]);

        $this->post(route('api.remove-webhook', config('telegram.token')))
             ->assertStatus(HttpResponse::HTTP_OK)
             ->assertExactJson(
                $this->mockDeleteWebhook()
             );
    }

    public function test_it_should_set_webhook()
    {
        $url = SetWebhookTelegramApi::getUrlToSetWebhook();
        Http::fake([
            $url => Http::response($this->mockSetWebhook(), HttpResponse::HTTP_OK, ['Headers']),
        ]);

        $this->post(route('api.set-webhook', config('telegram.token')))
             ->assertStatus(HttpResponse::HTTP_OK)
             ->assertExactJson(
                $this->mockSetWebhook()
             );
    }

    private function mockDeleteWebhook()
    {
        return [
            "ok"          => true,
            "result"      => true,
            "description" => "Webhook was deleted"
        ];
    }

    private function mockSetWebhook()
    {
        return [
            "ok"          => true,
            "result"      => true,
            "description" => "Webhook was set"
        ];
    }
}
