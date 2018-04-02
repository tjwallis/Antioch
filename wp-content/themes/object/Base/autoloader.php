<?php
class Autoloader
{
	/**
	 * Registers Slate_Autoloader as an SPL autoloader.
	 *
	 * @param boolean $prepend
	 */
	public static function register($prepend = false)
	{
		if (version_compare(phpversion(), '5.3.0', '>=')) {
			spl_autoload_register(array(new self, 'autoload'), true, $prepend);
		} else {
			spl_autoload_register(array(new self, 'autoload'));
		}
	}

	/**
	 * Handles autoloading of Slate classes.
	 *
	 * @param string $class
	 */
	public static function autoload($classname)
	{
		$class = str_replace( '\\', DIRECTORY_SEPARATOR, str_replace( '_', '-', $classname ) );

		// create the actual filepath
		$filePath = STYLESHEETPATH . DIRECTORY_SEPARATOR  . $class . '.php';

		// check if the file exists
		if(file_exists($filePath))
		{
			// require once on the file
			require_once $filePath;
		}
    }
}
