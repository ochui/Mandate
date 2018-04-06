<?php

$app->get('/', 'UserInterface:index')->setName('app.home');

#Authentication
$app->get('/auth/signup', 'UserInterface:signup')->setName('auth.signup');
$app->post('/auth/signup', 'AuthController:signup');

$app->get('/auth/signin', 'UserInterface:signin')->setName('auth.signin');
$app->post('/auth/signin', 'AuthController:signin');

$app->get('/auth/signout', 'AuthController:signout')->setName('auth.signout');

$app->get('/auth/validate/help', 'UserInterface:validationInstruction')->setName('auth.validation.instruction');
$app->get('/auth/validate/error', 'UserInterface:validationError')->setName('auth.validate.error');
$app->get('/auth/validate/success', 'UserInterface:validationSuccess')->setName('auth.validate.success');
$app->get('/auth/validate[/{activationToken}]', 'AuthController:validateEmail')->setName('auth.validation.email');

$app->get('/apply/voter', 'UserInterface:applyToVote')->setName('user.apply.vote');
$app->post('/apply/voter', 'Application:VotersApplication');
$app->get('/apply/candidate', 'UserInterface:applyToBeVoted')->setName('user.apply.candidate');
$app->post('/apply/candidate', 'Application:candidateApplication');
$app->post('/candidate', 'Application:voteCandidate');
$app->get('/candidate[/{position}]', 'UserInterface:showCandidate')->setName('app.show.candidate');

$app->get('/results', 'UserInterface:showResults')->setName('app.show.result');

$app->get('/admin', 'Admin:index')->setName('admin.home');

$app->get('/admin/users', 'Admin:viewAllUser')->setName('admin.view.users');
$app->get('/admin/candidates', 'Admin:browseCandidate')->setName('admin.view.candidates');
$app->post('/admin/candidates', 'Admin:processRequest');

$app->get('/admin/create/election', 'Admin:getPollForm')->setName('admin.create.poll');
$app->post('/admin/create/election', 'Admin:createPoll');
$app->get('/admin/view/election', 'Admin:browsePoll')->setName('admin.view.poll');

$app->get('/admin/result[/{position}]', 'Admin:showResult')->setName('admin.view.result');

$app->get('/admin/create/position', 'Admin:getPositionForm')->setName('admin.create.position');
$app->post('/admin/create/position', 'Admin:addPosition');
$app->get('/admin/view/position', 'Admin:browsePositions')->setName('admin.view.position');



