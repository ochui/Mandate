<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Candidate extends Model
 {
    

    protected $fillable = [
        'user_id',
        'position_id',
    ];

    protected $gaurded = [
        'approved'
    ];
    
    protected $hidden = [
 
    ];

}