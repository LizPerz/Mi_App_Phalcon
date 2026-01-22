<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Postgresql', 
        'host'        => getenv('DB_HOST') ?: 'dpg-d5ontvkoud1c739cae20-a.virginia-postgres.render.com',
        'username'    => getenv('DB_USERNAME') ?: 'lizeth',
        'password'    => getenv('DB_PASSWORD') ?: 'rT29EL2l9FQ9wxbla0QaJazwvAuNVSJg',
        'dbname'      => 'universidad_mjo6',
        'port'        => getenv('DB_PORT') ?: 5432,
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'viewsDir'       => APP_PATH . '/views/',
        'cacheDir'       => BASE_PATH . '/cache/',
        // Ajuste: Eliminamos la barra final en localhost para evitar duplicados
        'baseUri'        => $_SERVER['HTTP_HOST'] === 'localhost' ? '/Mi_App/' : '/',
    ]
]);