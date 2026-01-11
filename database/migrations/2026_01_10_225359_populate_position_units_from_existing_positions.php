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
        // Populate position_units table with existing position-unit relationships
        $positions = DB::table('positions')
            ->whereNotNull('unit_id')
            ->where('status', 'ACTIVE')
            ->get();

        foreach ($positions as $position) {
            DB::table('position_units')->updateOrInsert(
                [
                    'position_id' => $position->id,
                    'organization_unit_id' => $position->unit_id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear the position_units table
        // DB::table('position_units')->truncate();
    }
};
