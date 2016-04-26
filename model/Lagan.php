<?php

/*

Lagan base model for all Lagan models

*/

class Lagan {

	protected $type;
	// Allowed properties for an object,
	// that can or need to be directly written to the DB
	public $properties;
	
	// Validation rules
	public $rules;

	protected function universalCreate() {
	
		$bean = R::dispense($this->type);
		$bean->created = R::isoDateTime();
		
		return $bean;

	}
	
	// Set values
	// @param array $data $request->getParsedBody();
	// @param bean $bean
	public function set($data, $bean) {

		// Validate
		$this->validate($data, $this->rules);
		
		// Add all properties to bean
		foreach ( $this->properties as $property ) {
		
			// Check if specific set property method exists
			$method_name = $this->getMethodName( $property['name'], 'set' );
			if ( method_exists( $this, $method_name ) ) {
			
				$bean->{ $property['name'] } = $this->{ $method_name }( $bean, $data[ $property['name'] ] );
			
			} elseif ( $property['input'] === 'relation' ) {
				
				// Check and set relation
				$bean->{ $property['name'] } = R::findOne( $property['name'], ' id = :id ', [ ':id' => $data[ $property['name'] ] ] );
				if ( !$bean->{ $property['name'] } ) {
					R::trash( $bean );
					throw new Exception('The '.$property['name'].' for this '.$this->type.' does not exist.');
				}

			} else {

				if ( $data[ $property['name'] ] && strlen( $data[ $property['name'] ] ) > 0 ) {
					$bean->{ $property['name'] } = $data[ $property['name'] ];
				}

			}
	
		}
		
		$bean->modified = R::isoDateTime();
		R::store($bean);
		return $bean;
	
	}


	
	// CRUD //
	// ---- //


	
	// @param array $data $request->getParsedBody();
	public function create($data) {
	
		// Create
		$bean = $this->universalCreate();

		return $this->set($data, $bean);
	
	}
	
	// @param integer $id
	public function read($id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}
		
		return $bean;
		
	}

	// @param array $user_data
	// @param integer $id
	public function update($data, $id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}
		
		return $this->set($data, $bean);
		
	}
	
	// @param array $user_data
	// @param integer $id
	public function delete($id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}
		
		// Check for property specific delete methods
		foreach ( $this->properties as $property ) {
		
			$method_name = $this->getMethodName( $property['name'], 'delete' );
			if ( method_exists( $this, $method_name ) ) {
				$this->{ $method_name }( $bean );		
			}

		}
		
		R::trash( $bean );
		
	}
	
	
	
	// HELPER METHODS //
	// -------------- //
	
	// Validation
	protected function validate($variables, $rules) {

		$v = new Valitron\Validator($variables);
		$v->rules($rules);
		if( !$v->validate() ) {
			$exception = 'Validation error.';
			foreach ($v->errors() as $error) {
				$exception .= ' '.$error[0].'.';
			}
			throw new Exception($exception);
		}

	}
	
	// If appropriate, query properties for optional values, populate them with them.
	// This is needed for properties with input relation and file_select.
	public function populateProperties() {
		foreach ($this->properties as $key => $property) {

			if ( $property['input'] === 'relation' ) {

				$this->properties[$key]['options'] = R::findAll( $property['name'] );

			} elseif ( $property['input'] === 'image_select' ) {
			
				$images = glob( APP_PATH.'/'.$property['directory'].'/' . '#\.(jpe?g|gif|png)$#i');
				foreach ($images as $image) {
					$this->properties[$key]['images'][] = $image->getName();
				}

			}

		}
	}
	
	// Get method name of property
	// Add $prepend and convert snake_case to camelCase
	// http://www.phpro.org/examples/Underscore-To-Camel-Case.html
	protected function getMethodName($property_name, $prepend) {
		$property_name[0] = strtoupper( $property_name[0] );
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return $prepend . preg_replace_callback('/_([a-z])/', $func, $property_name);
	}

}

?>