<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InternetProvider;

class InternetProviderUpdateSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Update existing providers with cost and service information
        $providers = [
            [
                'name' => 'Telkom Indonesia',
                'monthly_cost' => 350000,
                'installation_cost' => 150000,
                'speed_package' => '100 Mbps Unlimited',
                'bandwidth_mbps' => 100,
                'connection_type' => 'fiber',
            ],
            [
                'name' => 'Indihome',
                'monthly_cost' => 389000,
                'installation_cost' => 200000,
                'speed_package' => '100 Mbps Unlimited',
                'bandwidth_mbps' => 100,
                'connection_type' => 'fiber',
            ],
            [
                'name' => 'Biznet',
                'monthly_cost' => 450000,
                'installation_cost' => 100000,
                'speed_package' => '150 Mbps Unlimited',
                'bandwidth_mbps' => 150,
                'connection_type' => 'fiber',
            ],
            [
                'name' => 'First Media',
                'monthly_cost' => 299000,
                'installation_cost' => 250000,
                'speed_package' => '50 Mbps Unlimited',
                'bandwidth_mbps' => 50,
                'connection_type' => 'cable',
            ],
            [
                'name' => 'MyRepublic',
                'monthly_cost' => 349000,
                'installation_cost' => 0,
                'speed_package' => '100 Mbps Unlimited',
                'bandwidth_mbps' => 100,
                'connection_type' => 'fiber',
            ],
        ];
        
        foreach ($providers as $providerData) {
            $provider = InternetProvider::where('name', $providerData['name'])->first();
            
            if ($provider) {
                $provider->update([
                    'monthly_cost' => $providerData['monthly_cost'],
                    'installation_cost' => $providerData['installation_cost'],
                    'speed_package' => $providerData['speed_package'],
                    'bandwidth_mbps' => $providerData['bandwidth_mbps'],
                    'connection_type' => $providerData['connection_type'],
                ]);
                
                $this->command->info("Updated provider: {$provider->name}");
            } else {
                // Create new provider if doesn't exist
                InternetProvider::create([
                    'name' => $providerData['name'],
                    'monthly_cost' => $providerData['monthly_cost'],
                    'installation_cost' => $providerData['installation_cost'],
                    'speed_package' => $providerData['speed_package'],
                    'bandwidth_mbps' => $providerData['bandwidth_mbps'],
                    'connection_type' => $providerData['connection_type'],
                    'status' => 'active',
                ]);
                
                $this->command->info("Created new provider: {$providerData['name']}");
            }
        }
        
        $this->command->info('Internet providers updated successfully with cost and service information!');
    }
}