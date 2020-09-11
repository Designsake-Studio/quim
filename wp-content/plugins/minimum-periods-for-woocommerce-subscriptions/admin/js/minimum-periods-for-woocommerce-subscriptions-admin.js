jQuery(
	function( $ ) {
		'use strict';

		// Add unique IDs for deeplinking fromm plugin action links.
		$( '.woocommerce_page_wc-settings .wrap.woocommerce h2:contains("Cancelling")' ).attr( 'id', 'mpws_cancelling_heading' );
		$( '#mpws_cancelling_heading + table' ).wrap( "<div class='mpws_cancelling_form'></div>" );

		// Show/Hide period field depending on state of checkbox.
		$( '#mpws_allow_cancelling' ).change(
			function() {
				$( '#mpws_allow_cancelling_periods' ).closest( 'tr' ).hide();

				if ( this.checked ) {
					$( '#mpws_allow_cancelling_periods' ).closest( 'tr' ).show();
				}
			}
		).change();

		// Show/Hide period field on single subscription product page depending on state of select field.
		var mpws_single_cancelling = {
			wrapper : $( '#woocommerce-product-data' ),
			singleWrapper : $( '#mpws_allow_cancelling' ),

			init : function() {
				this.wrapper.on( 'change', '#mpws_allow_cancelling', this.mpwsAllowCancellingForSingle );
				this.singleWrapper.change();
			},

			mpwsAllowCancellingForSingle : function( e ) {
				$( e.currentTarget ).closest( '#general_product_data' ).find( '.mpws_allow_cancelling_periods_field' ).hide();

				if ( 'override-storewide' === $( e.currentTarget ).val() ) {
					$( e.currentTarget ).closest( '#general_product_data' ).find( '.mpws_allow_cancelling_periods_field' ).show();
				}
			}
		}

		mpws_single_cancelling.init();

		// Show/Hide period field on variable subscription product page depending on state of the variation select field.
		var mpws_variation_cancelling = {
			wrapper : $( '#woocommerce-product-data' ),
			variationsWrapper : $( '#variable_product_options' ),

			init : function() {
				this.wrapper.on( 'woocommerce_variations_added woocommerce_variations_loaded', this.mpwsVariationLoaded );
				this.variationsWrapper.on( 'change', '.mpws_allow_cancelling', this.mpwsAllowCancellingForVariation );

			},

			mpwsVariationLoaded : function() {
				$( '.mpws_allow_cancelling' ).change();
			},

			mpwsAllowCancellingForVariation : function( e ) {
				$( e.currentTarget ).closest( '.woocommerce_variation' ).find( '.mpws_allow_cancelling_periods_variation' ).hide();

				if ( 'override-storewide' === $( e.currentTarget ).val() ) {
					$( e.currentTarget ).closest( '.woocommerce_variation' ).find( '.mpws_allow_cancelling_periods_variation' ).show();
				}
			},
		};

		mpws_variation_cancelling.init();

	}
);
