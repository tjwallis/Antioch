<?php
Namespace Base;
Use Base\Admin;
Use Base\ThemeView;

class Theme {
	public $theme_name = "slate";

	public function __construct() {
		$admin = new Admin;

		// Theme setup calls
		$this->theme_setup();

		// WP Filters
		$this->filters();
		$this->actions();

	}

	private function actions() {
		add_action( 'init', array( $this, 'base_cleanup'), 9999);
		add_action( 'init' , array( $this, 'add_tags_to_attachments') );
		add_action( 'init' , array( $this, 'add_custom_image_sizes') );
		add_action( 'wp_enqueue_scripts',  array( $this, 'base_enqueue_styles'));
		add_action( 'wp_enqueue_scripts',  array( $this, 'base_enqueue_scripts'));
	}

	private function filters() {
		add_filter( 'the_content', array( $this, 'filter_ptags_on_images'));
		add_filter( 'excerpt_more', array( $this, 'excerpt_more'));
		add_filter( 'image_size_names_choose', array( $this, 'add_custom_image_choose'));
		add_filter( 'wp_editor_set_quality', array( $this, 'jpeg_custom_quality') );
		add_filter( 'clean_url', array( $this, 'async_defer_js'), 11, 1 );
		add_filter( 'style_loader_src', array( $this, 'remove_query_strs' ), 10, 2 );
		add_filter( 'script_loader_src', array( $this, 'remove_query_strs' ), 10, 2 );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 * Runs into the after_setup_theme hook, which runs before the init hook.
	 */
	private function theme_setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5' );
		add_theme_support( 'post-thumbnails' );
		add_editor_style( get_template_directory_uri() . '/library/css/editor-style.css' );
	}

	/**
	 * Cleans up unwanted head code.
	 * Is called in dbs_init ("after_setup_theme");
	 */
	public function base_cleanup(){
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}

	public function base_enqueue_styles(){
		wp_register_style( 'main-styles', get_template_directory_uri() . '/library/css/style.css', null, '1.0' );
		wp_enqueue_style( 'main-styles' );
	}

	public function base_enqueue_scripts(){
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js', '2.2.3');
		wp_enqueue_script('jquery');

		// Must not be async and should be included in footer.
		wp_register_script('unveil', get_template_directory_uri() . '/library/js/vendors/unveil2/jquery.unveil.js', '2.0.5', true);
		wp_enqueue_script('unveil');

		wp_register_script('globaljs', get_template_directory_uri() . '/library/js/global.min.js#async', '1.0.0', true);
		wp_enqueue_script('globaljs');
	}

	/**
	 * Set JPG quality.
	 * For WP >= 3.5
	 * @since 1.0
	 * @param  [int] $quality An int representation of the quality.
	 * @return [int]
	 */
	public function jpeg_custom_quality( $quality ) {
		return 70;
	}

	/**
	 * Helper functions for async_defer_js()
	 * @param string $url The URL string.
	 */
	private function set_async($url){ return str_replace('?#async', '', $url)."' async='async"; }
	private function set_defer($url){ return str_replace('?#defer', '', $url)."' defer='defer"; }

	/**
	* Force defer or async of js.
	* Just add a #async or #defer to the script in the enqueue to use.
	* If you are utilizing a plugin and you want to defer or async their script
	* add it as a case in the correct place. ( SEE tribe-events ).
	* @param  string $url The included URL string.
	* @return [string]
	*/
	public function async_defer_js( $url ){
		switch( $url ):
			case ( is_admin() ): // DONT SET ASYNC OR DEFER
			case ( FALSE === strpos( $url, '.js' ) ):
				return $url;
				break;
			case ( FALSE !== strpos( $url, '#async') ): // SET ASYNC
				return $this->set_async($url);
				break;
			case ( FALSE !== strpos( $url, '#defer') ): // SET DEFER
			case ( FALSE !== strpos( $url, 'tribe-events') ):
				return $this->set_defer($url);
				break;
			default:
				return $url;
		endswitch;
	}

	/**
	 * Removes annoying [...] to a Read More Link.
	 * @return [string] Read more.. link
	 */
	public function excerpt_more($more) {
		global $post, $dbs;
		// edit here if you like
		return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read', $dbs->theme_name ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', $dbs->theme_name ) .'</a>';
	}

	/**
	 * Removes query strings from js / css, which aren't cached in some situations.
	 * @return $string
	 */
	public function remove_query_strs( $src ) {
		if ( strpos( $src, '?ver=' ) && !IS_LIVE ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}

	function add_tags_to_attachments() {
        register_taxonomy_for_object_type( 'post_tag', 'attachment' );
    }

	/**
	 * Adds custom image sizes.
	 *
	 * @author Michael Large DBS Interactive
	 * Update here. As of WP 4.4 the default WordPress image sizes are as follows:
	 * https://developer.wordpress.org/reference/functions/the_post_thumbnail/
	 *
	 * the_post_thumbnail( 'thumbnail' ); // Thumbnail (150 x 150 hard cropped)
	 * the_post_thumbnail( 'medium' ); // Medium resolution (300 x 300 max height 300px)
	 * the_post_thumbnail( 'medium_large' ); // Medium Large (added in WP 4.4) resolution (768 x 0 infinite height)
	 * the_post_thumbnail( 'large' ); // Large resolution (1024 x 1024 max height 1024px)
	 * the_post_thumbnail( 'full' ); // Full resolution (original size uploaded)
	 *
	 * There was discussion for an 'extra_large' : 1300px and a 'huge' : 2000px size.
	 * https://github.com/DBSInteractive/dbsinteractive-slate/issues/1
	 * Below are added custom image sizes for DBS>Interactive Dev use:
	 */
	public function add_custom_image_sizes(){
		add_image_size( 'extra_large', 1300, 800 ); // Was 'full-page-featured'
		add_image_size( 'huge', 2000, 1000 ); // was 'huge'
	}

	/**
	 * Adds custom sizes to Media admin area so that you can choose the custom sizes.
	 */
	public function add_custom_image_choose( $sizes ){
		return array_merge( $sizes, array(
			'full-page-featured' => __( 'Full Page Featured Image' )
		));
	}

	/**
	 * View
	 *
	 * An alternative to the native WP function `get_template_part`
	 *
	 * @see PHP class ThemeView
	 * @param string $file The file path, relative to theme root
	 * @param array $args The arguments to pass to this file. Optional.
	 * Default empty array.
	 *
	 * @return string The HTML from $file
	 */
	public static function view($file, $args = array()){
		$template = new ThemeView($file, $args);
		$template->render();
	}

	/**
	 * Remove the <p></p> from around <img> tags.
	 * Runs on the_content filter
	 * @link http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/
	 */
	public function filter_ptags_on_images($content){
	   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
	}

	/**
	 * TODO: Document how this works. What it is.
	 */
	function slides_add_local_field_groups() {
	register_field_group(array (
		'id' => 'acf_image-slider',
		'title' => 'Image Slider',
		'fields' => array (
			array (
				'key' => 'field_55b7db359ed22',
				'label' => 'Images',
				'name' => 'images',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_55b7db509ed23',
						'label' => 'image',
						'name' => 'image',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'object',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_55b7db5d9ed24',
						'label' => 'overlay',
						'name' => 'overlay',
						'type' => 'wysiwyg',
						'column_width' => '',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
				'row_min' => '',
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => 'Add Row',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '1721',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '701',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));

	acf_add_local_field_group(array(
		'key' => 'group_1',
		'title' => 'My Group',
		'fields' => array (
			array (
				'key' => 'field_1',
				'label' => 'Sub Title',
				'name' => 'sub_title',
				'type' => 'text',
			)
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
				),
			),
		),
	));
	}


}
