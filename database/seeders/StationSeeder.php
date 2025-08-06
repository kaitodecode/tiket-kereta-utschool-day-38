<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stations = [
            [
                'name' => 'Stasiun Gambir',
                'code' => 'GMR',
                'latitude' => '-6.1767',
                'longitude' => '106.8305',
                'city' => 'Jakarta'
            ],
            [
                'name' => 'Stasiun Bandung',
                'code' => 'BD',
                'latitude' => '-6.9147',
                'longitude' => '107.6021',
                'city' => 'Bandung'
            ],
            [
                'name' => 'Stasiun Yogyakarta',
                'code' => 'YK',
                'latitude' => '-7.7892',
                'longitude' => '110.3636',
                'city' => 'Yogyakarta'
            ],
            [
                'name' => 'Stasiun Surabaya Gubeng',
                'code' => 'SGU',
                'latitude' => '-7.2650',
                'longitude' => '112.7504',
                'city' => 'Surabaya'
            ],
            [
                'name' => 'Stasiun Semarang Tawang',
                'code' => 'SMT',
                'latitude' => '-6.9647',
                'longitude' => '110.4283',
                'city' => 'Semarang'
            ],
            [
                'name' => 'Stasiun Malang',
                'code' => 'ML',
                'latitude' => '-7.9777',
                'longitude' => '112.6337',
                'city' => 'Malang'
            ],
            [
                'name' => 'Stasiun Cirebon',
                'code' => 'CN',
                'latitude' => '-6.7084',
                'longitude' => '108.5563',
                'city' => 'Cirebon'
            ],
            [
                'name' => 'Stasiun Solo Balapan',
                'code' => 'SLO',
                'latitude' => '-7.5597',
                'longitude' => '110.8203',
                'city' => 'Surakarta'
            ],
            [
                'name' => 'Stasiun Purwokerto',
                'code' => 'PWT',
                'latitude' => '-7.4197',
                'longitude' => '109.2027',
                'city' => 'Purwokerto'
            ],
            [
                'name' => 'Stasiun Madiun',
                'code' => 'MDN',
                'latitude' => '-7.6184',
                'longitude' => '111.5237',
                'city' => 'Madiun'
            ]
        ];

        foreach ($stations as $key => $value) {
            Station::create($value);
        }
    }
}
