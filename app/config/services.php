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
use Phalcon\Mvc\Dispatcher\Exception as DispatchException; // Agregado para detectar errores de ruta

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

// === AQUÍ ESTÁ EL DISPATCHER (POLICÍA DE ERRORES) ACTUALIZADO ===
$di->setShared('dispatcher', function () {
    // 1. Crear el Gerente de Eventos
    $eventsManager = new EventsManager();

    // 2. Adjuntar la función para atrapar errores antes de que rompan la página
    $eventsManager->attach(
        'dispatch:beforeException',
        function ($event, $dispatcher, $exception) {
            
            // Si el error es "DispatchException", significa que la ruta o controlador NO EXISTE (Error 404)
            if ($exception instanceof DispatchException) {
                $dispatcher->forward([
                    'controller' => 'errors',
                    'action'     => 'show404'
                ]);
                return false;
            }

            // Para cualquier otro error (Base de datos, código roto, Captcha fallido), mandamos al 500
            $dispatcher->forward([
                'controller' => 'errors',
                'action'     => 'show500'
            ]);

            return false;
        }
    );

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    
    return $dispatcher;
});
