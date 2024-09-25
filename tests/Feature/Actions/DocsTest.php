<?php

namespace Tests\Feature\Actions;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TelegramTestCase;

class DocsTest extends TelegramTestCase
{
    #[Test]
    #[Group('Actions')]
    public function it_can_show_docs_with_back_button(): void
    {
        $user = $this->createTelegramUser();

        $response = $this->sendCallbackQuery($user->id, Action::Docs);

        $this->assertResponseHasText($response, 'Here is the docs.');
        $this->assertResponseHasButton($response, text: 'Back', action: Action::Main);
    }
}