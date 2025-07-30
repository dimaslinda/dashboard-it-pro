<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::updateOrCreate(
            ['code' => 'RKN'],
            [
                'name' => 'Rekanusa',
                'description' => 'PT Rekanusa Konstruksi',
                'is_active' => true,
            ]
        );

        Company::updateOrCreate(
            ['code' => 'KZN'],
            [
                'name' => 'Kaizen',
                'description' => 'PT Kaizen Engineering',
                'is_active' => true,
            ]
        );
    }
}