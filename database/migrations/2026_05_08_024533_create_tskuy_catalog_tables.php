<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Moods
        Schema::create('moods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable(); // Dibuat nullable jika seeder SQL polosan dimasukkan
            $table->timestamps();
        });

        // Tables (meja)
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number', 50);
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->timestamps();
        });

        // Menus
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true); // Pengganti is_active
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_promo')->default(false);
            $table->timestamps();
        });

        // Menu Mood (Pivot tanpa 's' sesuai konfigurasi Model/SQL Dump)
        Schema::create('menu_moods', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('mood_id')->constrained('moods')->onDelete('cascade');
            $table->primary(['menu_id', 'mood_id']);
        });

        // Menu Options
        Schema::create('menu_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Menu Option Values
        Schema::create('menu_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained('menu_options')->onDelete('cascade');
            $table->string('value');
            $table->decimal('additional_price', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 100)->unique();
            $table->string('customer_name')->nullable();
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null'); // Nullable support takeaway
            $table->enum('order_type', ['self order', 'cashier']);
            $table->enum('source', ['qr', 'kasir']);
            $table->enum('status', ['PENDING', 'COOKING', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->enum('eating_option', ['dine in', 'take away'])->default('dine in');
            $table->boolean('is_open_bill')->default(false);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Orders Item
        Schema::create('orders_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->text('note')->nullable();
            $table->enum('status', ['PENDING', 'COOKING', 'READY', 'REJECTED'])->default('PENDING');
            $table->string('rejected_reason')->nullable();
            $table->timestamps();
        });

        // Order Bill Shares
        Schema::create('order_bill_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('computed_amount', 10, 2);
            $table->timestamps();
        });

        // Order Item Options
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('orders_item')->onDelete('cascade');
            $table->foreignId('option_value_id')->constrained('menu_option_values')->onDelete('cascade');
        });

        // Order Logs
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('action', ['CREATE', 'UPDATE', 'CANCEL', 'REJECT']);
            $table->text('description');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'qris', 'transfer']);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->string('reference_number')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'FAILED'])->default('PENDING');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Payment Details
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('orders_item')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_details');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_logs');
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('order_bill_shares');
        Schema::dropIfExists('orders_item');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_moods');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('moods');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('sessions');
    }
};