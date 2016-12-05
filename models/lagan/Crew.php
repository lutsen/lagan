<?php

namespace Lagan\Model;

/**
 * Example Lagan content model
 */

class Crew extends \Lagan\Lagan {

	function __construct() {
		$this->type = 'crew';
		
		// Description in admin interface
		$this->description = 'Crewmembers to man the Hoverkraft.';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Name',
				'required' => true,
				'searchable' => true,
				'type' => '\Lagan\Property\Str',
				'input' => 'text',
				'validate' => 'minlength(3)'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'searchable' => true,
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'email',
				'description' => 'Email address',
				'searchable' => true,
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea',
				'validate' => 'emaildomain'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'required' => true,
				'type' => '\Lagan\Property\Upload',
				'directory' => '/uploads', // Directory relative to APP_PATH (no trailing slash)
				'input' => 'upload',
				'validate' => [ ['extension', 'allowed=jpeg,jpg,gif,png'], ['size', 'size=1M'] ]
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'required' => true,
				'type' => '\Lagan\Property\Manytoone',
				'input' => 'manytoone'
			]
		];
	}

}

?>