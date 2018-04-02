<?php
/**
 * @file Fields.php
 * @author DBS>Interactive
 *
 * Helper class to register ACF Fields and access them.
 */

Namespace Base;

Class Fields {
	var $locations,
		$fields,
		$title,
		$id;

	function __construct() {
		
	}

	function add_location($location) {
		$this->locations[] = $location;
	}

	function add_field($field, $group = false) {
		$field['parent'] = $key;

		if($group) {
			$this->fields[] = $field;
		} else {
			acf_add_local_field($field);
		}

	}

	function register_field() {

	}

	public function construct_array() {
		return $array = array(
			'id' => $this->id,
			'title' => $this->title,
			'fields' => $this->get_fields(),
			'locations' => $this->get_locations(),
			'options' => $this->get_options(),
			'menu_order' => $this->menu_order,
		);
	}
}

