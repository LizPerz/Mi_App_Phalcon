<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
    'adapter'     => 'Mysql',
    // Si hay variables de entorno (Render), úsalas. Si no, usa XAMPP local.
    'host'        => getenv('DB_HOST') ?: 'localhost',
    'username'    => getenv('DB_USERNAME') ?: 'root',
    'password'    => getenv('DB_PASSWORD') ?: '',
    'dbname'      => getenv('DB_NAME') ?: 'universidad',
    'port'        => (int) getenv('DB_PORT') ?: 3306,
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