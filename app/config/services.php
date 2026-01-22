<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;

$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

$di->setShared('url', function () {
    $config = $this->getConfig();
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

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

// SERVICIO DE BASE DE DATOS CENTRALIZADO (PostgreSQL)
$di->setShared('db', function () {
    $config = $this->getConfig();
    
    return new \Phalcon\Db\Adapter\Pdo\Postgresql([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'port'     => 5432,
        'options'  => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // 1001 es el código interno para activar SSL (PDO::PGSQL_ATTR_SSL_MODE)
            1001 => 'require', 
            PDO::ATTR_TIMEOUT => 15
        ]
    ]);
});

$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

$di->setShared('dispatcher', function () {
    $eventsManager = new EventsManager();
    $eventsManager->attach("dispatch:beforeException", function ($event, $dispatcher, $exception) {
        // En local, queremos ver el error real en lugar del 500 genérico
        if ($_SERVER['HTTP_HOST'] === 'localhost') {
            echo "<h1>Error detectado:</h1><pre>" . $exception->getMessage() . "</pre>";
            exit;
        }

        switch ($exception->getCode()) {
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward(['controller' => 'errors', 'action' => 'show404']);
                return false;
        }
        $dispatcher->forward(['controller' => 'errors', 'action' => 'show500']);
        return false;
    });
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});