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
        Schema::table('assets', function (Blueprint $table) {
            // Add company_id foreign key
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
            
            // Drop category column since we already have tool_category
            $table->dropColumn('category');
            
            // Add index for company_id
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropColumn('company_id');
            
            // Add back category column
            $table->string('category')->after('name');
        });
    }
};