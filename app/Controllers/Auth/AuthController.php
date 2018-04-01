<?php

namespace App\Controllers\Auth;

use App\Auth\Auth;
use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{

    public function signup($request, $response)
    {

        $validation = $this->validator->validate($request, [
            'surname' => v::notEmpty()->alpha()->noWhitespace(),
            'local_government' => v::notEmpty()->alpha(),
            'state_of_origin' => v::notEmpty(),
            'first_name' => v::notEmpty()->alpha()->noWhitespace(),
            'last_name' => v::notEmpty()->alpha()->noWhitespace(),
            'date_of_birth' => v::notEmpty()->date()->age(18),
            'email' => v::notEmpty()->noWhitespace()->email()->emailAvaliable(),
            'password' => v::notEmpty()->noWhitespace(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $uploadedFiles = $request->getUploadedFiles();
        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['image'];
        $directory = $this->container->get('upload');
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
        }else{
            $this->view->getEnvironment()->addGlobal('error', 'Unable to upload your photograph');
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $user = User::create([
            'surname' => $request->getParam('surname'),
            'local_government' => $request->getParam('local_government'),
            'state_of_origin' => $request->getParam('state_of_origin'),
            'date_of_birth' => $request->getParam('date_of_birth'),
            'first_name' => $request->getParam('first_name'),
            'last_name' => $request->getParam('last_name'),
            'avatar' => $filename,
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            'activation_token' => base64_encode(crypt($request->getParam('email') . $request->getParam('first_name') . $request->getParam('last_name'))),
            'email' => $request->getParam('email'),
        ]);

        $voteId = strtoupper(implode('', [
            'nationality' => 'ng',
            'state' => substr($request->getParam('state_of_origin'), 0, 3),
            'userId' => $user->id,
        ]));

        $user->voter_id = $voteId;
        $user->active = 1;
        $user->save();

        return $response->withRedirect($this->router->pathFor('auth.signin'));
        #return $response->withRedirect($this->router->pathFor('auth.validation.instruction'));
    }

    public function signin($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::notEmpty()->noWhitespace(),
            'password' => v::notEmpty()->noWhitespace(),
        ]);

        if ($validation->failed()) {

            if ($request->isXhr()) {
                return $response->withJson($_SESSION['errors']);
            }
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $authentication = (object) $this->Auth->login($request->getParam('email'), $request->getParam('password'));

        if ($authentication->error) {

            if ($request->isXhr()) {
                return $response->withJson($authentication);
            }

            $this->flash->addMessage('error', $authentication->message);
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        if ($request->isXhr()) {
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

        if (!$user) {
            #invalid activation token
            if ($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'validation failed',
                    'message' => 'invalid activation tokon',
                ]);
            }

            return $response->withRedirect($this->router->pathFor('auth.validate.error'));
        }

        if ($user->active == 1) {
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
