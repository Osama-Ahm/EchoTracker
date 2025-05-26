<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authority_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authority_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['authority_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authority_categories');
    }
};