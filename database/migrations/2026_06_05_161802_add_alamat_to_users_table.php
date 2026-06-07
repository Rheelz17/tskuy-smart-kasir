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
        Schema::table('users', function (Blueprint $blueprint) {
            // Menambahkan kolom alamat setelah kolom phone, tipenya TEXT dan boleh kosong (nullable)
            $blueprint->text('alamat')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            //Menghapus kolom alamat jika migration di-rollback
            $blueprint->dropColumn('alamat');
        });
    }
};
