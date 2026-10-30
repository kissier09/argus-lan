<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('strand')->nullable();
            $table->string('year')->nullable();
            $table->string('section')->nullable();
            $table->string('student_number')->nullable()->unique();
            $table->string('photo_path')->nullable();
            $table->uuid('qr_code_uuid')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['student_number']);
            $table->dropUnique(['qr_code_uuid']);

            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'strand',
                'year',
                'section',
                'student_number',
                'photo_path',
                'qr_code_uuid',
            ]);
        });
    }
};
