<?php

namespace Database\Factories;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundFactory extends Factory
{
    protected $model = Fund::class;

    public function definition(): array
    {
        return [
            // 'user_id' => $this->faker->name,
            // 'email' => $this->faker->unique()->safeEmail,
            // "password" =>  Hash::make("111222"),
            // "token" =>  strtoupper(Str::random()),
        ];
    }
}
