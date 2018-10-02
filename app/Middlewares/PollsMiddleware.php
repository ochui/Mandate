<?php

namespace App\Middlewares;

use \App\Middlewares\Middleware;
use \App\Models\Poll;

class PollsMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        $currentPoll = Poll::where('active', 1)->get();

        if (!count($currentPoll)) {
            $response = $next($request, $response);
            return $response;
        }

        $this->view->getEnvironment()->addGlobal('poll', [
            'active' => $currentPoll[0],
        ]);

        $response = $next($request, $response);
        return $response;
    }
}
