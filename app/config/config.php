<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        // Si detecta DB_HOST en Render, usa Postgresql, si no, Mysql (Local)
        'adapter'     => getenv('DB_HOST') ? 'Postgresql' : 'Mysql', 
        'host'        => getenv('DB_HOST') ?: 'localhost',
        'username'    => getenv('DB_USERNAME') ?: 'root',
        'password'    => getenv('DB_PASSWORD') ?: '',
        'dbname'      => getenv('DB_NAME') ?: 'universidad',
        // Si es local usa 3306, si es Render usa el puerto de la variable
        'port'        => getenv('DB_PORT') ?: 3306, 
        'charset'     => 'utf8',
    ],
    
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri' => $_SERVER['HTTP_HOST'] === 'localhost' ? '/Mi_App/' : '/',
    ]
]);