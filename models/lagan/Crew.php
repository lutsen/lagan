<?php

namespace Lagan\Model;

/**
 * Example Lagan content model
 */

class Crew extends \Lagan {

	function __construct() {
		$this->type = 'crew';
		
		// Description in admin interface
		$this->description = 'Crewmembers to man the Hoverkraft.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Name',
				'type' => '\Lagan\Property\Str',
				'input' => 'text'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => '\Lagan\Property\Fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}', // glob pattern
				'input' => 'fileselect'
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'type' => '\Lagan\Property\Manytoone',
				'input' => 'manytoone'
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