<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WifiNetwork;
use App\Models\InternetProvider;
use Carbon\Carbon;

class WifiNetworkTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some providers
        $biznet = InternetProvider::where('name', 'Biznet')->first();
        $indihome = InternetProvider::where('name', 'IndiHome')->first();
        $firstMedia = InternetProvider::where('name', 'First Media')->first();
        $myrepublic = InternetProvider::where('name', 'MyRepublic')->first();

        $wifiNetworks = [
            [
                'ssid' => 'OFFICE_MAIN_5G',
                'password' => 'OfficeMain2024!',
                'location' => 'Lantai 1 - Reception',
                'router_ip' => '192.168.1.1',
                'provider_id' => $biznet?->id,
                'service_expiry_date' => now(), // Expired today
                'monthly_cost' => 500000,
                'contract_start_date' => now()->subYear(),
                'status' => 'active',
                'notes' => 'WiFi utama untuk area reception dan meeting room'
            ],
            [
                'ssid' => 'OFFICE_FL2_5G',
                'password' => 'Floor2Secure!',
                'location' => 'Lantai 2 - Workspace',
                'router_ip' => '192.168.2.1',
                'provider_id' => $indihome?->id,
                'service_expiry_date' => now()->addDays(2), // Expiring in 2 days
                'monthly_cost' => 350000,
                'contract_start_date' => now()->subMonths(10),
                'status' => 'active',
                'notes' => 'WiFi untuk workspace lantai 2'
            ],
            [
                'ssid' => 'GUEST_WIFI',
                'password' => 'Guest123!',
                'location' => 'Lobby Area',
                'router_ip' => '192.168.3.1',
                'provider_id' => $firstMedia?->id,
                'service_expiry_date' => now()->addDays(5), // Expiring in 5 days
                'monthly_cost' => 250000,
                'contract_start_date' => now()->subMonths(8),
                'status' => 'active',
                'notes' => 'WiFi untuk tamu dan visitor'
            ],
            [
                'ssid' => 'BACKUP_NET',
                'password' => 'BackupSecure2024',
                'location' => 'Server Room',
                'router_ip' => '192.168.4.1',
                'provider_id' => $myrepublic?->id,
                'service_expiry_date' => now()->addDays(15), // Expiring in 15 days
                'monthly_cost' => 400000,
                'contract_start_date' => now()->subMonths(6),
                'status' => 'active',
                'notes' => 'Koneksi backup untuk redundancy'
            ],
            [
                'ssid' => 'CONF_ROOM_5G',
                'password' => 'Conference2024!',
                'location' => 'Conference Room A',
                'router_ip' => '192.168.5.1',
                'provider_id' => $biznet?->id,
                'service_expiry_date' => now()->addDays(45), // Expiring in 45 days
                'monthly_cost' => 600000,
                'contract_start_date' => now()->subMonths(3),
                'status' => 'active',
                'notes' => 'WiFi khusus untuk conference room dengan bandwidth tinggi'
            ],
            [
                'ssid' => 'WAREHOUSE_NET',
                'password' => 'Warehouse123',
                'location' => 'Warehouse Area',
                'router_ip' => '192.168.6.1',
                'provider_id' => $indihome?->id,
                'service_expiry_date' => now()->subDays(5), // Already expired 5 days ago
                'monthly_cost' => 300000,
                'contract_start_date' => now()->subYear()->subMonths(2),
                'status' => 'active',
                'notes' => 'WiFi untuk area warehouse dan inventory'
            ]
        ];

        foreach ($wifiNetworks as $wifi) {
            WifiNetwork::create($wifi);
        }

        $this->command->info('WiFi Network test data created successfully!');
    }
}
