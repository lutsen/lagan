<?php

class FileselectController {

	// @param array $property
	public function options($property) {

		$return = [];
		$files = glob( $property['pattern'], GLOB_BRACE );
		foreach ($files as $file) {
			$return[] = [
				'path' => substr( $file, strlen(APP_PATH) ),
				'name' => basename($file)
			];
		}
		return $return;

	}

}

?>