<?php 
/**
 * The helpers functions for theme use
 *
 * This file contains a toolbox to use in theme and plugins
 *
 * @link              Jérémie Gisserot
 * @since             1.0.0
 * @package           Wp_Mautic_Paywall
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

 /**
 * Returns the full like form
 */
function mautic_paywall_get_comment_form() {
    Wp_Mautic_Paywall_Public::maybe_output_comments_form();
}