<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class stuff extends Model
{
    use SoftDeletes;
    protected $fillable= ["name", "category"];

    public function inboudStuffs()
    {
         return $this->hasMany(InboudStuff::class);
    }

    public function lendings()
    {
         return $this->hasMany(Lending::class);
    }
    public function StuffStock()
    { 
         return $this->hasOne(StuffStock::class);
    }
}


