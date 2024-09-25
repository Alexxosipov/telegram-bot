<?php

namespace Tests\Traits;

use Alexxosipov\TelegramBot\Example\Enums\Action;
use Alexxosipov\TelegramBot\Response\Response;

trait HasResponseAsserts
{
    public function assertResponseHasButton(
        Response $response,
        ?string $text = null,
        ?string $url = null,
        ?Action $action = null,
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
            if ($action && !isset($data['action'])) {
                $data['action'] = $action->value;
            }

            $buttons = $buttons->where('data', json_encode($data));
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