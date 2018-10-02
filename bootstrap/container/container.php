<?php


#Register component on container

$container['db'] = function($container) use($capsule) {
    return $capsule;
};


$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('./resources/views/template', [
        'cache' => false
    ]);
    
    #Instantiate and add Slim extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['validator'] = function($container) {
    return new \App\Validation\Validator;
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['Auth'] = function () {
    return new \App\Auth\Auth;
};

$container['UserInterface'] = function ($container) {
    return new \App\Controllers\UserInterface($container);
};

$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['Admin'] = function ($container) {
    return new App\Controllers\Admin\Admin($container);
};

$container['Application'] = function ($container) {
    return new App\Controllers\Application($container);
};

$container['upload'] = BASEPATH.'/uploads';