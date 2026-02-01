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
        Schema::create('orders_details', function (Blueprint $table) {
            $table->bigIncrements('id');//id BIGINT PRIMARY KEY AUTO_INCREMENT,
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('item_variant_id')->nullable();
            $table->decimal('quantity',10,2);//quantity DECIMAL(10,2) NOT NULL
            $table->decimal('unit_price',10,2);//unit_price DECIMAL(10,2) NOT NULL
            $table->decimal('total_price',10,2);//total_price DECIMAL(10,2) NOT NULL,
            $table->Text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();


            ////////////////////foreign keys
            $table->foreign('order_id')->references('id')->on('orders');//->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items');//->cascadeOnDelete();
            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_details');
    }
};
