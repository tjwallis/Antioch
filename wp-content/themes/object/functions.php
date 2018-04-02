<?php
/**
 * @file functions.php
 * @author DBS>Interactive
 * @subpackage Slate
 * @since Slate 0.1.0
 */
namespace Slate;
require_once 'Base/autoloader.php';
\Autoloader::register();

use \Base\Utilities as Utils;
use \Base\Config;
use Slate\Slate;

add_action('after_setup_theme', function() {
	global $theme, $utils, $dbs;
	$theme = new Slate();
	$utils = new Utils();
	$dbs = new Config();
});
