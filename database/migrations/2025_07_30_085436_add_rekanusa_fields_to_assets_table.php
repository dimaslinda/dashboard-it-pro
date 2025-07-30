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
        Schema::table('assets', function (Blueprint $table) {
            // REKANUSA specific fields
            $table->string('asset_number')->nullable(); // No urut aset
            $table->string('tool_name')->nullable(); // Nama alat spesifik
            $table->string('subcategory')->nullable(); // Sub kategori dalam grup
            $table->integer('total_units')->default(1); // Total unit aset
            $table->json('availability_checklist')->nullable(); // Checklist ketersediaan
            $table->enum('tool_category', [
                'safety_first', 
                'documentation', 
                'support_tools', 
                'survey_architecture', 
                'civil_tools', 
                'mep_utility'
            ])->nullable(); // Kategori alat REKANUSA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'asset_number',
                'tool_name',
                'subcategory',
                'total_units',
                'availability_checklist',
                'tool_category'
            ]);
        });
    }
};
