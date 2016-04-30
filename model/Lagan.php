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

	protected function controllerName($input) {
		 return ucfirst($input) . 'Controller';
	}

	// Check if file exists, and if do, if method exists
	protected function inputMethodExists($input, $method) {

		// Load input type controllers
		$controller = $this->controllerName($input);
		$file = ROOT_PATH . '/input/' . $input . '/' . $controller . '.php';
		if (file_exists($file)) {
			include $file;
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
			if ( $this->inputMethodExists($property['input'], 'set') ) {
			
				$controller = $this->controllerName($property['input']);
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

	// @param integer $id
	public function read($id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}

		return $bean;

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
			if ( $this->inputMethodExists($property['input'], 'delete') ) {
				$controller = $this->controllerName($property['input']);
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
	// This is needed for properties with input relation and file_select.
	public function populateProperties() {
		foreach ($this->properties as $key => $property) {

			// Check for the template, else fallback to text template.
			$file = $property['input'] . '/' . $property['input'] .'_input.html';
			if ( file_exists(ROOT_PATH . '/input/' . $file) ) {
				$this->properties[$key]['template'] = $file;
			} else {
				$this->properties[$key]['template'] =  'text/text_input.html';;
			}

			// Check for options method in input type controller
			if ( $this->inputMethodExists($property['input'], 'options') ) {
				$controller = $this->controllerName($property['input']);
				$c = new $controller;
				$this->properties[$key]['options'] = $c->options( $property );
			}

		}
	}

}

?>