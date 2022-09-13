<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://digitalerdurchbruch.de
 * @since             1.0.0
 * @package           Eur2sek
 *
 * @wordpress-plugin
 * Plugin Name:       EUR2SEK
 * Plugin URI:        https://digitalerdurchbruch.de
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Karl Friedrich
 * Author URI:        https://digitalerdurchbruch.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eur2sek
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
define( 'EUR2SEK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eur2sek-activator.php
 */
function activate_eur2sek() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eur2sek-activator.php';
	Eur2sek_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eur2sek-deactivator.php
 */
function deactivate_eur2sek() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eur2sek-deactivator.php';
	Eur2sek_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_eur2sek' );
register_deactivation_hook( __FILE__, 'deactivate_eur2sek' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-eur2sek.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_eur2sek() {

	$plugin = new Eur2sek();
	$plugin->run();

}
run_eur2sek();
