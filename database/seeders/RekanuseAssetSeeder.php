<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RekanuseAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get REKANUSA company
        $company = Company::where('code', 'RKN')->first();
        
        if (!$company) {
            throw new \Exception('Company with code RKN not found. Please run CompanySeeder first.');
        }

        $assets = [
            // Safety First Tools
            ['category' => 'safety_first', 'items' => [
                ['name' => 'Rompi Rekanusa', 'tool_name' => 'Rompi Rekanusa'],
                ['name' => 'Rompi Polos', 'tool_name' => 'Rompi Polos'],
                ['name' => 'Helm Rekanusa', 'tool_name' => 'Helm Rekanusa'],
                ['name' => 'Helm Putih Polos', 'tool_name' => 'Helm Putih Pols'],
                ['name' => 'Helm Safety Baru', 'tool_name' => 'Helm safety Baru'],
                ['name' => 'Sepatu Safety', 'tool_name' => 'Sepatu Safety'],
                ['name' => 'Sepatu Safety Cheetah', 'tool_name' => 'Sepatu Safety cheetah', 'brand' => 'Cheetah'],
                ['name' => 'Sepatu Safety Krisbow Prince 6', 'tool_name' => 'Sepatu Safety Krisbow Prince 6', 'brand' => 'Krisbow'],
                ['name' => 'Safety Traffic Cone', 'tool_name' => 'Safety Traffic Cone'],
                ['name' => 'Krisbow Protective Case Black Size L', 'tool_name' => 'Krisbow Protective Case Black ( SRPC10BK) Size L', 'brand' => 'Krisbow'],
                ['name' => 'Krisbow Protective Case Black Size M', 'tool_name' => 'Krisbow Protective Case Black ( SRPC10BK) Size M', 'brand' => 'Krisbow'],
                ['name' => 'Bag/Tas Eiger', 'tool_name' => 'Bag/Tas Eiger', 'brand' => 'Eiger'],
                ['name' => 'Camera Bag C11', 'tool_name' => 'Camera Bag C11'],
                ['name' => 'Masker', 'tool_name' => 'Masker'],
                ['name' => 'Headlamp', 'tool_name' => 'Headlamp'],
            ]],
            
            // Documentation Tools
            ['category' => 'documentation', 'items' => [
                ['name' => 'Camera Canon EOS M10', 'tool_name' => 'Camera Canon EOS M10', 'brand' => 'Canon', 'model' => 'EOS M10'],
                ['name' => 'Digital Camera Bcare', 'tool_name' => 'Digital Camera Bcare', 'brand' => 'Bcare'],
                ['name' => 'Camera Nikon W300', 'tool_name' => 'Camera Nikon W300', 'brand' => 'Nikon', 'model' => 'W300'],
                ['name' => 'Handphone Samsung M23 5G', 'tool_name' => 'Handphone SAMSUNG M23 5G', 'brand' => 'Samsung', 'model' => 'M23 5G'],
                ['name' => 'Handphone Redmi 9C', 'tool_name' => 'Handphone REDMI 9C', 'brand' => 'Redmi', 'model' => '9C'],
                ['name' => 'Drone DJI Mini 2 SE (New)', 'tool_name' => 'Drone DJI Mini 2 SE (New)', 'brand' => 'DJI', 'model' => 'Mini 2 SE'],
                ['name' => 'Drone DJI Mini 2', 'tool_name' => 'Drone DJI Mini 2', 'brand' => 'DJI', 'model' => 'Mini 2'],
                ['name' => 'Drone SIRC F22', 'tool_name' => 'Drone SIRC F22', 'brand' => 'SIRC', 'model' => 'F22'],
                ['name' => 'Stabilizer Camera FEIYU SCORP', 'tool_name' => 'Stabilizer Camera FEIYU SCORP', 'brand' => 'FEIYU', 'model' => 'SCORP'],
            ]],
            
            // Support Tools
            ['category' => 'support_tools', 'items' => [
                ['name' => 'Kunci Torsi', 'tool_name' => 'Kunci Torsi'],
                ['name' => 'Hand Wrench+kunci sok', 'tool_name' => 'Hand Wrench+kunci sok'],
                ['name' => 'Kunci pas ( box )', 'tool_name' => 'Kunci pas ( box )'],
                ['name' => 'Kunci L bintang ( 1 set )', 'tool_name' => 'Kunci L bintrang ( 1 set )'],
                ['name' => 'Kunci L hexagonal ( 1 set )', 'tool_name' => 'Kunci L hexagonal ( 1 set )'],
                ['name' => 'Isku Tool box', 'tool_name' => 'Isku Tool box', 'brand' => 'Isku'],
                ['name' => 'Baterai A2', 'tool_name' => 'Baterai A2'],
                ['name' => 'Baterai A3', 'tool_name' => 'Baterai A3'],
                ['name' => 'Papan Jalan', 'tool_name' => 'Papan Jalan'],
                ['name' => 'Palu', 'tool_name' => 'Palu'],
                ['name' => 'Kabel & Stop Kontak', 'tool_name' => 'Kabel & Stop Kontak'],
            ]],
            
            // Survey Architecture Tools
            ['category' => 'survey_architecture', 'items' => [
                ['name' => 'Meteran Tangan 5m', 'tool_name' => 'Meteran Tangan 5m'],
                ['name' => 'Meteran Tangan 7,5m', 'tool_name' => 'Meteran Tangan 7,5m'],
                ['name' => 'Meteran Tangan 8m', 'tool_name' => 'Meteran Tangan 8m'],
                ['name' => 'Meteran Roll 50m', 'tool_name' => 'Meteran Roll 50m'],
                ['name' => 'Meteran Roll 100m', 'tool_name' => 'Meteran Roll 100m'],
                ['name' => 'Meteran Roda/Wheel Meter', 'tool_name' => 'Meteran Roda/Wheel Meter'],
                ['name' => 'Meteran Laser Sydney', 'tool_name' => 'Meteran Laser SYDNEY', 'brand' => 'Sydney'],
                ['name' => 'Meteran Laser Laica', 'tool_name' => 'Meteran Laser LAICA', 'brand' => 'Laica'],
            ]],
            
            // Civil Tools
            ['category' => 'civil_tools', 'items' => [
                ['name' => 'Hammer Test', 'tool_name' => 'Hammer Test'],
                ['name' => 'Thickness coating', 'tool_name' => 'Thickness coating'],
                ['name' => 'Hardness test', 'tool_name' => 'Hardnest test'],
                ['name' => 'Ultrasonic Thickness', 'tool_name' => 'Ultrasonic Thickness'],
                ['name' => 'Rebar Scan', 'tool_name' => 'Rebar Scan'],
                ['name' => 'Theodolite Nikon NE 101+meteran+tripod', 'tool_name' => 'Theodolite Nikon NE 101+meteran+tripod', 'brand' => 'Nikon', 'model' => 'NE 101'],
                ['name' => 'Bor Portable merk APR', 'tool_name' => 'Bor Portable merk APR', 'brand' => 'APR'],
                ['name' => 'DCA Diamond Core Bit', 'tool_name' => 'DCA Diamond Core Bit', 'brand' => 'DCA'],
                ['name' => 'Bosch Hammer GBH 2-28 DFV', 'tool_name' => 'Boschhammer GBH 2-28 DFV', 'brand' => 'Bosch', 'model' => 'GBH 2-28 DFV'],
                ['name' => 'Penggaris Crack', 'tool_name' => 'Penggaris Crack'],
                ['name' => 'Infrared Thermograph Flir MR277', 'tool_name' => 'Infrared Thermograph Flir MR277', 'brand' => 'Flir', 'model' => 'MR277'],
                ['name' => 'Ultrasonic Pulse Velocity Test', 'tool_name' => 'Ultrasonic Pulse Velocity Test'],
                ['name' => 'Carbonation Test', 'tool_name' => 'Carbonation Test'],
                ['name' => 'Half potential Test', 'tool_name' => 'Half potencial Test'],
                ['name' => 'UPE Test PROCEQ Pundit PL200PE', 'tool_name' => 'UPE Test PROCEQ Pundit PL200PE', 'brand' => 'PROCEQ', 'model' => 'Pundit PL200PE'],
            ]],
            
            // MEP & Utility Tools
            ['category' => 'mep_utility', 'items' => [
                ['name' => 'Lux Meter', 'tool_name' => 'Lux Meter'],
                ['name' => 'Humidity Meter', 'tool_name' => 'Humidity Meter'],
                ['name' => 'Sound Meter', 'tool_name' => 'Sound Meter'],
                ['name' => 'Anemometer', 'tool_name' => 'Anemometer'],
                ['name' => 'Compound gas monitor/CO2', 'tool_name' => 'Compound gas monitor/CO2'],
                ['name' => 'Tang Ampere', 'tool_name' => 'Tang Ampere'],
                ['name' => 'Thermal Imaging Gun (Thermogun)', 'tool_name' => 'Thermal Imaging Gun (Thermogun)'],
                ['name' => 'Digital Earth Tester penangkal Petir', 'tool_name' => 'Digital Earth Tester penangkal Petir'],
                ['name' => 'Clamp earth Resistance meter', 'tool_name' => 'Clamp earth Resistance meter'],
                ['name' => 'Vibrator With Tachometer', 'tool_name' => 'Vibrator With Tachometer'],
                ['name' => 'Halogen Leak Detector', 'tool_name' => 'Halogen Leak Detector'],
                ['name' => 'Isolation Continuity Tester/Merger Tester', 'tool_name' => 'Isoulation Continuity Tester/Merger Tester'],
                ['name' => 'Ultrasonic Flowmeter', 'tool_name' => 'Ultrasonic Flowmeter'],
            ]],
        ];

        $assetNumber = 1;
        foreach ($assets as $categoryData) {
            $toolCategory = $categoryData['category'];
            
            foreach ($categoryData['items'] as $item) {
                Asset::create([
                    'company_id' => $company->id,
                    'asset_code' => 'RKN-' . str_pad($assetNumber, 3, '0', STR_PAD_LEFT),
                    'asset_number' => (string) $assetNumber,
                    'name' => $item['name'],
                    'tool_name' => $item['tool_name'],
                    'tool_category' => $this->mapToolCategory($toolCategory),
                    'subcategory' => $this->getCategoryName($toolCategory),
                    'type' => $this->getToolType($item['tool_name']),
                    'brand' => $item['brand'] ?? null,
                    'model' => $item['model'] ?? null,
                    'serial_number' => null,
                    'total_units' => 1,
                    'location' => 'REKANUSA Office',
                    'department' => 'Survey & Inspection',
                    'condition' => 'good',
                    'purchase_date' => now()->subMonths(rand(1, 24)),
                    'purchase_price' => null,
                    'current_value' => null,
                    'depreciation_rate' => null,
                    'warranty_expiry' => null,
                    'last_maintenance' => null,
                    'next_maintenance' => now()->addMonths(6),
                    'status' => 'active',
                    'notes' => 'Asset imported from REKANUSA control list',
                    'specifications' => null,
                    'availability_checklist' => [
                        'physical_check' => true,
                        'functional_check' => true,
                        'safety_check' => true,
                        'documentation_check' => false
                    ],
                ]);
                
                $assetNumber++;
            }
        }
    }
    
    private function mapToolCategory($toolCategory): string
    {
        return match($toolCategory) {
            'safety_first' => 'safety_first',
            'documentation' => 'documentation',
            'support_tools' => 'support_tools',
            'survey_architecture' => 'survey_architecture',
            'civil_tools' => 'civil_tools',
            'mep_utility' => 'mep_utility',
            default => 'support_tools'
        };
    }
    
    private function getCategoryName($toolCategory): string
    {
        return match($toolCategory) {
            'safety_first' => 'Tools Alat Pelindung Diri/Safety First',
            'documentation' => 'Alat Dokumentasi',
            'support_tools' => 'Support Tools',
            'survey_architecture' => 'Alat Survey Arsitektur',
            'civil_tools' => 'Alat Sipil',
            'mep_utility' => 'Alat MEP & Utility',
            default => 'Other Tools'
        };
    }
    
    private function getToolType($toolName): string
    {
        if (str_contains(strtolower($toolName), 'camera') || str_contains(strtolower($toolName), 'drone')) {
            return 'Photography Equipment';
        }
        if (str_contains(strtolower($toolName), 'meter') || str_contains(strtolower($toolName), 'test')) {
            return 'Measurement Tool';
        }
        if (str_contains(strtolower($toolName), 'safety') || str_contains(strtolower($toolName), 'helm') || str_contains(strtolower($toolName), 'sepatu')) {
            return 'Safety Equipment';
        }
        if (str_contains(strtolower($toolName), 'kunci') || str_contains(strtolower($toolName), 'tool')) {
            return 'Hand Tool';
        }
        
        return 'General Equipment';
    }
}
