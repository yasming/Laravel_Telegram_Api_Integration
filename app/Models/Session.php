<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Session extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'sessions';
    protected $fillable = [
        'first_name',
        'last_name',
        'chat_id',
        'message'
    ];
}
