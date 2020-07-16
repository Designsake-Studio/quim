(function( $ ) {
	'use strict';


	function load_coupon_selector() {
		$( '.wt-coupon-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
				placeholder: $( this ).data( 'placeholder' ),
				minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url:         WTSmartCouponAdminOBJ.ajaxurl,
					contentType: "application/json; charset=utf-8",
					dataType:    'json',
					quietMillis: 300,
					data: function( params ) {
						return {
							term:     params.term,
							post_id:	jQuery( this ).data( 'postid' ),
							action:   jQuery( this ).data( 'action' ) || 'wt_json_search_coupons',
							_wpnonce: jQuery( this ).data( 'security' )
						};
					},
					processResults: function( data ) {
						var terms = [];
						if ( data ) {
							jQuery.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							});
						}
						return { results: terms };
					},
					cache: false
				}
			};
			
	
			jQuery( this ).selectWoo( select2_args );
	
	
		});
	}

	$(document).ready(function() {
		$( '#woocommerce-product-data' ).on('woocommerce_variations_loaded',function(){
			load_coupon_selector();
		});

	});
	
	
	$(document).ready(function() {
		// Insert Product condition
		var element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids[]\"]')"); //Since WC3.0.0
		if (element_product_ids.length != 1) element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids\"]')"); //Prior to WC3.0.0
		if (element_product_ids.length == 1) {
			var element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids[]\"]')"); 
			$("#woocommerce-coupon-data .form-field._wt_product_condition_field").detach().insertBefore( element_product_ids );
		}

		// Insert Category Condiiton
		var element_product_categories = $("#woocommerce-coupon-data .form-field:has('[name=\"product_categories[]\"]')");
		if (element_product_categories.length == 1) {
			$("#woocommerce-coupon-data .form-field._wt_category_condition_field").detach().insertBefore( element_product_categories );

		}

		$('#wt_smart_coupon_upload').on('change',function( ){
			$('.wt-file-container-label').html('File selected').addClass('selected');
		});

		load_coupon_selector();
		
		if ( typeof getEnhancedSelectFormatString == "undefined" ) {
			function getEnhancedSelectFormatString() {
				var formatString = {
					noResults: function() {
						return wc_enhanced_select_params.i18n_no_matches;
					},
					errorLoading: function() {
						return wc_enhanced_select_params.i18n_ajax_error;
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_enhanced_select_params.i18n_input_too_short_1;
						}

						return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_enhanced_select_params.i18n_input_too_long_1;
						}

						return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_enhanced_select_params.i18n_selection_too_long_1;
						}

						return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					loadingMore: function() {
						return wc_enhanced_select_params.i18n_load_more;
					},
					searching: function() {
						return wc_enhanced_select_params.i18n_searching;
					}
				};

				var language = { 'language' : formatString };

				return language;
			}
		}


		

		$('.wt_colorpick').wpColorPicker({
			'change':function(event, ui) {
				$(event.target).val(ui.color.toString());
				 $(event.target).click();
			 }
		});

		$('.wt_available_coupon_color').on('change click keyup irischange', function(){
			wt_reload_coupon_preview( 'available_coupon' );
		});

		$('.wt_used_coupon_color').on('change click keyup irischange', function(){
			wt_reload_coupon_preview( 'used_coupon' );
		});
		
		$('.wt_expired_coupon_color').on('change click keyup irischange', function(){
			wt_reload_coupon_preview( 'expired_coupon' );
		});




		var wt_create_coupon_preview = function( bg_color,text_color,border_color,style,is_style_change = false  ) {
			
			

			switch( style.id ) {

				case 'stitched_padding' :

					if( ! is_style_change ) {
						var css_style  = `style="background: `+ bg_color + `;
						color: `+ text_color + `; border:2px dashed `+ border_color + `;box-shadow: 0 0 0 4px `+ bg_color + `, 2px 1px 6px 4px rgba(10, 10, 0, 0)"`;
					} else {
						var css_style  = '';
					}

						var coupon_html = `
						<div class="wt-single-coupon `+ style.id +`" ` + css_style + `  >
							<div class="wt-coupon-content">
								<div class="wt-coupon-amount">
									<span class="amount">$10</span><span> CART DISCOUNT</span>
								</div>
								<div class="wt-coupon-code"> 
									<code>`+ style.text +`</code>
								</div>
							</div>
						</div>`;
						break;
				case 'stitched_edge' :

					if( ! is_style_change ) {
						var css_style  = `style="background: `+ bg_color + `;
						color: `+ text_color  + `; border:2px dashed `+ border_color + `;box-shadow:none"`;
					} else {
						var css_style  = '';
					}

					var coupon_html = `<div class="wt-single-coupon `+ style.id +`" ` + css_style +`>
						<div class="wt-coupon-content">
							<div class="wt-coupon-amount">
								<span class="amount">10$</span><span> CART DISCOUNT</span>
							</div>
							<div class="wt-coupon-code"> 
								<code>`+ style.text +`</code>
							</div>
						</div>
					</div>`;
					break;
				case 'ticket_style' : 
					if( ! is_style_change ) {
						var css_style  = `style="background:`+ bg_color +`; border:1px dotted `+ border_color +`; color: ` + text_color + `"`;
						var css_style2 = `style="color: `+ border_color +`"`;
					} else {
						var css_style  = '';
						var css_style2 = '';
					}
					var coupon_html =`<div class="wt-single-coupon `+ style.id +`"  ` + css_style +`>
								<div class="wt-coupon-content">
									<div class="wt-coupon-amount" ` + css_style2 +` >
										<span class="amount">10$</span>
									</div>
									<div class="wt-coupon-code"> 
										<span class="discount_type">CART DISCOUNT</span>
										<code>`+ style.text +`</code>
									</div>
								</div>
							</div>`;
						break;
				case 'plane_coupon' :
					if( ! is_style_change ) {
						var css_style  = `style="background:`+ bg_color +`; color: `+ border_color +`;"`;
					} else {
						var css_style  = '';
					}
					var coupon_html = `<div class="wt-single-coupon active-coupon `+ style.id +`" `+ css_style + `>
									<div class="wt-coupon-content">
										<div class="wt-coupon-amount">
											<span class="amount">10$</span><span> CART DISCOUNT</span>
										</div>
										<div class="wt-coupon-code"> 
											<code>`+ style.text +`</code>
										</div>
									</div>
								</div>`;
					break;
				default : 
					var coupon_html = `
						<div class="wt-single-coupon active-coupon `+ style.id +`" >
							<div class="wt-coupon-content">
								<div class="wt-coupon-amount">
									<span class="amount">$10</span><span> CART DISCOUNT</span>
								</div>
								<div class="wt-coupon-code"> 
									<code>`+ style.text +`</code>
								</div>
							</div>
						</div>`;
						break;
				

			}

			return coupon_html;

		};
	
		// Coupon Styles.
		var wt_reload_coupon_preview = function( coupon_type,is_style_change = false ) {
			switch( coupon_type) {
				case 'available_coupon' : 
					var coupon_preview_element = '.available_coupon_preview';
					var bg_color = $('#wt_available_coupon_color_0').val();
					var border_color = $('#wt_available_coupon_color_1').val();
					var text_color = $('#wt_available_coupon_color_2').val();
					var style_value = $("input[name='wt_available_coupon_style']:checked").val();
					
					// @need to be more dynamic 
					if( style_value == 'plane_coupon') {
						$('#wt_available_coupon_color_2').parents('.wp-picker-container').hide();
					} else {
						$('#wt_available_coupon_color_2').parents('.wp-picker-container').show();
					}
					var style_text = $("input[name='wt_available_coupon_style']:checked").attr('style');
		
					break;
				case 'used_coupon' : 
					var coupon_preview_element = '.used_coupon_preview';
					var bg_color = $('#wt_used_coupon_color_0').val();
					var border_color = $('#wt_used_coupon_color_1').val();
					var text_color = $('#wt_used_coupon_color_2').val();
					var style_value = $("input[name='wt_used_coupon_style']:checked").val();
					// @need to be more dynamic 
					if( style_value == 'plane_coupon') {
						$('#wt_used_coupon_color_2').parents('.wp-picker-container').hide();
					} else {
						$('#wt_used_coupon_color_2').parents('.wp-picker-container').show();
					}
					var style_text = $("input[name='wt_used_coupon_style']:checked").attr('style');
					break;
				case 'expired_coupon' : 
					var coupon_preview_element = '.expired_coupon_preview';
					var bg_color = $('#wt_expired_coupon_color_0').val();
					var border_color = $('#wt_expired_coupon_color_1').val();
					var text_color = $('#wt_expired_coupon_color_2').val();
					var style_value = $("input[name='wt_expired_coupon_style']:checked").val();
					// @need to be more dynamic 
					if( style_value == 'plane_coupon') {
						$('#wt_expired_coupon_color_2').parents('.wp-picker-container').hide();
					} else {
						$('#wt_expired_coupon_color_2').parents('.wp-picker-container').show();
					}
					var style_text = $("input[name='wt_expired_coupon_style']:checked").attr('style');
					break;

			}
			var style = {
				id: style_value,
				text: style_text
			};
			
			var preview = wt_create_coupon_preview( bg_color,text_color,border_color,style,is_style_change );
			
			$( coupon_preview_element ).find('.wt-coupon-preview-container').remove();
			$( coupon_preview_element ).append( '<span class="wt-coupon-preview-container">' + preview + '</span>' );
		};

		$('#wt_coupon_style_type').on('change',function(){
			var style_coupon = $(this).val();

			switch( style_coupon ) {
				case 'available_coupon' :
					$('#available_coupon_item').show();
					$('#used_coupon_item').hide();
					$('#expired_coupon_item').hide();
					break;
				case 'used_coupon' :
					$('#available_coupon_item').hide();
					$('#used_coupon_item').show();
					$('#expired_coupon_item').hide();
					break;
				case 'expired_coupon' :
					$('#available_coupon_item').hide();
					$('#used_coupon_item').hide();
					$('#expired_coupon_item').show();
					break;
				default:
					$('#available_coupon_item').show();
					$('#used_coupon_item').hide();
					$('#expired_coupon_item').hide();
			}
			
		});


		/**
		 * Set the preview on ready
		 */
		$(document).ready(function(){
			wt_reload_coupon_preview( 'available_coupon');
			wt_reload_coupon_preview( 'used_coupon');
			wt_reload_coupon_preview( 'expired_coupon');
		});

		/**
		 * Open modal
		 */
		$('.wt_modal_btn').on('click',function( e ){
			e.preventDefault();
			var target = $(this).attr('target');
			$(target).show();
			$('body').append('<div class="wt_modal_overlay"></div>');
			$('body').addClass('wt-modal-open');
		});


		/**
		 * Choose style
		 */
		$('.wt_choose_style').on('click',function() {
			var coupon_type = $(this).attr('coupon_type');
			var choosen_style = $('input[name="wt_'+coupon_type+'_style"]:checked').val();
			$.each(WTSmartCouponAdminOBJ.coupon_styles, function (index, value) {
				
				if( index == choosen_style ) {
					$('#wt_'+coupon_type+'_color_0').iris('color', value.colors[0] );
					$('#wt_'+coupon_type+'_color_1').iris('color', value.colors[1]);
					$('#wt_'+coupon_type+'_color_2').iris('color', value.colors[2]);
				}

			});
			wt_reload_coupon_preview( coupon_type,true );
			$(this).parents('.wt_modal').attr('coupon_type',coupon_type);
			$(this).parents('.wt_modal').attr('current-style',choosen_style);
			$('.wt_modal').hide();
			$('.wt_modal_overlay').remove();
			$('body').removeClass('wt-modal-open');
		});

		$('.wt-modal-close').on('click',function(){
			var coupon_type = $(this).parents('.wt_modal').attr('coupon_type');			
			var current_style = $(this).parents('.wt_modal').attr('current-style');
			$('input[name="wt_'+coupon_type+'_style"]:checked').val( current_style )
			$('.wt_modal').hide();
			$('.wt_modal_overlay').remove();
			$('body').removeClass('wt-modal-open');
		})
	});

	// Implement Subtab for admin screen.
	jQuery(document).ready(function(  ){

		jQuery('.wt_sub_tab li a').click(function( e ) {
			e.preventDefault();
			if( $(this).parent('li').hasClass('active') ) {
				return;//nothing to do;
			}
			var target=$(this).attr('href');
			var parent = $(this).parents('.wt_sub_tab');
			var container = $('.wt_sub_tab_container');
			$('.wt_sub_tab li').removeClass('active');
			$(this).parent('li').addClass('active');
			container.find('.wt_sub_tab_content').hide().removeClass('active');
			container.find(target).fadeIn().addClass('active');
		});
	});


	
	
	
	//Combo coupon
	$('document').ready(function() {
		// Insert Combo coupon HTML
		var element_individual_use_only = $("#woocommerce-coupon-data .form-field:has('[name=\"individual_use\"]')");
		if (element_individual_use_only.length == 1 ) {
			$("#woocommerce-coupon-data .wt_combo_coupon_fields").detach().insertAfter( element_individual_use_only );

		}
		
		$('input[name=\"individual_use\"]').on('change',function(){
			if( $(this).is(":checked") ) {
				$('.wt_combo_coupon_fields').hide();
			} else {
				$('.wt_combo_coupon_fields').show();
			}
			
		});

	});



	// Limit  max discount
	$('document').ready(function() {
		$('#discount_type').on('change',function(){
			var type = $(this).val();
			if( type == 'percent' || type == 'fixed_product' ) {
				$('#wt_max_discount').show();
			} else {
				$('#wt_max_discount').hide();
			}
		});
	});

	// resend coupon.
	$('document').ready(function() {
		$('.wt-btn-resend-coupon').on('click',function(e){
			e.preventDefault();
			var order_id = $(this).attr('order-id');
			
			var data = {
				'action'		: 'wt_send_coupon',
				'_wt_order_id'	: order_id,
				'_wpnonce'			: WTSmartCouponAdminOBJ.nonce
			};
			jQuery.ajax({
				type: "POST",
				url: WTSmartCouponAdminOBJ.ajaxurl,
				data: data,
				success: function (response) {
					$('.wt-send-status').removeClass('wt_error').removeClass('wt_success');
					var result = JSON.parse( response );
					if( result.error == false ) {
						$('.wt-send-status').addClass('wt_success').html( result.message );
					} else {
						$('.wt-send-status').addClass('wt_error').html( result.message );

					}
				}
			});

		});
	});

	

	/** signup coupon */
	$('document').ready(function() {
		$('#_wt_use_master_coupon_as_is').on('change',function(){
			if( $(this).is(":checked") ) {
				$('#_wt_signup_coupon_prefix, #_wt_signup_coupon_suffix, #_wt_signup_coupon_length').prop("disabled", true);
				$('#signup_coupon .wt_coupon_format').addClass('wt_disabled_form_item');
			} else {
				$('#_wt_signup_coupon_prefix, #_wt_signup_coupon_suffix, #_wt_signup_coupon_length').prop("disabled", false);
				$('#signup_coupon .wt_coupon_format').removeClass('wt_disabled_form_item');				
			}
		});
	});
	

	/** abandonment coupon */
	$('document').ready(function() {
		$('#_wt_use_master_coupon_as_is_abandonment').on('change',function(){
			if( $(this).is(":checked") ) {
				$('#_wt_abandonment_coupon_prefix, #_wt_abandonment_coupon_suffix, #_wt_abandonment_coupon_length').prop("disabled", true);
				$('#abandonment_coupon .wt_coupon_format').addClass('wt_disabled_form_item');
			} else {
				$('#_wt_abandonment_coupon_prefix, #_wt_abandonment_coupon_suffix, #_wt_abandonment_coupon_length').prop("disabled", false);
				$('#abandonment_coupon .wt_coupon_format').removeClass('wt_disabled_form_item');
			}
		});
	});


	

	/** create accordian */
	$('document').ready(function() {
		var acc = document.getElementsByClassName("accordion-title");
		var i;

		for (i = 0; i < acc.length; i++) {
			acc[i].addEventListener("click", function( e ) {
				e.preventDefault();
				$('.accordion-panel').hide();
				if( $(this).hasClass('active') ) {
					$('.accordion-title').removeClass('active');	
					return;
				}
				$('.accordion-title').removeClass('active');
				$(this).addClass('active');
				$(this).parents('.panel-item').children('.accordion-panel').show();
			});
		}
	});
	


})( jQuery );





