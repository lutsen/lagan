<?php

/*

 The Manytoone property type controller enables a many-to-one relation between 2 Lagan models,
 as described here: http://redbeanphp.com/index.php?p=/many_to_one
 The name of the property should be the name of the Lagan model this model can have a many-to-one relation with.
 For this to work properly the other model should have a one-to-many relation with this model.
 So in our example the LaganCrew model has a many-to-one relation with the LaganHoverkraft model, and the LaganHoverkraft model has a one-to-many relation with the LaganCrew model.

*/

 namespace Lagan\Property;

class ManytooneController {

	// @param bean $bean
	// @param array $property
	public function set($bean, $property, $new_value) {

		// Check and set relation
		$relation = \R::findOne( $property['name'], ' id = :id ', [ ':id' => $new_value ] );
		if ( !$relation ) {
			throw new Exception('This '.$property['name'].' does not exist.');
		} else {
			return $relation;
		}

	}

	// @param array $property
	public function options($property) {
		return \R::findAll( $property['name'] );
	}

}

?>