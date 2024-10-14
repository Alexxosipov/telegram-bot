<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedTinyInteger('action');
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->boolean('should_send_new_system_message')->default(false);
            $table->timestamps();
        });
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
