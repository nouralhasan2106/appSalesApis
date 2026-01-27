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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');//id BIGINT PRIMARY KEY AUTO_INCREMENT,
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->enum('order_type',['dine_in', 'takeaway', 'delivery'])->default('dine_in');
            $table->enum('status',['pending', 'confirmed', 'preparing', 'ready', 'completed',
                         'cancelled'])->default('pending');
            $table->decimal('subtotal',10,2);//subtotal DECIMAL(10,2) NOT NULL
            $table->decimal('tax_amount',10,2)->default(0.00);//tax_amount DECIMAL(10,2) DEFAULT 0.00
            $table->decimal('total_amount',10,2);//total_amount DECIMAL(10,2) NOT NULL,
            $table->decimal('paid_amount',10,2)->default(0.00);//paid_amount DECIMAL(10,2) DEFAULT 0.00,
            $table->Text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
