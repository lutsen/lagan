<?php

namespace Lagan\Model;

class Crew extends \Lagan {

	function __construct() {
		$this->type = 'crew';
		
		// Description in admin interface
		$this->description = 'Crewmembers to man the Hoverkraft.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Naam',
				'type' => '\Lagan\Property\Str',
				'input' => 'text.html'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea.html'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => '\Lagan\Property\Fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}', // glob pattern
				'input' => 'fileselect.html'
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'type' => '\Lagan\Property\Manytoone',
				'input' => 'manytoone.html'
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