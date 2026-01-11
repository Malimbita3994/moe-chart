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
        Schema::create('organization_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique()->nullable();
            $table->string('unit_type', 50); // MINISTRY, COUNCIL, DIRECTORATE, DIVISION, SECTION, UNIT, REGIONAL_OFFICE, DISTRICT_OFFICE
            $table->foreignId('parent_id')->nullable()->constrained('organization_units')->onDelete('cascade');
            $table->integer('level');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_units');
    }
};
