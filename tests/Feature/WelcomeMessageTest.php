<?php

namespace Tests\Feature;

use Alexxosipov\Telegram\Actions\Action;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TelegramTestCase;

class WelcomeMessageTest extends TelegramTestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function it_can_handle_welcome_message(): void
    {
        $this->sendTextMessage(1, '/start');

        $this->assertDatabaseHas('telegram_users', [
            'id' => 1,
            'action' => Action::Welcome
        ]);
    }
}