<?php

/**
 * Route helper functions
 */

/**
 * Set up a controller for a bean type.
 *
 * @var string $beantype The type of bean.
 *
 * @return string The name of the controller.
 */
function setupBeanModel($beantype) {
	// Return model
	$model_name = '\Lagan\Model\\' . ucfirst($beantype);
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