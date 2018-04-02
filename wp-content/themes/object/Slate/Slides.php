<?php
Namespace Slate;
Use \WP_Query;

class Slides extends WP_Query {
	function __construct( $args = array() ) {

		// Force these args
		$args = array_merge( $args, array(
			'post_type' => 'slides',
			'no_found_rows' => true, // Optimize query for no paging
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false
		) );

		parent::__construct( $args );

	}
}
