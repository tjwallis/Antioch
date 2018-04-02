<?php
/**
 * Displays Social Media link and icon if it is set in the options page.
 * @author Michael Large - DBS Interactive
 */
?>
<ul class="social-media__wrap">

<?php
    /**
     * Supported Social Media Platforms
     * @var array
     */
    $platforms = array(
        "facebook",
        "instagram",
        "twitter",
        "youtube",
        "pinterest",
        "linkedin",
        "google"
    );

    /**
     * Loop through the platforms.
     */
    foreach ( $platforms as $platform ):
        if( get_field( $platform . '_url' , 'option' ) ):
            $url = sanitize_text_field( get_field( $platform . '_url' , 'option') ); ?>
            <li class="social-media__item"><a href="<?php echo $url; ?>" target="_BLANK"><span class="icon-<?php echo $platform; ?>-white" data-grunticon-embed></span></a></li>
        <?php endif; ?>
    <?php endforeach;
?>
</ul>
