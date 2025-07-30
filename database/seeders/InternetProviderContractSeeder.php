<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InternetProvider;
use App\Models\WifiNetwork;
use Carbon\Carbon;

class InternetProviderContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating Internet Providers with contract information...');
        
        // Get all providers and update with contract dates
        $providers = InternetProvider::all();
        
        foreach ($providers as $provider) {
            // Set random contract start date (between 6 months ago and 1 year ago)
            $contractStart = Carbon::now()->subMonths(rand(6, 12));
            
            // Set contract duration (12 or 24 months)
            $contractDuration = collect([12, 24])->random();
            
            // Calculate service expiry date
            $serviceExpiry = $contractStart->copy()->addMonths($contractDuration);
            
            // Determine contract status based on expiry date
            $contractStatus = 'active';
            if ($serviceExpiry->isPast()) {
                $contractStatus = rand(0, 1) ? 'expired' : 'active'; // Some expired contracts might still be active
            }
            
            $provider->update([
                'contract_start_date' => $contractStart,
                'contract_duration_months' => $contractDuration,
                'service_expiry_date' => $serviceExpiry,
                'contract_status' => $contractStatus,
            ]);
            
            $this->command->info("Updated {$provider->name}: Contract {$contractStart->format('d/m/Y')} - {$serviceExpiry->format('d/m/Y')} ({$contractDuration} months, {$contractStatus})");
        }
        
        // Update some providers to have expiring contracts for testing
        $testProviders = InternetProvider::take(2)->get();
        foreach ($testProviders as $provider) {
            $provider->update([
                'service_expiry_date' => Carbon::now()->addDays(rand(5, 25)),
                'contract_status' => 'active',
            ]);
            
            $this->command->info("Set {$provider->name} to expire on {$provider->service_expiry_date->format('d/m/Y')} for testing");
        }
        
        $this->command->info('Internet providers contract information updated successfully!');
    }
}