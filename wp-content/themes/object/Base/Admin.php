<?php
/**
 * This file handles the Base admin functionality and UX/UI.
 * Limit changes to Base Approvied changes to the admin.
 *
 * @file Admin.php
 * @author Timothy Wallis @ DBS>Interactive
 */

Namespace Base;

class Admin {

	public $text_domain;

	public function __construct() {
		$this->actions();
		$this->filters();
	}

	public function actions() {
		add_action( 'admin_notices', array( $this,'synced_data_notice' ) );
		add_action( 'admin_menu', array( $this, 'disable_dashboard_widgets') );
		add_action( 'login_enqueue_scripts', array( $this, 'login_css'), 10 );
	}

	public function filters() {
		add_filter( 'login_headerurl', array( $this, 'login_url' ) );
		add_filter( 'login_headertitle', array( $this, 'login_title') );
		add_filter( 'admin_footer_text', array( $this, 'custom_dashboard_footer') );
	}


	/**
	 * Disable the default dashboard widgets.
	 * The less that the user can change,
	 * the less that they can break.
	 */
	public function disable_dashboard_widgets(){
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );

		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );

		// removing plugin dashboard boxes
		remove_meta_box( 'yoast_db_widget', 'dashboard', 'normal' );
	}

	/**
	 * Displays Alert on admin dashboard when using live database on staging.
	 */
	public function synced_data_notice(){
		if( !IS_LIVE && !strstr( DB_NAME, 'staging' ) ){
			echo "<div class='error'><p><strong>" . __('STAGING AND LIVE DATABASES ARE LINKED! ANY CHANGES WILL GO LIVE!', $this->text_domain ) . "</strong></p></div>";
		} else {
			return false;
		}
	}

	/**
	 * Sets up login page styles and title.
	 */
	public function login_css(){
		wp_enqueue_style( 'dbs_login_css', get_template_directory_uri() . '/library/css/login.css', false);
	}

	public function login_url(){
		return home_url();
	}

	public function login_title(){
		return get_option('blogname');
	}

	/**
	 * Adds nice contribution to dashboard.
	 */
	public function custom_dashboard_footer() {
		_e( '<span id="footer-thankyou">Developed by <a href="https://www.dbswebsite.com/" target="_blank">DBS&gt;Interactive</a></span>.', $this->text_domain );
	}

}
