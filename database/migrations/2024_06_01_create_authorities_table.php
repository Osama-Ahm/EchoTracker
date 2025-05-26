<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // government, ngo, etc.
            $table->string('jurisdiction_name');
            $table->text('jurisdiction_boundary')->nullable(); // Stored as WKT (Well-Known Text)
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('notification_email');
            $table->json('notification_preferences')->nullable();
            $table->string('verification_status')->default('pending'); // pending, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorities');
    }
};