<?php

php_uname();
$server_info = PHP_OS;
$os = strtoupper(substr(PHP_OS, 0, 3));

if ($os === 'WIN') {

    $path = dirname(__FILE__);
    $new_path = explode(':', $path);
    define('BASEPATH',str_replace(array('\\', '\\'), array('/', '/'), $new_path[1]));

} else {

    if (!defined('BASEPATH')) {
        define('BASEPATH', dirname(__FILE__));
    }

    
}

if (!defined('BASEPATH')) {
    define('BASEPATH', get_path(__FILE__) . '/');
}

#Get application settings
require BASEPATH.'/bootstrap/app.php';

#Run Application
$app->run();
