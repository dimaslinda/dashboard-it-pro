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
        Schema::create('provider_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('internet_providers')->onDelete('cascade');
            $table->string('company_name');
            $table->decimal('monthly_cost', 10, 2)->nullable();
            $table->decimal('installation_cost', 10, 2)->nullable();
            $table->string('speed_package')->nullable();
            $table->integer('bandwidth_mbps')->nullable();
            $table->string('connection_type')->nullable();
            $table->date('service_expiry_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->integer('contract_duration_months')->nullable();
            $table->enum('contract_status', ['active', 'inactive', 'expired', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_contracts');
    }
};
