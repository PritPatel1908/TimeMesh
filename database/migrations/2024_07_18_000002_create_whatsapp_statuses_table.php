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
        Schema::create('whatsapp_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('total_message_count')->default(0);
            $table->integer('pending_message_count')->default(0);
            $table->dateTime('renew_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_statuses');
    }
};
