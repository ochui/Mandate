<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Position extends Model
 {
    

    protected $fillable = [
        'poll_id',
        'name',
        'description',
    ];

    protected $gaurded = [

    ];
    
    protected $hidden = [
 
    ];

}