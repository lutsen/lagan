<?php

class Crew extends Lagan {

	function __construct() {
		$this->type = 'crew';
		
		// Description in admin interface
		$this->description = 'Crewmembers to man the Hoverkraft.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Naam',
				'type' => 'text'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'type' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => 'fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}' // glob pattern
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'type' => 'manytoone'
			]
		];
		
		$this->rules = [
			'required' => [
				['title', 'hoverkraft']
			],
			'integer' => [
				['hoverkraft']
			]
		];
	}

}

?>