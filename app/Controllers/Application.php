<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Auth\Auth;
use App\Models\User;
use App\Models\Candidate;

use Respect\Validation\Validator as v;

class Application extends Controller
{
    public function VotersApplication($request, $response)
    {
        if(!Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('auth.sign'));
        }

        $user = User::find($_SESSION['userId']);
        $voter_id = implode('-', [
            'NG',
            $this->timeStamp->format('YY'),
            strtoupper(sha1(uniqid(mt_rand(), true)))
        ]);

        if(!$user) {
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Invalid User',
                    'message' => 'An error occur will trying to verry your identity, make sure you are sign in befor performing this operation'
                ]);
            }

            $this->flash->addMessage('error', [
                'application' => 'An error occur will trying to verry your identity, make sure you are sign in befor performing this operation'
            ]);
            return $response->withRedirect($this->router->pathFor('user.apply.vote'));
        }

        if($request->isXhr()) {
            return $response->withJson([
                'error' => false,
                'title' => 'Request Completed',
                'message' => 'Your request to vote has been reviewed and approved'
            ]);
        }

        return $response->withRedirect($this->router->pathFor('user.apply.vote'));
    }

    public function candidateApplication($request, $response)
    {
        if(!Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $validation = $this->validator->validate($request, [
            'poll' => v::notEmpty(),
            'position' => v::notEmpty()
        ]);

        if($validation->failed()) {

            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Request Failed',
                    'message' => $validation->errors
                ]);
            }

            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $candidate = Candidate::where('user_id', $_SESSION['userId'])->get();

        // print_r($candidate);
        // die();
        
        if(count($candidate)) {

            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Request Failed',
                    'message' => 'Duplicate entry is not allowed'
                ]);
            }

            $this->flash->addMessage('error', [
                'candidate' => 'Duplicate entry is not allowed'
            ]);

            return $response->withRedirect($this->router->pathFor('user.apply.candidate'));
        }

        Candidate::create([
            'user_id' => $_SESSION['userId'],
            'position_id' => $request->getParam('position')
        ]);

        if($request->isXhr()) {
            return $response->withJson([
                'error' => false,
                'title' => 'Request Completed',
                'message' => 'Your request has been submited and is under review you will inform once these process is complete.'
            ]);
        }

        $this->flash->addMessage('message', [
            'candidate' => 'Your request has been submited and is under review you will inform once these process is complete'
        ]);

        return $response->withRedirect($this->router->pathFor('user.apply.candidate'));
    }
}
