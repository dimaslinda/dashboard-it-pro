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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('domain');
            $table->string('hosting_provider')->nullable();
            $table->string('registrar')->nullable();
            $table->date('domain_expiry')->nullable();
            $table->date('hosting_expiry')->nullable();
            $table->string('admin_email')->nullable();
            $table->string('admin_username')->nullable();
            $table->string('admin_password')->nullable();
            $table->string('ftp_host')->nullable();
            $table->string('ftp_username')->nullable();
            $table->string('ftp_password')->nullable();
            $table->integer('ftp_port')->default(21);
            $table->string('database_host')->nullable();
            $table->string('database_name')->nullable();
            $table->string('database_username')->nullable();
            $table->string('database_password')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};