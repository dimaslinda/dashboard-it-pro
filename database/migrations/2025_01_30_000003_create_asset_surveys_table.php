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
        Schema::create('asset_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('survey_date');
            $table->enum('survey_type', ['routine', 'maintenance', 'condition_assessment', 'location_verification', 'disposal_assessment']);
            $table->enum('condition_assessment', ['excellent', 'good', 'fair', 'poor', 'critical']);
            $table->enum('physical_condition', ['intact', 'minor_wear', 'moderate_wear', 'significant_wear', 'damaged']);
            $table->enum('functional_status', ['fully_functional', 'partially_functional', 'non_functional', 'needs_repair']);
            $table->boolean('maintenance_required')->default(false);
            $table->enum('maintenance_priority', ['low', 'medium', 'high', 'urgent'])->nullable();
            $table->decimal('estimated_repair_cost', 15, 2)->nullable();
            $table->json('recommendations')->nullable(); // Array of recommendations
            $table->json('photos')->nullable(); // Array of photo paths
            $table->boolean('location_verified')->default(true);
            $table->text('location_notes')->nullable();
            $table->text('surveyor_notes')->nullable();
            $table->date('next_survey_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            
            $table->index(['tenant_id', 'survey_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'survey_type']);
            $table->index(['asset_id', 'survey_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_surveys');
    }
};