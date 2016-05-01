<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Create your config.php file based on config_example.php
require '../config.php';

// Redbean datbase ORM setup
require ROOT_PATH.'/vendor/rb.php';
R::setup(
	'mysql:host='.$db_config['servername'].';dbname='.$db_config['database'],
	$db_config['username'],
	$db_config['password']
);

// Composer autoloader
require ROOT_PATH.'/vendor/autoload.php';

// Lagan autoloader
function laganAutoload($class_name) {

	// Load models, controllers and Slim middleware
	$paths = array(
		'/model/',
		'/controller/',
		'/middleware/'
	);

	foreach ($paths as $path) {
		$file = ROOT_PATH.$path.$class_name.'.php';
		if (file_exists($file)) {
			require $file;
			return;
		}
	}

	// Redbean fix
	if ( substr($class_name, 0, strlen("Model_")) != "Model_" ) {
		throw new Exception('The class ' . $class_name . ' could not be loaded');
	}
}
spl_autoload_register('laganAutoload');

// Required for flash messages in Slim: start a new session 
session_start();

// Error reporting
if (ERROR_REPORTING) {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', '1');
	ini_set('html_errors', '1');
}



// ### SLIM SETUP ### //

$app = new \Slim\App(["settings" => [
	'displayErrorDetails' => ERROR_REPORTING
]]);

$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($c) {
	$view = new \Slim\Views\Twig([
		ROOT_PATH.'/templates',
		ROOT_PATH.'/input'
	],
	[
		'cache' => ROOT_PATH.'/cache'
	]);

	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

	// General variables to render views
	$view->offsetSet('app_url', APP_URL);

	return $view;
};

// Register Slim flash messages
$container['flash'] = function () {
	return new \Slim\Flash\Messages();
};

// Add HTTP Basic Authentication middleware
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
	'path' => '/admin',
	'secure' => true,
	'relaxed' => ['localhost'],
	'users' => [
		'admin' => 'password'
	]
]));



// ### ROUTES ### //

// Include all the route files.
$static  = ROOT_PATH.'/routes/static.php';
$routeFiles = glob(ROOT_PATH.'/routes/*.php');

foreach( $routeFiles as $routeFile ) {
	if ( $routeFile !== ROOT_PATH.'/routes/static.php' ) {
		require $routeFile;
	}
}

// The route for static pages has to come last to work.
if ( file_exists($static) ) {
	require $static;
}

$app->run();

?>