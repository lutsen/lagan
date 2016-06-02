<?php

/*
 * We use "*" as the seperator, since "-" is in the slug, and "+" is filtered out of the $_GET variable name by PHP.
 *
 * Syntax:
 * From: *min
 * To: *max
 * Contains: *has
 * Equal to: *is
 * Sort: sort
 *
 * Query structure examples:
 * [model]?*has=[search string] :				Searches all searchable properties of a model
 * [model]?[property]*has=[search string] :		Searches single property of a model
 * [model]?[property]*min=[number]
 * sort=[property]*asc
 */

class Search {

	protected $type;
	protected $criteria;
	protected $sequences;
	protected $model;

	/**
	 * Construct function
	 *
	 * @param string $type The Bean type to search
	 */
	function __construct($type) {

		$this->type = $type;

		$this->criteria = array(
			'*min',
			'*max',
			'*has',
			'*is'
		);

		// Sorting order
		$this->sequences = array(
			'*asc',
			'*desc'
		);

		$model_name = '\Lagan\Model\\' . ucfirst($type);
		$this->model = new $model_name;

	}

	/**
	 * Search function
	 *
	 * @param array[] $params Request parameters array
	 *
	 * @return bean[] Array of Redbean beans matching the search criteria
	 */
	public function find($params) {

		// Search
		$loop = 0; // To create different name for all search values
		$q = [];
		$s= [];
		$values = [];
		foreach ($params as $left => $right) {

			$lhs = $this->lefthandside($left);

			// Sort
			if ( $lhs == 'sort' ) {

				$rhs = $this->righthandside($right);

				$glue = ' '.strtoupper($rhs['order']).', ';
				$s[] = implode( $glue, $rhs['properties'] ) . ' ' . strtoupper($rhs['order']); // Add latest order

			// Find
			} else if ( $lhs ) {

				$p = [];
				foreach ($lhs['properties'] as $k => $v) {

					if ( $this->isSearchable($v) ) {

						if ($lhs['criterion'] === '*min') {

							// Create '>=' query
							$p[] = ' '.$v.' >= :value'.$loop.' ';
							// Add value to Redbean named search values array
							$values[ ':value'.$loop ] = floatval($right);

						} elseif ($lhs['criterion'] === '*max') {

							// Create '<=' query
							$p[] = ' '.$v.' <= :value'.$loop.' ';
							// Add value to Redbean named search values array
							$values[ ':value'.$loop ] = floatval($right);

						} elseif ($lhs['criterion'] === '*has') {

							// Create 'LIKE' query
							$p[] = ' '.$v.' LIKE :value'.$loop.' ';
							// Add value to Redbean named search values array
							$values[ ':value'.$loop ] = '%'.$right.'%';

						} elseif ($lhs['criterion'] === '*is') {

							// Create '=' query
							$p[] = ' '.$v.' = :value'.$loop.' ';
							// Add value to Redbean named search values array
							$values[ ':value'.$loop ] = $right;

						}

					} // End isSearchable($v)

				} // End foreach $lhs['properties']


				// Implode array to create nice 'OR' query
				$q[] = implode('OR', $p);

				$loop++;

			} // End if else

		} // End foreach $params

		// Query

		// Implode array to create nice '( #query ) AND ( #query )'
		if (count($q) > 1) {
			$query = '(' . implode(') AND (', $q) . ')';
		} else if (count($q) > 0) {
			$query = $q[0];
		} else {
			return false;
		}

		// Implode different sort arrays
		$sort = '';
		if (count($s) > 0) {
			$sort = ' ORDER BY ' . implode(', ', $s);
		}

		return R::find( $this->type, $query.$sort, $values );

	}

	/*
	 * Check if property exists and is searchable
	 *
	 * @param string $propertyname
	 *
	 * @return boolean
	 */
	private function isSearchable($propertyname) {
		foreach ($this->model->properties as $property) {
			if ( $property['name'] == $propertyname && $property['searchable'] ) {
				return true;
			}
		}
	}

	/*
	 * Analyse the left hand side of the search equation
	 *
	 * @param string $input
	 *
	 * @return string[] Array containing criterion and nested array with properties to search
	 */
	private function lefthandside($input) {
		if ($input == 'sort') { // Sorting happens after searching
			return 'sort';
		} else {
			foreach($this->criteria as $criterion) {
				if (substr($input, strlen($criterion)*-1) == $criterion) {
					$return = [ 'criterion'=>$criterion ];
					if (strlen($input) > strlen($criterion)) {
						$return['properties'] = explode('*', substr($input, 0, strlen($criterion)*-1)); // Array of properties
					} else {
						// If no properties are defined, return all properties
						foreach ($this->model->properties as $property) {
							$return['properties'][] = $property['name'];
						}
					}
					return $return;
				}
			}
		}
		return false;
	}

	/*
	 * Analyse the right hand side of the search equation
	 *
	 * @param string $input
	 *
	 * @return string[] Array containing order and nested array with properties to sort by
	 */
	private function righthandside($input) {
		foreach($this->sequences as $order) {
			if (substr($input, strlen($order)*-1) == $order) {
				$return = [ 'order' => substr($order, 1) ];
				if (strlen($input) > strlen($order)) {
					$return['properties'] = explode('*', substr($input, 0, strlen($order)*-1)); // Array of properties
				} else {
					return false;
				}
				return $return;
			}
		}
		return false;
	}

}

?>