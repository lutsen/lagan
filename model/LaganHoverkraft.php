<?php

class LaganHoverkraft extends Lagan {

	function __construct() {
		$this->type = 'hoverkraft';
		
		// Description in admin interface
		$this->description = 'A hoverkraft is a very special vessel (and a great design angency).';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Title',
				'input' => 'text'
			],
			[
				'name' => 'description',
				'description' => 'Describe the kraft',
				'input' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'input' => 'image_select',
				'directory' => 'files'
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'input' => 'text'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'input' => 'text'
			]
		];
		
		$this->rules = array(
			'required' => [
				['title', 'picture']
			],
			'integer' => [
				['position']
			]
		);
	}
	


	// PROPERTY METHODS //
	// ---------------- //
	
	// Property methods are methods to set and delete individual properties of bean types.

	protected function setPosition($bean, $new_value) {

		$position = new Position();
		return $position->setPosition($bean, $new_value);

	}
	
	protected function deletePosition($bean) {
	
		$position = new Position();
		$position->deletePosition($bean);

	}
	
	protected function setSlug($bean, $new_value) {

		$slug = new Slug();
		return $slug->setSlug($bean, $new_value);

	}

}

?>