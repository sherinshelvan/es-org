<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       sherinshelvan.com
 * @since      1.0.0
 *
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Easy_Shortcode_Generator
 * @subpackage Easy_Shortcode_Generator/includes
 * @author     Sherin Shelvan <sherinshelvan@gmail.coom>
 */
class Easy_Shortcode_Generator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'easy-shortcode-generator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
