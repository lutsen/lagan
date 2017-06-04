<?php

/**
 * Route helper functions
 */

/**
 * Set up a controller for a bean type.
 *
 * @var string $beantype The type of bean.
 *
 * @return object The controller.
 */
function setupBeanModel($beantype) {
	$beantype = ucfirst( strtolower( $beantype ) );
	if ( !file_exists( ROOT_PATH. '/models/lagan/'.$beantype.'.php' ) ) {
		throw new \Exception('The '.$beantype.' model does not exist.');
	}

	// Return model
	$model_name = '\Lagan\Model\\' . $beantype;
	return new $model_name();
}

/**
 * Get all bean types from the models/lagan directory
 *
 * @return string[] Array with names of all bean types
 */
function getBeantypes () {
	$beantypes = glob(ROOT_PATH. '/models/lagan/*.php');
	foreach ($beantypes as $key => $value) {
		$beantypes[$key] = strtolower( substr(
			$value,
			strlen(ROOT_PATH. '/models/lagan/'),
			strlen($value) - strlen(ROOT_PATH. '/models/lagan/') - 4
		) );
	}

	return $beantypes;
}

?>