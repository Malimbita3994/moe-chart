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
        Schema::table('positions', function (Blueprint $table) {
            // Drop the old grade_required string column
            $table->dropColumn('grade_required');
        });
        
        Schema::table('positions', function (Blueprint $table) {
            // Add designation_id as foreign key
            $table->foreignId('designation_id')->nullable()->after('reports_to_position_id')->constrained('designations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });
        
        Schema::table('positions', function (Blueprint $table) {
            // Re-add as string column
            $table->string('grade_required', 50)->nullable()->after('reports_to_position_id');
        });
    }
};
