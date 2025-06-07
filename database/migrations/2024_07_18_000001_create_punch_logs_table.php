<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('punch_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('punch_date_time');
            $table->string('user_code');
            $table->boolean('punch_status')->comment('true for out, false for in');
            $table->boolean('send_message')->default(false);
            $table->boolean('is_process')->default(false);
            $table->timestamps();

            $table->foreign('user_code')->references('user_code')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_logs');
    }
};
