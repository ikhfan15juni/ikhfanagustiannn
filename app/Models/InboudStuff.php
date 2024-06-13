<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InboudStuff extends Model
{
    use SoftDeletes;
    protected $fillable= ["stuff_id", "total" , "date", "proff_file"];
    protected $table = "inboud_stuff";

    public function Stuff()
    {
         return $this->belongsTo(Stuff::class);
    }

}

