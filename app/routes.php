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
