<?php
/**
 * Created by PhpStorm.
 * User: Princewill
 * Date: 6/13/2018
 * Time: 10:56 PM
 */

namespace App\Middlewares\Twig\Filters;

use App\Middlewares\Middleware;
use App\Models\Poll;


class electionName extends Middleware
{

    public function __invoke($request, $response, $next)
    {
        $electionName = new \Twig_Filter('electionName', function ($elctionId){
            $election = Poll::where('id', $elctionId)->get();
            if(count($election)) {
                return $election[0]->name;
            }else {
                return null;
            }
        });

        $this->view->getEnvironment()->addFilter($electionName);

        return $next($request, $response);
    }
}