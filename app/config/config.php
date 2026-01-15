<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        // Usa la variable de Render, si no existe (local), usa TiDB directamente
        'host'        => getenv('DB_HOST') ?: 'gateway01.us-east-1.prod.aws.tidbcloud.com',
        'username'    => getenv('DB_USERNAME') ?: '3ezqrRxoc1nCBuQ.root',
        'password'    => getenv('DB_PASSWORD') ?: 'i4Vx4NM2nA3ONLCJ',
        'dbname'      => getenv('DB_NAME') ?: 'test',
        'port'        => getenv('DB_PORT') ?: 4000,
        'charset'     => 'utf8',

        // ESTA ES LA LLAVE QUE FALTA PARA TIDB:
    'options' => [
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
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