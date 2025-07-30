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
        // Create provider_contracts table for multi-tenant support
        Schema::create('provider_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('internet_providers')->onDelete('cascade');
            $table->string('company_name'); // Nama perusahaan/tenant
            $table->decimal('monthly_cost', 10, 2);
            $table->decimal('installation_cost', 10, 2)->nullable();
            $table->string('speed_package')->nullable();
            $table->integer('bandwidth_mbps')->nullable();
            $table->string('connection_type')->nullable();
            $table->date('service_expiry_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->integer('contract_duration_months')->nullable();
            $table->enum('contract_status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi kontrak per provider per company
            $table->unique(['provider_id', 'company_name'], 'unique_provider_company');
        });
        
        // Add contract_id to wifi_networks table
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->foreignId('contract_id')->nullable()->after('provider_id')
                  ->constrained('provider_contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
        
        Schema::dropIfExists('provider_contracts');
    }
};