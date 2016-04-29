<?php

class LaganCrew extends Lagan {

	function __construct() {
		$this->type = 'crew';
		
		// Description in admin interface
		$this->description = 'Crewmembers to man the Hoverkraft.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Naam',
				'input' => 'text'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'input' => 'textarea'
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'input' => 'relation'
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