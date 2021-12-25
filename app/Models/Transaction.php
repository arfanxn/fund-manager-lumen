<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const EXPENSE = "EXPENSE", INCOME = "INCOME";

    protected $fillable = ["user_id", "fund_id", "type", "note", "amount"];
}
