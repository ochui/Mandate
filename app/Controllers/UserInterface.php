<?php

namespace App\Controllers;

use \App\Controllers\Controller;
use App\Auth\Auth;
use App\Models\Position;
use App\Models\Poll;

use Respect\Validation\Validator as v;

class UserInterface extends Controller
{
    

    public function index($request, $response)
    {
        return $this->view->render($response, 'home.html');
    }

    public function signup($request, $response)
    {
        if(Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        return $this->view->render($response, 'auth/signup.html');
    }

    public function signin($request, $response)
    {
        if(Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        return $this->view->render($response, 'auth/signin.html');
    }

    public function validationInstruction($request, $response)
    {
        if(Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        return $this->view->render($response, 'auth/validationInstruction.html');
    }

    public function validationError($request, $response)
    {
        return $this->view->render($response, 'auth/validationError.html');
    }

    public function validationSuccess($request, $response)
    {
        return $this->view->render($response, 'auth/validationSuccess.html');
    }

    public function applyToVote($request, $response)
    {
        return $this->view->render($response, 'applyToVote.html');
    }

    public function applyToBeVoted($request, $response)
    {
        $polls = Poll::all();
        $position = Position::all();
        
        $this->view->getEnvironment()->addGlobal('data', [
            'polls' => $polls,
            'positions' => $position
        ]);

        return $this->view->render($response, 'applyToBeVoted.html');
    }
}
