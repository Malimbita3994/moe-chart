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
            // Add title_id column
            $table->foreignId('title_id')->nullable()->after('id')->constrained('titles')->onDelete('restrict');
            
            // Rename grade to grade_required and make it nullable
            $table->string('grade_required', 50)->nullable()->after('title_id');
        });
        
        // Migrate existing data if any
        // Note: This assumes titles table is populated from system_configurations
        // We'll handle data migration separately if needed
        
        // Drop old title column after migration (commented out for safety)
        // Schema::table('positions', function (Blueprint $table) {
        //     $table->dropColumn('title');
        //     $table->dropColumn('grade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['title_id']);
            $table->dropColumn('title_id');
            $table->dropColumn('grade_required');
        });
    }
};
