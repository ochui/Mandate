<?php

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Controllers\Controller;
use App\Models\Poll;
use App\Models\User;
use App\Models\Candidate;
use App\Auth\Auth;

use Respect\Validation\Validator as v;
use Xandros15\SlimPagination\Pagination;

class Admin extends Controller
{

    public function index(Request $request, Response $response)
    {
        return $this->view->render($response, 'admin/home.html');
    }

    public function viewAllUser(Request $request, Response $response)
    {
        $users = User::get();

        $this->view->getEnvironment()->addGlobal('users', $users);

        return $this->view->render($response, 'admin/browseUser.html');
    }

    public function processRequest(Request $request, Response $response)
    {
        if(!Auth::userIsAuthenticated()) {
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Authentication Failed',
                    'message' => 'Please login'
                ]);
            }

            $this->flash->addMessage('error', 'Please sign in to continue');
            return $response->withRedirect($th->router->pathFor('auth.signin'));
        }

        if(!$_SESSION['canManage']) {
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Unauthorise Access',
                    'message' => 'You Don Not Have Administrative Privillages To Perform These Operation'
                ]);
            }

            $this->flash->addMessage('error', 'You Don Not Have Administrative Privillages To Perform These Operation');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $candidate = Candidate::where('user_id', $request->getParam('candidate_id'))->get();

        if(!count($candidate)) {
            if($request->isXhr()) {
                return $response->withJson([
                    'error' => true,
                    'title' => 'Invalid Candidate Id',
                    'message' => 'No Candidate Found For The Specified Id'
                ]);
            }

            $this->flash->addMessage('error', 'No Candidate Found For The Specified Id');
            return $response->withRedirect($this->router->pathFor('admn.view.candidates'));
        }

        $candidate = $candidate[0];
        
        $candidate->approved = $request->getParam('accept') ? 1 : 2;
        $candidate->save();

        if($request->isXhr()) {
            return $response->withJson([
                'error' => false,
                'title' => 'Action Successful',
                'message' => $request->getParam('accept') ? 'Candidate Request Accepted' : 'Candidate Request Rejected',
                'element' => $request->getParam('candidate_id'),
                'text' => $request->getParam('accept') ? 'Accepted' : 'Rejected'
            ]);
        }

        $this->flash->addMessage('error', $request->getParam('accept') ? 'Request Accepted' : 'Request Rejected');
        return $response->withRedirect($this->router->pathFor('admn.view.candidates'));
    }


    public function getPollForm(Request $request, Response $response)
    {
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

        Poll::create([
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'starts' => $request->getParam('starts'),
            'ends' => $request->getParam('end'),
        ]);

        return $response->withRedirect($this->router->pathFor('admin.view.poll'));
    }

    public function browsePoll($request, $response)
    {
        $polls = Poll::all();
        $this->view->getEnvironment()->addGlobal('polls', $polls);
        return $this->view->render($response, 'admin/browsePolls.html');
    }

    public function browseCandidate($request, $response)
    {
        $data = Candidate::all();
        $candidates = [];

        foreach ($data as $candidate) {
            array_push($candidates, [
                'user' => $candidate->user,
                'candidate' => $candidate
            ]);
        }

        $this->view->getEnvironment()->addGlobal('candidates', $candidates);

        return $this->view->render($response, 'admin/browseCandidate.html');
    }
}
