<?php

namespace Alexxosipov\TelegramBot\Tests\Feature\Actions;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Alexxosipov\TelegramBot\Tests\TelegramTestCase;

class JoinGroupTest extends TelegramTestCase
{
    #[Test]
    #[Group('Actions')]
    public function it_can_handle_welcome_message(): void
    {
        $user = $this->createTelegramUser();

        $response = $this->sendCallbackQuery($user->id, Action::Group);

        $this->assertResponseHasText($response, 'You are welcome to join our group via link:');
        $this->assertResponseHasButton($response, text: 'Join the group', url: 'https://t.me/alexxoispov');
        $this->assertResponseHasButton($response, text: 'Back', action: Action::Main);
    }
}