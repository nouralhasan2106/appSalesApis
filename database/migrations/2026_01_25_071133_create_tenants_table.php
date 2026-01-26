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
        Schema::create('tenants', function (Blueprint $table) {
            $table->bigIncrements('id');//id BIGINT PRIMARY KEY AUTO_INCREMENT,
            $table->string('name',255);//name VARCHAR(255) NOT NULL,
            $table->string('email',255)->unique()->nullable();;//email VARCHAR(255) UNIQUE
            $table->string('phone',50)->nullable();//phone VARCHAR(50),
            $table->Text('address')->nullable();//address TEXT,
            $table->string('logo',255)->nullable();//logo VARCHAR(255),
            $table->string('currency',3)->default('EGP');//currency VARCHAR(3) DEFAULT 'EGP'
            $table->decimal('tax_rate',5,2)->default(0.00);//tax_rate DECIMAL(5,2) DEFAULT 0.00,
            $table->timestamps(); // created_at + updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
