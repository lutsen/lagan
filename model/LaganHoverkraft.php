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
				'input' => 'fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}' // glob pattern
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'input' => 'position'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'input' => 'slug'
			]
		];
		
		$this->rules = [
			'required' => [
				['title', 'picture']
			],
			'integer' => [
				['position']
			]
		];
	}

}

?>