<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "name" => "Admin",
                "email" => "admin@example.com",
                "password" => bcrypt("password"),
                "role" => "admin",
            ],
            [
                "name" => "adi",
                "email" => "adi@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "dawud",
                "email" => "dawud@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "aden",
                "email" => "aden@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "aby",
                "email" => "aby@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "dafa",
                "email" => "dafa@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "adha",
                "email" => "adha@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "name" => "hafiz",
                "email" => "hafiz@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
        ];
    }
}
