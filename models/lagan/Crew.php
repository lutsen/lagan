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
			// Allways have a name
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
				'type' => '\Lagan\Property\Upload',
				'extensions' => 'jpeg,jpg,gif,png', // Allowed extensions
				'max' => '2M', // Maximum filesize
				'directory' => '/uploads', // Directory relative to APP_PATH (no trailing slash)
				'input' => 'upload'
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