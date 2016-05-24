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
				'input' => 'text',
				'required' => true,
				'validate' => 'minlength(3)'
			],
			[
				'name' => 'bio',
				'description' => 'Biography',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea'
			],
			[
				'name' => 'email',
				'description' => 'Email address',
				'type' => '\Lagan\Property\Str',
				'input' => 'textarea',
				'validate' => 'emaildomain'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => '\Lagan\Property\Upload',
				'directory' => '/uploads', // Directory relative to APP_PATH (no trailing slash)
				'input' => 'upload',
				'validate' => [ ['extension', 'allowed=jpeg,jpg,gif,png'], ['size', 'size=1M'] ]
			],
			[
				'name' => 'hoverkraft',
				'description' => 'Hoverkraft',
				'type' => '\Lagan\Property\Manytoone',
				'input' => 'manytoone',
				'required' => true
			]
		];
	}

}

?>