<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.savage-note.com/
 * @since      1.0.0
 *
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sn_Plugin
 * @subpackage Sn_Plugin/admin
 * @author     Savage Note <quentin@com-maker.fr>
 */
class Sn_Plugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sn_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sn_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sn-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sn_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sn_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sn-plugin-admin.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'easytimer', plugin_dir_url( __FILE__ ) . 'js/easytimer.js', [], $this->version);
        wp_enqueue_script('notiflix', plugin_dir_url( __FILE__ ) . 'js/notiflix.js', array('jquery'), $this->version, false);

		$options = get_option('sn_options');

        $data = array(
            'api_key' => $options['api_key'],
			'site_name' => SAVAGE_NOTE_SITE_NAME,
			'site_url' => SAVAGE_NOTE_SITE_URL
        );
        wp_localize_script($this->plugin_name, 'data', $data);


	}

	public function require()
    {
        require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-plugin-menu.php');
        add_action('plugins_loaded', array('Savage_Note_AdminMenu', 'get_instance'));

		require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-ajax.php');
        add_action('plugins_loaded', array('Savage_Note_Ajax', 'get_instance'));

		require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-settings.php');
        add_action('plugins_loaded', array('Savage_Note_Settings', 'get_instance'));

		require_once(SAVAGE_NOTE_PATH . 'admin/classes/class-sn-helpers.php');
        add_action('plugins_loaded', array('Savage_Note_Helpers', 'get_instance'));
    }

}
