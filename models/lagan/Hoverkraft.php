<?php

namespace Lagan\Model;

/**
 * Example Lagan content model
 */

class Hoverkraft extends \Lagan {

	function __construct() {
		$this->type = 'hoverkraft';
		
		// Description in admin interface
		$this->description = 'A hoverkraft is a very special vessel (and a great design angency).';

		$this->properties = [
			// Allways have a name
			[
				'name' => 'title',
				'description' => 'Title',
				'type' => '\Lagan\Property\Str',
				'input' => 'text'
			],
			[
				'name' => 'description',
				'description' => 'Describe the kraft',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => '\Lagan\Property\Fileselect',
				'extensions' => 'jpeg,jpg,gif,png', // Allowed extensions
				'directory' => '/files', // Directory relative to APP_PATH (no trailing slash)
				'input' => 'fileselect'
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'type' => '\Lagan\Property\Position',
				'input' => 'text'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'type' => '\Lagan\Property\Slug',
				'input' => 'text'
			],
			[
				'name' => 'crew',
				'description' => 'Crewmembers for this Hoverkraft',
				'type' => '\Lagan\Property\Onetomany',
				'input' => 'onetomany'
			],
			[
				'name' => 'feature',
				'description' => 'Features this Hoverkraft has',
				'type' => '\Lagan\Property\Manytomany',
				'input' => 'onetomany'
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