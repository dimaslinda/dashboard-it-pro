<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user = Role::firstOrCreate(['name' => 'user']);
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to super_admin
        $superAdmin->syncPermissions($allPermissions);
        
        // Assign specific permissions to admin (all except user management)
        $adminPermissions = $allPermissions->filter(function ($permission) {
            return !str_contains($permission->name, 'role::') && 
                   !str_contains($permission->name, 'permission::') &&
                   !str_contains($permission->name, 'user::delete');
        });
        $admin->syncPermissions($adminPermissions);
        
        // Assign view and create permissions to manager
        $managerPermissions = $allPermissions->filter(function ($permission) {
            return str_contains($permission->name, '::view') || 
                   str_contains($permission->name, '::create') ||
                   str_contains($permission->name, '::update');
        });
        $manager->syncPermissions($managerPermissions);
        
        // Assign only view permissions to user
        $userPermissions = $allPermissions->filter(function ($permission) {
            return str_contains($permission->name, '::view');
        });
        $user->syncPermissions($userPermissions);
        
        // Assign super_admin role to admin@admin.com if exists
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }
        
        $this->command->info('Roles and permissions seeded successfully!');
    }
}