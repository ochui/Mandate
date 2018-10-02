<?php

namespace App\validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class EmailAvaliableException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} is already taken',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} is avaliable',
        ]
    ];
}