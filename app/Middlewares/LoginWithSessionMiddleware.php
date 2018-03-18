<?php

namespace App\Middlewares;

use \App\Middlewares\Middleware;
use \App\Auth\Auth;

class LoginWithSessionMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['userId'])) {
            $this->view->getEnvironment()->addGlobal('auth', [
            'userIsAuthenticated' => Auth::userIsAuthenticated(),
            'userIsAdmin' => Auth::adminIsAuthenticated(),
            'userData' => Auth::userData()
            ]);
        }
        return $response = $next($request, $response);
    }
}
