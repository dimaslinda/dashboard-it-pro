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
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('borrower_name');
            $table->string('borrower_position')->nullable();
            $table->string('pic_name'); // Person in Charge
            $table->string('pic_contact')->nullable();
            $table->date('loan_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'out', 'in', 'overdue', 'rejected'])->default('pending');
            $table->text('purpose')->nullable();
            $table->string('location_used')->nullable();
            $table->integer('calibration_count')->default(0);
            $table->date('calibration_date')->nullable();
            $table->enum('condition_out', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->enum('condition_in', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approval_date')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['asset_id', 'status']);
            $table->index(['loan_date', 'expected_return_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};