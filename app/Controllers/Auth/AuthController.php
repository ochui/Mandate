<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Auth\Auth;
use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{

    public function signup($request, $response)
    {

        $validation = $this->validator->validate($request, [
            'first_name' => v::notEmpty()->alpha()->noWhitespace(),
            'last_name' => v::notEmpty()->alpha()->noWhitespace(),
            'email' => v::notEmpty()->noWhitespace()->email()->emailAvaliable(),
            'password' => v::notEmpty()->noWhitespace()
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        User::create([
            'first_name' => $request->getParam('first_name'),
            'last_name' => $request->getParam('last_name'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'activation_token' => base64_encode(crypt($request->getParam('email') . $request->getParam('first_name') . $request->getParam('last_name'))),
            'email' => $request->getParam('email'),
        ]);

        return $response->withRedirect($this->router->pathFor('auth.validation.instruction'));
    }

    public function signin($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::notEmpty()->noWhitespace(),
            'password' => v::notEmpty()->noWhitespace(),
        ]);

        if ($validation->failed()) {

            if($request->isXhr()) {
                return $response->withJson($_SESSION['errors']);
            }
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $authentication = (object) $this->Auth->login($request->getParam('email'), $request->getParam('password'));

        if (!$authentication->error) {

            if($request->isXhr()) {
                return $response->withJson($authentication);
            }

            $this->flash->addMessage('error', $authentication->message);
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        if($request->isXhr()) {
            return $response->withJson($authentication);
        }

        return $response->withRedirect($this->router->pathFor('app.home'));
    }

    public function validateEmail($request, $response)
    {
        if (Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        $route = $request->getAttribute('route');
        $arguments = $route->getArguments();

        $activationToken = strip_tags(trim($arguments['activationToken']));

        $user = User::where('activation_token', $activationToken)->get()[0];

        if(!$user) {
            #invalid activation token
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'validation failed',
                    'message' => 'invalid activation tokon'
                ]);
            }

            return $response->withRedirect($this->router->pathFor('auth.validate.error'));
        }

        if($user->active == 1) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        $user->active = 1;
        $user->save();


        return $response->withRedirect($this->router->pathFor('auth.validate.success'));
    }

    public function signout($request, $response)
    {
        Auth::signout();

        return $response->withRedirect($this->router->pathFor('auth.signin'));
    }
}
