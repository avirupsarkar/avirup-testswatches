(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	// get variation 
	$(document).on('click', '.avs-swatches', function(){
		var $el = $(this);
		var attributes = $el.data('attributes')
		var thisName = attributes.name
		var thisVal  = attributes.value
		makeActiveSwatch($el);
		$('select[name="'+thisName+'"]').val(thisVal).trigger('change');
	});

	// reset variations

	$(document).on('click', '.reset_variations', function(){
		$('.avs-attr-image').each(function(i, el){
			$(el).removeClass('swatches-active-img')
		})
		$('.avs-attr-color').each(function(i, el){
			$(el).removeClass('swatches-active')
		})
		$('.avs-attr-button').each(function(i, el){
			$(el).removeClass('button-active')
		})
	})

	// make the swatch active
	function makeActiveSwatch(element){
		if( element.hasClass('avs-attr-image') ){
			$('.avs-attr-image').each(function(i, el){
				$(el).removeClass('swatches-active-img')
			})
			element.addClass('swatches-active-img');
		}else if( element.hasClass('avs-attr-color') ){
			$('.avs-attr-color').each(function(i, el){
				$(el).removeClass('swatches-active')
			})
			element.addClass('swatches-active');
		}else if( element.hasClass('avs-attr-button') ){
			$('.avs-attr-button').each(function(i, el){
				$(el).removeClass('button-active')
			})
			element.addClass('button-active');
		}else{
			console.log('No matches!')
		}
	}


	function addVariationFunctionality() {
		$( '.avs-variations-form:not(.variation-function-added)' ).each(
			function () {
				const thisForm = $( this );
				thisForm.addClass( 'variation-function-added' );
				thisForm.wc_variation_form();
				thisForm.on( 'found_variation', function ( e, variation ) {
					// console.log(variation);
					updateThumbnail( thisForm, variation.image );
					updatePrice( thisForm, variation );
					updatebuttonData( thisForm, variation );
				} );
			}
		);
	}

	function updateThumbnail( swatch, imageData ) {
		const listItem = swatch.closest( 'li' );
		if(listItem.length > 1){
			const thumbnail = listItem.find( 'img:first' );
			if ( 0 === listItem.find( '.avs-original-thumbnail' ).length ) {
				const originalThumbnail = thumbnail.clone();
				thumbnail.after( '<span class="avs-original-thumbnail"></span>' );
				listItem
					.find( '.avs-original-thumbnail' )
					.html( originalThumbnail );
			}
			thumbnail.attr( 'src', imageData.thumb_src );
			thumbnail.attr( 'srcset', '' );
		}else{
			const listItem = swatch.closest( '.has-post-thumbnail' );
			const thumbnail = listItem.find( 'img:first' );
			console.log(swatch);
			if ( 0 === thumbnail.find( '.avs-original-thumbnail' ).length ) {
				const originalThumbnail = thumbnail.clone();
				thumbnail.after( '<span class="avs-original-thumbnail"></span>' );
				thumbnail
					.find( '.avs-original-thumbnail' )
					.html( originalThumbnail );
			}
			thumbnail.attr( 'src', imageData.thumb_src );
			thumbnail.attr( 'srcset', '' );
		}
		
	}

	function updatePrice( swatch, variation ) {
		if ( 0 === variation.price_html.length ) {
			return;
		}
		if ( swatch.parents( 'li' ).find( '.avs-original-price' ).length ) {
			const price = swatch.parents( 'li' ).find( '.price' );
			price.replaceWith( variation.price_html );
		} else {
			const price = swatch.parents( 'li' ).find( '.price' );
			
			if( price.length > 1 ){
				price.removeClass( 'price' ).addClass( 'avs-original-price' );
				price.after( variation.price_html );
			}else{
				const price = swatch.parents( '.has-post-thumbnail' ).find( '.price' );
				price.removeClass( 'price' ).addClass( 'avs-original-price' );
				price.after( variation.price_html );
			}
			
			
		}
	}

	function updatebuttonData( variant, variation ) {
		const select = variant.find( '.variations select' );
		const data = {};
		const button = variant
			.parents( 'li' )
			.find( '.avs-ajax-add-to-cart' );
		select.each( function () {
			const attributeName =
				$( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			const value = $( this ).val() || '';
			data[ attributeName ] = value;
		} );

		if( button.length > 1 ){
			button.html( button.data( 'add_to_cart_text' ) );
			button.addClass( 'avs-variation-found' );
			button.attr( 'data-variation_id', variation.variation_id );
			button.attr( 'data-selected_variant', JSON.stringify( data ) );
		}else{
			const button = variant
				.closest( '.has-post-thumbnail' )
				.find( '.add_to_cart_button' );
			button.html( 'Add To Cart' );
			button.addClass( 'avs-variation-found' );
			button.attr( 'data-variation_id', variation.variation_id );
			button.attr( 'data-selected_variant', JSON.stringify( data ) );
		}
		
	}

	$( window ).load( function () {
		addVariationFunctionality();
	} );



	$( document ).on('click', '.avs-ajax-add-to-cart.avs-variation-found', function ( e ) {
			e.preventDefault();
			triggerAddToCart( $( this ) );
	});

	$( document ).on('click', '.add_to_cart_button.avs-variation-found', function ( e ) {
		e.preventDefault();
		triggerAddToCart( $( this ) );
	});


	function triggerAddToCart( variant ) {
		if ( variant.is( '.wc-variation-is-unavailable' ) ) {
			return window.alert( avs_swatches_settings.unavailable_text );
		}
		const productId = variant.data( 'product_id' );
		let variationId = variant.attr( 'data-variation_id' );
		variationId = parseInt( variationId );
		if (
			isNaN( productId ) ||
			productId === 0 ||
			isNaN( variationId ) ||
			variationId === 0
		) {
			return true;
		}
		let variation = variant.attr( 'data-selected_variant' );
		variation = JSON.parse( variation );
		const data = {
			action: 'avs_ajax_add_to_cart',
			security: avs_swatches_settings.ajax_add_to_cart_nonce,
			product_id: productId,
			variation_id: variationId,
			variation,
		};
		$( document.body ).trigger( 'adding_to_cart', [ variant, data ] );
		variant.removeClass( 'added' ).addClass( 'loading' );
		// Ajax add to cart request
		$.ajax( {
			type: 'POST',
			url: avs_swatches_settings.ajax_url,
			data,
			dataType: 'json',
			success( response ) {
				if ( ! response ) {
					return;
				}

				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}

				// Trigger event so themes can refresh other areas.
				$( document.body ).trigger( 'added_to_cart', [
					response.fragments,
					response.cart_hash,
					variant,
				] );
				$( document.body ).trigger( 'update_checkout' );

				variant.removeClass( 'loading' ).addClass( 'added' );
			},
			error( errorThrown ) {
				variant.removeClass( 'loading' );
				console.log( errorThrown );
			},
		} );
	}

})( jQuery );
