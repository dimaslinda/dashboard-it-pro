<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get super_admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        
        // Create or get asset_survey role
        $assetSurveyRole = Role::firstOrCreate(['name' => 'asset_survey']);
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to super_admin
        $superAdminRole->syncPermissions($allPermissions);
        
        // Assign asset-related permissions to asset_survey role
        $assetPermissions = $allPermissions->filter(function ($permission) {
            return str_contains($permission->name, 'asset') ||
                   str_contains($permission->name, 'company') ||
                   str_contains($permission->name, 'AssetResource') ||
                   str_contains($permission->name, 'AssetLoanResource') ||
                   str_contains($permission->name, 'AssetProcurementResource') ||
                   str_contains($permission->name, 'AssetSurveyResource');
        });
        $assetSurveyRole->syncPermissions($assetPermissions);
        
        // Get or create IT Dashboard tenant
        $itTenant = Tenant::firstOrCreate(
            ['slug' => 'it-dashboard'],
            [
                'name' => 'IT Dashboard',
                'settings' => [
                    'theme_color' => 'amber',
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
            ]
        );
        
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@dashboard.com'],
            [
                'tenant_id' => $itTenant->id,
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        
        // Assign super_admin role
        $superAdmin->assignRole('super_admin');
        
        // Get or create Asset Survey tenant
        $assetTenant = Tenant::firstOrCreate(
            ['slug' => 'asset-survey'],
            [
                'name' => 'Asset Survey',
                'settings' => [
                    'theme_color' => 'blue',
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
            ]
        );
        
        // Create Asset Survey user
        $assetSurveyUser = User::firstOrCreate(
            ['email' => 'assetsurvey@dashboard.com'],
            [
                'tenant_id' => $assetTenant->id,
                'name' => 'Asset Survey Manager',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        
        // Assign asset_survey role
        $assetSurveyUser->assignRole('asset_survey');
        
        $this->command->info('Super Admin and Asset Survey users created successfully!');
        $this->command->info('Super Admin: superadmin@dashboard.com / password');
        $this->command->info('Asset Survey: assetsurvey@dashboard.com / password');
    }
}