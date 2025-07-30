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
        Schema::create('asset_procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null'); // Optional: if replacing existing asset
            $table->string('requester_name');
            $table->string('requester_position')->nullable();
            $table->date('request_date');
            $table->string('item_name');
            $table->text('item_specification')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->text('justification');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('budget_source')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->enum('status', [
                'pending', 
                'approved', 
                'rejected', 
                'ordered', 
                'delivered', 
                'completed'
            ])->default('pending');
            $table->text('approval_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approval_date')->nullable();
            $table->string('purchase_order_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->enum('condition_received', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->string('warranty_period')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'urgency_level']);
            $table->index(['request_date', 'expected_delivery_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_procurements');
    }
};