<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Website;
use Carbon\Carbon;

class WebsiteExpirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $websites = [
            // Domain expiring today
            [
                'name' => 'Company Website',
                'url' => 'https://company.com',
                'domain' => 'company.com',
                'domain_expiry' => Carbon::today(),
                'hosting_expiry' => Carbon::today()->addMonths(6),
                'registrar' => 'Namecheap',
                'hosting_provider' => 'DigitalOcean',
                'status' => 'active',
                'notes' => 'Main company website'
            ],
            // Domain expiring in 3 days
            [
                'name' => 'E-commerce Store',
                'url' => 'https://store.example.com',
                'domain' => 'store.example.com',
                'domain_expiry' => Carbon::today()->addDays(3),
                'hosting_expiry' => Carbon::today()->addMonths(3),
                'registrar' => 'GoDaddy',
                'hosting_provider' => 'AWS',
                'status' => 'active',
                'notes' => 'Online store platform'
            ],
            // Domain expiring in 7 days
            [
                'name' => 'Blog Website',
                'url' => 'https://blog.example.com',
                'domain' => 'blog.example.com',
                'domain_expiry' => Carbon::today()->addDays(7),
                'hosting_expiry' => Carbon::today()->addMonths(8),
                'registrar' => 'Cloudflare',
                'hosting_provider' => 'Vultr',
                'status' => 'active',
                'notes' => 'Company blog'
            ],
            // Domain expiring in 15 days
            [
                'name' => 'Portfolio Site',
                'url' => 'https://portfolio.dev',
                'domain' => 'portfolio.dev',
                'domain_expiry' => Carbon::today()->addDays(15),
                'hosting_expiry' => Carbon::today()->addMonths(4),
                'registrar' => 'Porkbun',
                'hosting_provider' => 'Linode',
                'status' => 'active',
                'notes' => 'Developer portfolio'
            ],
            // Domain expiring in 30 days
            [
                'name' => 'API Service',
                'url' => 'https://api.service.com',
                'domain' => 'api.service.com',
                'domain_expiry' => Carbon::today()->addDays(30),
                'hosting_expiry' => Carbon::today()->addMonths(12),
                'registrar' => 'Namecheap',
                'hosting_provider' => 'Google Cloud',
                'status' => 'active',
                'notes' => 'REST API service'
            ],
            
            // Hosting expiring today
            [
                'name' => 'Landing Page',
                'url' => 'https://landing.example.com',
                'domain' => 'landing.example.com',
                'domain_expiry' => Carbon::today()->addMonths(6),
                'hosting_expiry' => Carbon::today(),
                'registrar' => 'GoDaddy',
                'hosting_provider' => 'Hostinger',
                'status' => 'active',
                'notes' => 'Product landing page'
            ],
            // Hosting expiring in 3 days
            [
                'name' => 'Documentation Site',
                'url' => 'https://docs.example.com',
                'domain' => 'docs.example.com',
                'domain_expiry' => Carbon::today()->addMonths(8),
                'hosting_expiry' => Carbon::today()->addDays(3),
                'registrar' => 'Cloudflare',
                'hosting_provider' => 'Netlify',
                'status' => 'active',
                'notes' => 'Technical documentation'
            ],
            // Hosting expiring in 7 days
            [
                'name' => 'Demo Application',
                'url' => 'https://demo.app.com',
                'domain' => 'demo.app.com',
                'domain_expiry' => Carbon::today()->addMonths(10),
                'hosting_expiry' => Carbon::today()->addDays(7),
                'registrar' => 'Namecheap',
                'hosting_provider' => 'Heroku',
                'status' => 'active',
                'notes' => 'Demo application for clients'
            ],
            // Hosting expiring in 15 days
            [
                'name' => 'Staging Environment',
                'url' => 'https://staging.example.com',
                'domain' => 'staging.example.com',
                'domain_expiry' => Carbon::today()->addMonths(5),
                'hosting_expiry' => Carbon::today()->addDays(15),
                'registrar' => 'Porkbun',
                'hosting_provider' => 'DigitalOcean',
                'status' => 'active',
                'notes' => 'Staging server for testing'
            ],
            // Hosting expiring in 30 days
            [
                'name' => 'Analytics Dashboard',
                'url' => 'https://analytics.company.com',
                'domain' => 'analytics.company.com',
                'domain_expiry' => Carbon::today()->addMonths(9),
                'hosting_expiry' => Carbon::today()->addDays(30),
                'registrar' => 'GoDaddy',
                'hosting_provider' => 'AWS',
                'status' => 'active',
                'notes' => 'Internal analytics dashboard'
            ],
            
            // Both domain and hosting expiring soon
            [
                'name' => 'Client Portal',
                'url' => 'https://portal.client.com',
                'domain' => 'portal.client.com',
                'domain_expiry' => Carbon::today()->addDays(5),
                'hosting_expiry' => Carbon::today()->addDays(8),
                'registrar' => 'Cloudflare',
                'hosting_provider' => 'Vultr',
                'status' => 'active',
                'notes' => 'Client management portal'
            ],
            [
                'name' => 'Support System',
                'url' => 'https://support.example.com',
                'domain' => 'support.example.com',
                'domain_expiry' => Carbon::today()->addDays(12),
                'hosting_expiry' => Carbon::today()->addDays(10),
                'registrar' => 'Namecheap',
                'hosting_provider' => 'Linode',
                'status' => 'active',
                'notes' => 'Customer support ticketing system'
            ],
            
            // Safe websites (not expiring soon)
            [
                'name' => 'Main Corporate Site',
                'url' => 'https://corporate.example.com',
                'domain' => 'corporate.example.com',
                'domain_expiry' => Carbon::today()->addMonths(6),
                'hosting_expiry' => Carbon::today()->addMonths(8),
                'registrar' => 'GoDaddy',
                'hosting_provider' => 'AWS',
                'status' => 'active',
                'notes' => 'Corporate website'
            ],
            [
                'name' => 'Mobile App Backend',
                'url' => 'https://api.mobile.com',
                'domain' => 'api.mobile.com',
                'domain_expiry' => Carbon::today()->addYear(),
                'hosting_expiry' => Carbon::today()->addMonths(10),
                'registrar' => 'Cloudflare',
                'hosting_provider' => 'Google Cloud',
                'status' => 'active',
                'notes' => 'Mobile application backend API'
            ]
        ];

        foreach ($websites as $websiteData) {
            Website::updateOrCreate(
                ['domain' => $websiteData['domain']],
                $websiteData
            );
        }

        $this->command->info('Website expiry test data seeded successfully!');
        $this->command->info('Created ' . count($websites) . ' websites with various expiry dates.');
        $this->command->info('');
        $this->command->info('Test the notifications with:');
        $this->command->info('php artisan expiry:check --days=0   # Today');
        $this->command->info('php artisan expiry:check --days=3   # 3 days');
        $this->command->info('php artisan expiry:check --days=7   # 7 days');
        $this->command->info('php artisan expiry:check --days=30  # 30 days');
    }
}