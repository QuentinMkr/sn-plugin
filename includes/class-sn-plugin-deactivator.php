<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://com-maker.fr
 * @since      1.0.0
 *
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/includes
 * @author     Savage Note <quentin@com-maker.fr>
 */
class Sn_Plugin_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// flush_rewrite_rules();
	}

}
