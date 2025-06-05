<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('whatsapp_statuses', function (Blueprint $table) {
            $table->renameColumn('pending_message_count', 'success_message_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_statuses', function (Blueprint $table) {
            $table->renameColumn('success_message_count', 'pending_message_count');
        });
    }
};
