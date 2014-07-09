<?php
/**
 * @package   Wp_Job_Manager_Shortwidget
 * @author    Myles McNamara <myles@smyl.es>
 * @license   GPL-2.0+
 * @link      http://smyl.es
 * @copyright 2014 Myles McNamara
 *
 * @wordpress-plugin
 * Plugin Name: WP Job Manager ShortWidget
 * Plugin URI:  http://github.com/tripflex/wp-job-manager-shortwidget
 * Description: Shortcode and Widget builder for WP Job Manager
 * Version:     1.0.0
 * Author:      Myles McNamara
 * Author URI:  http://smyl.es
 * Text Domain: wp-job-manager-shortwidget
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-wp-job-manager-shortwidget.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/functions-job_field.php' );

require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-job.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-jobs.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-job_summary.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/widget-job_field.php' );

// Register hooks that are fired when the plugin is activated or deactivated.
// When the plugin is deleted, the uninstall.php file is loaded.
register_activation_hook( __FILE__, array( 'Wp_Job_Manager_Shortwidget', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Wp_Job_Manager_Shortwidget', 'deactivate' ) );

// Load instance
add_action( 'plugins_loaded', array( 'Wp_Job_Manager_Shortwidget', 'get_instance' ) );
//Wp_Job_Manager_Shortwidget::get_instance();
?>