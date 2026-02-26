<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->time('open_time')->default('09:00:00');
            $table->time('close_time')->default('18:00:00');
            $table->json('working_days')->comment('Array of day numbers: 0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
