<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InternetProvider;
use App\Models\ProviderContract;
use App\Models\WifiNetwork;
use Carbon\Carbon;

class ProviderContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing providers
        $indihome = InternetProvider::where('name', 'IndiHome')->first();
        $biznet = InternetProvider::where('name', 'Biznet')->first();
        
        if (!$indihome || !$biznet) {
            $this->command->info('Creating sample providers...');
            
            $indihome = InternetProvider::create([
                'name' => 'IndiHome',
                'contact_person' => 'Customer Service',
                'phone' => '147',
                'email' => 'cs@telkom.co.id',
                'website' => 'https://indihome.co.id',
                'notes' => 'Provider internet fiber dari Telkom Indonesia',
                'status' => 'active'
            ]);
            
            $biznet = InternetProvider::create([
                'name' => 'Biznet',
                'contact_person' => 'Sales Team',
                'phone' => '1500-988',
                'email' => 'info@biznetnetworks.com',
                'website' => 'https://www.biznetnetworks.com',
                'notes' => 'Provider internet fiber untuk bisnis',
                'status' => 'active'
            ]);
        }
        
        // Create contracts for different companies
        $contracts = [
            [
                'provider_id' => $indihome->id,
                'company_name' => 'PT. Tech Solutions',
                'monthly_cost' => 500000,
                'bandwidth_mbps' => 100,
                'speed_package' => '100 Mbps',
                'connection_type' => 'Fiber Optic',
                'contract_start_date' => Carbon::now()->subMonths(6),
                'service_expiry_date' => Carbon::now()->addDays(5), // Expiring soon
                'contract_duration_months' => 12,
                'contract_status' => 'active'
            ],
            [
                'provider_id' => $indihome->id,
                'company_name' => 'CV. Digital Media',
                'monthly_cost' => 750000,
                'bandwidth_mbps' => 200,
                'speed_package' => '200 Mbps',
                'connection_type' => 'Fiber Optic',
                'contract_start_date' => Carbon::now()->subMonths(3),
                'service_expiry_date' => Carbon::now()->addDays(15), // Expiring in 15 days
                'contract_duration_months' => 24,
                'contract_status' => 'active'
            ],
            [
                'provider_id' => $biznet->id,
                'company_name' => 'PT. Enterprise Solutions',
                'monthly_cost' => 1200000,
                'bandwidth_mbps' => 500,
                'speed_package' => '500 Mbps',
                'connection_type' => 'Dedicated Fiber',
                'contract_start_date' => Carbon::now()->subMonths(8),
                'service_expiry_date' => Carbon::now()->addDays(25), // Expiring in 25 days
                'contract_duration_months' => 12,
                'contract_status' => 'active'
            ],
            [
                'provider_id' => $biznet->id,
                'company_name' => 'PT. Startup Inovasi',
                'monthly_cost' => 800000,
                'bandwidth_mbps' => 300,
                'speed_package' => '300 Mbps',
                'connection_type' => 'Dedicated Fiber',
                'contract_start_date' => Carbon::now()->subMonths(2),
                'service_expiry_date' => Carbon::now()->subDays(2), // Already expired
                'contract_duration_months' => 6,
                'contract_status' => 'active'
            ]
        ];
        
        foreach ($contracts as $contractData) {
            $contract = ProviderContract::updateOrCreate(
                [
                    'provider_id' => $contractData['provider_id'],
                    'company_name' => $contractData['company_name']
                ],
                $contractData
            );
            
            // Create sample WiFi networks for each contract
            WifiNetwork::create([
                'ssid' => $contractData['company_name'] . '_WiFi',
                'password' => 'password123',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz',
                'channel' => 6,
                'location' => 'Office - ' . $contractData['company_name'],
                'router_brand' => 'TP-Link',
                'router_model' => 'Archer C6',
                'router_ip' => '192.168.1.1',
                'admin_username' => 'admin',
                'admin_password' => 'admin123',
                'max_devices' => 50,
                'guest_network' => true,
                'guest_ssid' => $contractData['company_name'] . '_Guest',
                'guest_password' => 'guest123',
                'notes' => 'WiFi network for ' . $contractData['company_name'],
                'status' => 'active',
                'provider_id' => $contractData['provider_id'],
                'contract_id' => $contract->id
            ]);
        }
        
        $this->command->info('Provider contracts and WiFi networks created successfully!');
    }
}