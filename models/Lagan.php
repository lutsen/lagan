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

		// Add all properties to bean
		foreach ( $this->properties as $property ) {

			$value = false; // We need to clear possible previous $value

			// Define property controller
			$c = new $property['type'];

			// New input fot the property
			if ( isset( $data[ $property['name'] ] ) || $_FILES[ $property['name'] ]['size'] > 0 ) {

				// Check if specific set property type method exists
				if ( method_exists( $c, 'set' ) ) {

					$value = $c->set( $bean, $property, $data[ $property['name'] ] );

				} else {

					$value = $data[ $property['name'] ];

				}

				if ($value) {
					$hasvalue = true;
				} else {
					$hasvalue = false;
				}

			// No new input for the property
			} else {

				// Check if property already has value for required validation
				if ( $property['required'] ) {

					if ( method_exists( $c, 'read' ) && $c->read( $bean, $property ) ) {
						$hasvalue = true;
					} else if ( $bean->{ $property['name'] } ) {
						$hasvalue = true;
					} else {
						$hasvalue = false;
					}

				}

			}

			// Check if proerty is required
			if ( $property['required'] && !$hasvalue ) {
				throw new Exception('Validation error. '.$property['description'].' is required.');
			}

			// Results from methods that return boolean values are not stored.
			// Many-to-many relations for example are stored in a seperate table.
			if ( !is_bool($value) ) {
				$bean->{ $property['name'] } = $value;
			}

		}

		$bean->modified = R::isoDateTime();
		R::store($bean);

		return $bean;

	}


	// CRUD:
	// Create, Read, Update and Delete methods

	/**
	 * Create
	 *
	 * @param array	$data	The raw data to create the Redbeean bean.
	 *
	 * @return bean	New bean with values based on $data.
	 */
	public function create($data) {

		// Create
		$bean = $this->universalCreate();

		// Catch exception, because bean might already have been created by property method.
		try {
			return $this->set($data, $bean);
		} catch (Exception $e) {
			// Delete bean
			$this->delete($bean->id);
			throw new Exception( $e->getMessage() );
		}

	}

	/**
	 * Read
	 *
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
			// NOTE: We're not executing the read method for each bean. Before I implement this I want to check potential performance issues.
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
	 * Update
	 *
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
	 * Delete
	 *
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
	 * Populate properties
	 *
	 * Properties can have optional values, for example relation and file_select.
	 * This method, if appropriate, queries properties for optional values, and populates them with them.
	 * Searches bean by an unique property, like an id or a slug.
	 * If $value is not set, nu bean is supplied to the property options method.
	 *
	 * @param mixed		$value		The value of the property
	 * @param string	$property	The name of the property, defaults to id
	 */
	public function populateProperties($value = false, $property_name = 'id') {

		if ( $value ) {

			$bean = R::findOne( $this->type, $property_name.' = :value ', [ ':value' => $value ] );
			if ( !$bean ) {
				throw new Exception('This '.$this->type.' does not exist.');
			}

		} else {
			$bean = false;
		}

		foreach ($this->properties as $key => $property) {

			// Check for options method in property type controller
			$c = new $property['type'];
			if ( method_exists( $c, 'options' ) ) {
				$this->properties[$key]['options'] = $c->options( $bean, $property );
			}

		}
	}

}

?>