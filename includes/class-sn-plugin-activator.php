<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.savage-note.com/
 * @since      1.0.0
 *
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/includes
 * @author     Savage Note <quentin@com-maker.fr>
 */
class Sn_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// flush_rewrite_rules();

		$default_settings_plugin = get_option('sn_options');

		$args = ['orderby' => 'ID'];


		$categories = get_categories(['hide_empty' => false, 'orderby' => 'term_id']);

		$tags = get_tags(['hide_empty' => false, 'orderby' => 'term_id']);

		if( !empty($tags) ){
			$tag_default = $tags[0]->term_id;
		}else{
			$tag_default = 0;
		}

		if(!$default_settings_plugin || ! isset($default_settings_plugin['author'] ) ){
			$default_settings_plugin = [
				'api_key' => '',
				'status' => 'draft',
				'category' => $categories[0]->term_id,
				'post_type' => 'post',
				'author' => $users[0]->ID,
				'tag' => $tag_default
			];
		}

		update_option( 'sn_options', $default_settings_plugin);
	}

}
