<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
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
                "id" => Str::uuid()->toString(),
                "name" => "Admin",
                "email" => "admin@gmail.com",
                "password" => bcrypt("password"),
                "role" => "admin",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "adi",
                "email" => "adi@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "dawud",
                "email" => "dawud@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "aden",
                "email" => "aden@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "aby",
                "email" => "aby@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "dafa",
                "email" => "dafa@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "adha",
                "email" => "adha@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
            [
                "id" => Str::uuid()->toString(),
                "name" => "hafiz",
                "email" => "hafiz@gmail.com",
                "password" => bcrypt("password"),
                "role" => "customer",
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
