<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Auth\Auth;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Vote;

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
                    'message' => $validation->errors()
                ]);
            }

            return $response->withRedirect($this->router->pathFor('user.apply.candidate'));
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

    public function voteCandidate($request, $response)
    {
        if(!Auth::userIsAuthenticated()) {
            if($request->isxhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Authentication Faild',
                    'message' => 'Please login and try again'
                ]);
            }
            return $response->withRedirect($this->router->pathFor('auth.sign'));
        }

        $validation = $this->validator->validate($request, [
            'position_id' => v::notEmpty(),
            'candidate_id' => v::notEmpty(),
            'pin' => v::notEmpty()
        ]);

        if($validation->failed()) {
            
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'validation failed',
                    'message' => $validation->errors()
                ]);
            }

            $this->flash->addMessage('error', [
                'vote' => 'Invalid candidate id'
            ]);
            return $response->withRedirect($this->router->pathFor('app.vote'));
        }

        $user = User::where('voter_id', $request->getParam('pin'))->where('id', $_SESSION['userId'])->get();
        $pinIsValid = $user->count();
        
        if(!$pinIsValid) {
            if($request->isXhr()) {

                return $response->withJson([
                    'error' => true,
                    'title' => 'Request denied',
                    'message' => 'Sorry, but the pin you provided is incorrect'
                ]);
                
            }

            $this->flash->addMessage('message', [
                'vote' => 'Sorry, but the pin you provided is incorrect'
            ]);
            return $response->withRedirect($this->router->pathFor('app.vote'));
        }
        
        $userCanVote = Vote::where('user_id', $_SESSION['userId'])->where('position_id', $request->getParam('position_id'))->where('candidate_id', $request->getParam('candidate_id'))->get()->count() === 0;
        

        if(!$userCanVote) {
            if($request->isXhr()) {

                return $response->withJson([
                    'error' => true,
                    'title' => 'Request denied',
                    'message' => 'Sorry but you can only vote onces'
                ]);
                
            }

            $this->flash->addMessage('message', [
                'vote' => 'Sorry but you can only vote onces'
            ]);
            return $response->withRedirect($this->router->pathFor('app.vote'));
        }

        $vote = Vote::where('user_id', $_SESSION['userId'])->where('position_id', $request->getParam('position_id'));
        $userHasVoted = $vote->count() === 1;

        if($userHasVoted) {
            $vote->update(['candidate_id' => $request->getParam('candidate_id')]);

            if($request->isXhr()) {

                return $response->withJson([
                    'error' => false,
                    'title' => 'Request Completed',
                    'message' => 'Your vote has been updated'
                ]);
                
            }
    
            $this->flash->addMessage('message', [
                'vote' => 'Your vote has been updated'
            ]);
            return $response->withRedirect($this->router->pathFor('app.vote'));
        }

        Vote::create([
            'user_id' => $_SESSION['userId'],
            'candidate_id' => $request->getParam('candidate_id'),
            'position_id' => $request->getParam('position_id'),
            'poll_id' => $request->getParam('poll_id')
        ]);

        if($request->isXhr()) {

            return $response->withJson([
                'error' => false,
                'title' => 'Request Completed',
                'message' => 'Thank you for voting'
            ]);
            
        }

        $this->flash->addMessage('message', [
            'vote' => 'Thank you for voting'
        ]);
        return $response->withRedirect($this->router->pathFor('app.vote'));
    }
}
