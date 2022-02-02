<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.labubulle.com
 * @since      1.0.0
 *
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Mautic_Paywall
 * @subpackage Wp_Mautic_Paywall/includes
 * @author     Jérémie Gisserot <jeremie@labubulle.com>
 */
class Wp_Mautic_Paywall_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-mautic-paywall',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
