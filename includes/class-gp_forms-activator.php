<?php

/**
 * Fired during plugin activation
 *
 * @link       http://giantpeach.agency
 * @since      1.0.0
 *
 * @package    Gp_forms
 * @subpackage Gp_forms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Gp_forms
 * @subpackage Gp_forms/includes
 * @author     Giant Peach <support@giantpeach.agency>
 */
class Gp_forms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( !file_exists( get_template_directory() . '/gp_forms' ) ) {
			mkdir( get_template_directory() . '/gp_forms', 0777, true );
			file_put_contents( get_template_directory() . '/gp_forms/forms.php', "" );
		}

		Gp_forms_activator::create_db();
	}

	public static function create_db() {
		global $wpdb;

		$table_name = $wpdb->prefix . "gp_forms_entires";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			form_id varchar(255) NOT NULL,
			entry_id mediumint(9) NOT NULL,
			field varchar(255) NOT NULL,
			value varchar(255) NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
