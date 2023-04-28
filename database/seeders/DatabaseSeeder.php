<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\EscapeRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'Admin']);
        $user = Role::create(['name' => 'Customer']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'manageradmin@gmail.com',
            'password' => Hash::make('manageradmin')
        ]);

        $admin->assignRole('Admin');

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'usercustomer@gmail.com',
            'birthday' => Carbon::parse('1998-08-03'),
            'password' => Hash::make('usercustomer')
        ]);

        $customer->assignRole('Customer');


        $roomCounter = 1;
        foreach (range(1, 4) as $floor) {
            foreach (range(1, 6) as $room) {
                EscapeRoom::create([
                    'name' => $roomCounter . 'Room',
                    'floor' =>  $floor,
                    'price' => (mt_rand(500, 1000) / 10),
                    'capacity' => rand(3, 5)
                ]);

                $roomCounter++;
            }
        }




        echo 'Test users created with their role admin and customer' . PHP_EOL;

        echo 'Rooms was created all rooms are emty right now. Start booking after login with user info' . PHP_EOL;

        echo 'user-email : ' . $user->customer . PHP_EOL;
        echo 'user-password : ' . 'usercustomer' . PHP_EOL;
    }
}
