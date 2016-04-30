<?php

class FileselectController {

	// @param array $property
	public function options($property) {

		$return = [];
		$files = glob( $property['pattern'] );
		foreach ($files as $file) {
			$return[] = $file->getName();
		}
		return $return;

	}

}

?>