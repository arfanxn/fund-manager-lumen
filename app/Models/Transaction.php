<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const EXPENSE = "EXPENSE", INCOME = "INCOME";

    const CREATED_AT = null, UPDATED_AT = null;

    protected $fillable = ["user_id", "fund_id", "type", "note", "amount", "date"];
}
