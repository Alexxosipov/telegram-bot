<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Response;

use BackedEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Response
{
    public function __construct(
        public string $text,
        public Keyboard|array|null $keyboard = null,
        public bool $shouldSendNewMessage = false,
        public bool $shouldUpdateDbMessageId = true,
    )
    {
    }

    public static function fromPaginator(
        LengthAwarePaginator $paginator,
        string $text,
        callable $itemButtonResolver,
        BackedEnum $paginationAction,
        BackedEnum $backAction
    ): Response
    {
        $keyboard = [];

        foreach ($paginator->items() as $item) {
            $keyboard[] = [$itemButtonResolver($item)];
        }

        $paginationLine = static::getPaginationLine($paginator, $paginationAction);

        if ($paginationLine) {
            $keyboard[] = $paginationLine;
        }

        $keyboard[] = [new KeyboardButton('⬅️ Back', $backAction)];

        return new Response(
            $text,
            new Keyboard($keyboard)
        );
    }

    private static function getPaginationLine(LengthAwarePaginator $paginator, BackedEnum $paginationAction): ?array
    {
        if (!$paginator->hasPages()) {
            return null;
        }

        $paginationLine = [];

        if (
            $paginator->previousPageUrl()
            && $paginator->firstItem()
            && $paginator->currentPage() + 1 !== $paginator->firstItem()
        ) {
            $paginationLine[] = new KeyboardButton('⏪', $paginationAction, [
                'page' => $paginator->firstItem()
            ]);
        }

        if ($paginator->previousPageUrl()) {
            $paginationLine[] = new KeyboardButton((string)($paginator->currentPage() - 1), $paginationAction, [
                'page' => $paginator->currentPage() - 1
            ]);
        }

        $paginationLine[] = new KeyboardButton((string)$paginator->currentPage(), $paginationAction, [
            'page' => $paginator->currentPage()
        ]);

        if ($paginator->nextPageUrl()) {
            $paginationLine[] = new KeyboardButton((string)($paginator->currentPage() + 1), $paginationAction, [
                'page' => $paginator->currentPage() + 1
            ]);
        }

        if (
            $paginator->lastPage() !== $paginator->currentPage()
            && $paginator->lastPage() !== $paginator->currentPage() + 1
        ) {
            $paginationLine[] = new KeyboardButton('⏩', $paginationAction, [
                'page' => $paginator->lastPage()
            ]);
        }

        return $paginationLine;
    }
}
