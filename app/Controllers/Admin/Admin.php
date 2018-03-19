<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Poll;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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

        $pagination = new Pagination($request, $this->router, [
            Pagination::OPT_TOTAL => count($users), #number of items
            #Pagination::OPT_PARAM_TYPE => PageList::PAGE_ATTRIBUTE
        ]);

        $param = $pagination->toArray();

        $this->view->getEnvironment()->addGlobal('users', User::offset(($param['current_page'] - 1) * $param['per_page'])->limit($param['per_page'])->get());
        $this->view->getEnvironment()->addGlobal('pagination', $pagination);

        return $this->view->render($response, 'admin/browseUser.html');
    }

    public function viewAspirant(Request $request, Response $response)
    {

    }

    public function viewApprovedAspirant(Request $request, Response $response)
    {

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
        $candidates = Candidate::all();

        $pagination = new Pagination($request, $this->router, [
            Pagination::OPT_TOTAL => count($candidate), #number of items
            #Pagination::OPT_PARAM_TYPE => PageList::PAGE_ATTRIBUTE
        ]);

        $param = $pagination->toArray();

        $this->view->getEnvironment()->addGlobal('candidates', Candidate::offset(($param['current_page'] - 1) * $param['per_page'])->limit($param['per_page'])->get());
        $this->view->getEnvironment()->addGlobal('pagination', $pagination);

        return $this->view->render($response, 'admin/browseUser.html');
    }
}
