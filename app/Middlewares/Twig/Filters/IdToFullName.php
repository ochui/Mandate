<?php

namespace App\Middlewares\Twig\Filters;

use App\Models\User;
use App\Middlewares\Middleware;

class idToFullName extends Middleware
{

    public function __invoke($request, $response, $next)
    {

        $idToUsername = new \Twig_Filter('fullname', function ($userId){
            $user = User::find($userId);
            return $user->surname.' '.$user->first_name.' '.$user->last_name;
        });

        $this->view->getEnvironment()->addFilter($idToUsername);
        
        return $next($request, $response);
    }

}
