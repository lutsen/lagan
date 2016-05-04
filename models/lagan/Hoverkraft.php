<?php

class Hoverkraft extends Lagan {

	function __construct() {
		$this->type = 'hoverkraft';
		
		// Description in admin interface
		$this->description = 'A hoverkraft is a very special vessel (and a great design angency).';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Title',
				'type' => 'text'
			],
			[
				'name' => 'description',
				'description' => 'Describe the kraft',
				'type' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => 'fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}' // glob pattern
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'type' => 'position'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'type' => 'slug'
			],
			[
				'name' => 'crew',
				'description' => 'Crew',
				'type' => 'onetomany'
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