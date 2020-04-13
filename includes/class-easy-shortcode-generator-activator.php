<?php

/**
 * Fired during plugin activation
 *
 * @link       sherinshelvan.com
 * @since      1.0.0
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/includes
 * @author     Sherin Shelvan <sherinshelvan@gmail.coom>
 */
class Easy_Shortcode_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $table_prefix, $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$tbl_name        = $table_prefix . "easy_shortcode";


		$sql = "CREATE TABLE IF NOT EXISTS $tbl_name (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(150) NOT NULL,
				  `post` varchar(40) NOT NULL,
				  `taxonomy` varchar(150) NOT NULL,
				  `terms` text NOT NULL,
				  `pagination` enum('0','1') NOT NULL,
				  `pagination_count` int(11) NOT NULL,
				  `trim_content` enum('0','1') NOT NULL,
				  `trim_count` double NOT NULL,
				  `maximum_post` int(11) NOT NULL,
				  `sort` enum('asc','desc') NOT NULL,
				  `order_by` enum('date','modified', 'title') NOT NULL,
				  `template` text NOT NULL,
				  `wrapper_class` varchar(150) NOT NULL,
				  `created_by` int(11) NOT NULL,
				  `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `active` enum('0','1') NOT NULL,
				  PRIMARY KEY (`id`)
				) $charset_collate;";


		

		// $sql = "CREATE TABLE IF NOT EXISTS $tbl_name ( `id` INT NOT NULL AUTO_INCREMENT ,`name` VARCHAR(150) NOT NULL, `post_id` INT NOT NULL , `category_id` INT NOT NULL , `pagination` ENUM('0','1') NOT NULL , `pagination_count` INT NOT NULL , `title` ENUM('0','1') NOT NULL , `content` ENUM('0','1') NOT NULL , `image` ENUM('0','1') NOT NULL , `sort` ENUM('asc','desc') NOT NULL , `order_by` ENUM('created','modified') NOT NULL , `created_by` INT NOT NULL , `active` ENUM('0','1') NOT NULL , PRIMARY KEY (`id`)) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
