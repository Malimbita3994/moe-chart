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
            $table->string('full_name')->after('id');
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('employee_number', 100)->unique()->nullable()->after('phone');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->after('employee_number');
            
            // Make name nullable since we're using full_name
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'phone', 'employee_number', 'status']);
            $table->string('name')->nullable(false)->change();
        });
    }
};
