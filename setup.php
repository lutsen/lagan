<?php

/**
 * This is the setup file for your Lagan app and the unit tests.
 *
 * The setup.php file contains the configuration for RedBean,
 * the Composer autoloader and the autoloader for Lagan models.
 */

// Redbean datbase ORM setup
require ROOT_PATH.'/vendor/gabordemooij/redbean/rb.php'; // rb.php file is created bij the replica2.php script executed by composer
R::setup(
	'mysql:host='.$db_config['servername'].';dbname='.$db_config['database'],
	$db_config['username'],
	$db_config['password']
);

// Composer autoloader
require ROOT_PATH.'/vendor/autoload.php';

/**
 * Lagan autoloader
 *
 * Loads the Lagan models.
 *
 * @param string $class_name The name of the class to load.
 */
function laganAutoload($class_name) {

	// Load models, controllers
	$paths = array(
		'/models/',
		'/models/lagan/',
		'/controllers/',
		'/twigextensions/'
	);

	foreach ($paths as $path) {
		// Handle backslashes in namespaces
		if ( strpos( $class_name, '\\' ) ) {
			$file = ROOT_PATH.$path.substr( $class_name, strrpos( $class_name, '\\' )+1 ).'.php';
		} else {
			$file = ROOT_PATH.$path.$class_name.'.php';
		}
		if (file_exists($file)) {
			require_once $file;
			return;
		}
	}
}
spl_autoload_register('laganAutoload');

?>