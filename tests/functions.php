<?php

/**
 * Test helper functions
 */

function setStr( $str, $length ) {
	$length = (int)$length;
	if ( isset( $val ) && count( $val ) <= $length ) {
		return str_pad($val, $length, 'x'); 
	} else if ( isset( $val ) && count( $val ) > $length ) {
		return substr($val, 0, $length);
	} else {
		return str_pad('x', $length, 'x'); 
	}
}

function betweenBrackets( $str ) {
	return trim( substr( $str, strpos( $str, '(' ) + 1, strpos( $str, ')' ) - 1 ) );
}

function createContent( $object ) {
	$data = [];

	foreach ( $object->properties as $property ) {

		// Loop through all properties
		switch ( $property['type'] ) {

			// Value: File path
			case '\Lagan\Property\Fileselect':
				$val = '/files/hoverkraft-1.jpg';
				break;

			// Value: The id of the Instagram post.
			// The Instagram embed code is stored in $property['name'].'_embed'
			case '\Lagan\Property\Instaembed':
				$val = 'BNq-AmcDoYp';
				break;

			// Value: An array with id's of the objects the object with this property has a many-to-many relation with.
			case '\Lagan\Property\Manytomany':
				$val = [1];
				break;

			// Value: The id of the object the object with this property has a many-to-one relation with.
			case '\Lagan\Property\Manytoone':
				$val = 1;
				break;

			// Value: An array with id's of the objects the object with this property has a one-to-many relation with.
			case '\Lagan\Property\Onetomany':	
				$val = [1];
				break;

			// Value: The input position of the object with this property.
			case '\Lagan\Property\Position':
				$val = 0;
				break;

			// Value: The input string for the slug of the object with this property.
			case '\Lagan\Property\Slug':
				$val = 'sluggish';
				break;

			// Value: The input string of this property.
			case '\Lagan\Property\Str':
				// Check validation
				if ( isset( $property['validate'] ) ) {
					$rules = array_map( 'trim', explode( '|', $property['validate'] ) );
					// Sort array to make sure alpha rules are set before length rules
					sort($rules);
					// Set right string according to each validation rule
					foreach ($rules as $rule) {
						switch (true) {
							case $rule == 'alpha':
								$val = 'alpha rule';
								break;

							case $rule == 'alphanumeric':
								$val = '123 alpha number rule';
								break;

							case $rule == 'alphanumhyphen':
								$val = '1-2_3 alpha num hyphen rule';
								break;

							case substr($rule, 0, 6) == 'length':
								$optioms = betweenBrackets( $rule );
								$optioms = array_map( 'trim', explode( ',', $optioms ) );
								$val = setStr( !isset($test) ? 'lorum' : $val , $optioms[0] );
								break;

							case substr($rule, 0, 9) == 'minlength':
								$min = betweenBrackets( $rule );
								$val = setStr( !isset($test) ? 'lorum' : $val, $min );
								break;

							case substr($rule, 0, 9) == 'maxlength':
								$max = betweenBrackets( $rule );
								$val = setStr( !isset($test) ? 'lorum' : $val, $max );
								break;

							case $rule == 'fullname':
								$val = 'Full Name';
								break;

							case $rule == 'number':
								$val = 19.99;
								break;

							case $rule == 'integer':
								$val = 19;
								break;

							case substr($rule, 0, 8) == 'lessthan':
								$options = betweenBrackets( $rule );
								$optioms = array_map( 'trim', explode( ',', $optioms ) );
								$val = $optioms[0] - 1;
								break;

							case substr($rule, 0, 11) == 'greaterthan':
								$options = betweenBrackets( $rule );
								$optioms = array_map( 'trim', explode( ',', $optioms ) );
								$val = $optioms[0] + 1;
								break;

							case substr($rule, 0, 7) == 'between':
								$options = betweenBrackets( $rule );
								$optioms = array_map( 'trim', explode( ',', $optioms ) );
								$val = $optioms[0] + 1;
								break;

							case $rule == 'email':
								$val = 'somebody@dsfflhfduifyd.com';
								break;

							case $rule == 'emaildomain':
								$val = 'somebody@gmail.com';
								break;

							case $rule == 'url':
								$val = 'ftp://www.hoverkraft.nl';
								break;

							case $rule == 'website':
								$val = 'www://www.hoverkraft.nl';
								break;

							case substr($rule, 0, 4) == 'date':
								$format = betweenBrackets( $rule );
								$val = DateTime::createFromFormat( $format, '19-Dec-2016' );
								break;

							case $rule == 'datetime':
								$val = '2016-12-19 14:34:00';
								break;

							case $rule == 'time':
								$val = '14:34:00';
								break;

							default:
								throw new \Exception( 'The validation rule "'.$rule.'" is not part of this test.' );
								break;
						}
					}
				} else {
					$val = 'Some string input.';
				}
				break;

			// Value: No value is submtted, instead $_FILES[ $property['name'] ] is used.
			case '\Lagan\Property\Upload':
				// Simulate file upload
				$tmp = APP_PATH.'/files/tmp_file_'.uniqid().'.jpg';
				copy( APP_PATH.'/files/hoverkraft-1.jpg', $tmp );
				$_FILES = array(
					$property['name'] => array(
						'name' => 'hoverkraft-1.jpg',
						'type' => 'image/jpeg',
						'size' => 152000,
						'tmp_name' => $tmp,
						'error' => 0
					)
				);
				break;

			default:
				throw new \Exception( 'This property type is not in the createContent function.' );

		}

		if ( isset($val) ) {
			$data[ $property['name'] ] = $val;
		}

	}

	return $data;

}

?>