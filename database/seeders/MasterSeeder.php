<?php

namespace Database\Seeders;

use App\Models\Master;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'group' => 'absensi',
                'key' => 'kantor_latitude',
                'value' => '-8.6500000',
                'description' => 'Latitude lokasi kantor untuk validasi absensi',
            ],
            [
                'group' => 'absensi',
                'key' => 'kantor_longitude',
                'value' => '115.2166670',
                'description' => 'Longitude lokasi kantor untuk validasi absensi',
            ],
            [
                'group' => 'absensi',
                'key' => 'radius_meter',
                'value' => '100',
                'description' => 'Radius maksimal absensi dari kantor dalam meter',
            ],
        ];

        foreach ($data as $item) {
            Master::updateOrCreate(
                [
                    'group' => $item['group'],
                    'key' => $item['key'],
                ],
                [
                    'value' => $item['value'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}