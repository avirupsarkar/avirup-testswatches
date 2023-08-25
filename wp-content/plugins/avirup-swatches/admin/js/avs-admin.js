(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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


	// term meta type

	let fileFrame;

	$( document ).ready( function () {
		$( '.avs_upload_image_button' ).on( 'click', function ( event ) {
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( fileFrame ) {
				// Open frame
				fileFrame.open();
				return;
			}

			// Create the media frame.
			fileFrame = wp.media.frames.fileFrame = wp.media( {
				title: avs_swatches_term_meta.image_upload_text.title,
				button: {
					text:
						avs_swatches_term_meta.image_upload_text.button_title,
				},
				multiple: false, // Set to true to allow multiple files to be selected
			} );

			// When an image is selected, run a callback.
			fileFrame.on( 'select', function () {
				// We set multiple to false so only get one image from the uploader
				const attachment = fileFrame
					.state()
					.get( 'selection' )
					.first()
					.toJSON();
				const attachmentUrl = attachment.url;
				$( '.avs-image-preview' )
					.attr( 'src', attachmentUrl )
					.css( 'width', 'auto' )
					.show();
				$( '.avs_remove_image_button' ).show();
				$( '.avs_product_attribute_image' ).val( attachmentUrl );
			} );

			// Finally, open the modal
			fileFrame.open();
		} );

		$( '.avs_remove_image_button' ).on( 'click', function () {
			$( '.avs_product_attribute_image' ).val( '' );
			$( '.avs-image-preview' ).attr( 'src', '' ).hide();
			$( '.avs_remove_image_button' ).hide();
		} );

		$( '.avs_color' ).wpColorPicker();
	} );

	$( document ).ajaxSuccess( function ( event, xhr, settings ) {
		//Check ajax action of request that succeeded
		if ( -1 === settings.data.indexOf( 'action=add-tag' ) ) {
			return;
		}
		const params = settings.data.split( '&' );
		const data = [];
		$.map( params, function ( val ) {
			const temp = val.split( '=' );
			data[ temp[ 0 ] ] = temp[ 1 ];
		} );
		if ( data.action === 'add-tag' ) {
			$( '.avs_product_attribute_image' ).val( '' );
			$( '.avs-image-preview' ).attr( 'src', '' ).hide();
			$( '.avs_remove_image_button' ).hide();
			$( '.avs_product_attribute_color .wp-picker-clear' ).trigger(
				'click'
			);
			$( '.avs_product_attribute_color' ).trigger( 'click' );
		}

		if ( 'undefined' !== typeof data.avs_image ) {
			$( '.wp-list-table #the-list' )
				.find( 'tr:first' )
				.find( 'th' )
				.after(
					'<td class="preview column-preview" data-colname="Preview"><img class="avs-preview" src="' +
						decodeURIComponent( data.avs_image ) +
						'" width="44px" height="44px"></td>'
				);
		}

		if ( 'undefined' !== typeof data.avs_color ) {
			$( '.wp-list-table #the-list' )
				.find( 'tr:first' )
				.find( 'th' )
				.after(
					'<td class="preview column-preview" data-colname="Preview"><div class="avs-preview" style="background-color:' +
						decodeURIComponent( data.avs_color ) +
						';width:30px;height:30px;"></div></td>'
				);
		}
	} );
})( jQuery );

