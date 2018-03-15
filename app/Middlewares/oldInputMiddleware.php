<?php

namespace App\Middlewares;

use \App\Middlewares\Middleware;

class oldInputMiddleware extends Middleware {

    public function __invoke($request, $response, $next) {

        $this->container->view->getEnvironment()->addGlobal('oldFormInput', @$_SESSION['oldFormInput']);
        $_SESSION['oldFormInput'] = $request->getParams();
        $response = $next($request, $response);
        return $response;

    }
}