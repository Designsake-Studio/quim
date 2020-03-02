(function( $ ) {
    'use strict';
	$('document').ready(function() {
		var section_array = { 
			title: { enable: '#wt_smart_coupon_enable_banner_title', banner_section:'.wt_banner_title' , content:'#wt_smart_coupon_title_content' , font_size: '#wt_smart_coupon_title_font_size', color: '#wt_smart_coupon_enable_title_color' }, 
			description: { enable: '#wt_smart_coupon_enable_banner_description',banner_section:'.banner-description' , content: '#wt_smart_coupon_description_content', font_size: '#wt_smart_coupon_description_font_size', color: '#wt_smart_coupon_enable_description_color' },
			coupon_section: { enable: '#wt_smart_coupon_enable_coupon_section',banner_section:'.banner-coupon-code' , font_size: '#wt_smart_coupon_coupon_block_font_size', color: '#wt_smart_coupon_enable_coupon_block_color', border : '#wt_smart_coupon_enable_coupon_block_border_color', bg_color: '#wt_smart_coupon_enable_coupon_block_bg_color' },
			coupon_timer: { enable: '#wt_smart_coupon_enable_coupon_timer',banner_section:'.banner-coupon-timer' , font_size: '#wt_smart_coupon_coupon_timer_font_size', color: '#wt_smart_coupon_enable_coupon_timer_color', border : '#wt_smart_coupon_coupon_timer_border_color', bg_color: '#wt_smart_coupon_enable_coupon_timer_bg_color' },
			dismissbale_btn: { enable: '#wt_smart_coupon_enable_allow_dismissable', banner_section:'.wt_dismissable' , color: '#wt_smart_coupon_enable_title_color' }, 

        }
        
		$.each( section_array, function(i, item) {
			if(item.enable) {
				$( item.enable ).on('change',function() {
					if ( jQuery(this).is(":checked")) {
						$('.wt_banner '+ item.banner_section ).show();
					} else {
						$('.wt_banner '+ item.banner_section ).hide();
					}
				});
			}

			if( item.content ) {
				// Update content
				$( item.content  ).on('change',function() {
					if( '' == $(this).val() ) {
						$('.wt_banner '+ item.banner_section ).hide();
					} else {
						$('.wt_banner '+ item.banner_section ).show();
						$('.wt_banner_content ' + item.banner_section ).text($(this).val());
					}
				});

			}
			if( item.font_size ) {
				$( item.font_size  ).on('change',function() {
					var font_size = $(this).val();			
					if( font_size < 8 ) {
						return;
					}
					$('.wt_banner_content .coupon-banner-items ' + item.banner_section ).css( 'font-size',parseInt(font_size) );
				});
			}
			if( item.color ) {
				$( item.color ).on('change click keyup irischange',function(){
					var color = $(this).val();
					$('.wt_banner_content .coupon-banner-items ' + item.banner_section).css('color',color);
				});
			}

			if( item.bg_color ) {
				$( item.bg_color ).on('change click keyup irischange',function(){
					var bg_color = $(this).val();
					if( i == 'coupon_timer'  ) {
						$('.wt_banner_content .coupon-banner-items ' + item.banner_section + ' .wt_time_entry span' ).css('background-color',bg_color);
					} else {
						$('.wt_banner_content .coupon-banner-items ' + item.banner_section ).css('background-color',bg_color);

					}
				});
			}
			if( item.border ) {
				$( item.border ).on('change click keyup irischange',function(){
					var border_color = $(this).val();
					if( i == 'coupon_timer'  ) { 
						$('.wt_banner_content .coupon-banner-items ' + item.banner_section + ' .wt_time_entry span' ).css('border-color',border_color);
					} else {
						$('.wt_banner_content .coupon-banner-items ' + item.banner_section ).css('border-color',border_color);

					}
				});
			}
			
		});

		$('#wt_coupon_banner_type').on('change',function() {
			var banner_type = $(this).val();
			if( banner_type == 'widget') {
				$('.wt-banner-width').show();
				$('.wt-banner-height').show();
			} else {
				$('.wt-banner-width').hide();
				$('.wt-banner-height').hide();
			}

			$('.banner_type_postion').hide();
			var show_banner_postion = '.' + banner_type + '_position';
			$(show_banner_postion).show();
		});

		$('#wt_smart_coupon_banner_bg_color').on('change click keyup irischange',function() {
			var bg_color = $(this).val();
			$('.wt_banner').css('background-color',bg_color);
		});

		$('#wt_banner_border_color').on('change click keyup irischange',function(){
			var border_color = $(this).val();
			if( border_color ) {
				$('.wt_banner').css('border','1px solid ' + border_color);
			} else {
				$('.wt_banner').css('none');

			}
		});


		$('#wt_smart_coupon_action_on_applyign_coupon').on('change',function(){
			var val = $(this).val();
			if( 'redirect_to_url' === val ) {
				$('.child-wt_smart_coupon_action_on_applyign_coupon').show();
			} else {
				$('.child-wt_smart_coupon_action_on_applyign_coupon').hide();;
			}

		});

		$('#wt_smart_coupon_action_on_expiry_coupon').on('change',function(){
			var val = $(this).val();
			if( 'display_text' === val ) {
				$('.child-wt_smart_coupon_action_on_expiry_coupon').show();
			} else {
				$('.child-wt_smart_coupon_action_on_expiry_coupon').hide();;
			}

		});

		$('#wt_coupon_banner_type').on('change',function() {
			$('.wt_banner').removeClass('show_as_widget').removeClass('show_as_banner').removeClass('show_as_popup');
			var banner_type = $(this).val();
			$('.wt_banner').addClass( 'show_as_' + banner_type );
			$('.wt_banner').removeAttr('style');
			var bg_color = $('#wt_smart_coupon_banner_bg_color').val();
			var border_color = $('#wt_smart_coupon_banner_border_color').val();
			if( bg_color ) {
				$('.wt_banner').css("background-color",bg_color);

			} 
			if( border_color ) {
				$('.wt_banner').css("border-color",border_color);
			}
			if( banner_type == 'widget' ) {
				var height = $('#wt_smart_coupon_banner_height').val();
				var width = $('#wt_smart_coupon_banner_width').val();
				if( height ) {
					$('.wt_banner').css("height",height);
				}
				if( width ) {
					$('.wt_banner').css("width",width);
				}
			}
		});

		$('#wt_smart_coupon_banner_width').on('change',function() {
			var width = $('#wt_smart_coupon_banner_width').val();
			if( width ) {
				$('.wt_banner').css("width",width);
			}
		});

		$('#wt_smart_coupon_banner_height').on('change',function() {
			var height = $('#wt_smart_coupon_banner_height').val();
			if( height ) {
				$('.wt_banner').css("height",height);
			}
		});

		$('#wt_banner_dismissable_color').on('change click keyup irischange',function() {
			var color = $(this).val();
			$('.wt_dismissable').css('color',color);
		});


		$('.wt-slide_settings_menu').on('click',function(){
			$(this).toggleClass('expanded');
			$(this).next('.wt-sliding-menu').toggleClass('expanded');
			$(this).parents('.wt-sliding-menu-wrapper').toggleClass('expanded');;
			var prev = $(this).parents('.wt-sliding-menu-wrapper').prev('.wt-expand-item').toggleClass('expanded');
		});
		
		wt_popup.Set();		
	});


})( jQuery );



wt_popup={
	Set:function() {
		this.regPopupOpen();
		this.regPopupClose();
		jQuery('body').prepend('<div class="wt_popup_overlay"></div>');
	},
	regPopupOpen:function() {
		jQuery('[data-wt_popup]').click(function() {
			var elm_class	=	jQuery(this).attr('data-wt_popup');
			var elm	=	jQuery('#'+elm_class);
			if(elm.length > 0) {
				wt_popup.showPopup( elm );
			}
		});
	},
	showPopup:function( popup_elm ) {
		var pw = popup_elm.outerWidth();
		pw = 1035;
		var wh = jQuery(window).height();
		var ph = wh-150;
		popup_elm.css({'margin-left':( ( pw/2 )*-1 ),'display':'block','top':'20px'}).animate({'top':'50px'});
		popup_elm.find('.wt_popup_body').css({'max-height':ph+'px','overflow':'auto'});
		jQuery('.wt_popup_overlay').show();
	},
	hidePopup:function() {
		jQuery('.wt_popup_close').click();
	},
	regPopupClose:function( ) {
		jQuery(document).keyup(function(e){
			if( e.keyCode==27 ) {
				wt_popup.hidePopup();
			}
		});
		jQuery('.wt_popup_close, .wt_cancel, .wt_popup_overlay ').unbind('click').click(function() {
			jQuery('.wt_popup_overlay, .wt_popup').hide();
		});
		jQuery('.wt_popup_overlay').on('click',function() {
			jQuery('.wt_popup_overlay, .wt_popup').hide();
		});
	}
}