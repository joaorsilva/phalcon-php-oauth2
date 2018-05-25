<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        $config->application->modelsDir,
        $config->application->servicesDir
    ]
);

$loader->registerNamespaces(
    [
      "\\Oauth2\\Models" => $config->application->modelsDir,
      "\\Oauth2\\Services" => $config->application->servicesDir
    ]
)->register();
