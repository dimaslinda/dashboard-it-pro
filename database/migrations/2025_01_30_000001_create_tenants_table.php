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
        // Create tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama tenant (IT Dashboard, Asset Survey)
            $table->string('slug')->unique(); // URL slug (it-dashboard, asset-survey)
            $table->string('domain')->nullable(); // Custom domain jika ada
            $table->string('database_name')->nullable(); // Nama database terpisah jika menggunakan multi-database
            $table->json('settings')->nullable(); // Konfigurasi khusus tenant
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add tenant_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')
                  ->constrained('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
        
        Schema::dropIfExists('tenants');
    }
};