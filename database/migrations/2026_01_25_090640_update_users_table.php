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
        Schema::table('users', function (Blueprint $table) {
           
            $table->unsignedBigInteger('tenant_id')->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('tenant_id')   ;
           
            $table->enum('role',['super_admin', 'tenant_owner', 'branch_manager', 'cashier',
                        'accountant']);
            $table->boolean('is_active')->default(true)->nullable();
           $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
    $table->dropForeign(['tenant_id']);
    $table->dropForeign(['branch_id']);
    $table->dropColumn(['tenant_id','branch_id','role','is_active']);
});

    }
};
