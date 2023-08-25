<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.3
 *
 * @package    Avs
 * @subpackage Avs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Avs
 * @subpackage Avs/public
 * @author     avirup <avirup@gmail.com>
 */

require_once(plugin_dir_path(__DIR__) .'includes/class-avirup-swatches-helper.php');


class Avs_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.3
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.3
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.3
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Avs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Avs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/avirup-swatches-public.css', array(), $this->version, 'all' );
		$get_options = Avs_Helper::get_option('avs_general');
		if($get_options['tooltip'] == true){
			$tooltip_css = "
				.avs-tooltip .".esc_attr($get_options['tooltip_position'])."{
					background-color:".esc_attr($get_options['tooltip_background']).";
					color:".esc_attr($get_options['tooltip_font_color']).";
					border-color:".esc_attr($get_options['tooltip_background']).";
				}

				.avs-tooltip .".esc_attr($get_options['tooltip_position'])."::after{
					background-color:".esc_attr($get_options['tooltip_background']).";
				}
			";

			wp_add_inline_style($this->plugin_name, $tooltip_css);
		}

		$swatch_size_css = "
			.avs-attr-image,
			.avs-attr-color{
				flex:1 0 ".esc_attr($get_options['swatch_size'])."px;
				max-width:".esc_attr($get_options['swatch_size'])."px;
			}

			.avs-attr-color {
				height: ".esc_attr($get_options['swatch_size'])."px;
				width: ".esc_attr($get_options['swatch_size'])."px;
			}
		";

		wp_add_inline_style($this->plugin_name, $swatch_size_css);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.3
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Avs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Avs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/avirup-swatches-public.js', array( 'jquery', 'wc-add-to-cart-variation' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'avs_swatches_settings',
			[
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'admin_url'              => admin_url( 'admin.php' ),
				'unavailable_text'       => __( 'Selected variant is unavailable.', 'avirup-swatches' ),
				'ajax_add_to_cart_nonce' => wp_create_nonce( 'avs_ajax_add_to_cart' )
			]
		);

	}

	/**
	 * Add to cart functionality for shop page
	 *
	 * @return mixed
	 * @since 1.0.3
	 */
	public function avs_ajax_add_to_cart() {
		check_ajax_referer( 'avs_ajax_add_to_cart', 'security' );

		if ( empty( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product_title     = get_the_title( $product_id );
		$quantity          = ! empty( $_POST['quantity'] ) ? wc_stock_amount( absint( $_POST['quantity'] ) ) : 1;
		$product_status    = get_post_status( $product_id );
		$variation_id      = ! empty( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
		$variation         = ! empty( $_POST['variation'] ) ? array_map( 'sanitize_text_field', $_POST['variation'] ) : array();
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
		$cart_page_url     = wc_get_cart_url();

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			} else {
				$added_to_cart_notice = sprintf(
					/* translators: %s: Product title */
					esc_html__( '"%1$s" has been added to your cart. %2$s', 'avirup-swatches' ),
					esc_html( $product_title ),
					'<a href="' . esc_url( $cart_page_url ) . '">' . esc_html__( 'View Cart', 'avirup-swatches' ) . '</a>'
				);

				wc_add_notice( $added_to_cart_notice );
			}

			WC_AJAX::get_refreshed_fragments();
		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
	}

	/**
	 * Arguments for shop page add to cart button
	 *
	 * @param array  $args array of button arguments.
	 * @param object $product curreent product object.
	 * @return array
	 * @since 1.0.3
	 */
	public function shop_page_add_to_cart_args( $args, $product ) {
		if ( $product->is_type( 'variable' ) ) {
			$args['class']                                 .= ' avs-ajax-add-to-cart';
			$args['attributes']['data-add_to_cart_text']    = esc_html__( 'Add to Cart', 'avirup-swatches' );
			$args['attributes']['data-select_options_text'] = apply_filters( 'woocommerce_product_add_to_cart_text', $product->add_to_cart_text(), $product );
		}

		return $args;
	}

	/**
	 * Generates variation attributes for shop page
	 *
	 * @return void
	 * @since  1.0.3
	 */
	public function variation_attribute_html_shop_page() {
		global $product;

		if ( ! $product->is_type( 'variable' ) ) {
			return;
		}

		if ( ! $product->get_available_variations() ) {
			return;
		}
		$product_id = $product->get_id();
		
		// Get Available variations?
		$get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
		$available_variations = $get_variations ? $product->get_available_variations() : false;
		$attributes           = $product->get_variation_attributes();

		$attribute_keys  = array_keys( $attributes );
		$variations_json = wp_json_encode( $available_variations );
		$get_options = Avs_Helper::get_option('avs_shop');
		
		?>
		<div class="avs-variations-form variations_form" data-product_variations="<?php echo esc_attr( $variations_json ); ?>" data-product_id="<?php echo absint( $product_id ); ?>">
			<?php if ( empty( $available_variations ) && false !== $available_variations ) { ?>
				<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'avirup-swatches' ) ) ); ?></p>
			<?php } else { ?>
				<table class="avs-shop-variations variations <?php echo esc_attr($get_options['swatch_alignments']); ?>" cellspacing="0">
					<tbody>
						<?php foreach ( $attributes as $attribute_name => $options ) { ?>
							<tr class="<?php echo esc_attr($get_options['swatch_alignments']); ?>">
								<?php if ( $get_options['swatch_label'] == true ) { ?>
								<th class="label woocommerce-loop-product__title <?php echo esc_attr($get_options['swatch_alignments']); ?>">
									<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
										<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
									</label>
								</th>
								<?php } ?>
								<td class="value">
									<?php
									wc_dropdown_variation_attribute_options(
										array(
											'options'   => $options,
											'attribute' => $attribute_name,
											'product'   => $product,
										)
									);
									echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '' ) ) : '';
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
		<?php
	}



	/**
	 * 
	 * Variation Markup By Type
	 * 
	 */

	public function variation_markup_by_type( string $type, object $term, string $attribute_name, string $selected ){
		$custom_markup = '';
		$get_options = Avs_Helper::get_option('avs_general');

		switch($type){
			case 'image':
				$get_term_value = get_term_meta($term->term_id, 'avs_image', true);
				$attr_json = wp_json_encode(array(
					"name"  => $attribute_name, 
					"value" => $term->slug
				));
				if($get_options['tooltip'] == true){
					$custom_markup .= '<div class="avs-tooltip">';
				}
				$custom_markup .= sprintf('<div class="avs-attr-image avs-swatches %s" data-attributes="%s">', esc_attr($get_options['swatch_style']), esc_attr($attr_json));
				$custom_markup .= sprintf('<img src="%s" alt=""/>', esc_html($get_term_value)); 
				$custom_markup .= '</div>';
				if($get_options['tooltip'] == true){
					$custom_markup .= '<div class="'.esc_attr($get_options['tooltip_position']).'">
							<span>'.ucwords(esc_html($term->slug)).'</span>
							
						</div>
					</div>';
				}
				break;

			case 'color':
				$get_term_value = get_term_meta($term->term_id, 'avs_color', true);
				$attr_json = wp_json_encode(array(
					"name"  => $attribute_name, 
					"value" => $term->slug
				));

				if(empty($get_term_value)){
					$custom_markup = esc_html__('No Colors Added!', '');
				}else{
					if($get_options['tooltip'] == true){
						$custom_markup .= '<div class="avs-tooltip">';
					}
					$custom_markup .= sprintf('<div style="background:%s" class="avs-attr-color avs-swatches %s" data-attributes="%s"></div>', esc_html($get_term_value), esc_attr($get_options['swatch_style']), esc_attr($attr_json));
					if($get_options['tooltip'] == true){
						$custom_markup .= '<div class="'.esc_attr($get_options['tooltip_position']).'">
								<span>'.ucwords(esc_html($term->slug)).'</span>
								
							</div>
						</div>';
					}
				}
				
				break;

			default:
				$attr_json = wp_json_encode(array(
					"name"  => $attribute_name, 
					"value" => $term->slug
				));

				$custom_markup = sprintf('<span class="avs-attr-button avs-swatches %s" data-attributes="%s">%s</span>', esc_attr($get_options['swatch_style']), esc_attr($attr_json), esc_html($term->name));
				
				break;
		}


		return $custom_markup;
	}


	/**
	 * Variation HTML Woocomerce select field modify
	 */

	public function variation_html($html, $args) {
		$args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
		  'options'          => false,
		  'attribute'        => false,
		  'product'          => false,
		  'selected'         => false,
		  'name'             => '',
		  'id'               => '',
		  'class'            => '',
		  'show_option_none' => __('Choose an option', 'avirup-swatches'),
		));
	  
		if(false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
		  $selected_key     = 'attribute_'.sanitize_title($args['attribute']);
		  $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
		}
	  
		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_'.sanitize_title($attribute);
	  
		if(empty($options) && !empty($product) && !empty($attribute)) {
		  $attributes = $product->get_variation_attributes();
		  $options    = $attributes[$attribute];
		}

		$type         = Avs_Helper::get_attr_type_by_name( $attribute );
		$type_array = array('color', 'image', 'select');
		
	  
		$avs_markup = '<div class="avs-variation-html variation-html avs-type-'.esc_attr($type).'">';
	  
		if(!empty($options)) {
		  if($product && taxonomy_exists($attribute)) {
			$terms = wc_get_product_terms($product->get_id(), $attribute, array(
			  'fields' => 'all',
			));
			if(in_array($type, $type_array, true)){
				foreach($terms as $term) {
					if(in_array($term->slug, $options, true)) {
						$avs_markup .= $this->variation_markup_by_type($type, $term, $name, $args['selected']);
					}
				}
			}else{
				$avs_markup .= $html;
			}
		  } else {
			foreach($options as $option) {
				$obj = (object) array("name" => $option, "slug" => $option);
				$avs_markup .= $this->variation_markup_by_type($type, $obj, $name, $args['selected']);
			}
		  }
		}
	  
		$avs_markup .= '</div>';

		if( !empty($avs_markup) ){
			return '<div class="avs-hidden-select">'.$html.'</div>' . $avs_markup;
		}
		
		  
		return $html;
	}
	  
	public function variation_check($active, $variation) {
		if(!$variation->is_in_stock() && !$variation->backorders_allowed()) {
			return false;
		}
		return $active;
	}

}
