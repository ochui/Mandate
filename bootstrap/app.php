<?php
//start php session
session_start();

//include composer auto-loader
require './vendor/autoload.php';

#include for Slim Framework
require __DIR__.'./configurations/settings.php';

#instantiate Slim app
$app = new \Slim\App($configurations);
#Get Slim container
$container = $app->getContainer();

#require eloquent
require __DIR__.'./dependencies/eloquent.php';
#require respect validation
require __DIR__.'./dependencies/respectValidation.php';

#require container
require __DIR__.'./container/container.php';

#require middleware
require __DIR__.'./configurations/middleware.php';

require './app/routes.php';