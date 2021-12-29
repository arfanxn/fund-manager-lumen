<?php

namespace Database\Factories;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = ["EXPENSE", "INCOME"];
        return [
            "user_id" => rand(1, 35000),
            "fund_id" => null,
            "amount" => rand(10000, 9999999),
            "type" => $type[rand(0, 1)],
            "note" => substr($this->faker->sentence(), 0, 200),
            "date" => Carbon::today()->subDays(rand(0, 365))->toDateString(),
        ];
    }
}
