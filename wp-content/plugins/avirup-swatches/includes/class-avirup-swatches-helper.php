<?php

/**
 * Helper Class For Woocommerce Custom Settings
 */


 class Avs_Helper{

	/**
	 * Keep default values of all settings.
	 *
	 * @var array
	 * @since  1.0.3
	 */
	public static $defaults = [
		'avs_general' => [
			'tooltip'      			=> true,
			'tooltip_position' 	  	=> 'top',
			'tooltip_background' 	=> '#000',
			'tooltip_font_color' 	=> '#fff',
			'swatch_style' 	  		=> 'square',
			'swatch_size' 	  		=> '26'
			
		],
		'avs_shop'   => [
			'enable_swatches'      	=> true,
			'swatch_position'		=> 'after_title',
			'swatch_alignments'		=> 'left',
			'swatch_label'			=> false,
		],
		'avs_style'  => [
			'tooltip_background' => '#000000',
			'tooltip_font_color' => '#ffffff',
			'tooltip_font_size'  => 12,
			'tooltip_image'      => false,
			'border_color'       => '#000000',
			'label_font_size'    => '',
			'filters'            => false,
		],
	];

    /**
	 * Get attribute type from database from attribute name
	 *
	 * @param string $name attribute name of product attribute.
	 * @return mixed
	 * @since  1.0.3
	 */
	public static function get_attr_type_by_name( $name = '' ) {
		if ( empty( $name ) || ! taxonomy_exists( $name ) ) {
			return '';
		}

		global $wpdb;
		$name = substr( $name, 3 );
		$type = $wpdb->get_var( $wpdb->prepare( 'SELECT attribute_type FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies WHERE attribute_name = %s', $name ) );
		return is_null( $type ) ? '' : $type;
	}


	/**
	 * Get option value from database and retruns value merged with default values
	 *
	 * @param string $option option name to get value from.
	 * @return array
	 * @since  1.0.3
	 */
	public static function get_option( $option ) {
		$db_values = get_option( $option, [] );
		return wp_parse_args( $db_values, self::$defaults[ $option ] );
	}
 }