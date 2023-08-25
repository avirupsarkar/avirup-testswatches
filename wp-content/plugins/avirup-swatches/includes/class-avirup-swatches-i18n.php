<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://example.com
 * @since      1.0.3
 *
 * @package    Avs
 * @subpackage Avs/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.3
 * @package    Avs
 * @subpackage Avs/includes
 * @author     avirup <avirup@gmail.com>
 */
class Avs_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.3
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'avirup-swatches',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
