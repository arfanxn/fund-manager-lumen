<?php

namespace Database\Seeders;

use App\Models\Fund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        // User::create([
        //     "name" => "Arfan",
        //     "email" => "arf@gm.com",
        //     "password" => Hash::make("111222"),
        // ]);

        // User::factory()->count(10000)->create();

        // for ($i = 1; $i <= 1000; $i++) {
        //     Fund::create([
        //         "user_id" =>  $i,
        //         "balance" => rand(100000, 999999),
        //     ]);
        // }


        for ($i = 1; $i < 10000; $i++) {
            try {
                Transaction::factory()->count(10000)->create();
            } catch (QueryException $e) {
                continue;
            }
        }
    }
}
