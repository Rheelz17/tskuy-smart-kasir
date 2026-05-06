<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Ini otomatis jadi BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->timestamps(); // Ini otomatis bikin created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};