<?php
/**
 * Repeater Field Template: Half and Half
 * This template is used to create a 50% 50% split template
 * with the option of having a background image and call to action button.
 *
 * @author Michael Large 2016 - DBS>Interactive
 */
global $utils;

$leftContainerClass = get_sub_field('left_container_class');
$leftContent = $utils->slate_esc_content( get_sub_field('left_side_content') );
$leftBgImage = get_sub_field('left_side_background_image');

$rightContainerClass = get_sub_field('right_container_class');
$rightContent = $utils->slate_esc_content( get_sub_field('right_side_content') );
$rightBgImage =  get_sub_field('right_side_background_image');
?>

<div class="layout layout--full-bleed container layout-half-and-half">

    <div class="container__inner">
        <div
            class="half-and-half__left half-and-half--<?php echo $leftContainerClass; ?> bgimg"
            data-bg-srcset="<?php echo wp_get_attachment_image_srcset($leftBgImage['ID']) ?>">
            <div class="half-and-half-content__wrap">
                <?php echo $leftContent; ?>
            </div>
        </div>

        <div
            class="half-and-half__right half-and-half--<?php echo $rightContainerClass; ?> bgimg"
            data-bg-srcset="<?php echo wp_get_attachment_image_srcset($rightBgImage['ID']) ?>">
            <div class="half-and-half-content__wrap">
                <?php echo $rightContent; ?>
            </div>
        </div>
    </div>
</div>
