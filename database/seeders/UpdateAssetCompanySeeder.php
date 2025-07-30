<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateAssetCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Rekanusa company
        $rekanusaCompany = Company::where('code', 'RKN')->first();
        
        if (!$rekanusaCompany) {
            $this->command->error('Company with code RKN not found.');
            return;
        }
        
        // Update all assets with null company_id to Rekanusa
        $updatedCount = Asset::whereNull('company_id')->update([
            'company_id' => $rekanusaCompany->id
        ]);
        
        $this->command->info("Updated {$updatedCount} assets with Rekanusa company ID.");
        
        // Also update assets that might have asset_code starting with RKN but no company_id
        $rkn_assets = Asset::where('asset_code', 'like', 'RKN-%')
            ->whereNull('company_id')
            ->update(['company_id' => $rekanusaCompany->id]);
            
        if ($rkn_assets > 0) {
            $this->command->info("Updated {$rkn_assets} additional RKN assets.");
        }
    }
}