<?php


namespace App\validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use App\Models\User;

class EmailAvaliable extends AbstractRule
{
    public function validate($input)
    {
        return User::where('email', $input)->count() === 0;
    }
}