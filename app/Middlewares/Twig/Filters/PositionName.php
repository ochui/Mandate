<?php

namespace App\Middlewares\Twig\Filters;

use App\Middlewares\Middleware;
use App\Models\Position;

class PositionName extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        
        $idToUsername = new \Twig_Filter('positionname', function ($positionId) {
            $position = Position::where('id', $positionId)->get();

            if(!count($position)) {
                return $next($request, $response);
            }
            $position = $position[0];
            return $position->name;
        });

        $this->view->getEnvironment()->addFilter($idToUsername);

        return $next($request, $response);
    }
}
