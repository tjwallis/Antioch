<?php
/*
 * Smk Theme View
 *
 * Do not replace your theme functions.php file! Copy the code from this file in your
 * theme functions.php on top of other files that are included.
 *
 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:   2014-06-20 03:40:47
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-06-23 22:46:40
 *
 */
################################################################################
/**
 * Theme View
 *
 * Include a file and(optionally) pass arguments to it.
 *
 * @param string $file The file path, relative to theme root
 * @param array $args The arguments to pass to this file. Optional.
 * Default empty array.
 *
 * @return object Use render() method to display the content.
 */
Namespace Base;
use Exception;

class ThemeView{
	private $args;
	private $file;

	public function __get($name) {
		return $this->args[$name];
	}

	public function __construct($file, $args = array()) {
		$this->file = $file;
		$this->args = $args;
	}

	public function __isset($name){
		return isset( $this->args[$name] );
	}

	public function render() {
		if( locate_template($this->file) ){
			include( locate_template($this->file) );//Theme Check free. Child themes support.
		}
	}

	private function check($key, $value) {
			if(!isset($this->$key) && empty($value)) {
				throw new Exception($this->file . ' Expects an value for ' . $key );
			} elseif( !isset($this->$key) && !empty($value) ) {
				$this->$key = $value;
			}
	}

	public function expected_args($args = array()){
		foreach($args as $key => $value ) {
			try {
				$this->check($key, $value);
			} catch (Exception $e ) {
				echo 'Error:', $e->getMessage(), "\n";
			}
		}
	}
}
