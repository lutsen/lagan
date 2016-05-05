<?php

namespace Lagan\Model;

class Hoverkraft extends \Lagan {

	function __construct() {
		$this->type = 'hoverkraft';
		
		// Description in admin interface
		$this->description = 'A hoverkraft is a very special vessel (and a great design angency).';

		$this->properties = [
			// Allways have a title
			[
				'name' => 'title',
				'description' => 'Title',
				'type' => 'text',
				'input' => 'text.html'
			],
			[
				'name' => 'description',
				'description' => 'Describe the kraft',
				'type' => 'text',
				'input' => 'textarea.html'
			],
			[
				'name' => 'picture',
				'description' => 'Image',
				'type' => 'fileselect',
				'pattern' => APP_PATH.'/files/*.{jpeg,jpg,gif,png}', // glob pattern
				'input' => 'fileselect.html'
			],
			[
				'name' => 'position',
				'description' => 'Order',
				'type' => 'position',
				'input' => 'text.html'
			],
			[
				'name' => 'slug',
				'description' => 'Slug',
				'type' => 'slug',
				'input' => 'text.html'
			],
			[
				'name' => 'crew',
				'description' => 'Crew',
				'type' => 'onetomany',
				'input' => 'onetomany.html'
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