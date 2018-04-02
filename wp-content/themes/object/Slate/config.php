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

# For our purposes, the theme name is the folder name that holds the WP theme.
$this->theme_name = ( basename( dirname( dirname( __FILE__ ) ) ) );

# Declare possible variable defaults.
$this->has_picturefill = false;
$this->has_blog = false;
$this->has_ecommerce = false ;
$this->has_news_feed = false;
$this->has_search = true;
$this->has_image_slider = true;
$this->has_ie8_support=true;
$this->has_open_graph=false;
$this->has_critical_css=true;
//$this->is_template_debug= ( $app_mode === LOCAL ) ? true : false;
$this->has_breadcrumbs=false;

# dynamically added settings from wp_install.sh:


