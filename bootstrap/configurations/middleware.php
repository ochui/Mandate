<?php

$app->add(new \App\Middlewares\ValidationErrorsMiddleware($container));
$app->add(new \App\Middlewares\oldInputMiddleware($container));
$app->add(new \App\Middlewares\LoginWithSessionMiddleware($container));
$app->add(new \App\Middlewares\flashMessagesMiddleware($container));
