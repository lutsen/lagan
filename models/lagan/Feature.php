<?php

namespace Lagan\Model;

/**
 * Example Lagan content model
 */

class Feature extends \Lagan {

	function __construct() {
		$this->type = 'feature';
		
		// Description in admin interface
		$this->description = 'Feastures the Hoverkrafts can have. Like a turbo, or a coffee machine.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Name',
				'type' => '\Lagan\Property\Str',
				'input' => 'text'
			],
			[
				'name' => 'description',
				'description' => 'Describe the feature',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkrafts with this feature',
				'type' => '\Lagan\Property\Manytomany',
				'input' => 'onetomany'
			]
		];
		
		$this->rules = [
			'required' => [
				['title', 'hoverkraft']
			]
		];
	}

}

?>