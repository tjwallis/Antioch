<?php
/**
 * Repeater Field Template: Default
 * This template is used to imitate the default page behavior.
 *
 * @author Michael Large 2016 - DBS>Interactive
 */
global $utils;
?>

<div class="container layout-default">
    <div class="container__inner">
        <?php $utils->slate_esc_content( the_sub_field('content') ); ?>
    </div>
</div>
