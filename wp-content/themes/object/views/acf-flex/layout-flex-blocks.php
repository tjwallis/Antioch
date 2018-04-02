<?php
/**
 * Repeater Field Template: Tri Block
 * This template is used to create a three-block CTA.
 *
 * @author Michael Large 2016 - DBS>Interactive
 */
global $utils;

$bgImageObject =  get_sub_field('background_image');
if($bgImageObject){
	$flexClass = 'full-bleed';
}else{
	$flexClass = 'spaced';
}
?>

<div class="container bgimg layout layout-flex-block layout--<?php echo $flexClass; ?>"
    data-bg-srcset="<?php echo wp_get_attachment_image_srcset($bgImageObject['ID']) ?>">
    <div class="container__inner">
        <div class="flex-block__heading">
            <?php $utils->slate_esc_content( the_sub_field('section_title') ); ?>
        </div>
        <?php if( have_rows('flex_block_repeater') ): ?>
            <div class="flex-block__repeater">
                <?php while( have_rows('flex_block_repeater')): the_row(); ?>
                    <div class="flex-block__cell">
                        <?php $utils->slate_esc_content( the_sub_field('flex_block_content') );?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
