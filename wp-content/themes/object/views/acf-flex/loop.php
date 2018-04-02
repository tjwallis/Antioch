<?php
/**
 * Parent template to be included in other page templates.
 * This handles the logic to select the correct flex field template.
 * TODO: Create error handling with "no layout file"
 * @author Michael Large 2016 - DBS>Interactive
 */

if( have_rows('flex_content') ): while( have_rows('flex_content') ): the_row();

    // Look in views/acf-flex for layouts and get those parts.
    $layout = str_replace('_', '-', get_row_layout() );
    get_template_part('views/acf-flex/layout', $layout );

endwhile; endif;
