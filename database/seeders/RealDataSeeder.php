<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailAccount;
use App\Models\Website;
use App\Models\WifiNetwork;
use App\Models\Equipment;
use Illuminate\Support\Facades\Hash;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data perusahaan dari SQL dump
        $companies = [
            1 => 'inovasika',
            2 => 'Kaizen Enjiniring Nusantara', 
            3 => 'Kinarya Kompegriti Rekanusa',
            4 => 'Maestro Realty',
            5 => 'Maestro Kontraktor',
            6 => 'Holding'
        ];

        // Seed Email Accounts dari tabel emails
        $emailData = [
            ['email' => 'itwebkinarya@gmail.com', 'password' => 'mimpi7menara', 'company' => 'Holding', 'provider' => 'Gmail'],
            ['email' => 'cctvrekanusa@gmail.com', 'password' => 'mimpi7menara#', 'company' => 'Holding', 'provider' => 'Gmail'],
            ['email' => 'cctvkaizen66@gmail.com', 'password' => 'Satriok605#', 'company' => 'Holding', 'provider' => 'Gmail'],
            ['email' => 'kinaryamaestronusantara@gmail.com', 'password' => 'maestrokontraktor2023', 'company' => 'Maestro Kontraktor', 'provider' => 'Gmail'],
            ['email' => 'inovasikadigital@gmail.com', 'password' => 'BupatiGK#2029', 'company' => 'inovasika', 'provider' => 'Gmail'],
            ['email' => 'ikualiva@gmail.com', 'password' => '#inovasihargamati', 'company' => 'inovasika', 'provider' => 'Gmail'],
            ['email' => 'najmainstitute@gmail.com', 'password' => 'Biggerdreambig2', 'company' => 'inovasika', 'provider' => 'Gmail'],
            ['email' => 'cctvmaestro5@gmail.com', 'password' => 'Satriok605#', 'company' => 'Maestro Kontraktor', 'provider' => 'Gmail'],
            ['email' => 'cctvinovasikanew@gmail.com', 'password' => 'Satriok605#', 'company' => 'inovasika', 'provider' => 'Gmail'],
        ];

        foreach ($emailData as $email) {
            EmailAccount::create([
                'email' => $email['email'],
                'password' => $email['password'],
                'provider' => $email['provider'],
                'status' => 'active',
                'department' => $email['company'],
                'notes' => 'Email untuk ' . $email['company'],
            ]);
        }

        // Seed Websites dari tabel webs
        $websiteData = [
            [
                'name' => 'Niagahoster VPN -> jagoanhosting.com',
                'url' => 'https://jagoanhosting.com',
                'domain' => 'jagoanhosting.com',
                'status' => 'active',
                'hosting_provider' => 'Niagahoster',
                'registrar' => 'Niagahoster',
                'admin_username' => 'kaizenkonsultan@gmail.com',
                'admin_password' => 'Mimpi7menara@12345',
                'database_host' => 'localhost',
                'database_name' => 'jagoanhosting_db',
                'database_username' => 'jagoan_user',
                'database_password' => 'Mimpi7menara@12345',
            ],
            [
                'name' => 'Kaizen Konsultan cPanel',
                'url' => 'https://kaizenkonsultan.co.id:2083',
                'domain' => 'kaizenkonsultan.co.id',
                'status' => 'active',
                'hosting_provider' => 'Custom',
                'registrar' => 'Custom',
                'admin_username' => 'k5772372',
                'admin_password' => 'mimpi7menara@123456',
                'database_host' => 'localhost',
                'database_name' => 'kaizen_db',
                'database_username' => 'k5772372',
                'database_password' => 'mimpi7menara@123456',
            ],
            [
                'name' => 'Kaizen WHM',
                'url' => 'https://kaizenkonsultan.co.id:2087',
                'domain' => 'kaizenkonsultan.co.id',
                'status' => 'active',
                'hosting_provider' => 'Custom',
                'registrar' => 'Custom',
                'admin_username' => 'root',
                'admin_password' => '08^RMXhaareez',
                'database_host' => 'localhost',
                'database_name' => 'whm_db',
                'database_username' => 'root',
                'database_password' => '08^RMXhaareez',
            ],
            [
                'name' => 'Rekanusa Website',
                'url' => 'https://rekanusa.co.id:2083',
                'domain' => 'rekanusa.co.id',
                'status' => 'active',
                'hosting_provider' => 'Niagahoster',
                'registrar' => 'Niagahoster',
                'admin_username' => 'rekanusa',
                'admin_password' => 'MIMPI7menara#',
                'database_host' => 'localhost',
                'database_name' => 'rekanusa_db',
                'database_username' => 'rekanusa',
                'database_password' => 'MIMPI7menara#',
            ],
            [
                'name' => 'Kelas UI/UX',
                'url' => 'https://kelasuiux.com:2083',
                'domain' => 'kelasuiux.com',
                'status' => 'active',
                'hosting_provider' => 'Custom',
                'registrar' => 'Custom',
                'admin_username' => 'u1005762',
                'admin_password' => 'MIMPI7menara#',
                'database_host' => 'localhost',
                'database_name' => 'kelasuiux_db',
                'database_username' => 'u1005762',
                'database_password' => 'MIMPI7menara#',
            ],
            [
                'name' => 'Maestro Kontraktor',
                'url' => 'https://maestrokontraktor.com:2083',
                'domain' => 'maestrokontraktor.com',
                'status' => 'active',
                'hosting_provider' => 'Niagahoster',
                'registrar' => 'Niagahoster',
                'admin_username' => 'u1012852',
                'admin_password' => 'DONGO@rasta12345',
                'database_host' => 'localhost',
                'database_name' => 'maestro_db',
                'database_username' => 'u1012852',
                'database_password' => 'DONGO@rasta12345',
            ],
        ];

        foreach ($websiteData as $website) {
            Website::create($website);
        }

        // Seed WiFi Networks dari tabel admin_routers
        $wifiData = [
            [
                'ssid' => 'TPLINK_ARCHER_AX10_A',
                'password' => 'zeeraah@12345',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz/5GHz',
                'channel' => 1,
                'status' => 'active',
                'location' => 'Holding Office',
                'router_brand' => 'TP-Link',
                'router_model' => 'Archer AX10 AC1500',
                'router_ip' => '192.168.1.1',
                'admin_username' => 'hhaareez@gmail.com',
                'admin_password' => 'zeeraah@12345',
                'guest_network' => true,
                'guest_ssid' => 'TPLINK_GUEST_A',
                'guest_password' => 'guest123',
            ],
            [
                'ssid' => 'HUAWEI_BIZNET_EG8145V5',
                'password' => 'adminEP',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz',
                'channel' => 6,
                'status' => 'active',
                'location' => 'Inovasika Office',
                'router_brand' => 'Huawei',
                'router_model' => 'EchoLife EG8145V5',
                'router_ip' => '192.168.100.1',
                'admin_username' => 'Epadmin',
                'admin_password' => 'adminEP',
                'guest_network' => false,
                'guest_ssid' => null,
                'guest_password' => null,
            ],
            [
                'ssid' => 'TPLINK_ARCHER_C80_A',
                'password' => 'haareez@12345',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz/5GHz',
                'channel' => 11,
                'status' => 'active',
                'location' => 'Holding Office 2',
                'router_brand' => 'TP-Link',
                'router_model' => 'Archer C80 AC1900',
                'router_ip' => '192.168.1.1',
                'admin_username' => 'laporanipaddress@gmail.com',
                'admin_password' => 'haareez@12345',
                'guest_network' => true,
                'guest_ssid' => 'TPLINK_GUEST_C80',
                'guest_password' => 'guest456',
            ],
            [
                'ssid' => 'UNIFI_UAP_AC_LR',
                'password' => 'haareez@12345',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz/5GHz',
                'channel' => 11,
                'status' => 'active',
                'location' => 'Inovasika Office 2',
                'router_brand' => 'Ubiquiti',
                'router_model' => 'UniFi UAP AC LR Lite',
                'router_ip' => '192.168.1.245',
                'admin_username' => 'haareez2021',
                'admin_password' => 'haareez@12345',
                'guest_network' => false,
                'guest_ssid' => null,
                'guest_password' => null,
            ],
            [
                'ssid' => 'MIKROTIK_RB951Ui',
                'password' => 'haareez@12345',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz',
                'channel' => 1,
                'status' => 'active',
                'location' => 'Inovasika Office 3',
                'router_brand' => 'MikroTik',
                'router_model' => 'RB951Ui-2nd',
                'router_ip' => '192.168.88.1',
                'admin_username' => 'harris',
                'admin_password' => 'haareez@12345',
                'guest_network' => false,
                'guest_ssid' => null,
                'guest_password' => null,
            ],
            [
                'ssid' => 'HUAWEI_FIBERHOME_HG6145F',
                'password' => '%0|F?H@f!berhO3e',
                'security_type' => 'WPA2',
                'frequency_band' => '2.4GHz',
                'channel' => 6,
                'status' => 'active',
                'location' => 'Holding Office 3',
                'router_brand' => 'Huawei',
                'router_model' => 'XL HG6145F FIBERHOME',
                'router_ip' => '192.168.1.1',
                'admin_username' => 'admin',
                'admin_password' => '%0|F?H@f!berhO3e',
                'guest_network' => false,
                'guest_ssid' => null,
                'guest_password' => null,
            ],
        ];

        foreach ($wifiData as $wifi) {
            WifiNetwork::create($wifi);
        }

        // Seed Equipment dari data router yang ada
        $equipmentData = [
            [
                'name' => 'TP-Link Archer AX10 AC1500 (A)',
                'type' => 'router',
                'brand' => 'TP-Link',
                'model' => 'Archer AX10',
                'serial_number' => '2229371001921',
                'mac_address' => 'AC-15-A2-12-2E-30',
                'ip_address' => '192.168.1.1',
                'location' => 'Holding Office',
                'status' => 'active',
                'purchase_date' => '2023-09-15',
                'warranty_expiry' => '2025-09-15',
                'admin_username' => 'hhaareez@gmail.com',
                'admin_password' => 'zeeraah@12345',
                'notes' => 'Router utama untuk Holding Office',
            ],
            [
                'name' => 'Huawei EchoLife EG8145V5',
                'type' => 'other',
                'brand' => 'Huawei',
                'model' => 'EG8145V5',
                'serial_number' => '48575443846A30A7',
                'mac_address' => 'CC-B1-82-A6-3E-81-91',
                'ip_address' => '192.168.100.1',
                'location' => 'Inovasika Office',
                'status' => 'active',
                'purchase_date' => '2023-09-15',
                'warranty_expiry' => '2025-09-15',
                'admin_username' => 'Epadmin',
                'admin_password' => 'adminEP',
                'notes' => 'Modem Biznet untuk Inovasika',
            ],
            [
                'name' => 'TP-Link Archer C80 AC1900 (A)',
                'type' => 'router',
                'brand' => 'TP-Link',
                'model' => 'Archer C80',
                'serial_number' => '22241E3002772',
                'mac_address' => '28-87-BA-7C-C3-71',
                'ip_address' => '192.168.1.1',
                'location' => 'Holding Office 2',
                'status' => 'active',
                'purchase_date' => '2023-09-15',
                'warranty_expiry' => '2025-09-15',
                'admin_username' => 'laporanipaddress@gmail.com',
                'admin_password' => 'haareez@12345',
                'notes' => 'Router backup untuk Holding Office',
            ],
            [
                'name' => 'UniFi UAP AC LR Lite',
                'type' => 'other',
                'brand' => 'Ubiquiti',
                'model' => 'UAP AC LR Lite',
                'serial_number' => 'ON PROGRESS',
                'mac_address' => '68-D7-9A-19-CC-04',
                'ip_address' => '192.168.1.245',
                'location' => 'Inovasika Office 2',
                'status' => 'active',
                'purchase_date' => '2023-09-15',
                'warranty_expiry' => '2025-09-15',
                'admin_username' => 'haareez2021',
                'admin_password' => 'haareez@12345',
                'notes' => 'Access Point untuk coverage area luas',
            ],
            [
                'name' => 'MikroTik RB951Ui-2nd',
                'type' => 'router',
                'brand' => 'MikroTik',
                'model' => 'RB951Ui-2nd',
                'serial_number' => 'ED330F192F1B206r2',
                'mac_address' => 'DC-2C-6E-BF-53-4F',
                'ip_address' => '192.168.88.1',
                'location' => 'Inovasika Office 3',
                'status' => 'active',
                'purchase_date' => '2023-09-15',
                'warranty_expiry' => '2025-09-15',
                'admin_username' => 'harris',
                'admin_password' => 'haareez@12345',
                'notes' => 'Router MikroTik untuk network advanced',
            ],
            [
                'name' => 'Nokia Beacon 1 HA-020W-B',
                'type' => 'router',
                'brand' => 'Nokia',
                'model' => 'Beacon 1 HA-020W-B',
                'serial_number' => 'ALCLEB2FF553',
                'mac_address' => 'DC-BD-BA-75-00-B1',
                'ip_address' => '192.168.1.1',
                'location' => 'Rekanusa Office',
                'status' => 'active',
                'purchase_date' => '2023-09-18',
                'warranty_expiry' => '2025-09-18',
                'admin_username' => 'admin',
                'admin_password' => 'PTq4HqtBKm',
                'notes' => 'Router Nokia untuk Rekanusa',
            ],
        ];

        foreach ($equipmentData as $equipment) {
            Equipment::create($equipment);
        }
    }
}