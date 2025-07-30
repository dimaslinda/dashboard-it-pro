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
        Schema::table('asset_surveys', function (Blueprint $table) {
            // REKANUSA specific survey fields
            $table->integer('condition_good_count')->default(0)->after('functional_status'); // Jumlah kondisi baik
            $table->integer('condition_bad_count')->default(0)->after('condition_good_count'); // Jumlah kondisi buruk
            $table->enum('availability_status', ['available', 'not_available', 'partial'])->default('available')->after('condition_bad_count'); // Status ketersediaan
            $table->enum('usage_frequency', ['daily', 'weekly', 'monthly', 'rarely', 'never'])->nullable()->after('availability_status'); // Frekuensi penggunaan
            $table->text('maintenance_needs')->nullable()->after('usage_frequency'); // Kebutuhan maintenance spesifik
            $table->json('checklist_results')->nullable()->after('maintenance_needs'); // Hasil checklist detail
            $table->string('surveyor_name')->nullable()->after('user_id'); // Nama surveyor
            $table->string('surveyor_position')->nullable()->after('surveyor_name'); // Posisi surveyor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'condition_good_count',
                'condition_bad_count',
                'availability_status',
                'usage_frequency',
                'maintenance_needs',
                'checklist_results',
                'surveyor_name',
                'surveyor_position'
            ]);
        });
    }
};
