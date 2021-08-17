<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'check_approved',
        'description',
        'picture'
    ];
}
