<?php
/**
 * Theme File for ACF Image slides.
 */

/*
 * @param slides - The name of the AFC repeter field.
 * @param id - override the post ID.
 * @param class - The css to wrap the slider in, also used by the JS.
 */
$this->expected_args( array(
	'slides' => 'slides',
	'id' => get_the_ID(),
	'class' => 'slider',
	)
);

$slides = $this->slides;
if( have_rows($slides, $this->id) ) : ?>
	<section class="block <?php echo $this->class; ?>">
	<?php while( have_rows($slides, $this->id) ) : the_row(); ?>
		<div class="blocks__full">
			<?php $image = get_sub_field('image'); ?>
<?php if( !empty($image) ): ?>

	<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

<?php endif; ?>
			<h2 class="title"><?php the_sub_field('overlay'); ?></h2>
		</div>
	<?php endwhile; ?>
	</section>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.css"/>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick-theme.css"/>
	<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.min.js"></script>
	<script type="text/javascript">
		// https://github.com/kenwheeler/slick/
		jQuery(document).on("ready",function(){
			var options = {
				accessibility: true,
				adaptiveheight: true,
				autoplay: true,
				autoplaySpeed: 3000,
				centerMode: false,
				centerPadding: '50px',
				cssEase: 'ease',
				dots: false,
				dotsClass: 'slick-dots',
				draggable: true,
				easing: 'linear',
				edgeFriction: 0.15,
				fade: false,
				arrows: true,
				mobileFirst: true,
				infinite: true,
				lazyLoad: 'progressive',
				pauseOnFocus: true,
				pauseOnHover: true,
				respondTo: 'window',
				rows: 1,
				speed: 300,
				swipe: true,
				swipeToSlide: true,
				touchMove: true,
				useCSS: true,
				useTransform: true
			};

			var slider = jQuery(".<?php echo $this->class; ?>").slick(options);

			// Available Events
			// afterChange
			// beforeChange
			// breakpoint
			// destroy
			// edgeFrictioninit
			// reInit
			// setPostiion
			// swipe
			// lazyLoaded
			// lazyLoadError
		});
	</script>
<?php
endif;
