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
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->foreignId('provider_id')->nullable()->after('status')->constrained('internet_providers')->onDelete('set null');
            $table->date('service_expiry_date')->nullable()->after('provider_id');
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('service_expiry_date');
            $table->date('contract_start_date')->nullable()->after('monthly_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wifi_networks', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn(['provider_id', 'service_expiry_date', 'monthly_cost', 'contract_start_date']);
        });
    }
};
