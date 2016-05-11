<?php

/**
 * Lagan base model for all Lagan models.
 * Each type of content has it's own model that extends this model.
 * Each model has a type, description and properties.
 * The validation rules are optional.
*/

class Lagan {

	/** @var string $type The type of the model. It is the same as the modelname in lowercase, and defines the name of the RedBean beans and the name of the table in the database. */
	protected $type;

	/** @var string $description The description of the model displayed in the admin interface. */
	public $description;

	/** @var array $properties An array defining the different content data-fields of the model. Each property is an array with at least the following keys: name, description, type, input. There can be other optional keys. */
	public $properties;

	/** @var array $rules An array of validation rules, based on the Valitron library. */
	public $rules;

	/**
	 * Dispenses a Redbean bean ans sets it's creation date.
	 *
	 * @return bean
	 */
	protected function universalCreate() {
	
		$bean = R::dispense($this->type);
		$bean->created = R::isoDateTime();
		
		return $bean;

	}


	/**
	 * Set values for a bean. Used by Create and Update.
	 * Checks for each property if a "set" method exists for it's type.
	 * If so, it executes it.
	 *
	 * @param array	$data	The raw data, usually from the Slim $request->getParsedBody()
	 * @param bean	$bean
	 *
	 * @return bean	Bean with values based on $data.
	 */
	public function set($data, $bean) {

		// Validate
		$this->validate($data, $this->rules);
		
		// Add all properties to bean
		foreach ( $this->properties as $property ) {
		
			// Check if specific set property type method exists
			$c = new $property['type'];
			if ( method_exists( $c, 'set' ) ) {
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


	/*
	 * CRUD:
	 * Create, Read, Update and Delete methods
	 */

	/**
	 * Create.
	 *
	 * @param array	$data	The raw data to create the Redbeean bean.
	 *
	 * @return bean	New bean with values based on $data.
	 */
	public function create($data) {

		// Create
		$bean = $this->universalCreate();

		return $this->set($data, $bean);

	}

	/**
	 * Read.
	 * Search bean by an unique property, like an id or a slug.
	 * If $value is not set, it returns all beans of it's type.
	 *
	 * @param mixed		$value		The value of the property
	 * @param string	$property	The name of the property, defaults to id
	 *
	 * @return mixed	Can return single bean or array of beans if $value is not set.
	 */
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
				$c = new $property['type'];
				if ( method_exists( $c, 'read' ) ) {
					$bean->{ $property['name'] } = $c->read( $bean, $property );
				}

			}

			return $bean;

		} else {

			// All beans of this type
			// Oder by position(s) if exits
			$add_to_query = '';
			foreach($this->properties as $property) {
				if ( $property['type'] === '\\Lagan\\Property\\Position' ) {
					$add_to_query = $property['name'].' ASC, ';
				}
			}
			return R::findAll( $this->type, ' ORDER BY '.$add_to_query.'title ASC ');

		}

	}

	/**
	 * Update.
	 * Update the data of the bean.
	 *
	 * @param array		$data The raw data to create the Redbeean bean.
	 * @param integer	$id
	 *
	 * @return bean		Bean with updated values based on $data.
	 */
	public function update($data, $id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}
		return $this->set($data, $bean);

	}
	
	/**
	 * Delete.
	 * Delete the bean.
	 *
	 * @param integer	$id
	 */
	public function delete($id) {

		$bean = R::findOne( $this->type, ' id = :id ', [ ':id' => $id ] );
		if ( !$bean ) {
			throw new Exception('This '.$this->type.' does not exist.');
		}

		// Check for property type specific delete methods
		foreach ( $this->properties as $property ) {

			// Check if specific delete property method exists
			$c = new $property['type'];
			if ( method_exists( $c, 'delete' ) ) {
				$c->delete( $bean, $property );
			}

		}

		R::trash( $bean );

	}



	// HELPER METHODS

	/**
	 * Validation.
	 * Validate properties with the Valitron library.
	 * Throws an error if validation fails.
	 *
	 * @param array	$variables
	 * @param array	$rules
	 */
	protected function validate($variables, $rules) {

		$v = new \Valitron\Validator($variables);
		$v->rules($rules);
		if( !$v->validate() ) {
			$exception = 'Validation error.';
			foreach ($v->errors() as $error) {
				$exception .= ' '.$error[0].'.';
			}
			throw new Exception($exception);
		}

	}

	/**
	 * If appropriate, query properties for optional values, populate them with them.
	 * This is needed for properties with types like for example relation and file_select.
	 */
	public function populateProperties() {
		foreach ($this->properties as $key => $property) {

			// Check for options method in property type controller
			$c = new $property['type'];
			if ( method_exists( $c, 'options' ) ) {
				$this->properties[$key]['options'] = $c->options( $property );
			}

		}
	}

}

?>