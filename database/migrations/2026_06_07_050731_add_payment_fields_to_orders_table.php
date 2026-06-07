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
        Schema::table('orders', function (Blueprint $table) {
                $table->string('transaction_id')
                    ->nullable()
                    ->after('total');
                $table->text('snap_token')
                    ->nullable()
                    ->after('transaction_id');
                $table->enum('payment_status', [
                    'pending',
                    'paid',
                    'failed',
                    'expired'
                ])
                ->default('pending')
                ->after('snap_token');
                $table->string('payment_type')
                    ->nullable()
                    ->after('payment_status');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
