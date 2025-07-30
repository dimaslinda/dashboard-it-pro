<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create IT Dashboard tenant
        $itTenant = Tenant::create([
            'name' => 'IT Dashboard',
            'slug' => 'it-dashboard',
            'settings' => [
                'theme_color' => 'amber',
                'logo' => null,
                'features' => [
                    'wifi_management' => true,
                    'equipment_tracking' => true,
                    'invoice_management' => true,
                    'website_monitoring' => true,
                    'email_accounts' => true,
                ],
                'dashboard_title' => 'IT Management Dashboard',
            ],
            'is_active' => true,
        ]);

        // Create Asset Survey tenant
        $assetTenant = Tenant::create([
            'name' => 'Asset Survey',
            'slug' => 'asset-survey',
            'settings' => [
                'theme_color' => 'blue',
                'logo' => null,
                'features' => [
                    'asset_tracking' => true,
                    'survey_management' => true,
                    'location_mapping' => true,
                    'condition_assessment' => true,
                    'reporting' => true,
                ],
                'dashboard_title' => 'Asset Survey Management',
            ],
            'is_active' => true,
        ]);

        // Update existing users to belong to IT Dashboard tenant
        User::whereNull('tenant_id')->update(['tenant_id' => $itTenant->id]);

        // Create sample users for Asset Survey tenant
        User::create([
            'tenant_id' => $assetTenant->id,
            'name' => 'Asset Manager',
            'email' => 'asset@kaizenkonsultan.co.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        User::create([
            'tenant_id' => $assetTenant->id,
            'name' => 'Survey Coordinator',
            'email' => 'survey@kaizenkonsultan.co.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }
}