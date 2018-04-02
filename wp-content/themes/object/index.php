<?php
Namespace Slate;

get_header();

/*
 * Include the Post-Loop view for the content.
 * If you want to override the default posts pass it WP_query array.
 */
$theme->view('views/posts/loop.php', array('query' => $wp_query));

the_posts_pagination( array(
	'prev_text'          => __( 'Previous page', $theme->slug ),
	'next_text'          => __( 'Next page', $theme->slug ),
	'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', $theme->slug ) . ' </span>',
) );

get_footer();
