<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Vote extends Model
 {
    

    protected $fillable = [
        'user_id',
        'candidate_id',
        'position_id',
        'poll_id'
    ];

    protected $gaurded = [

    ];
    
    protected $hidden = [
 
    ];

}