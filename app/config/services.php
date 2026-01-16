<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;

// NUEVOS USES PARA ERRORES
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'port'     => $config->database->port,
        'charset'  => $config->database->charset

    ];
// SI NO ES LOCALHOST, ACTIVAMOS SSL (Para TiDB en Render)
    if ($_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
        $params['options'] = [
            PDO::MYSQL_ATTR_SSL_CA => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
    } else {
        // EN TU PC NO USAMOS SSL PARA EVITAR EL ERROR 500
        $params['options'] = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
    }

    return new $class($params);
});
    // Detectamos si es local para decidir si usar SSL (necesario para TiDB en la nube)
    /*$isLocal = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');

    if (!$isLocal) {
        $params['options'] = [
            PDO::MYSQL_ATTR_SSL_CA => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
    } else {
        $params['options'] = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
    }

    return new $class($params);
});*/


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});


/**
 * Dispatcher con manejo de errores 404 y 500
 */
$di->setShared('dispatcher', function () {
    $eventsManager = new EventsManager();

    // Capturar excepciones antes de que crashee la app
    $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {
        
        // Manejar errores de "No encontrado" (404)
        switch ($exception->getCode()) {
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward([
                    'controller' => 'errors',
                    'action'     => 'show404'
                ]);
                return false;
        }

        // Manejar errores internos del servidor (500)
        $dispatcher->forward([
            'controller' => 'errors',
            'action'     => 'show500'
        ]);
        return false;
    });

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});