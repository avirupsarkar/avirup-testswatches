<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://example.com
 * @since             1.0.3
 * @package           Avs
 *
 * @wordpress-plugin
 * Plugin Name:       avirup swatches
 * Plugin URI:        https://example.com/plugin/avirup-swatches.zip
 * Description:       Nice and easy woocommerce product color variation swatches. Design your store with more modern looks.
 * Version:           1.0.0
 * Author:            avirup
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       avirup-swatches
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.3 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Avs_VERSION', '1.0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-avirup-swatches-activator.php
 */
if( !function_exists('activate_Avs') ){
    function activate_Avs() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-avirup-swatches-activator.php';
        Avs_Activator::activate();
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-avirup-swatches-deactivator.php
 */
if( !function_exists('deactivate_Avs') ){
    function deactivate_Avs() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-avirup-swatches-deactivator.php';
        Avs_Deactivator::deactivate();
    }   
}
register_activation_hook( __FILE__, 'activate_Avs' );
register_deactivation_hook( __FILE__, 'deactivate_Avs' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-avs-swatches.php';





/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.3
 */
if( !function_exists('run_Avs') ){
    function run_Avs() {

        $plugin = new Avs();
        $plugin->run();
    
    }
    run_Avs();
}