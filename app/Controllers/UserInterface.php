<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Position;
use App\Models\Vote;
use \App\Controllers\Controller;

class UserInterface extends Controller
{

    public function index($request, $response)
    {
        return $this->view->render($response, 'home.html');
    }

    public function signup($request, $response)
    {
        if (Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        return $this->view->render($response, 'auth/signup.html');
    }

    public function signin($request, $response)
    {
        if (Auth::userIsAuthenticated()) {
            return $response->withRedirect($this->router->pathFor('app.home'));
        }

        return $this->view->render($response, 'auth/signin.html');
    }

    public function validationInstruction($request, $response)
    {
        if (Auth::userIsAuthenticated()) {
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
        $polls = Poll::where('active', 1)->get();

        if (!count($polls)) {
            return $this->view->render($response, 'applyToBeVoted.html');
        }
        $position = @Position::where('poll_id', $polls[0]->id)->get();

        $this->view->getEnvironment()->addGlobal('data', [
            'polls' => $polls,
            'positions' => $position,
        ]);

        return $this->view->render($response, 'applyToBeVoted.html');
    }

    public function showCandidate($request, $response)
    {

        $election = Poll::where('active', 1)->get();

        if (!count($election)) {
            return $this->view->render($response, 'candidate.html');
        }

        $route = $request->getAttribute('route');
        $arguments = $route->getArguments();

        $position = Position::where('poll_id', $election[0]->id)->get();

        if (!count($arguments)) {

            $this->view->getEnvironment()->addGlobal('data', [
                'positions' => $position,
            ]);

            return $this->view->render($response, 'candidate.html');
        }

        $position = Position::where('name', trim($arguments['position']))->where('poll_id', $election[0]->id)->get();

        if (!count($position)) {
            $position = Position::all();
            $this->view->getEnvironment()->addGlobal('data', [
                'positions' => $position,
            ]);

            return $this->view->render($response, 'candidate.html');
        }

        $data = Candidate::where('approved', 1)->where('position_id', $position[0]->id)->get();
        $candidates = [];
        foreach ($data as $candidate) {
            array_push($candidates, [
                'user' => Candidate::find($candidate->id)->user,
                'candidate' => $candidate,
            ]);
        }

        $this->view->getEnvironment()->addGlobal('data', [
            'candidates' => $candidates,
            'position' => $position,
            #'electionId' => Poll::where('active', 1)->get()[0]->id,
        ]);

        return $this->view->render($response, 'candidate.html');
    }

    public function showResults($request, $response)
    {
        $election = Poll::where('active', 1)->get();

        if (!$election[0]->show_result) {
            return $this->view->render($response, 'results.html');
        }
        $results = [];

        if (!count($election)) {
            return $this->view->render($response, 'results.html');
        }

        $positions = Position::where('poll_id', $election[0]->id)->get();

        foreach ($positions as $position) {
            $positionId = $position->id;
            $votes = Vote::where('position_id', $positionId)->get();
            $voteResult = [];
            $proccessed = [];

            foreach ($votes as $vote) {
                $candidateVote = Vote::where('position_id', $positionId)->where('candidate_id', $vote->candidate_id)->get();
                if (in_array($vote->candidate_id, $proccessed)) {
                    continue;
                }
                array_push($proccessed, $vote->candidate_id);
                array_push($voteResult, [
                    'user' => $vote->candidate_id,
                    'count' => $candidateVote->count(),
                    'position' => $position->name,
                ]);

            }

            array_push($results, $voteResult);

        }
        $this->view->getEnvironment()->addGlobal('results', $results);
        return $this->view->render($response, 'results.html');
    }
}
