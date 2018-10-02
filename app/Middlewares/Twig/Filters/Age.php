<?php

namespace App\Middlewares\Twig\Filters;

use App\Middlewares\Middleware;

class Age extends Middleware
{

    public function __invoke($request, $response, $next)
    {

        $age = new \Twig_Filter('age', function ($date){
            $datetime1 = new \DateTime("$date");
            $datetime2 = new \DateTime('now');
            $interval = $datetime1->diff($datetime2);
            return $interval->format("%r%y Years");
        });

        $this->view->getEnvironment()->addFilter($age);
        
        return $next($request, $response);
    }

}
