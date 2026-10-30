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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('hostname')->unique();
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('status')->default('offline');
            $table->decimal('cpu_usage', 5, 2)->default(0);
            $table->decimal('ram_usage', 5, 2)->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
