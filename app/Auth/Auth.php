<?php

namespace App\Auth;

use \App\Models\User;

class Auth
{
    public static function userIsAuthenticated()
    {
        return isset($_SESSION['userId']);
    }

    public static function adminIsAuthenticated()
    {
        return isset($_SESSION['canManage']);
    }

    public static function userData()
    {
        return User::find($_SESSION['userId']);
    }

    public static function login($email, $password)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $userData = User::where('email', $email)->first();
        }else {
            $userData = User::where('voter_id', $email)->first();
        }

        if (!$userData) {
            return [
                'error' => true,
                'title' => 'authentication failed',
                'message' => 'user invalid email or voter id'
            ];
        }

        if($userData->active == 0) {
            return [
                'error' => true,
                'title' => 'authentication failed',
                'message' => 'please active your account'
            ];
        }
        
        if (password_verify($password, $userData->password)) {
            $_SESSION['userId'] = $userData->id;
            if (in_array($userData->role, [1,2,3])) {
                $_SESSION['canManage'] = true;
            }
            return [
                'error' => false,
                'title' => 'authentication successful',
                'message' => 'login successfull'
            ];
        }

        return [
            'error' => true,
            'title' => 'authentication failed',
            'message' => 'invalid password'
        ];
    }

    public static function signout()
    {
        $_SESSION = array();
        session_destroy();
    }
}
