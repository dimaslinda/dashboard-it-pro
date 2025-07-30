<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign keys and indexes manually
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Drop indexes if they exist
        try {
            DB::statement('ALTER TABLE assets DROP INDEX assets_tenant_id_status_index');
        } catch (Exception $e) {}
        try {
            DB::statement('ALTER TABLE assets DROP INDEX assets_tenant_id_condition_index');
        } catch (Exception $e) {}
        try {
            DB::statement('ALTER TABLE assets DROP INDEX assets_tenant_id_category_index');
        } catch (Exception $e) {}
        
        // Drop foreign key if it exists
        try {
            DB::statement('ALTER TABLE assets DROP FOREIGN KEY assets_tenant_id_foreign');
        } catch (Exception $e) {}
        
        Schema::table('assets', function (Blueprint $table) {
            // Drop tenant_id column
            $table->dropColumn('tenant_id');
            
            // Add new indexes without tenant_id
            $table->index('status');
            $table->index('condition');
            $table->index('category');
            $table->index('asset_code'); // For grouping by asset code prefix
        });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Drop indexes if they exist
        try {
            DB::statement('ALTER TABLE asset_surveys DROP INDEX asset_surveys_tenant_id_survey_date_index');
        } catch (Exception $e) {}
        try {
            DB::statement('ALTER TABLE asset_surveys DROP INDEX asset_surveys_tenant_id_status_index');
        } catch (Exception $e) {}
        try {
            DB::statement('ALTER TABLE asset_surveys DROP INDEX asset_surveys_tenant_id_survey_type_index');
        } catch (Exception $e) {}
        
        // Drop foreign key if it exists
        try {
            DB::statement('ALTER TABLE asset_surveys DROP FOREIGN KEY asset_surveys_tenant_id_foreign');
        } catch (Exception $e) {}
        
        Schema::table('asset_surveys', function (Blueprint $table) {
            // Drop tenant_id column
            $table->dropColumn('tenant_id');
            
            // Add new indexes without tenant_id
            $table->index('survey_date');
            $table->index('status');
            $table->index('survey_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop new indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['condition']);
            $table->dropIndex(['category']);
            $table->dropIndex(['asset_code']);
            
            // Add tenant_id column back
            $table->foreignId('tenant_id')->after('id')->constrained('tenants')->onDelete('cascade');
            
            // Add tenant-based indexes back
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'condition']);
            $table->index(['tenant_id', 'category']);
        });
        
        Schema::table('asset_surveys', function (Blueprint $table) {
            // Drop new indexes
            $table->dropIndex(['survey_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['survey_type']);
            
            // Add tenant_id column back
            $table->foreignId('tenant_id')->after('id')->constrained('tenants')->onDelete('cascade');
            
            // Add tenant-based indexes back
            $table->index(['tenant_id', 'survey_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'survey_type']);
        });
    }
};