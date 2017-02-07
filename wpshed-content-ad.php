<?php
/**
 * Plugin Name: WPshed Conted Ad
 * Plugin URI: http://wpshed.com/
 * Description: This plugin will place an Ad (script) in your post content after a predefined number of paragraphs. Plugin by <a href="http://wpshed.com/">WPshed.com</a>.
 * Version: 0.1
 * Author: Stefan I.
 * Author URI: http://wpshed.com/
 */


// Set default values
$wpshed_ca_defaults = array(
    'p_nr'      => 1,
    'align'     => 'center',
    'ad'        => '',
);
add_option( 'wpshed_ca', $wpshed_ca_defaults, '', 'yes' );


// Init plugin options to white list our options
add_action( 'admin_init', 'wpshed_ca_options_init' );

function wpshed_ca_options_init() {
    register_setting( 'wps_options', 'wpshed_ca' );
}


// Hook for adding admin menus
add_action('admin_menu', 'wpshed_ca_add_options_page');


// Action function for above hook
function wpshed_ca_add_options_page() {

    // Add a new options menu:
    add_options_page(
        __( 'Content Ad', 'wpshed' ),
        __( 'Content Ad', 'wpshed' ),
        'manage_options',
        'content-ad',
        'wpshed_ca_options_page'
    );

}


// Displays the page content for the custom options menu
function wpshed_ca_options_page() { 

    $opt = get_option('wpshed_ca');
    ?>

	<div class="wrap">

		<h1><?php _e( 'WPshed Content Ad', 'wpshed' ); ?></h1>

        <p><?php _e( 'This plugin will place an Ad (script) in your post content after a predefined number of paragraphs.', 'wpshed' ); ?></p>
        <p class="description"><?php _e( 'Please note that images are usually counted as paragraphs as well in the way WordPress outputs the content.', 'wpshed' ); ?></p>

        <form method="post" action="options.php">
        <?php settings_fields( 'wps_options' ); ?>
        
        <table class="form-table">

        <tr valign="top"><th scope="row"><?php _e( 'Ad position', 'wpshed' ); ?></th>
            <td>
                <select name="wpshed_ca[p_nr]">
                    <option value="1" <?php selected( $opt['p_nr'], 1 ); ?>>1</option>
                    <option value="2" <?php selected( $opt['p_nr'], 2 ); ?>>2</option>
                    <option value="3" <?php selected( $opt['p_nr'], 3 ); ?>>3</option>
                    <option value="4" <?php selected( $opt['p_nr'], 4 ); ?>>4</option>
                    <option value="5" <?php selected( $opt['p_nr'], 5 ); ?>>5</option>
                </select>
                <label class="description"><?php _e( 'number of paragraphs after which your Ad should be displayed.', 'wpshed' ); ?></label>
            </td>
        </tr>

        <tr valign="top"><th scope="row"><?php _e( 'Ad Alignment', 'wpshed' ); ?></th>
            <td>
                <select name="wpshed_ca[align]">
                    <option value="center" <?php selected( $opt['align'], 'center' ); ?>><?php _e( 'center', 'wpshed' ); ?></option>
                    <option value="none" <?php selected( $opt['align'], 'none' ); ?>><?php _e( 'none', 'wpshed' ); ?></option>
                    <option value="left" <?php selected( $opt['align'], 'left' ); ?>><?php _e( 'left', 'wpshed' ); ?></option>
                    <option value="right" <?php selected( $opt['right'], 'none' ); ?>><?php _e( 'right', 'wpshed' ); ?></option>
                </select>
                <label class="description"><?php _e( 'Ad alignment.', 'wpshed' ); ?></label>
            </td>
        </tr>

        <tr valign="top"><th scope="row"><?php _e( 'Ad Code', 'wpshed' ); ?></th>
            <td>
                <textarea name="wpshed_ca[ad]" rows="8" cols="60"><?php echo $opt['ad']; ?></textarea>
                <p class="description"><?php _e( 'Google Adsense or any type of Banner Ad code.', 'wpshed' ); ?></p>
            </td>
        </tr>

        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'wpshed' ); ?>" />
        </p>
        </form>

	</div><!-- .wrap -->

<?php

}


// Insert ads after second paragraph of single post content.
add_filter( 'the_content', 'wpshed_ca_insert_post_ads' );

function wpshed_ca_insert_post_ads( $content ) {
    
    $opt        = get_option('wpshed_ca');

    $align      = ( $opt['align'] != '' ) ? $opt['align'] : 'center';
    $paragraph  = ( $opt['p_nr'] != '' ) ? (int)$opt['p_nr'] : 1;
    $ad_code    = '';

    if ( $opt['ad'] != '' ) {
        $ad_code .= "<p class='wpshed-ca align{$align}'>";
        $ad_code .= $opt['ad'];
        $ad_code .= '</p>';
    }

    if ( is_single() && ! is_admin() ) {
        return wpshed_ca_insert_after_paragraph( $ad_code, $paragraph, $content );
    }
    
    return $content;
}
 

// Parent Function that makes the magic happen
function wpshed_ca_insert_after_paragraph( $insertion, $paragraph_id, $content ) {

    $closing_p  = '</p>';
    $paragraphs = explode( $closing_p, $content );
    
    foreach ( $paragraphs as $index => $paragraph ) {

        if ( trim( $paragraph ) ) {
            $paragraphs[$index] .= $closing_p;
        }

        if ( $paragraph_id == $index + 1 ) {
            $paragraphs[$index] .= $insertion;
        }
    }
    
    return implode( '', $paragraphs );
}


function wpshed_ca_styles() {

        $custom_css = "
            <style type='text/css'>
            .wpshed-ca { display: inline-block; }
            .wpshed-ca.alignleft { float: left; margin-right: 20px; }
            .wpshed-ca.alignright { float: right; margin-left: 20px; }
            .wpshed-ca.aligncenter { text-align: center; display: block; }
            </style>";

        echo $custom_css;
}
add_action( 'wp_head', 'wpshed_ca_styles' );
