<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             5.0.1
 * @package           extLinkReplacer
 *
 * @wordpress-plugin
 * Plugin Name:       extLinkReplacer
 * Description:       This plugin scan all of your posts for external image links, download images to folder`/wp-content/uploads/'year'/'months'/` and replace old links with `/wp-content/uploads/'year'/'months'/'image-name'`. After activating the plugin, a table with logs (name) will be created. When going to the settings page, the plugin automatically sends a query to the database for all id posts. After clicking the start button, a separate request is sent for each id to get the content of the post, which searches for images from external resources and copying them to our site using standard wordpress methods. As a result, the plugin logs will be saved to the name table and displayed on the screen. After deleting the plugin, the table with logs will be deleted.
 * Version:           5.0.1
 * Author:            WizardsDev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       extLinkReplacer
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 5.0.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'extLinkReplacer_VERSION', '5.0.1' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-extLinkReplacer-activator.php
 */

function activate_extLinkReplacer() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-extLinkReplacer-activator.php';
    extLinkReplacer_Activator::activate();

}
register_activation_hook( __FILE__, 'activate_extLinkReplacer' );


/**
 * Add settings link to plugin actions
 *
 * @param  array  $plugin_actions
 * @param  string $plugin_file
 * @since  1.0
 * @return array
 */
add_filter( 'plugin_action_links', 'wpcf_plugin_action_links', 10, 2 );
function wpcf_plugin_action_links( $actions, $plugin_file ){
    if( false === strpos( $plugin_file, basename(__FILE__) ) )
        return $actions;

    $settings_link = '<a href="/wp-admin/admin.php?page=extLinkReplacerOptions' .'">Settings</a>';
    array_unshift( $actions, $settings_link );

    return $actions;
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */


add_action( 'rest_api_init', 'extLinkReplacer_register_my_rest_routes' );
function extLinkReplacer_register_my_rest_routes() {
    require_once 'includes' . DIRECTORY_SEPARATOR . 'RestController.php';
    $controller = new RestController();
    $controller->register_routes();
}


require plugin_dir_path( __FILE__ ) . 'includes/class-extLinkReplacer.php';
new extLinkReplacer();