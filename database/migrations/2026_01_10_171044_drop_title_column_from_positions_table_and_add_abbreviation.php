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
            // Drop the old title column if it exists
            if (Schema::hasColumn('positions', 'title')) {
                $table->dropColumn('title');
            }
            
            // Add abbreviation column for position abbreviation (e.g., HICT)
            $table->string('abbreviation', 20)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // Re-add title column if needed
            $table->string('title')->after('id');
            
            // Drop abbreviation column
            $table->dropColumn('abbreviation');
        });
    }
};
