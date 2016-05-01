<?php

class PositionController {

	// Does not actually update position of bean, bur "makes room for it" by updating the positions of other beans.
	// @param bean $bean
	// @param array $property
	// @param integer $new_value new position of bean 
	public function set($bean, $property, $new_value) {

		$all = R::findAll( $bean->getMeta('type') );
		$count_all = R::count( $bean->getMeta('type') );
		$curr_value = $bean->{ $property['name'] };
		
		// New bean
		if ( empty($curr_value) && $curr_value !== '0' ) {
		
			// Position at the bottom
			$curr_value = $count_all;
		
		}
		
		// No new input
		if ( ( empty($new_value) && $new_value !== '0' ) || $new_value == $curr_value ) {
		
			return $curr_value;
		
		} else {
		
			if ( $new_value < 0 ) $new_value = 0;
			if ( $new_value > $count_all - 1 ) $new_value = $count_all - 1;
			//if ( $curr_value !== $count_all && $new_value > $count_all - 1 ) $new_value = $count_all - 1;
			if ( $new_value < $curr_value ) {
				foreach ( $all as $b ) {
					if ($b->{ $property['name'] } >= $new_value AND $b->{ $property['name'] } < $curr_value) {
						$b->{ $property['name'] } = $b->{ $property['name'] } + 1;
						$b->modified = R::isoDateTime();
						R::store($b);
					}
				}
			} else if ( $new_value > $curr_value ) {
				foreach ( $all as $b ) {
					if ( $b->{ $property['name'] } <= $new_value AND $b->{ $property['name'] } > $curr_value ) {
						$b->{ $property['name'] } = $b->{ $property['name'] } - 1;
						$b->modified = R::isoDateTime();
						R::store($b);
					}
				}
			}
		
			return $new_value;
		
		}

	}
	
	// @param bean $bean
	// @param array $property
	public function delete($bean, $property) {

		$count_all = R::count( $bean->getMeta('type') );
		$bottom = $count_all - 1;
		$this->set($bean, $property, $bottom ); // No need to store new position of this bean

	}

}

?>