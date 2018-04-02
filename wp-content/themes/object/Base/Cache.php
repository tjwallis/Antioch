<?php
/**
 * This gives you a beautiful array of methods for caching objects, strings,
 * and more using WordPress Transients.
 *
 * @file Cache.php
 */

Namespace Base;

class Cache {

	public function __construct(){
		$this->filters();
		$this->actions();
	}

	private function actions(){
		// add_action( 'wp_update_nav_menu', array( $this, 'delete_menu_transients') );
	}

	private function filters(){}

	private function delete_menu_transients() {
		delete_transient( 'main_menu_query' );
	}
}
