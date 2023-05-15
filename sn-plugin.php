<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.savage-note.com/
 * @since             2.2.0
 * @package           Sn_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Savage Note
 * Plugin URI:        https://www.savage-note.com
 * Description:       Connectez-vous à notre api et récupérez vos articles.
 * Version:           2.2.0
 * Author:            Savage Note
 * Author URI:        https://www.savage-note.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sn-plugin
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
define( 'SAVAGE_NOTE_VERSION', '2.2.0' );
define(	'SAVAGE_NOTE_PATH', plugin_dir_path( __FILE__ ));
define(	'SAVAGE_NOTE_URL', plugin_dir_url( __FILE__ ));
define('SAVAGE_NOTE_SITE_NAME', get_bloginfo('name'));
define('SAVAGE_NOTE_SITE_URL', get_bloginfo('wpurl'));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sn-plugin-activator.php
 */
function activate_sn_plugin() {
	require_once SAVAGE_NOTE_PATH . 'includes/class-sn-plugin-activator.php';
	Sn_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sn-plugin-deactivator.php
 */
function deactivate_sn_plugin() {
	require_once SAVAGE_NOTE_PATH . 'includes/class-sn-plugin-deactivator.php';
	Sn_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sn_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_sn_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SAVAGE_NOTE_PATH . 'includes/class-sn-plugin.php';
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
require SAVAGE_NOTE_PATH . 'admin/classes/class-sn-plugin-list.php';
require SAVAGE_NOTE_PATH . 'admin/classes/class-sn-plugin-list-articles.php';
require SAVAGE_NOTE_PATH . 'admin/classes/class-sn-plugin-list-purchase.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sn_plugin() {

	$plugin = new Sn_Plugin();
	$plugin->run();

}
run_sn_plugin();
