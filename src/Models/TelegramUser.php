<?php

namespace Alexxosipov\TelegramBot\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property ?int $message_id
 * @property ?BackedEnum $action
 */
class TelegramUser extends Model
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
