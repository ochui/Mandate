<?php

namespace App\Middlewares;

use \App\Middlewares\Middleware;

class flashMessagesMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        $this->view->getEnvironment()->addGlobal('message', $this->flash->getMessages());

        $response = $next($request, $response);
        return $response;
    }
}
