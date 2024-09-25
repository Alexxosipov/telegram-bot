<?php

namespace Tests\Feature\Actions;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TelegramTestCase;

class MainTest extends TelegramTestCase
{
    #[Test]
    #[Group('Actions')]
    public function it_can_handle_welcome_message(): void
    {
        $user = $this->createTelegramUser();

        $response = $this->sendCallbackQuery($user->id, Action::Main);

        $this->assertResponseHasText($response, "Hey {$user->first_name}, welcome to the bot!");
        $this->assertResponseHasButton($response, text: 'Read the docs');
        $this->assertResponseHasButton($response, text: 'Order bot development');
        $this->assertResponseHasButton($response, text: 'Join the group');
    }
}