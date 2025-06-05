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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_code')->unique();
            $table->string('first_name');
            $table->string('father_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_no');
            $table->string('guardian_name');
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_contact_no')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('email')->nullable();
            $table->string('room_number')->nullable();
            $table->text('vehicle_detail')->nullable();
            $table->string('occupation')->nullable();
            $table->text('occupation_address')->nullable();
            $table->text('medical_detail')->nullable();
            $table->text('other_details')->nullable();
            $table->datetime('left_date')->nullable();
            $table->text('left_remark')->nullable();
            $table->datetime('joining_date')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
