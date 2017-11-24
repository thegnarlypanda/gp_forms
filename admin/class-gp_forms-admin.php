<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://giantpeach.agency
 * @since      1.0.0
 *
 * @package    Gp_forms
 * @subpackage Gp_forms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gp_forms
 * @subpackage Gp_forms/admin
 * @author     Giant Peach <support@giantpeach.agency>
 */
class Gp_forms_Admin {

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
	 * Register custom post type.
	 *
	 * @since    1.0.0
	 */
	public static function gp_register_post_type() {

		register_post_type('gp_forms',
			array(
				'labels' => array(
				'name' => __( 'Form Entries' ),
				'singular_name' => __( 'Form Entry' ),
				'add_new_item' => "Add New Form Entry",
				'edit_item' => "Edit Form Entry",
			),
			'supports' => array(
				'title'
			),
			'menu_icon' => 'dashicons-admin-users',
			'show_in_rest' => false,
			'public' => true,
			'has_archive' => false,
			'publicly_queryable'  => false,
			)
		);

		register_taxonomy( "form", "gp_forms", array(
			'labels' => array(
				'name' => 'Forms',
				'singular_name' => 'Form',
				'new_item_name' => 'Form',
				'add_new_item' => 'Add New Form',
				'edit_item' => 'Edit Form'
			),
			'show_admin_column' => true,
			'hierarchical' => true
		) );
	}

	public static function gp_add_metabox() {
		add_meta_box( 'gp_forms_entry', 'Entry', array(get_called_class(), 'gp_populate_metabox'), 'gp_forms', 'normal', 'high' );		
	}

	public static function gp_populate_metabox() {
		global $post;
		global $wpdb;
		$table_name = $wpdb->prefix . "gp_forms_entires";
		$results = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $table_name WHERE entry_id=%d", $post->ID)
		);
		
		foreach ($results as $result) {
			echo $result->field . ": " . $result->value . "<br>";
		}
	}

	/**
	 * Register rest api endpoint.
	 *
	 * @since    1.0.0
	 */
	public static function gp_register_endpoint() {

		register_rest_route( 'gp_forms/v1', '/submit', array(
			'methods' => 'GET',
			'callback' => array( $plugin_public, 'gp_form_submit' )
		) );

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
		 * defined in Gp_forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gp_forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gp_forms-admin.css', array(), $this->version, 'all' );

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
		 * defined in Gp_forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gp_forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gp_forms-admin.js', array( 'jquery' ), $this->version, false );

	}

}
