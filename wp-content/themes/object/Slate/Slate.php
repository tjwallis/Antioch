<?php
/*
 * Slate extends Base the base theme object.
 */
Namespace Slate;

use \Base\Theme;
use \Base\SimpleWalker;

class Slate extends Theme {

	public $slug = "slate";

	public function __construct() {
		parent::__construct();

		//Setup text domain
		load_theme_textdomain( $this->slug, get_template_directory() . '/languages' );

		$this->actions();
		$this->filters();

		$this->build_options_page();
	}

	/**
	 * Actions
	 */
	private function actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'register_menus' ) );
		add_action( 'acf/init', array( $this, 'slides_add_local_field_groups') );
		add_action( 'admin_menu', array( $this, 'remove_admin_menu_items') );
		add_action( 'init' , array( $this, 'add_custom_image_sizes') );
		add_action( 'widgets_init', array( $this, 'register_sidebar') );
	} // actions

	/**
	 * Filters
	 */
	private function filters(){}


	/**
	 * Register New Navigation Menus
	 */
	function register_menus() {
		register_nav_menus(
			array(
				'main_menu' => __( 'Main Menu', $this->slug ),
				'footer_menu' => __( 'Footer Menu', $this->slug ),
			)
		);
	}



	/**
	 * Enqueue scripts and styles
	 * Needs to be public So that wordpress can call it.
	 */
	public function enqueue_scripts(){}

	/**
	 * Removes menu items from the dashboard.
	 * The client isn't using Blog Posts so let's remove it.
	 */
	function remove_admin_menu_items(){
		return false;
	}

	/**
	 * Adds custom image sizes.
	 */
	public function add_custom_image_sizes(){
		// add_image_size( 'full-page-featured', 1200, 800 );
	}

	/**
	 * Build the Slate Theme Settings Page
	 * AKA "Options" page.
	 */
	function build_options_page(){
		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page(array(
				'page_title' 	=> 'Slate Theme Settings',
				'menu_title'	=> 'Slate Settings',
				'menu_slug' 	=> 'theme-general-settings',
				'capability'	=> 'edit_posts',
				'redirect'		=> false,
				'icon_url' 		=> 'dashicons-admin-settings',
			));

			acf_add_options_sub_page(array(
				'page_title' 	=> 'Social Media',
				'menu_title'	=> 'Social Media',
				'parent_slug'	=> 'theme-general-settings',
			));

			acf_add_options_sub_page(array(
				'page_title' 	=> 'Administrator Settings',
				'menu_title'	=> 'Admin',
				'parent_slug'	=> 'theme-general-settings',
			));

		}
	}

	/**
	 * Register our sidebars and widgetized areas.
	 *
	 */
	function register_sidebar() {

		$args = array(
		'name'          => __( 'Sidebar 1', 'theme_text_domain' ),
		'id'            => 'sidebar1',
		'description'   => '',
		'class'         => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>'
		);

		register_sidebar($args);

	}

}
