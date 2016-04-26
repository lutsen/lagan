<?php

class Position {

	// Does not actually update position of bean, bur "makes room for it" by updating the positions of other beans.
	// @param bean $bean
	// @param integer $new_value new position of bean 
	public function setPosition($bean, $new_value) {
	
		$all = R::findAll( $bean->getMeta('type') );
		$count_all = R::count( $bean->getMeta('type') );
		$curr_value = $bean->position;
		
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
			if ( $new_value > $count_all ) $new_value = $count_all;
			if ( $curr_value !== $count_all && $new_value > $count_all - 1 ) $new_value = $count_all - 1;
			if ( $new_value < $curr_value ) {
				foreach ( $all as $b ) {
					if ($b->position >= $new_value AND $b->position < $curr_value) {
						$b->position = $b->position + 1;
						$b->modified = R::isoDateTime();
						R::store($b);
					}
				}
			} else if ( $new_value > $curr_value ) {
				foreach ( $all as $b ) {
					if ( $b->position <= $new_value AND $b->position > $curr_value ) {
						$b->position = $b->position - 1;
						$b->modified = R::isoDateTime();
						R::store($b);
					}
				}	
			}
		
			return $new_value;
		
		}

	}
	
	public function deletePosition($bean) {

		if ( !empty($bean->position) || $bean->position === 0 || $bean->position === '0' ) {
			$count_all = R::count( $bean->getMeta('type') );
			$bottom = $count_all - 1;
			$this->setPosition($bean, $bottom ); // No need to store new position of this bean
		}

	}

}

?>