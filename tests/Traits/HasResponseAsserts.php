<?php

namespace Alexxosipov\TelegramBot\Tests\Traits;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\KeyboardButton;
use Alexxosipov\TelegramBot\Response\Response;

trait HasResponseAsserts
{
    public function assertResponseHasButton(
        Response $response,
        ?string $text = null,
        ?string $url = null,
        ?\BackedEnum $action = null,
        ?array $data = null
    ): void
    {
        if (!$text && !$action) {
            return;
        }

        $buttons = collect($response->keyboard ?? []);
        $buttons = $buttons->flatten();

        if (!is_null($text)) {
            $buttons = $buttons->where('label', $text);
        }

        if (!is_null($action)) {
            $buttons = $buttons->where('action', $action);
        }

        if (!is_null($url)) {
            $buttons = $buttons->where('url', $url);
        }

        if (!is_null($data)) {
            $buttons = $buttons->filter(function (KeyboardButton $button) use ($data) {
                return empty(array_diff($data, $button->data));
            });
        }

        $this->assertTrue($buttons->isNotEmpty());
    }

    public function assertResponseHasText(Response $response, string $text, bool $strict = true): void
    {
        $this->assertTrue($strict
            ? $response->text === $text
            : \Str::contains($response->text, $text));
    }

}