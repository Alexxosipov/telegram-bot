<?php

namespace Alexxosipov\Telegram\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property ?int $message_id
 * @property ?BackedEnum $action
 */
class TelegramUser extends Model implements TelegramUserContract
{
    use HasFactory;

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'action' => config('telegram-bot.action-enum')
        ];
    }

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'username',
        'action',
        'message_id',
    ];
}
