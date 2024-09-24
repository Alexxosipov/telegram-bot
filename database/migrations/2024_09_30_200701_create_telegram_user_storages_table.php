<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('telegram_storage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_user_id');
            $table->string('key');
            $table->string('value')->nullable();

            $table->unique(['telegram_user_id', 'key']);

            $table->timestamps();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('telegram_storage');
    }
};
