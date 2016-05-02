<?php

/*

 The Onetomany property type controller enables a one-to-many relation between 2 Lagan models.
 The name of the property should be the name of the Lagan model this model can have a one-to-many relation with.
 For this to work properly the other model should have a many-to-one relation with this model.
 So in our examplet he LaganHoverkraft model has a one-to-many relation with the LaganCrew model, and the LaganCrew model has a many-to-one relation with the LaganHoverkraft model.

*/

class OnetomanyController {

	// @param bean $bean
	// @param array $property
	public function read($bean, $property) {

		$list_name = 'own'.ucfirst($property['name']).'List';
		return  $bean->{ $list_name };

	}

	// @param array $property
	public function options($property) {
		return R::findAll( $property['name'] );
	}

}

?>