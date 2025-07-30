<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in order
        $this->call([
            CompanySeeder::class,
            RekanuseAssetSeeder::class,
            InternetProviderSeeder::class,
            ProviderContractSeeder::class,
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            ITManagementSeeder::class,
            InvoiceSeeder::class,
            RealDataSeeder::class,
            WebsiteExpirySeeder::class,
            // Note: Excluded update/migration seeders and test seeders:
            // - InternetProviderContractSeeder (contract specific)
            // - InternetProviderUpdateSeeder (update specific)
            // - ProviderContractSeeder (contract specific)
            // - TenantSeeder (tenant specific)
            // - UpdateAssetCompanySeeder (update specific)
            // - WifiNetworkTestSeeder (test data)
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
