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
        // Add cost fields to internet_providers table
        Schema::table('internet_providers', function (Blueprint $table) {
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('website');
            $table->decimal('installation_cost', 10, 2)->nullable()->after('monthly_cost');
            $table->string('speed_package')->nullable()->after('installation_cost');
            $table->integer('bandwidth_mbps')->nullable()->after('speed_package');
            $table->enum('connection_type', ['fiber', 'cable', 'dsl', 'wireless', 'satellite'])->nullable()->after('bandwidth_mbps');
        });
        
        // Remove monthly_cost from wifi_networks table
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->dropColumn('monthly_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add monthly_cost back to wifi_networks table
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('service_expiry_date');
        });
        
        // Remove cost fields from internet_providers table
        Schema::table('internet_providers', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_cost',
                'installation_cost', 
                'speed_package',
                'bandwidth_mbps',
                'connection_type'
            ]);
        });
    }
};