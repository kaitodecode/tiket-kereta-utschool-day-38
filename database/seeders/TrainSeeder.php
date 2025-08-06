<?php

namespace Database\Seeders;

use App\Models\Train;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trains = [
            [
                'name' => 'Argo Bromo Anggrek',
                'code' => 'ABA-EXE',
                'class' => 'executive',
                'capacity' => 50
            ],
            [
                'name' => 'Argo Bromo Anggrek',
                'code' => 'ABA-BUS', 
                'class' => 'business',
                'capacity' => 60
            ],
            [
                'name' => 'Argo Bromo Anggrek',
                'code' => 'ABA-ECO',
                'class' => 'economy',
                'capacity' => 80
            ],
            [
                'name' => 'Argo Bromo Anggrek',
                'code' => 'ABA-PRE',
                'class' => 'non-economy',
                'capacity' => 70
            ],
            [
                'name' => 'Gajayana',
                'code' => 'GJY-EXE',
                'class' => 'executive',
                'capacity' => 50
            ],
            [
                'name' => 'Gajayana',
                'code' => 'GJY-BUS',
                'class' => 'business',
                'capacity' => 60
            ],
            [
                'name' => 'Gajayana',
                'code' => 'GJY-ECO',
                'class' => 'economy',
                'capacity' => 80
            ],
            [
                'name' => 'Gajayana',
                'code' => 'GJY-PRE',
                'class' => 'non-economy',
                'capacity' => 70
            ],
            [
                'name' => 'Bima',
                'code' => 'BMA-EXE',
                'class' => 'executive',
                'capacity' => 50
            ],
            [
                'name' => 'Bima',
                'code' => 'BMA-BUS',
                'class' => 'business',
                'capacity' => 60
            ],
            [
                'name' => 'Bima',
                'code' => 'BMA-ECO',
                'class' => 'economy',
                'capacity' => 80
            ],
            [
                'name' => 'Bima',
                'code' => 'BMA-PRE',
                'class' => 'non-economy',
                'capacity' => 70
            ],
            [
                'name' => 'Taksaka',
                'code' => 'TKS-EXE',
                'class' => 'executive',
                'capacity' => 50
            ],
            [
                'name' => 'Taksaka',
                'code' => 'TKS-BUS',
                'class' => 'business',
                'capacity' => 60
            ],
            [
                'name' => 'Taksaka',
                'code' => 'TKS-ECO',
                'class' => 'economy',
                'capacity' => 80
            ],
            [
                'name' => 'Taksaka',
                'code' => 'TKS-PRE',
                'class' => 'non-economy',
                'capacity' => 70
            ]
        ];
        foreach ($trains as $key => $value) {
            Train::create($value);
        }
    }
}
