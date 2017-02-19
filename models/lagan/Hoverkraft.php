<?php

namespace Lagan\Model;

/**
 * Example Lagan content model
 */

class Hoverkraft extends \Lagan\Lagan {

	function __construct() {
		$this->type = 'hoverkraft';
		
		// Description in admin interface
		$this->description = 'A hoverkraft is a very special vessel (and a great design angency).';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Title',
				'required' => true,
				'searchable' => true,
				'type' => '\Lagan\Property\Str',
				'input' => 'text'
			],
			[
				'name' => 'description',
				'description' => 'Describe the kraft',
				'searchable' => true,
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'required' => true,
				'type' => '\Lagan\Property\Fileselect',
				'extensions' => 'jpeg,jpg,gif,png', // Allowed extensions
				'directory' => '/files', // Directory relative to APP_PATH (no trailing slash)
				'input' => 'fileselect'
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'autovalue' => true,
				'type' => '\Lagan\Property\Position',
				'input' => 'text'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'autovalue' => true,
				'type' => '\Lagan\Property\Slug',
				'input' => 'text'
			],
			[
				'name' => 'crew',
				'description' => 'Crewmembers for this Hoverkraft',
				'type' => '\Lagan\Property\Onetomany',
				'input' => 'tomany'
			],
			[
				'name' => 'feature',
				'description' => 'Features this Hoverkraft has',
				'type' => '\Lagan\Property\Manytomany',
				'input' => 'tomany'
			]
		];
	}

}

?>