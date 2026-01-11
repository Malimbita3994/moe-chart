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
        Schema::table('position_assignments', function (Blueprint $table) {
            // Rename user_id to person_id for clarity (optional, keeping user_id for now)
            // Add new fields
            $table->enum('assignment_type', ['SUBSTANTIVE', 'ACTING'])->default('SUBSTANTIVE')->after('position_id');
            $table->string('authority_reference')->nullable()->after('end_date');
            $table->enum('allowance_applicable', ['Yes', 'No'])->default('No')->after('authority_reference');
            
            // Change is_active to status enum
            $table->enum('status', ['Active', 'Ended'])->default('Active')->after('allowance_applicable');
        });
        
        // Migrate existing is_active to status
        DB::statement("UPDATE position_assignments SET status = CASE WHEN is_active = 1 THEN 'Active' ELSE 'Ended' END");
        
        // Drop old is_active column
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('end_date');
        });
        
        // Migrate status back to is_active
        DB::statement("UPDATE position_assignments SET is_active = CASE WHEN status = 'Active' THEN 1 ELSE 0 END");
        
        Schema::table('position_assignments', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
            $table->dropColumn('authority_reference');
            $table->dropColumn('allowance_applicable');
            $table->dropColumn('status');
        });
    }
};
