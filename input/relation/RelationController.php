<?php

class RelationController {

	// @param bean $bean
	// @param array $property
	public function set($bean, $property, $new_value) {

		// Check and set relation
		$relation = R::findOne( $property['name'], ' id = :id ', [ ':id' => $new_value ] );
		if ( !$relation ) {
			throw new Exception('The '.$property['name'].' for this '.$bean->type.' does not exist.');
		} else {
			return $relation;
		}

	}

	// @param array $property
	public function options($property) {
		return R::findAll( $property['name'] );
	}

}

?>