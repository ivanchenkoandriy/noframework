<?php

/**
 * The bootstrap
 */
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Root directory
 */
define('ROOT_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);

/**
 * Application directory
 */
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

/**
 * Views path
 */
define('VIEWS_PATH', APP_PATH . 'views' . DIRECTORY_SEPARATOR);

/**
 * Constant
 */
define('NO_FILE_WAS_UPLOADED', 4);

/**
 * Environments
 */
$dotenv = new \Dotenv\Dotenv(ROOT_PATH);
$environments = $dotenv->load();

define('APP_CHARSET', getenv('APP_CHARSET'));

/**
 * DI
 */
$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$container = $containerBuilder->build();

return $container;
