<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class User extends Model
 {
    

    protected $fillable = [
        'surname',
            'local_government',
            'state_of_origin',
            'date_of_birth',
            'first_name' ,
            'last_name' ,
            'password',
            'activation_token',
            'email',
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