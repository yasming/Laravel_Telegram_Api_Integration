<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Session extends Eloquent
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mongodb';
    protected $collection = 'sessions';
    protected $fillable = [
        'first_name',
        'last_name',
        'chat_id',
        'messages',
        'full_name'
    ];

    public function scopefilterByName(Builder $query,$name)
    {
       return $query->when($name, function ($query) use ($name) {
              return $query->where('full_name','like','%'.$name.'%');
       });
    }
}
