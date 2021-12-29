<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;

use function PHPUnit\Framework\at;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', "password", "token"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', "token"
    ];

    public function createToken()
    {
        $token = Str::random(100);
        $this->token = $token;
        $this->save();
        return $token;
    }

    public function fund()
    {
        return $this->hasOne(Fund::class, "user_id", "id");
    }

    public function allTransactions()
    {
        return $this->hasMany(Transaction::class, "user_id", "id");
    }
}
