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
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('provider')->nullable(); // Gmail, Outlook, etc
            $table->string('smtp_server')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('imap_server')->nullable();
            $table->integer('imap_port')->nullable();
            $table->boolean('ssl_enabled')->default(true);
            $table->string('department')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};