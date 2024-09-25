<?php

namespace Tests\Feature\Actions;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TelegramTestCase;

class OrderBotTest extends TelegramTestCase
{
    #[Test]
    #[Group('Actions')]
    public function it_can_handle_welcome_message(): void
    {
        $user = $this->createTelegramUser();

        $response = $this->sendCallbackQuery($user->id, Action::OrderBot, [
            'username' => '@test'
        ]);

        $this->assertResponseHasText($response, 'To order bot please text @test');
        $this->assertResponseHasButton($response, text: 'Back', action: Action::Main);
    }
}