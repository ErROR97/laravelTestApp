<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpRegister extends Model
{
    protected $fillable = [
        'phone_number',
        'status',
        'type',
        'explain',
        'code',
    ];
    protected $table = 'tmp_registers' ;
}
