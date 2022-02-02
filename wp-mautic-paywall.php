<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.labubulle.com
 * @since             1.0.0
 * @package           Wp_Mautic_Paywall
 *
 * @wordpress-plugin
 * Plugin Name:       WP Mautic
 * Plugin URI:        http://www.labubulle.com
 * Description:       Deals with the mautic paywall functions
 * Version:           1.0.0
 * Author:            Jérémie Gisserot
 * Author URI:        http://www.labubulle.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-mautic-paywall
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Wp_Mautic_Paywall_PAYWALL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-mautic-paywall-activator.php
 */
function activate_Wp_Mautic_Paywall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-mautic-paywall-activator.php';
	Wp_Mautic_Paywall_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-mautic-paywall-deactivator.php
 */
function deactivate_Wp_Mautic_Paywall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-mautic-paywall-deactivator.php';
	Wp_Mautic_Paywall_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Wp_Mautic_Paywall' );
register_deactivation_hook( __FILE__, 'deactivate_Wp_Mautic_Paywall' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-mautic-paywall.php';

/**
 * The helpers functions for theme use
 */
require plugin_dir_path( __FILE__ ) . 'helpers/helpers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Wp_Mautic_Paywall() {

	$plugin = new Wp_Mautic_Paywall();
	$plugin->run();

}
run_Wp_Mautic_Paywall();
