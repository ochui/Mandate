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

    public function user()
    {
        return $this->belongsTo(new \App\Models\User);
    }

    public function position()
    {
        return $this->belongsTo(new \App\Models\Position);
    }

}