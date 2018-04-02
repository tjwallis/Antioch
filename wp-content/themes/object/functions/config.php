<?php
/**
* @file config.php
*
* This file holds theme related variables that help control functionality for
* the DBS theme. It is modified dynamically by the DBS installation script, wp_install.sh.
*
* @author Hal Burgiss  2015-01-31
*
*/

$dbs = new stdClass();

# For our purposes, the theme name is the folder name that holds the WP theme.
$dbs->theme_name = ( basename( dirname( dirname( __FILE__ ) ) ) );

# Declare possible variable defaults.
$dbs->has_picturefill = false;
$dbs->has_blog = false;
$dbs->has_ecommerce = false ;
$dbs->has_news_feed = false;
$dbs->has_search = true;
$dbs->has_image_slider = true;
$dbs->has_ie8_support=true;
$dbs->has_open_graph=false;
$dbs->has_critical_css=true;
$dbs->is_template_debug= ( $app_mode === LOCAL ) ? true :false ;
$dbs->has_breadcrumbs=false;

# dynamically added settings from wp_install.sh:
