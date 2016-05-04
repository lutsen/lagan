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

	protected function controllerName($type) {
		 return ucfirst($type) . 'Controller';
	}

	// Check if file exists, and if do, if method exists
	// @param string $type The property type
	// @param string $method The property type method
	protected function propertyMethodExists($type, $method) {

		// Load property type controllers
		$controller = $this->controllerName($type);
		$file = ROOT_PATH . '/properties/' . $type . '/' . $controller . '.php';
		if (file_exists($file)) {
			require_once $file;
			if ( method_exists( $controller, $method ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	// Set values
	// @param array $data $request->getParsedBody();
	// @param bean $bean
	public function set($data, $bean) {

		// Validate
		$this->validate($data, $this->rules);
		
		// Add all properties to bean
		foreach ( $this->properties as $property ) {
		
			// Check if specific set property type method exists
			if ( $this->propertyMethodExists($property['type'], 'set') ) {
			
				$controller = $this->controllerName($property['type']);
				$c = new $controller;
				$bean->{ $property['name'] } = $c->set( $bean, $property, $data[ $property['name'] ] );
			
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

	// Search bean by an unique property, like an id or a slug.
	// If $value is not set, it returns all beans of it's type.
	// @param $value The value of the property
	// @param string $property The name of the property, defaults to id
	public function read($value = false, $property_name = 'id') {

		if ( $value ) {

			// Single bean
			$bean = R::findOne( $this->type, $property_name.' = :value ', [ ':value' => $value ] );
			if ( !$bean ) {
				throw new Exception('This '.$this->type.' does not exist.');
			}

			// Check for property type specific read methods
			foreach ( $this->properties as $property ) {

				// Check if specific read property method exists
				if ( $this->propertyMethodExists($property['type'], 'read') ) {
					$controller = $this->controllerName($property['type']);
					$c = new $controller;
					$bean->{ $property['name'] } = $c->read( $bean, $property );
				}

			}

			return $bean;

		} else {

			// All beans of this type
			// Oder by position(s) if exits
			$add_to_query = '';
			foreach($this->properties as $property) {
				if ( $property['type'] === 'position' ) {
					$add_to_query = $property['name'].' ASC, ';
				}
			}
			return R::findAll( $this->type, ' ORDER BY '.$add_to_query.'title ASC ');

		}

	}

	// @param array $data
	// @param integer $id
	public function update($data, $id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}
		return $this->set($data, $bean);

	}
	
	// @param integer $id
	public function delete($id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}

		// Check for property type specific delete methods
		foreach ( $this->properties as $property ) {

			// Check if specific delete property method exists
			if ( $this->propertyMethodExists($property['type'], 'delete') ) {
				$controller = $this->controllerName($property['type']);
				$c = new $controller;
				$c->delete( $bean, $property );
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
	// This is needed for properties with type relation and file_select.
	public function populateProperties() {
		foreach ($this->properties as $key => $property) {

			// Check for the template, else fallback to text template.
			$file = $property['type'] . '/' . $property['type'] .'_input.html';
			if ( file_exists(ROOT_PATH . '/properties/' . $file) ) {
				$this->properties[$key]['template'] = $file;
			} else {
				$this->properties[$key]['template'] =  'text/text_input.html';;
			}

			// Check for options method in property type controller
			if ( $this->propertyMethodExists($property['type'], 'options') ) {
				$controller = $this->controllerName($property['type']);
				$c = new $controller;
				$this->properties[$key]['options'] = $c->options( $property );
			}

		}
	}

}

?>