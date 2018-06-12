<?php

namespace App\Controllers\Admin;

use App\Auth\Auth;
use App\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Position;
use App\Models\User;
use App\Models\Vote;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;

class Admin extends Controller
{

    public function index(Request $request, Response $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
        return $this->view->render($response, 'admin/home.html');
    }

    public function viewAllUser(Request $request, Response $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
        $users = User::get();

        $this->view->getEnvironment()->addGlobal('users', $users);

        return $this->view->render($response, 'admin/browseUser.html');
    }

    public function processRequest(Request $request, Response $response)
    {
        if (!Auth::userIsAuthenticated()) {
            if ($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Authentication Failed',
                    'message' => 'Please login',
                ]);
            }

            $this->flash->addMessage('error', 'Please sign in to continue');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        if (!$_SESSION['canManage']) {
            if ($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Unauthorise Access',
                    'message' => 'You Don Not Have Administrative Privillages To Perform These Operation',
                ]);
            }

            $this->flash->addMessage('error', 'You Don Not Have Administrative Privillages To Perform These Operation');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $candidate = Candidate::where('user_id', $request->getParam('candidate_id'))->get();

        if (!count($candidate)) {
            if ($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Invalid Candidate Id',
                    'message' => 'No Candidate Found For The Specified Id',
                ]);
            }

            $this->flash->addMessage('error', 'No Candidate Found For The Specified Id');
            return $response->withRedirect($this->router->pathFor('admn.view.candidates'));
        }

        $candidate = $candidate[0];

        $candidate->approved = $request->getParam('accept') ? 1 : 2;
        $candidate->save();

        if ($request->isXhr()) {
            return $response->withJson([
                'error' => false,
                'title' => 'Action Successful',
                'message' => $request->getParam('accept') ? 'Candidate Request Accepted' : 'Candidate Request Rejected',
                'element' => $request->getParam('candidate_id'),
                'text' => $request->getParam('accept') ? 'Approved' : 'Rejected',
            ]);
        }

        $this->flash->addMessage('error', $request->getParam('accept') ? 'Request Accepted' : 'Request Rejected');
        return $response->withRedirect($this->router->pathFor('admn.view.candidates'));
    }

    public function getPollForm(Request $request, Response $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $polls = Poll::all();
        $this->view->getEnvironment()->addGlobal('polls', $polls);
        return $this->view->render($response, 'admin/createPoll.html');
    }

    public function createPoll(Request $request, Response $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => v::notEmpty(),
            'description' => v::notEmpty(),
            'start' => v::notEmpty(),
            'end' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('admin.create.poll'));
        }

        $activeElection = Poll::where('active', 1)->get();
        if (count($activeElection)) {
            if ($request->isXhr()) {
                return $response->withJson([
                    'error' => false,
                    'title' => 'Request Failed',
                    'message' => 'You can not have more than one active election at a time',
                ]);
            }

            $this->flash->addMessage('error', 'You can not have more than one active election at a time');
            return $response->withRedirect($this->router->pathFor('admin.create.poll'));
        }

        Poll::create([
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'starts' => $request->getParam('start'),
            'ends' => $request->getParam('end'),
            'active' =>  1,
        ]);

        return $response->withRedirect($this->router->pathFor('admin.view.poll'));
    }

    public function browsePoll($request, $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $polls = Poll::all();
        $this->view->getEnvironment()->addGlobal('polls', $polls);
        return $this->view->render($response, 'admin/browsePolls.html');
    }

    public function browseCandidate($request, $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $data = Candidate::all();
        $candidates = [];

        foreach ($data as $candidate) {
            array_push($candidates, [
                'user' => $candidate->user,
                'candidate' => $candidate,
            ]);
        }

        $this->view->getEnvironment()->addGlobal('candidates', $candidates);

        return $this->view->render($response, 'admin/browseCandidate.html');
    }

    public function showResult($request, $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $route = $request->getAttribute('route');
        $arguments = $route->getArguments();

        $positionName = $arguments['position'];
        if (!$positionName) {

            $position = Position::all();
            $this->view->getEnvironment()->addGlobal('position', $position);

            return $this->view->render($response, 'admin/showResult.html');
        }

        $position = Position::where('name', $positionName)->get();
        $hasResult = $position->count();

        if (!$hasResult) {
            $positions = Position::all();
            $this->view->getEnvironment()->addGlobal('positions', $positions);

            return $this->view->render($response, 'admin/showResult.html');
        }

        $positionId = $position[0]->id;
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
            ]);
        }

        $this->view->getEnvironment()->addGlobal('votes', $voteResult);

        return $this->view->render($response, 'admin/showResult.html');
    }

    public function getPositionForm($request, $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $polls = Poll::where('active', 1)->get();
        $this->view->getEnvironment()->addGlobal('data', [
            'polls' => $polls,
        ]);

        return $this->view->render($response, 'admin/addPosition.html');
    }

    public function addPosition($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => v::notEmpty(),
            'description' => v::notEmpty(),
            'poll' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('admin.create.position'));
        }

        Position::create([
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'poll_id' => $request->getParam('poll'),
        ]);

        return $response->withRedirect($this->router->pathFor('admin.view.position'));

    }

    public function browsePositions($request, $response)
    {

        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $positions = Position::all();

        $this->view->getEnvironment()->addGlobal('positions', $positions);

        return $this->view->render($response, 'admin/browsePosition.html');

    }

    public function publishResult($request, $response)
    {
        if (!Auth::userIsAuthenticated() || !$_SESSION['canManage']) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        // $route = $request->getAttribute('route');
        // $arguments = $route->getArguments();

        // $pollName = $arguments['poll'];
        $pollName = $request->getParam('poll');

        if (!$pollName) {
            return $response->withJson([
                'error' => true,
                'title' => 'Request Failed',
                'message' => 'Invalid election id',
            ]);
        }

        $poll = Poll::where('id', $pollName);

        if (!count($poll)) {
            return $response->withJson([
                'error' => true,
                'title' => 'Request Failed',
                'message' => 'Invalid election id',
            ]);
        }

        $poll->update([
            'show_result' => 1,
            'archive' => 1,
            ]);

        return $response->withJson([
            'error' => false,
            'title' => 'Request Completed',
            'message' => 'Result has been published',
            'element' => $request->getParam('poll'),
            'text' => 'Published',
        ]);

    }
}
