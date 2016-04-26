<?php

// Create your config.php file based on config_example.php
require 'config.php';

// Redbean datbase ORM setup
require 'vendor/rb.php';
R::setup(
	'mysql:host='.$db_config['servername'].';dbname='.$db_config['database'],
	$db_config['username'],
	$db_config['password']
);

// Composer autoloader
require 'vendor/autoload.php';

// Lagan autoloader
function myAutoload($class_name) {

	// Load models, controllers and Slim middleware
	$paths = array(
		'/model/',
		'/controller/',
		'/middleware/'
	);

	foreach ($paths as $path) {
		$file = ROOT_PATH.$path.$class_name.'.php';
		if (file_exists($file)) {
			include $file;
			return;
		}
	}
	// Redbean hack
	// No exception if file doesn't exist, because it then is probably a class created by Redbean
	//if ( $class_name !== 'R' && substr($class_name, 0, strlen("Model_")) != "Model_" ) {
	if ( substr($class_name, 0, strlen("Model_")) != "Model_" ) {
		throw new Exception('The class ' . $class_name . ' could not be loaded');
	}
}
spl_autoload_register('myAutoload');

// - Set this for login stuff in combination with Slim
//session_cache_limiter(false);

// Required for flash messages in Slim: start a new session 
session_start();

// Error reporting
if (ERROR_REPORTING) {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', '1');
	ini_set('html_errors', '1');
}

?>