<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InternetProvider;

class InternetProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Biznet',
                'contact_phone' => '1500-988',
                'contact_email' => 'cs@biznetnetworks.com',
                'website' => 'https://www.biznetnetworks.com',
                'status' => 'active',
                'notes' => 'Provider fiber optic dengan coverage luas di Jakarta dan sekitarnya'
            ],
            [
                'name' => 'IndiHome',
                'contact_phone' => '147',
                'contact_email' => 'cs@telkom.co.id',
                'website' => 'https://www.indihome.co.id',
                'status' => 'active',
                'notes' => 'Provider internet dari Telkom Indonesia'
            ],
            [
                'name' => 'First Media',
                'contact_phone' => '1500-999',
                'contact_email' => 'cs@firstmedia.com',
                'website' => 'https://www.firstmedia.com',
                'status' => 'active',
                'notes' => 'Provider internet dan TV kabel'
            ],
            [
                'name' => 'MyRepublic',
                'contact_phone' => '1500-000',
                'contact_email' => 'cs@myrepublic.co.id',
                'website' => 'https://myrepublic.co.id',
                'status' => 'active',
                'notes' => 'Provider fiber internet dengan fokus gaming'
            ],
            [
                'name' => 'CBN',
                'contact_phone' => '1500-095',
                'contact_email' => 'cs@cbn.net.id',
                'website' => 'https://www.cbn.net.id',
                'status' => 'active',
                'notes' => 'Cyberindo Aditama (CBN) - Provider internet fiber'
            ],
            [
                'name' => 'MNC Play',
                'contact_phone' => '1500-816',
                'contact_email' => 'cs@mncplay.id',
                'website' => 'https://www.mncplay.id',
                'status' => 'active',
                'notes' => 'Provider internet dan TV dari MNC Group'
            ]
        ];

        foreach ($providers as $provider) {
            InternetProvider::create($provider);
        }
    }
}
