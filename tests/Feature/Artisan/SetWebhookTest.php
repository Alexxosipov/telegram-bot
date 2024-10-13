<?php

namespace Alexxosipov\TelegramBot\Tests\Feature\Artisan;

use Alexxosipov\TelegramBot\Artisan\SetWebhook;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Alexxosipov\TelegramBot\Tests\TelegramTestCase;

class SetWebhookTest extends TelegramTestCase
{
    #[Test]
    #[Group('Artisan')]
    public function it_can_set_webhook(): void
    {
        $this->withoutMockingConsoleOutput();

        $signal = $this->artisan(SetWebhook::class);

        $this->assertEquals(0, $signal);
    }
}