<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class User extends Model
 {
    

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'login_token',
        'activation_token',
        'registration_ip',
        'password'
    ];

    protected $gaurded = [
        'voter_id',
        'role'
    ];
    
    protected $hidden = [
        'password'  
    ];

    public function candidate()
    {
        return $this->hasOne(new \App\Models\Candidate);
    }

}