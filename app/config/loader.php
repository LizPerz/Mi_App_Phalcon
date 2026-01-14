<?php

$loader = new \Phalcon\Loader();

/**
 * Registramos tanto directorios como Namespaces para que VS Code y Phalcon se entiendan
 */
$loader->registerNamespaces([
    'App\Models'      => $config->application->modelsDir,
    'App\Controllers' => $config->application->controllersDir,
])->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir
])->register();

