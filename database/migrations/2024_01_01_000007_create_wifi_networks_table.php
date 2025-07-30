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
        Schema::create('wifi_networks', function (Blueprint $table) {
            $table->id();
            $table->string('ssid');
            $table->string('password');
            $table->string('security_type')->default('WPA2'); // WPA, WPA2, WPA3, Open
            $table->string('frequency_band')->default('2.4GHz'); // 2.4GHz, 5GHz, 6GHz
            $table->integer('channel')->nullable();
            $table->string('location');
            $table->string('router_brand')->nullable();
            $table->string('router_model')->nullable();
            $table->string('router_ip')->nullable();
            $table->string('admin_username')->nullable();
            $table->string('admin_password')->nullable();
            $table->integer('max_devices')->nullable();
            $table->boolean('guest_network')->default(false);
            $table->string('guest_ssid')->nullable();
            $table->string('guest_password')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->foreignId('provider_id')->nullable()->constrained('internet_providers')->onDelete('set null');
            $table->foreignId('contract_id')->nullable()->constrained('provider_contracts')->onDelete('set null');
            $table->date('service_expiry_date')->nullable();
            $table->decimal('monthly_cost', 10, 2)->nullable();
            $table->date('contract_start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_networks');
    }
};
