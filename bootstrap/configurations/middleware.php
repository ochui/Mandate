<?php

$app->add(new \App\Middlewares\ValidationErrorsMiddleware($container));
$app->add(new \App\Middlewares\oldInputMiddleware($container));
$app->add(new \App\Middlewares\LoginWithSessionMiddleware($container));
$app->add(new \App\Middlewares\flashMessagesMiddleware($container));
$app->add(new \App\Middlewares\PollsMiddleware($container));
$app->add(new \App\Middlewares\Twig\Filters\IdToFullName($container));
$app->add(new \App\Middlewares\Twig\Filters\Age($container));
$app->add(new \App\Middlewares\Twig\Filters\PositionName($container));
