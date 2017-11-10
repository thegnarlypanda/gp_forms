<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://giantpeach.agency
 * @since      1.0.0
 *
 * @package    Gp_forms
 * @subpackage Gp_forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gp_forms
 * @subpackage Gp_forms/public
 * @author     Giant Peach <support@giantpeach.agency>
 */
class Gp_forms_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gp_forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gp_forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gp_forms-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gp_forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gp_forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gp_forms-public.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'gp_forms' );

	}

	public function add_to_form_array() {
		echo "test";
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

		wp_register_script( 'gp_forms', plugin_dir_url( __FILE__ ) . 'js/gp_forms.js' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function localize_scripts() {

		wp_localize_script( 'gp_forms', 'nonce', [ 'gp_forms/v1', wp_create_nonce( 'wp_rest' ) ] );

	}

	public function gp_form_submit() {
		if ( !check_ajax_referer( 'wp_rest', '_wpnonce', false ) ) {
			echo 'kys';
		} else {
			echo "something";
		}
	}

}
