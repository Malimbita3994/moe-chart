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
            // Add name column for specific position name (e.g., "Director of HRM")
            $table->string('name')->nullable()->after('title_id');
        });
        
        // Migrate existing title data to name
        \DB::statement('UPDATE positions SET name = title WHERE name IS NULL');
        
        // Make name required after migration
        Schema::table('positions', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
