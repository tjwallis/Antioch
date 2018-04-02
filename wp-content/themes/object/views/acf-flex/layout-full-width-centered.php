<?php
/**
 * Repeater Field Template: Full Width Centered
 * This template is used to make a full width centered section.
 * This also includes the option for a colored image background.
 *
 * @author Michael Large 2016 - DBS>Interactive
 */
global $utils;

$bg = get_sub_field('background');
$bgImageObject = get_sub_field('background_image');
if($bgImageObject){
	$flexClass = 'full-bleed';
}else{
	$flexClass = 'spaced';
}
$content = $utils->slate_esc_content( get_sub_field('content') );
?>

<div
    class="container bgimg layout-full-width
	layout-full-width--<?php echo $utils->slate_esc_content( the_sub_field('container_class') ); ?>
	layout layout--<?php echo $flexClass; ?>
	layout--bg-<?php echo $bg; ?> layout--parallax-image"
    data-bg-srcset="<?php echo wp_get_attachment_image_srcset( $bgImageObject['ID'] ) ?>">
    <div class="contain container__inner">
        <div class="full-width__heading"><?php echo $title; ?></div>
        <div class="full-width__content">
            <?php echo $content; ?>
        </div>
    </div>
</div>
