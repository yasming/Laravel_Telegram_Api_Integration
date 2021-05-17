<?php

namespace Tests\Feature\Api\Telegram;

use App\Jobs\Telegram\StoreMessagesFromBotInDatabaseJob;
use App\Jobs\Telegram\SendMessageToTelegramChatJob;
use App\Facades\Services\Telegram\Webhook\DeleteWebhookTelegramApi;
use App\Facades\Services\Telegram\Webhook\SetWebhookTelegramApi;
use App\Http\Resources\Session\SessionCollection;
use App\Models\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Tests\RefreshDatabase;
class TelegramControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_should_return_all_active_sessions()
    {
        Session::factory()->create();
        $allSessions = new SessionCollection(Session::filterByName(null)->get());

        $response    = $this->get(route('api.get-active-sessions'))
                            ->assertStatus(HttpResponse::HTTP_OK);
                         
        $this->assertEquals($allSessions->response()->getData(true)['data'],$response->getData(true));
        $this->assertEquals(count($response->getData(true)), Session::all()->count());

        Session::factory()->create(['full_name' => 'janedoe']);

        $allSessions = new SessionCollection(Session::filterByName('janedoe')->get());
        
        $response    = $this->get(route('api.get-active-sessions',['name' => 'janedoe']))
                            ->assertStatus(HttpResponse::HTTP_OK);
                         
        $this->assertEquals($allSessions->response()->getData(true)['data'],$response->getData(true));
        $this->assertEquals(count($response->getData(true)), 1);
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
