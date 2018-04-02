<?php
/**
* Theme files loaded inside of a ThemeView Object.
*/

// Checks to see if the required arguments were passed.
$this->expected_args( array(
	'wrap_link' => true,
	'query' => '',
	'wrapper' => 'div',
	)
);
$query = $this->query;
$wrap_link = $this->wrap_link;


if( $query->have_posts() ) : ?>
<?php while( $query->have_posts() ) : $query->the_post(); ?>
<<?php echo $this->wrapper; ?> class="contain">
		<?php if( $wrap_link ) : ?>
			<a href="<?php the_permalink(); ?>">
		<?php endif; ?>

		<?php if ( has_post_format( 'image' )) : ?>
			<img src="http://lorempixel.com/400/300/cats">
			<h3><?php the_title(); ?></h3>
		<?php else : ?>
			<h2><?php the_title(); ?></h2>
		<?php endif; ?>

			<div><?php the_content(); ?></div>

		<?php if( $wrap_link ) : ?>
			<span class="button button--magenta">Read More</span>
		<?php elseif( !is_single() && !is_page() ) : ?>
			<a href="<?php the_permalink(); ?>">Learn More
		<?php endif; ?>

			</a>
	</<?php echo $this->wrapper; ?> >
<?php endwhile;
wp_reset_postdata();
endif;
