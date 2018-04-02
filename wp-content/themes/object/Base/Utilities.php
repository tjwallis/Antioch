<?php
/**
 * General Utilities.
 */
Namespace Base;

class Utilities {

	public function __construct() {
		$this->actions();
		$this->filters();
	}

	private function actions(){
		add_action('wp_head', array( $this, 'slate_head'));
		add_action('wp_footer', array( $this, 'slate_template_debug'));
	}

	private function filters(){
		add_filter( 'posts_join', array( $this, 'custom_attachments_join'), 10, 2 );
		add_filter( 'posts_where', array( $this, 'custom_attachments_where'), 10, 2 );
		add_filter( 'posts_groupby', array( $this, 'custom_attachments_groupby'), 10, 2 );
	}

	/**
	 * Displays dbs link-back chevron
	 */
	public static function dbs_chevron(){
		include_once( TEMPLATEPATH . '/library/dbs-chevron.php');
	}

	public static function site_logo(){
		include_once( TEMPLATEPATH . '/library/images/site-logo.svg');
	}

	/**
	 * Handles terms to include tags as a part of the search query in the
	 * media library.
	 */
	public function custom_attachments_join( $join, $query ){
		global $wpdb;

		//if we are not on admin or the current search is not on attachment return
		if(!is_admin() || (!isset($query->query['post_type']) || $query->query['post_type'] != 'attachment')){
			return $join;
		}

		//  if current query is the main query and a search...
		if( is_main_query() && is_search() ){
			$join .= "
			LEFT JOIN
			{$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
			LEFT JOIN
			{$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
			LEFT JOIN
			{$wpdb->terms} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id ";
		}

		return $join;
	}

	/**
	 * Handles terms to include tags as a part of the search query in the
	 * media library.
	 */
	public function custom_attachments_where( $where, $query ){
		global $wpdb;

		//if we are not on admin or the current search is not on attachment return
		if(!is_admin() || (!isset($query->query['post_type']) || $query->query['post_type'] != 'attachment')){
			return $where;
		}

		//  if current query is the main query and a search...
		if( is_main_query() && is_search() ){
			//  explictly search post_tag taxonomies
			$where .= " OR (
			( {$wpdb->term_taxonomy}.taxonomy IN('post_tag') AND {$wpdb->terms}.name LIKE '%" . esc_sql( get_query_var('s') ) . "%' )
			)";
		}

		return $where;
	}

	/**
	 * Handles terms to include tags as a part of the search query in the
	 * media library.
	 */
	public function custom_attachments_groupby( $groupby, $query ){
		global $wpdb;

		if(!is_admin() || (!isset($query->query['post_type']) || $query->query['post_type'] != 'attachment')){
			return $groupby;
		}

		if( is_main_query() && is_search() ){
			$groupby = "{$wpdb->posts}.ID";
		}

		return $groupby;
	}


	/**
	 * Defines the custom "Head" markup.
	 * This is included in header.php
	 */
	public function slate_head(){
		$output = '';
		$output .= '<meta name="HandheldFriendly" content="True">';
		$output .= '<meta name="MobileOptimized" content="320">';
		$output .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>';

		$output .= '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" >';
		$output .= '<meta property="og:title" content="' . get_the_title() . '"/>';
		$output .= '<meta property="og:image" content="change this path"/>';
		$output .= '<meta property="og:site_name" content="' . get_bloginfo('name') . '"/>';
		$output .= '<meta property="og:description" content="' . get_bloginfo('description') . '"/>';

		$output .= '<link rel="apple-touch-icon" sizes="57x57" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-57x57.png">';
		$output .= '<link rel="apple-touch-icon" sizes="60x60" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-60x60.png">';
		$output .= '<link rel="apple-touch-icon" sizes="72x72" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-72x72.png">';
		$output .= '<link rel="apple-touch-icon" sizes="76x76" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-76x76.png">';
		$output .= '<link rel="apple-touch-icon" sizes="114x114" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-114x114.png">';
		$output .= '<link rel="apple-touch-icon" sizes="120x120" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-120x120.png">';
		$output .= '<link rel="apple-touch-icon" sizes="144x144" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-144x144.png">';
		$output .= '<link rel="apple-touch-icon" sizes="152x152" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-152x152.png">';
		$output .= '<link rel="apple-touch-icon" sizes="180x180" href="' . get_template_directory_uri() . '/library/favicons/apple-touch-icon-180x180.png">';
		$output .= '<link rel="icon" type="image/png" href="' . get_template_directory_uri() . '/library/favicons/favicon-32x32.png" sizes="32x32">';
		$output .= '<link rel="icon" type="image/png" href="' . get_template_directory_uri() . '/library/favicons/favicon-194x194.png" sizes="194x194">';
		$output .= '<link rel="icon" type="image/png" href="' . get_template_directory_uri() . '/library/favicons/favicon-96x96.png" sizes="96x96">';
		$output .= '<link rel="icon" type="image/png" href="' . get_template_directory_uri() . '/library/favicons/android-chrome-192x192.png" sizes="192x192">';
		$output .= '<link rel="icon" type="image/png" href="' . get_template_directory_uri() . '/library/favicons/favicon-16x16.png" sizes="16x16">';
		$output .= '<link rel="manifest" href="' . get_template_directory_uri() . '/library/favicons/manifest.json">';
		$output .= '<link rel="mask-icon" href="' . get_template_directory_uri() . '/library/favicons/safari-pinned-tab.svg" color="#000000">';
		$output .= '<link rel="shortcut icon" href="' . get_template_directory_uri() . '/library/favicons/favicon.ico">';
		$output .= '<meta name="apple-mobile-web-app-title" content="Big Ass Solutions">';
		$output .= '<meta name="application-name" content="Big Ass Solutions">';
		$output .= '<meta name="msapplication-TileColor" content="#da532c">';
		$output .= '<meta name="msapplication-TileImage" content="' . get_template_directory_uri() . '/library/favicons/mstile-144x144.png">';
		$output .= '<meta name="msapplication-config" content="' . get_template_directory_uri() . '/library/favicons/browserconfig.xml">';
		$output .= '<meta name="theme-color" content="#ffffff">';

		echo $output;
	}


	/**
	 * Helper Utility to show the current page template.
	 */
	public function get_current_template( $echo = false ) {
		if( !isset( $GLOBALS['template'] ) ){
			return false;
		}

		if( $echo ){
			echo basename( $GLOBALS['template'] );
		} else {
			return basename( $GLOBALS['template'] );
		}
	}

	/**
	 * Helper Utility to show the current page debug code.
	 * Display template file used to render page, and some stats if 'is_template_debug' is true and its one of us.
	 */
	public function slate_template_debug() {
		global $dbs, $post;
		wp_reset_query();
		if( $dbs->is_template_debug && is_me() ): ?>
			<div class="debug-current-template">
				<strong>Current template:</strong> <?php $this->get_current_template( true ); ?>
				<span>Ran <?php echo get_num_queries(); ?> queries in <?php timer_stop(1); ?>s. Ready in <span id="dbsready"></span>s. Post ID: <?php echo $post->ID?></span>
			</div>
			<script>jQuery( window ).load( function() { jQuery( "#dbsready").html(  (window.performance.timing.domContentLoadedEventStart - window.performance.timing.connectEnd) / 1000 ); }); </script>
			<script>jQuery( window ).load( function() { jQuery("img[srcset]:not('.avatar')").each(function(){ var currentSrc = this.currentSrc.substr(this.currentSrc.lastIndexOf('/') + 1); jQuery(this).wrap('<span class="srcset-debug"></span>'); jQuery(this).parent().append('<span class="srcset-debug__currentsrc">Current Src: ' + currentSrc + '</span>'); }); });</script>
			 <?php if ( defined( 'DEBUG' ) && defined( 'SAVEQUERIES' ) ) dump( $wpdb->queries ); ?>
		 <?php endif;
	}

	/**
	* @params unfiltered HTML string
	*
	* @return filtered HTML string using WP wp_kses().
	*
	* This function escapes content that might contain HTML (potentially malicious),
	* to prevent XSS type vulnerabilities! This should be used in situations like WP
	* comments where users might be able to inject HTML.
	*/
	public static function slate_esc_content( $unfiltered ) {
		// this is what we allow
		$allowed_html = array(
			'a' => array(
				'style' => array (),
				'href' => array (),
				'class' => array (),
				'target' => array (),
				'title' => array ()
			),
			'p' => array(
				'style' => array (),
				'align' => array (),
				'id' => array (),
				'style' => array (),
				'class' => array ()
			),
			'div' => array(
				'style' => array (),
				'class' => array (),
				'id' => array ()
			),
			'img' => array(
				'src' => array (),
				'alt' => array (),
				'style' => array (),
				'class' => array (),
				'id' => array ()
			),
			'h1' => array(
				'style' => array (),
				'class' => array (),
				'id' => array ()
			),
			'h2' => array(
				'style' => array (),
				'class' => array (),
				'id' => array ()
			),
			'h3' => array(
				'class' => array (),
				'id' => array ()
			),
			'h4' => array(
				'class' => array (),
				'id' => array ()
			),
			'svg' => array(
				'class' => array (),
				'id' => array ()
			),
			'ul' => array(
				'style' => array (),
				'id' => array (),
				'class' => array ()
			),
			'li' => array(
				'class' => array ()
			),
			'span' => array(
				'style' => array (),
				'id' => array (),
				'class' => array (),
				'data-grunticon-embed' => array ()
			),
			'br' => array(
				'style' => array (),
				'class' => array ()
			),
			'em' => array(),
			'strong' => array(),
			'blockquote' => array(),
			'cite' => array()
		);
		return wp_kses( $unfiltered, $allowed_html );
	}

	/**
	 * Check to see if page is an anscestor.
	 * @param  [type]  $post_id [description]
	 * @return boolean          [description]
	 */
	public function is_ancestor( $post_id ){
		global $wp_query;
		$ancestors = $wp_query->post->ancestors;
		if ( in_array( $post_id, $ancestors ) ){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check to see if page is direct child.
	 * @param  int  $page_id
	 * @return boolean
	 */
	public function is_child( $page_id ){
		global $post;
		if( is_page() && ( $post->post_parent == $page_id ) ){
			return true;
		} else {
			return false;
		}
	}

	/**
	* Get a page's ID from it's slug
	*
	* @param string $slug Page slug to search on
	* @return null Empty on error
	* @return int Page ID
	*/
	public function get_page_ID_by_slug( $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return (int) $page->ID;
		}
		else {
			return null;
		}
	}

	/**
	 * Creates an img tag with appropriate srcset and sizes attributes.
	 *
	 * @author Michael Large DBS Interactive
	 * @param int $image_id The id of the image being requested.
	 * @param string $default_size The min size of the image.
	 * @param string $attributes  classes as a string.
	 * @example
	 * 		<?php echo $utils->get_image_with_srcset( $id ); ?>
	 *
	 * TODO: Cleanup this script. I wrote it with little coffee and I'm sorry. -ML
	 */
	public function get_image_with_srcset( $image_id, $lazyload = false, $attributes = array() ){
		$attachment_array = wp_get_attachment_image_src( $image_id, $default_size );
		$image_sizes = '100vw';
		$image_attributes = "";

		if( empty( $attributes ) && $lazyload === true ){
			$attributes["class"] = "lazy-load";
		}
		// Loop through the attributes and apply the correct class if it needs it.
		foreach ( $attributes as $key => $value ) {
			if( $lazyload === true && $key == "class" ){
				$value .= " lazy-load";
			}
			$image_attributes .= $key . '="'. $value . '" ';
		}

		// If Lazyload is true, rewrite the tag.
		if( $lazyload === true ){
			$html  = '<img src="' . $attachment_array[0] . '" ';
			$html .= 'sizes="' . $image_sizes . '" ';
			$html .= $image_attributes;
			$html .= '>';
		} else {
			$html  = '<img src="' . $attachment_array[0] . '" ';
			$html .= 'srcset="' . wp_get_attachment_image_srcset( $image_id ) . '" ';
			$html .= 'sizes="' . $image_sizes . '" ';
			$html .= $image_attributes;
			$html .= '>';
		}
		return $html;
	}

	/**
	 * Creates a custom background sourceset attribute that is picked up by javascript.
	 *
	 * The scripts for this is in js/global/vendors/bg-sourceset
	 * @param int $image_id	Image Id
	 * @param string $default_size The min size of the image.
	 * @example
	 * 		<div <?php echo $utils->get_background_image_srcset( $img['ID'] ); ?>></div>
	 */
	public function get_background_image_srcset( $image_id, $default_size = "medium" ){
		$html  = 'data-bg-srcset="' . wp_get_attachment_image_srcset( $image_id, $size = $default_size ) . '"';
		return $html;
	}

}
