<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support modifying enum directly, so we need to:
        // 1. Add a temporary column with new enum values
        // 2. Copy data
        // 3. Drop old column
        // 4. Add new column with correct name
        
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->enum('assignment_type_new', ['SUBSTANTIVE', 'ACTING', 'TEMPORARY', 'SECONDMENT'])
                ->default('SUBSTANTIVE')
                ->after('position_id');
        });
        
        // Copy existing data
        DB::statement("UPDATE position_assignments SET assignment_type_new = assignment_type");
        
        // Drop old column
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
        });
        
        // Add new column with correct name
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->enum('assignment_type', ['SUBSTANTIVE', 'ACTING', 'TEMPORARY', 'SECONDMENT'])
                ->default('SUBSTANTIVE')
                ->after('position_id');
        });
        
        // Copy data back
        DB::statement("UPDATE position_assignments SET assignment_type = assignment_type_new");
        
        // Drop temporary column
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('assignment_type_new');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->enum('assignment_type_old', ['SUBSTANTIVE', 'ACTING'])
                ->default('SUBSTANTIVE')
                ->after('position_id');
        });
        
        // Copy data (TEMPORARY and SECONDMENT will become SUBSTANTIVE)
        DB::statement("UPDATE position_assignments SET assignment_type_old = CASE 
            WHEN assignment_type IN ('TEMPORARY', 'SECONDMENT') THEN 'SUBSTANTIVE'
            ELSE assignment_type 
        END");
        
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
        });
        
        // Add column with original name
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->enum('assignment_type', ['SUBSTANTIVE', 'ACTING'])
                ->default('SUBSTANTIVE')
                ->after('position_id');
        });
        
        // Copy data back
        DB::statement("UPDATE position_assignments SET assignment_type = assignment_type_old");
        
        // Drop temporary column
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('assignment_type_old');
        });
    }
};
