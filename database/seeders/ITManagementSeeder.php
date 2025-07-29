<?php

namespace Database\Seeders;

use App\Models\EmailAccount;
use App\Models\Website;
use App\Models\WifiNetwork;
use App\Models\Equipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ITManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Email Accounts
        EmailAccount::create([
            'email' => 'admin@company.com',
            'password' => 'SecurePass123',
            'provider' => 'Google Workspace',
            'smtp_server' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'imap_server' => 'imap.gmail.com',
            'imap_port' => 993,
            'ssl_enabled' => true,
            'department' => 'IT',
            'assigned_to' => 'John Doe',
            'status' => 'active',
            'notes' => 'Main admin email account'
        ]);

        EmailAccount::create([
            'email' => 'support@company.com',
            'password' => 'Support2024',
            'provider' => 'Microsoft 365',
            'smtp_server' => 'smtp.office365.com',
            'smtp_port' => 587,
            'imap_server' => 'outlook.office365.com',
            'imap_port' => 993,
            'ssl_enabled' => true,
            'department' => 'Customer Service',
            'assigned_to' => 'Jane Smith',
            'status' => 'active',
            'notes' => 'Customer support email'
        ]);

        // Websites
        Website::create([
            'name' => 'Company Website',
            'url' => 'https://company.com',
            'domain' => 'company.com',
            'hosting_provider' => 'DigitalOcean',
            'hosting_expiry' => now()->addMonths(6),
            'registrar' => 'Namecheap',
            'domain_expiry' => now()->addYear(),
            'admin_username' => 'admin',
            'admin_password' => 'AdminPass123',
            'ftp_host' => 'ftp.company.com',
            'ftp_username' => 'ftpuser',
            'ftp_password' => 'FtpPass123',
            'ftp_port' => 21,
            'database_host' => 'localhost',
            'database_name' => 'company_db',
            'database_username' => 'dbuser',
            'database_password' => 'DbPass123',
            'status' => 'active',
            'notes' => 'Main company website'
        ]);

        Website::create([
            'name' => 'E-commerce Store',
            'url' => 'https://shop.company.com',
            'domain' => 'shop.company.com',
            'hosting_provider' => 'AWS',
            'hosting_expiry' => now()->addDays(15), // Expiring soon
            'registrar' => 'GoDaddy',
            'domain_expiry' => now()->addMonths(8),
            'admin_username' => 'shopAdmin',
            'admin_password' => 'ShopPass123',
            'ftp_host' => 'ftp.shop.company.com',
            'ftp_username' => 'shopFtp',
            'ftp_password' => 'ShopFtp456',
            'ftp_port' => 21,
            'database_host' => 'aws-rds.amazonaws.com',
            'database_name' => 'shop_database',
            'database_username' => 'shopUser',
            'database_password' => 'ShopDb789',
            'status' => 'active',
            'notes' => 'Online store platform'
        ]);

        Website::create([
            'name' => 'Company Blog',
            'url' => 'https://blog.company.com',
            'domain' => 'blog.company.com',
            'hosting_provider' => 'Bluehost',
            'hosting_expiry' => now()->addMonths(3),
            'registrar' => 'Domain.com',
            'domain_expiry' => now()->addDays(30), // Expiring soon
            'admin_username' => 'blogAdmin',
            'admin_password' => 'BlogPass789',
            'ftp_host' => 'ftp.blog.company.com',
            'ftp_username' => 'blogFtp',
            'ftp_password' => 'BlogFtp123',
            'ftp_port' => 21,
            'database_host' => 'localhost',
            'database_name' => 'blog_cms',
            'database_username' => 'blogDb',
            'database_password' => 'BlogDb456',
            'status' => 'active',
            'notes' => 'Company blog and news'
        ]);

        // WiFi Networks
        WifiNetwork::create([
            'ssid' => 'CompanyWiFi-Main',
            'password' => 'CompanyWiFi2024!',
            'security_type' => 'WPA3',
            'frequency_band' => 'Dual',
            'channel' => 6,
            'location' => 'Main Office',
            'router_brand' => 'ASUS',
            'router_model' => 'AX6000',
            'router_ip' => '192.168.1.1',
            'admin_username' => 'admin',
            'admin_password' => 'RouterPass123',
            'max_devices' => 50,
            'guest_network' => true,
            'guest_ssid' => 'CompanyGuest',
            'guest_password' => 'Guest2024',
            'status' => 'active',
            'notes' => 'Main office WiFi network'
        ]);

        WifiNetwork::create([
            'ssid' => 'CompanyWiFi-Warehouse',
            'password' => 'WarehouseWiFi2024!',
            'security_type' => 'WPA2',
            'frequency_band' => '2.4GHz',
            'channel' => 11,
            'location' => 'Warehouse',
            'router_brand' => 'TP-Link',
            'router_model' => 'Archer C7',
            'router_ip' => '192.168.2.1',
            'admin_username' => 'admin',
            'admin_password' => 'WarehouseRouter123',
            'max_devices' => 20,
            'guest_network' => false,
            'status' => 'active',
            'notes' => 'Warehouse area WiFi'
        ]);

        // Equipment
        Equipment::create([
            'name' => 'Main Entrance CCTV',
            'type' => 'cctv',
            'brand' => 'Hikvision',
            'model' => 'DS-2CD2143G0-I',
            'serial_number' => 'HK001234567',
            'mac_address' => '00:11:22:33:44:55',
            'ip_address' => '192.168.1.100',
            'location' => 'Main Entrance',
            'purchase_date' => now()->subMonths(18),
            'purchase_price' => 2500000,
            'vendor' => 'Security Solutions Inc',
            'warranty_expiry' => now()->addMonths(6),
            'admin_username' => 'admin',
            'admin_password' => 'CctvPass123',
            'firmware_version' => 'V5.7.3',
            'last_maintenance' => now()->subMonths(3),
            'next_maintenance' => now()->subDays(5), // Maintenance due
            'status' => 'active',
            'specifications' => '4MP, Night Vision, Motion Detection',
            'notes' => 'Main entrance security camera'
        ]);

        Equipment::create([
            'name' => 'Core Network Router',
            'type' => 'router',
            'brand' => 'Cisco',
            'model' => 'ISR4331',
            'serial_number' => 'CS987654321',
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'ip_address' => '192.168.1.1',
            'location' => 'Server Room',
            'purchase_date' => now()->subYear(),
            'purchase_price' => 15000000,
            'vendor' => 'Cisco Partner',
            'warranty_expiry' => now()->addYears(2),
            'admin_username' => 'netadmin',
            'admin_password' => 'NetworkPass123',
            'firmware_version' => 'IOS XE 16.12.04',
            'last_maintenance' => now()->subMonths(6),
            'next_maintenance' => now()->addMonths(6),
            'status' => 'active',
            'specifications' => 'Gigabit Ethernet, VPN Support, QoS',
            'notes' => 'Main network router for office'
        ]);

        Equipment::create([
            'name' => 'Parking Lot CCTV #1',
            'type' => 'cctv',
            'brand' => 'Dahua',
            'model' => 'IPC-HFW4431R-Z',
            'serial_number' => 'DH123456789',
            'ip_address' => '192.168.1.101',
            'location' => 'Parking Lot Area A',
            'purchase_date' => now()->subMonths(12),
            'purchase_price' => 1800000,
            'vendor' => 'Security Tech',
            'warranty_expiry' => now()->addMonths(12),
            'admin_username' => 'admin',
            'admin_password' => 'ParkingCctv123',
            'firmware_version' => 'V2.800.0000000.25.R',
            'last_maintenance' => now()->subMonths(2),
            'next_maintenance' => now()->addMonths(4),
            'status' => 'active',
            'specifications' => '4MP, Varifocal Lens, IR 80m',
            'notes' => 'Monitors parking area A'
        ]);

        Equipment::create([
            'name' => 'Office Printer HP LaserJet',
            'type' => 'printer',
            'brand' => 'HP',
            'model' => 'LaserJet Pro M404dn',
            'serial_number' => 'HP987654321',
            'ip_address' => '192.168.1.150',
            'location' => 'Main Office',
            'purchase_date' => now()->subMonths(8),
            'purchase_price' => 3500000,
            'vendor' => 'HP Authorized Dealer',
            'warranty_expiry' => now()->addMonths(16),
            'last_maintenance' => now()->subMonths(1),
            'next_maintenance' => now()->addMonths(5),
            'status' => 'active',
            'specifications' => 'Duplex, Network, 38ppm',
            'notes' => 'Main office printer for documents'
        ]);
    }
}
