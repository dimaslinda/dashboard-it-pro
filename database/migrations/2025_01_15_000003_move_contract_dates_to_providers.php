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
        // Add contract dates to internet_providers table
        Schema::table('internet_providers', function (Blueprint $table) {
            $table->date('service_expiry_date')->nullable()->after('connection_type');
            $table->date('contract_start_date')->nullable()->after('service_expiry_date');
            $table->integer('contract_duration_months')->nullable()->after('contract_start_date');
            $table->enum('contract_status', ['active', 'expired', 'cancelled'])->default('active')->after('contract_duration_months');
        });
        
        // Remove contract dates from wifi_networks table
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->dropColumn(['service_expiry_date', 'contract_start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add contract dates back to wifi_networks table
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->date('service_expiry_date')->nullable()->after('provider_id');
            $table->date('contract_start_date')->nullable()->after('service_expiry_date');
        });
        
        // Remove contract dates from internet_providers table
        Schema::table('internet_providers', function (Blueprint $table) {
            $table->dropColumn([
                'service_expiry_date',
                'contract_start_date', 
                'contract_duration_months',
                'contract_status'
            ]);
        });
    }
};