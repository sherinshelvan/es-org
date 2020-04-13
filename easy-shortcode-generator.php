<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.sherinshelvan.com
 * @since             1.0.0
 * @package           Easy_Shortcode_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Shortcode Generator
 * Plugin URI:        https://www.sherinshelvan.com
 * Description:       Generate customizable shortcode to list any post contents in page contents and widget areas.
 * Version:           1.0.0
 * Author:            Sherin Shelvan
 * Author URI:        https://www.sherinshelvan.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-shortcode-generator
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
define( 'ESG_NAME', 'Easy Shortcode Generator' );
define( 'ESG_PLUGIN_NAME', 'easy_shortcode_generator' );
define( 'ESG_VERSION', '1.0.0' );
define( 'ESG_SLUG', 'easy-shortcode' );
define( 'ESG_ROOT', plugin_dir_path( __FILE__ ) );
define( 'ESG_URL', plugin_dir_url( __FILE__ ) );
define( 'ESG_ROOT_FILE', plugin_basename( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-shortcode-generator-activator.php
 */
function activate_easy_shortcode_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-shortcode-generator-activator.php';
	Easy_Shortcode_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-shortcode-generator-deactivator.php
 */
function deactivate_easy_shortcode_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-shortcode-generator-deactivator.php';
	Easy_Shortcode_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_easy_shortcode_generator' );
register_deactivation_hook( __FILE__, 'deactivate_easy_shortcode_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-easy-shortcode-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_easy_shortcode_generator() {

	$plugin = new Easy_Shortcode_Generator();
	$plugin->run();

}
run_easy_shortcode_generator();
