<?php

if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_Banner' ) ) {
    class Wt_Smart_Coupon_Banner {
        protected $version;

        function __construct() {
            if ( defined( 'WEBTOFFEE_SMARTCOUPON_VERSION' ) ) {
				$this->version = WEBTOFFEE_SMARTCOUPON_VERSION;
			} else {
                $this->version = '1.2.10';
            }
        }

        /**
         * Add required Scripts
         * @since 1.2.9
         */
        function enqueue_scripts() {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';

            $script_parameters['ajaxurl'] = admin_url( 'admin-ajax.php' ) ;

            if ( function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids() ) ) {
                wp_enqueue_script('wt-smart-coupon-banner', plugin_dir_url(__FILE__) . 'js/wt-coupon-banner.js', array('jquery','wp-color-picker'), $this->version, false);
                wp_localize_script('wt-smart-coupon-banner','WTSmartCouponOBJ',$script_parameters );
            }
        }

        /**
         * Add required Scripts
         * @since 1.2.9
         */
        function enqueue_styles() {
            $screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            
            if ( function_exists('wc_get_screen_ids') && in_array( $screen_id, wc_get_screen_ids() ) ) {
                wp_enqueue_style( 'wt-smart-coupon-banner', plugin_dir_url(__FILE__) . 'css/wt-coupon-banner.css', array(), $this->version, 'all');
                wp_enqueue_style( 'wp-color-picker' );
            }
        }


        /**
         * Add coupon banner settings under Settings tab Smart Coupon
         * @since 1.2.9
         */
        function add_coupon_banner_settings( $settings ) {
            $settings['coupon_banner'] = __('Coupon banners','wt-smart-coupons-for-woocommerce-pro');

            return $settings;
        }
        /**
         * Define default coupon banner settings options
         * @since 1.2.9
         */
        function coupon_banner_option( $default_options ) {
            
            $coupon_banner_options = array(
                'display_banner'       => array(
                    'banner_type'               =>  'banner',
                    'height'                    =>  '',
                    'width'                     =>  '',
                    'bg_color'                  =>  '#3389ff',
                    'is_border'                 =>  false,
                    'border_color'              =>  '',
                    'banner_postion'            =>  'top',
                    'widget_postion'            =>  'bottom_left',
                    'allow_dismissable'         =>  true,
                    'dismissable_color'         => '#f3ecec61'
                ),
                'banner_title'                  =>  array(
                    'enable_banner_title'       => true,
                    'title'                     => 'FINAL HOURS!',
                    'font-size'                 => 20,
                    'font-color'                => '#f8f8f8',
                ),
                'banner_description'            => array(
                    'enable_banner_description' => true,
                    'title'                     => '20% OFF',
                    'font-size'                 => 18,
                    'font-color'                => '#f8f8f8',
                ),
                'coupon_section'            => array(
                    'enable_coupon_section' => true,
                    'font-size'             => 15,
                    'font-color'            => '#ffffff',
                    'bg-color'              => '',
                    'border-color'          => 'rgba(255, 255, 255, 0.37)',
                ),
                'coupon_timer'              => array(
                    'enable_coupon_timer'   => true,
                    'font-size'             => 13,
                    'font-color'            => '#ffffff',
                    'bg-color'              => '',
                    'border-color'          => '#f3ecec61',
                ),
                'inject_coupon'             => array (
                    'enable_inject_coupon'  => true,
                    'inject_coupon'         => '',
                    'inject_into_pages'     => ''
                )
            );
            if(isset( $default_options['wt_coupon_banner_settings'] )) {
                return array_merge( $default_options,$coupon_banner_options );
            }
            $default_options['wt_coupon_banner_settings'] = $coupon_banner_options;
            return $default_options;
        }

        /**
         * Get coupon banner settings options
         * @since 1.2.9
         */
        public function get_option( $option_name = '' ) {
            $smart_coupon_saved_option = get_option('wt_smart_coupon_options');
            if( '' != $option_name && isset( $smart_coupon_saved_option['wt_coupon_banner_settings'][$option_name] ) ) {
                return ( isset( $smart_coupon_saved_option['wt_coupon_banner_settings'][$option_name] ) )? $smart_coupon_saved_option['wt_coupon_banner_settings'][$option_name] : '';
            }
            return  $smart_coupon_saved_option['wt_coupon_banner_settings'];
            
        }
        /**
         * Update Coupon banner settings options
         * @since 1.2.9
         */
        function update_option( $coupon_banner_settings ) {
            if( empty($coupon_banner_settings) ) {
                return;
            }
           $smart_coupon_option = get_option( 'wt_smart_coupon_options' );
           $smart_coupon_option['wt_coupon_banner_settings'] = $coupon_banner_settings;
           update_option('wt_smart_coupon_options',$smart_coupon_option);
        }
        /**
         * Define different customizable option for smart coupon banner
         * @since 1.2.9
         */
        function banner_customisable_sections() {
            $sections = array(
                'show-us'           => array( 
                                        'title' => __('Show as','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => false
                                        ),
                'banner_title'      => array(
                                        'title' =>__('Title','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => true
                                        ),
                'banner_description' => array(
                                        'title' =>__('Short description','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => true
                                        ),
                 'coupon_timer'     => array(
                                        'title' =>__('Coupon timer','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => true
                                        ),
                'coupon_section'    => array(
                                        'title' => __('Display coupon','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => true
                                        ),
                
                'inject_coupon'     => array(
                                        'title' =>__('Inject coupon','wt-smart-coupons-for-woocommerce-pro'),
                                        'is_require_enable' => false
                                        ),
            );

            return apply_filters('wt_smart_coupon_banner_template_sections',$sections);
        }

        public function timer_divitions() {
            return  apply_filters( 'wt_smart_coupon_banner_timer_labels',array(
                'days' => __('Days','wt-smart-coupons-for-woocommerce-pro'),
                'hour' => __('Hours','wt-smart-coupons-for-woocommerce-pro'),
                'minutes' => __('Minutes','wt-smart-coupons-for-woocommerce-pro'),
                'seconds' => __('Seconds','wt-smart-coupons-for-woocommerce-pro'),
                'expired' => __('Expired !','wt-smart-coupons-for-woocommerce-pro'),
            ) );
        }


        public function coupon_timer_html_content( $expire_time, $timer_styles = array(), $coupon_banner_count = '' ) {

            $timer_divitions = $this->timer_divitions();
            $style =  '';
            $style = ( isset( $timer_styles['bg-color'] ) )?  $style.'background-color:'.$timer_styles['bg-color'].';' : $style ;
            $style = ( isset ( $timer_styles['border-color'] ) ) ?  $style.'border-color:'.$timer_styles['border-color'].';' : $style ;
            $selector = 'banner-coupon-timer';

            if( $coupon_banner_count ) {
                $selector = 'banner-coupon-timer_'.$coupon_banner_count;
            }
            ?>
            <script type="text/javascript">

                wt_get_timer_content( '<?php echo $expire_time; ?>','<?php echo $style; ?>','<?php echo json_encode($timer_divitions); ?>','<?php echo $selector; ?>' );

            
            </script>

            <?php
        }

        /**
         * Coupon banner setting page content
         * @since 1.2.9
         */
        function banner_coupon_settings_content() {
            
            do_action('wt_smart_coupon_before_banner_settings_form');
                $banner_copon_options = $this->get_option();
                $coupon_timer           = $banner_copon_options['coupon_timer'];
                $banner_dtails          = $banner_copon_options['display_banner'];
                $time_after_15_days = strtotime('+15 days');
                $counddownndate = date('M j, Y h:i:s',$time_after_15_days);
                // $this->coupon_timer_html_content( $counddownndate,$coupon_timer );
                $timer_divitions = $this->timer_divitions();

                $active_tab = get_transient( 'wt_active_tab_genral_settings' );
                $class = ' ';
                if( isset( $active_tab ) &&  $active_tab == 'coupon_banner' ) {
                    $class = 'active';
                }
            ?>

            
            <div  class="wt_sub_tab_content <?php echo $class; ?>" id="coupon_banner">
                    <div class="wt_settings_section">
                        <h2><?php _e('Coupon banners','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                        <p><?php  _e('Use the configuration panel to style your coupon banner. You can also keyin the shortcode manually within your pages to display/announce the discounts likewise. Or use the option within the panel so that we can inject it into the respective pages.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                    </div>
                    <div class="coupon-banner-preview wt-expand-item">
                        <div class="wt_banner_preview_wrapper">
                            <div class="banner_preview_title">
                                <h4><?php _e('Preview','wt-smart-coupons-for-woocommerce-pro') ?></h4>
                            </div>
                            <div class="banner_preview_content">
                            <?php
                            $banner_settings    =  $banner_copon_options['display_banner'];

                            $style = 'background-color:'.$banner_settings['bg_color'].';';
                            if( '' != $banner_settings['border_color'] ) {
                                $style .= 'border:1px solid '.$banner_settings['border_color'].';';
                            }
                            
                            if( $banner_settings['banner_type'] == 'widget' ) {
                                $style .= 'height:'.$banner_settings['height'].'px;width:'.$banner_settings['width'].'px;';
                            }
                            
                            ?>

                                <div class="wt_banner show_as_<?php echo $banner_settings['banner_type']; ?>"  style="<?php echo $style; ?>">
                                    <div class="wt_banner_content" <?php echo ( $banner_settings['allow_dismissable'] ) ? ' style="padding-right:35px;"': ''; ?> >
                                        <div class="coupon-items-container">
                                            <div class="coupon-banner-items">
                                                <?php 
                                                
                                                $title_tab_details      = $banner_copon_options['banner_title'];
                                                    $style = 'style="font-size:'.$title_tab_details['font-size'].'px;color:'.$title_tab_details['font-color'].';';
                                                    $style = ( ! $title_tab_details['enable_banner_title'] || '' == $title_tab_details['title'] )?  $style.'display:none;' : $style;
                                                    $style .= '"';
                                                 ?>
                                                <div class="wt_banner_title" <?php echo $style; ?>>
                                                    <?php echo $title_tab_details['title']; ?>
                                                </div>
                                                <?php 
                                                    $description_details    = $banner_copon_options['banner_description'];
                                                    $style = 'style="font-size:'.$description_details['font-size'].'px;color:'.$description_details['font-color'].';';

                                                    $style = ( ! $description_details['enable_banner_description'] || '' == $description_details['title'] )?  $style.'display:none;' : $style;
                                                    $style .= '"';
                                                 ?>
                                                <div class="banner-description" <?php echo $style; ?>>
                                                    <?php echo $description_details['title']; ?>
                                                </div>
                                                
                                                <?php
                                                    $style = 'style="font-size:'.$coupon_timer['font-size'].'px;color:'.$coupon_timer['font-color'].';';

                                                    $style = ( $coupon_timer['enable_coupon_timer'] )? $style  : $style.';display:none;';
                                                    $entry_style = ( $coupon_timer['bg-color'] )?  'background-color:'.$coupon_timer['bg-color'].';' : '' ;
                                                    $entry_style = ( $coupon_timer['border-color'] )?  $entry_style.'border-color:'.$coupon_timer['border-color'].';' : $entry_style ;
                                                    $style .= '"';
                                                ?>
                                                <div class="banner-coupon-timer" id="banner-coupon-timer"  <?php echo $style; ?> >
                                                    <div class=" wt_timer timer-day">
                                                        <div class="wt_time_entry">
                                                            <span style="<?php echo $entry_style; ?>" >0</span>
                                                            <span style="<?php echo $entry_style; ?>"> 3 </span>
                                                        </div>
                                                        <div class="wt_time_details">
                                                            <small><?php echo  $timer_divitions['days']; ?></small>
                                                        </div>
                                                    </div>
                                                    <div class=" wt_timer timer-hours">
                                                        <div class="wt_time_entry">
                                                            <span style="<?php echo $entry_style; ?>">1</span>
                                                            <span style="<?php echo $entry_style; ?>">7 </span>
                                                        </div>
                                                        <div class="wt_time_details">
                                                            <small><?php echo  $timer_divitions['hour']; ?></small>
                                                        </div>
                                                    </div>
                                                    <div class=" wt_timer timer-minutes">
                                                        <div class="wt_time_entry">
                                                            <span style="<?php echo $entry_style; ?>">5</span>
                                                            <span style="<?php echo $entry_style; ?>"> 9 </span>
                                                        </div>
                                                        <div class="wt_time_details">
                                                            <small><?php echo  $timer_divitions['minutes']; ?></small>
                                                        </div>
                                                    </div>
                                                    <div class=" wt_timer timer-seconds">
                                                        <div class="wt_time_entry">
                                                            <span style="<?php echo $entry_style; ?>">3</span>
                                                            <span style="<?php echo $entry_style; ?>">4</span>
                                                        </div>
                                                        <div class="wt_time_details">
                                                            <small><?php echo  $timer_divitions['seconds']; ?></small>
                                                        </div>
                                                    </div>
                                            
                                            
                                                </div>

                                                <?php
                                                    $coupon_section         = $banner_copon_options['coupon_section'];
                                                    $style = 'style="font-size:'.$coupon_section['font-size'].'px;color:'.$coupon_section['font-color'].';';

                                                    $style = ( $coupon_section['enable_coupon_section'] )? $style  : $style.';display:none;';
                                                    $style = ( $coupon_section['bg-color'] )?  $style.'background-color:'.$coupon_section['bg-color'].';' : $style ;
                                                    $style = ( $coupon_section['border-color'] )?  $style.'border-color:'.$coupon_section['border-color'].';' : $style ;
                                                    $style .= '"';
                                                ?>
                                                <div class="banner-coupon-code" <?php echo $style; ?>>
                                                    <?php _e( 'XXX-XXX-XXX','wt-smart-coupons-for-woocommerce-pro');?>
                                                </div>
                                                

                                            </div>
                                        </div>
                                        <?php if ( $banner_settings['allow_dismissable'] ) {
                                            echo '<div class="wt_dismissable" style="color:'.$banner_settings['dismissable_color'].'"> X </div>';
                                        } ?>
                                        
                                    </div>
                                </div>
                            </div>
                           
                        </div>

                        <?php do_action('wt_smart_coupon_after_banner_preview'); ?>
                    </div>

                    <!--  settings starts here -->
                    <div class="coupon-banner-settings wt-sliding-menu-wrapper">
                        <form  name = "wt_coupon_banner_settings" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" >
                            <?php wp_nonce_field('wt_smart_coupons_settings'); ?>
                        <span class="wt-slide_settings_menu">  </span>
                        <div class="wt-sliding-menu">
                        <?php
                            $template_sections = $this->banner_customisable_sections();
                            
                            foreach( $template_sections as $section_key => $template_section ) { ?>
                                <div class="panel-item <?php echo $section_key ?>">
                                    
                                    <div class="accordion-title-content">
                                        <div class="accordion-title"><?php echo trim( $template_section['title']); ?></div>
                                        <?php if( isset( $template_section['is_require_enable'] ) && $template_section['is_require_enable'] ) :
                                            $enabled = '';
                                            $section_enabled = ( isset( $banner_copon_options[ $section_key ]['enable_'.$section_key] ))?$banner_copon_options[ $section_key ]['enable_'.$section_key] : false ;
                                            if( $section_enabled ) {
                                                $enabled = 'checked=checked';
                                            }
                                            ?>
                                            <div class="wt-switch-checkbox">
                                                <label>
                                                    <input type="checkbox" name="wt_smart_coupon_enable_<?php echo $section_key; ?>" id="wt_smart_coupon_enable_<?php echo $section_key; ?>" <?php echo $enabled ; ?>>                       
                                                    <span class="wt-slider"></span>
                                                </label>
                                            </div>
                                            <?php endif; ?>
                                    </div>
                                    <div class="accordion-panel">
                                        <?php 
                                        do_action('wt_smart_coupon_banner_section_'.$section_key); ?>
                                    </div>
                                </div>
                            <?php
                            }


                            do_action('wt_smart_coupon_after_banner_settings_content');
                        ?>
                                <div class="form-submit">
                                    <button id="update_wt_smart_coupon_banner_settings" name="update_wt_smart_coupon_banner_settings" type="submit" class="button button-primary button-large" style="float:right"><?php _e( 'Apply changes','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="wt_form_submit">
                        
                    </div>
            </div>

            <?php
            do_action('wt_smart_coupon_after_banner_settings_form');
        }

        /**
         * inject Required script for Coupon banner page
         * @since 1.2.9
         */
        function inject_required_script() {
            $enable_disable_items = apply_filters('wt_smart_coupon_coupon_child_items',array('border','banner_title','banner_description','coupon_section','coupon_timer','allow_dismissable','inject_coupon'));
            $enable_displaye_items_script ='';
            foreach( $enable_disable_items as $enable_disable_item ) {
                $enable_displaye_items_script .= "#wt_smart_coupon_enable_".$enable_disable_item.",";
            }
            $enable_displaye_items_script  = substr_replace($enable_displaye_items_script ,"", -1);
            
            ?>
            <script type="text/javascript">
                
                jQuery('<?php echo $enable_displaye_items_script; ?>').on('change',function( e ) {
                    e.preventDefault();
                    var item_id = jQuery(this).attr('id');
                    if ( jQuery(this).is(":checked")) {
                        jQuery('.child-'+item_id).show();
                        return;
                    }
                    jQuery('.child-'+item_id).hide();
                });
            </script>
                
            <?php
        }

        /**
         * Show us tab content
         * @since 1.2.9
         */
        function show_us_tab_content() {
            $banner_tpes = apply_filters('wt_smart_coupon_add_display_types',array(
                'banner'    => __('Banner','wt-smart-coupons-for-woocommerce-pro'),
                'widget'    => __('Widget','wt-smart-coupons-for-woocommerce-pro'),
                // 'popup'     => __('Popup','wt-smart-coupons-for-woocommerce-pro'),
            ));

            $banner_position_banner = apply_filters('wt_smart_coupon_positions_for_banner',array(
                'top'    => __('Top','wt-smart-coupons-for-woocommerce-pro'),
                'bottom'    => __('Bottom','wt-smart-coupons-for-woocommerce-pro'),
                'custom'    => __('Custom','wt-smart-coupons-for-woocommerce-pro'),
            ));

            $banner_position_widget = apply_filters('wt_smart_coupon_positions_for_widget',array(
                'top_left'          => __('Top Left','wt-smart-coupons-for-woocommerce-pro'),
                'top_right'         => __('Top Right','wt-smart-coupons-for-woocommerce-pro'),
                'bottom_left'       => __('Bottom Left','wt-smart-coupons-for-woocommerce-pro'),
                'bottom_right'      => __('Bottom Right','wt-smart-coupons-for-woocommerce-pro'),
                'custom'            => __('Custom','wt-smart-coupons-for-woocommerce-pro'),
            ));

            $banner_dtails  = $this->get_option('display_banner');

            $banner_type_selected       = $banner_dtails['banner_type'];
            $banner_position_selected   = ( isset( $banner_dtails['banner_postion'] ) ) ? $banner_dtails['banner_postion'] : 'top';
            $widget_position_selected   = ( isset( $banner_dtails['widget_postion'] ) ) ? $banner_dtails['widget_postion'] : 'bottom_left';
            
            $banner_postion_style = 'style="display:none"';
            $widget_postion_style = 'style="display:none"';
            if( $banner_type_selected == 'widget') {
                $widget_postion_style = 'style="display:block"';
            } elseif( $banner_type_selected == 'banner' ) {
                $banner_postion_style = 'style="display:block"';
            }
            ?>

            <div class="form-item-grouped">
                <div class="form-group">
                    <label><?php _e('Type','wt-smart-coupons-for-woocommerce-pro') ?></label>
                    <select id="wt_coupon_banner_type" name="wt_coupon_banner_type" style="width: 140px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                        <?php
                            foreach( $banner_tpes  as $banner_tpe => $type ) {
                                $selected = '  ';
                                if( $banner_type_selected == $banner_tpe  ) {
                                    $selected = ' selected';
                                }
                                echo '<option value="'.$banner_tpe.'" '.$selected.'> '.$type.'</option>';
                            }
                        ?>
                    </select>
                </div>

                <!-- Postioning for Banner Type -->
                <div class="form-group banner_position banner_type_postion" <?php echo $banner_postion_style; ?> >
                    <label><?php _e('Position','wt-smart-coupons-for-woocommerce-pro') ?></label>
                    <select id="wt_coupon_banner_position" name="wt_coupon_banner_position" style="width: 140px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                        <?php
                            foreach( $banner_position_banner  as $position_key => $position ) {
                                $selected = '  ';
                                if( $banner_position_selected == $position_key  ) {
                                    $selected = ' selected';
                                }
                                echo '<option value="'.$position_key.'" '.$selected.'> '.$position.'</option>';
                            }
                        ?>
                    </select>
                </div>

                 <!-- Postioning for Widget Type -->
                 <div class="form-group widget_position banner_type_postion" <?php echo $widget_postion_style; ?>>
                    <label><?php _e('Position','wt-smart-coupons-for-woocommerce-pro') ?></label>
                    <select id="wt_coupon_widget_position" name="wt_coupon_widget_position" style="width: 140px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                        <?php
                            foreach( $banner_position_widget  as $position_key => $position ) {
                                $selected = '  ';
                                if( $widget_position_selected == $position_key  ) {
                                    $selected = ' selected';
                                }
                                echo '<option value="'.$position_key.'" '.$selected.'> '.$position.'</option>';
                            }
                        ?>
                    </select>
                </div>



            </div>
            <?php
            $style = '';
            if( $banner_type_selected != 'widget') {
                $style = 'style="display:none"';
            }
            ?>

            <div class="form-item-grouped">
                <div class="form-group wt-banner-height" <?php echo $style; ?> >
                    <label><?php _e('Height','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                    <input type="text" name="wt_smart_coupon_banner_height" id="wt_smart_coupon_banner_height" value="<?php echo $banner_dtails['height']; ?>">
                    <div class="wt_input_addon">
                        <input type="text" value="px" name="" />
                    </div>
                </div>

                <div class="form-group wt-banner-width"  <?php echo $style; ?> >
                    <label><?php _e('Width','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                    <input type="text" name="wt_smart_coupon_banner_width" id="wt_smart_coupon_banner_width" value="<?php echo $banner_dtails['width']; ?>">
                    <div class="wt_input_addon">
                        <input type="text" value="px" name="" />
                    </div>
                </div>
            </div>
            <?php
            $enabled    = '';
            $style      =  '';
            if( isset( $banner_dtails['allow_dismissable']) && $banner_dtails['allow_dismissable'] ) {
                $enabled =  'checked="checked"';
                $style = 'style="display:block"';
            }
            ?>
             

            <div class="form-group">
                <label><?php _e('Background Color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_banner_bg_color" type="text" name="wt_smart_coupon_banner_bg_color" id="wt_smart_coupon_banner_bg_color" value="<?php echo $banner_dtails['bg_color']; ?>">
            </div>

            
            <div class="form-group" >
                <label><?php _e('Border Color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input name="wt_banner_border_color" id="wt_banner_border_color" type="text" class="wt_colorpick wt_banner_border_color" value="<?php echo $banner_dtails['border_color']; ?>"/>
                
            </div>

            <div class="form-group form-group-checkbox">
                <label>
                    <?php _e('Dismissable','wt-smart-coupons-for-woocommerce-pro'); ?>
                    <input type="checkbox" name="wt_smart_coupon_banner_allow_dismissable" id="wt_smart_coupon_enable_allow_dismissable" <?php echo $enabled; ?>>
                </label>
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_allow_dismissable"  <?php echo $style; ?>>
                <label><?php _e('Dismissable Button Color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input name="wt_banner_dismissable_color" id="wt_banner_dismissable_color" type="text" class="wt_colorpick wt_banner_border_color" value="<?php echo isset( $banner_dtails['dismissable_color'] )? $banner_dtails['dismissable_color'] : '#ffffff'; ?>"/>
                
            </div>
            <?php
            $style      =  'style="display:block"';
            $apply_coupon = '';
            $redirect_to_url = '';
            if( isset( $banner_dtails['action_on_click']) && 'apply_coupon' == $banner_dtails['action_on_click'] ) {
                $style = 'style="display:none"';
                $apply_coupon = 'selected = "selected"';
            } else{
                $redirect_to_url = 'selected = "selected"';
            }
            ?>

            <div class="form-group form-group-selectbox">
                <label> <?php _e('Action on clicking banner','wt-smart-coupons-for-woocommerce-pro'); ?> </label>
                <select id="wt_smart_coupon_action_on_applyign_coupon" name="wt_smart_coupon_action_on_applyign_coupon" style="width: 140px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <option value="apply_coupon" <?php echo $apply_coupon; ?>><?php _e('Apply coupon','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                    <option value="redirect_to_url" <?php echo $redirect_to_url; ?>><?php _e('Redirect to URL','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                </select>
                
            </div>
            
            <div class="form-group child-form-group child-wt_smart_coupon_action_on_applyign_coupon"  <?php echo $style; ?>>
                <label><?php _e('Redirect to URL','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input name="wt_banner_redirect_url" id="wt_banner_redirect_url" type="text" value="<?php echo isset( $banner_dtails['redirect_url'] )? $banner_dtails['redirect_url'] : ''; ?>"/>
            </div>

            <?php 
            $enabled    = '';
            if( isset( $banner_dtails['url_open_in_another_tab']) && $banner_dtails['url_open_in_another_tab'] ) {
                $enabled =  'checked="checked"';
            }

            ?>

            <div class="form-group form-group-checkbox  child-form-group child-wt_smart_coupon_action_on_applyign_coupon"  <?php echo $style; ?>>
                <label>
                    <?php _e('Open in another tab','wt-smart-coupons-for-woocommerce-pro'); ?>
                    <input type="checkbox" name="wt_banner_url_open_in_another_tab" id="wt_banner_url_open_in_another_tab" <?php echo $enabled; ?>>
                </label>
            </div>




            <?php
        }
        /**
         * Coupon banner title settings tab content.
         * @since 1.2.9
         */
        function title_tab_content() {
            $title_tab_details  = $this->get_option('banner_title');
            

            $enabled    = '';
            $style = '';
            if( $title_tab_details['enable_banner_title'] ) {
                $enabled = 'checked';
                $style = 'style="display:block"';
            }
            ?>
            <!-- <div class="form-group form-group-checkbox">
                <label><?php //_e('Enable title','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input type="checkbox" name="wt_smart_coupon_enable_title" id="wt_smart_coupon_enable_title" <?php //echo $enabled; ?>>
            </div> -->
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_title"  <?php echo  $style; ?>>
                <label><?php _e('Text','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input type="text" name="wt_smart_coupon_title_content" id="wt_smart_coupon_title_content" value="<?php echo $title_tab_details['title'] ;?>">                
            </div>
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_title" <?php echo  $style; ?>>
                <label><?php _e('Font size','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="has-wt-addon" type="number" min=8 name="wt_smart_coupon_title_font_size" id="wt_smart_coupon_title_font_size" value="<?php echo $title_tab_details['font-size'] ;?>">
                
                <div class="wt_input_addon">
                    <input type="text" value="px" name="" />
                </div>

            </div>
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_title" <?php echo  $style; ?>>
                <label><?php _e('Text color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_title_color" type="text" name="wt_smart_coupon_enable_title_color" id="wt_smart_coupon_enable_title_color" value="<?php echo $title_tab_details['font-color'] ;?>">                                           
            </div>
            <?php
        }
        /**
         * Coupon settings coupon description content
         * @since 1.2.9
         */
        function short_description_content() {
            $description_details  = $this->get_option('banner_description');

            $enabled    = '';
            $style = '';
            if( $description_details['enable_banner_description'] ) {
                $enabled = 'checked';
                $style = 'style="display:block"';
            }
            ?>
            <!-- <div class="form-group form-group-checkbox">
                <label><?php // _e('Enable description','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input type="checkbox" name="wt_smart_coupon_enable_short_description" id="wt_smart_coupon_enable_short_description" <?php  //echo $enabled;?>>
            </div> -->
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_description" <?php echo $style; ?> >
                <label><?php _e('Text','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input type="text" name="wt_smart_coupon_description_content" id="wt_smart_coupon_description_content" value="<?php echo $description_details['title']; ?>">                
            </div>
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_description" <?php echo $style; ?>>
                <label><?php _e('Font size','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="has-wt-addon" type="number"  min=8 name="wt_smart_coupon_description_font_size" id="wt_smart_coupon_description_font_size"  value="<?php echo $description_details['font-size']; ?>">
                <div class="wt_input_addon">
                    <input type="text" value="px" name="" />
                </div>
            </div>
            <div class="form-group child-form-group1 child-wt_smart_coupon_enable_banner_description" <?php echo $style; ?> >
                <label><?php _e('Text color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_description_color" type="text" name="wt_smart_coupon_enable_description_color" id="wt_smart_coupon_enable_description_color" value="<?php echo $description_details['font-color']; ?>">                                           
            </div>
            <?php
        }
        /**
         * Coupon section settings content
         * @since 1.2.9
         */
        function coupon_content() {
            $coupon_section  = $this->get_option('coupon_section');

            $enabled    = '';
            $style = '';
            if( $coupon_section['enable_coupon_section'] ) {
                $enabled = 'checked';
                $style = 'style="display:block"';
            }
            ?>
            <!-- <div class="form-group form-group-checkbox">
                <label><?php //_e('Enable Coupon block','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <div class="wt-switch-checkbox">
                    <label>
                        <input type="checkbox" name="wt_smart_coupon_enable_display_coupon" id="wt_smart_coupon_enable_display_coupon" <?php //echo $enabled; ?>>                       
                        <span class="wt-slider"></span>
                    </label>
                </div>
            </div> -->
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_section" <?php echo  $style; ?>>
                <label><?php _e('Font size','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="has-wt-addon" type="number"  min=8 name="wt_smart_coupon_coupon_block_font_size" id="wt_smart_coupon_coupon_block_font_size" value="<?php echo $coupon_section['font-size']; ?>" >
                <div class="wt_input_addon">
                    <input type="text" value="px" name="" />
                </div>
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_section" <?php echo  $style; ?>>
                <label><?php _e('Text color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_coupon_block_color" type="text" name="wt_smart_coupon_enable_coupon_block_color" id="wt_smart_coupon_enable_coupon_block_color" value="<?php echo $coupon_section['font-color']; ?>">                                           
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_section" <?php echo  $style; ?>>
                <label><?php _e('Border color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_coupon_block_border_color" type="text" name="wt_smart_coupon_enable_coupon_block_border_color" id="wt_smart_coupon_enable_coupon_block_border_color" value="<?php echo $coupon_section['border-color']; ?>">                                           
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_section" <?php echo  $style; ?>>
                <label><?php _e('Backgrund color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_coupon_block_bg_color" type="text" name="wt_smart_coupon_enable_coupon_block_bg_color" id="wt_smart_coupon_enable_coupon_block_bg_color" value="<?php echo $coupon_section['bg-color']; ?>">                                           
            </div>
            <?php
        }
        /**
         * Coupon tier content.
         * @since 1.2.9
         */
        function coupon_timer_content() {
            $coupon_timer  = $this->get_option('coupon_timer');
            $enabled    = '';
            $style = '';
            if( $coupon_timer['enable_coupon_timer'] ) {
                $enabled = 'checked';
                $style = 'style="display:block"';
            }
            ?>
            <!-- <div class="form-group form-group-checkbox ">
                <label><?php //_e('Enable Coupon timer','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <div class="wt-switch-checkbox">
                    <label>
                        <input type="checkbox" name="wt_smart_coupon_enable_coupon_timer" id="wt_smart_coupon_enable_coupon_timer" <?php //echo $enabled; ?>>
                        <span class="wt-slider"></span>
                    </label>
                </div>
            </div> -->
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_timer" <?php echo $style; ?>>
                <label><?php _e('Font size','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="has-wt-addon" type="number"  min=8 name="wt_smart_coupon_coupon_timer_font_size" id="wt_smart_coupon_coupon_timer_font_size" value="<?php echo $coupon_timer['font-size']; ?>">
                <div class="wt_input_addon">
                    <input type="text" value="px" name="" />
                </div>
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_timer" <?php echo $style; ?>>
                <label><?php _e('Text color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_coupon_timer_color" type="text" name="wt_smart_coupon_enable_coupon_timer_color" id="wt_smart_coupon_enable_coupon_timer_color" value="<?php echo $coupon_timer['font-color']; ?>">                                           
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_timer" <?php echo $style; ?>>
                <label><?php _e('Border color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_coupon_timer_border_color" type="text" name="wt_smart_coupon_coupon_timer_border_color" id="wt_smart_coupon_coupon_timer_border_color"  value="<?php echo $coupon_timer['border-color']; ?>">                                           
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_coupon_timer" <?php echo $style; ?>>
                <label><?php _e('Backgrund color','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="wt_colorpick wt_smart_coupon_enable_coupon_timer_bg_color" type="text" name="wt_smart_coupon_enable_coupon_timer_bg_color" id="wt_smart_coupon_enable_coupon_timer_bg_color"  value="<?php echo $coupon_timer['bg-color']; ?>">                                           
            </div>

            <?php
            $style      =  'style="display:block"';
            $hide_banner = '';
            $display_text = '';
            if( isset( $coupon_timer['action_on_expiry']) && 'hide_banner' == $coupon_timer['action_on_expiry'] ) {
                $style = 'style="display:none"';
                $apply_coupon = 'selected = "selected"';
            } else{
                $display_text = 'selected = "selected"';
            }
            ?>
            <div class="form-group form-group-selectbox">
                <label> <?php _e('Action on expiry coupon','wt-smart-coupons-for-woocommerce-pro'); ?> </label>
                <select id="wt_smart_coupon_action_on_expiry_coupon" name="wt_smart_coupon_action_on_expiry_coupon" style="width: 140px"  class="wc-enhanced-select" data-placeholder="<?php _e('Please select', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <option value="hide_banner" <?php echo $hide_banner; ?>><?php _e('Hide banner','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                    <option value="display_text" <?php echo $display_text; ?>><?php _e('Display Text','wt-smart-coupons-for-woocommerce-pro'); ?></option>
                </select>
                
            </div>


            <div class="form-group child-form-group child-wt_smart_coupon_action_on_expiry_coupon" <?php echo $style; ?>>
                <label><?php _e('Text to display','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <input class="" type="text" name="wt_expiry_date_text_to_display" id="wt_expiry_date_text_to_display" value="<?php echo isset( $coupon_timer['expiry_text'] ) ? $coupon_timer['expiry_text'] : '' ; ?>">                                           
            </div>
            
            <?php
        }


        /**
         * Inject coupon tba content
         * @since 1.2.9
         */
        function inject_coupon_content() {
            $inject_coupon  = $this->get_option('inject_coupon');
            $enabled    = '';
            $style = '';
            if( $inject_coupon['enable_inject_coupon'] ) {
                $enabled = 'checked';
                $style = 'style="display:block"';
            }

            ?>
            <div class="form-group form-group-checkbox ">
                <label>
                    <?php _e('Inject coupon automatically','wt-smart-coupons-for-woocommerce-pro'); ?>
                    <input type="checkbox" name="wt_smart_coupon_enable_inject_coupon" id="wt_smart_coupon_enable_inject_coupon" <?php echo $enabled; ?>>
                    <?php echo wc_help_tip( __('By enabling the option the system will automatically embed a coupon banner in the specified pages. You must associate a coupon and also specify the pages where you need the coupon banner to be displayed. The style/layout will be set as is in the configuration panel.','wt-smart-coupons-for-woocommerce-pro') ); ?>
                </label>
            </div>
            <div class="form-group child-form-group child-wt_smart_coupon_enable_inject_coupon" <?php echo $style; ?>>
                <label><?php _e('Associate a coupon','wt-smart-coupons-for-woocommerce-pro'); ?></label>
                <select class="wt-coupon-search" style="width:300px;" id="master_coupon_for_inject_coupon" name="master_coupon_for_inject_coupon" data-placeholder="<?php echo esc_attr__( 'Search for a coupon&hellip;', 'wt-smart-coupons-for-woocommerce-pro' ); ?>" data-action="wt_json_search_coupons" data-security="<?php echo esc_attr( wp_create_nonce( 'search-coupons' ) ); ?>" >
                    <?php
                    $coupon_id = $inject_coupon['inject_coupon'];
                    
                    if( '' != $coupon_id ) {
                        $coupon_title = get_the_title( $coupon_id );
                        $coupon = new WC_Coupon( $coupon_id );

                        $discount_type = $coupon->get_discount_type();

                        if ( ! empty( $discount_type ) ) {
                            $discount_type = sprintf( __( ' ( %1$s: %2$s )', 'wt-smart-coupons-for-woocommerce-pro' ), __( 'Type', 'wt-smart-coupons-for-woocommerce-pro' ), $all_discount_types[ $discount_type ] );
                        }
                        if( $coupon_title && $discount_type ) {
                            echo '<option value="' . esc_attr( $coupon_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $coupon_title . $discount_type ) . '</option>';
                        }
                    }
                    
                    ?>

                </select>
            </div>

            <div class="form-group child-form-group child-wt_smart_coupon_enable_inject_coupon"  <?php echo $style; ?>>
                <label><?php _e('Pages','wt-smart-coupons-for-woocommerce-pro'); ?></label>

                <?php
                    $args   =  array(
                        
                        'sort_column'       => 'menu_order',
                        'sort_order'        => 'ASC',
                        'post_status'       => 'publish,private,draft',
                    );
                    $all_pages = get_pages($args);
                
                    $page_array = array( 0 => 'home' );
                    foreach( $all_pages as $page ) {
                        $page_array[$page->ID] = $page->post_title;
                    }

                    $pages_selected = explode(',',$inject_coupon['inject_into_pages'] );
                ?>


                <select id="wt_smart_coupon_inject_pages" name="wt_smart_coupon_inject_pages[]" style="width: 300px"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e('Select a pages&hellip;', 'wt-smart-coupons-for-woocommerce-pro'); ?>">
                    <?php
                    if( !empty( $page_array) ) {
                        foreach ($page_array as $id => $title ) {
                            if( !empty( $pages_selected  )  && in_array($id, $pages_selected  ) ) {
                                $selected = 'selected = selected';
                            } else {
                                $selected = '';
                            }
    
                            echo '<option value="' . esc_attr($id) . '" '.$selected.'>'
                            . esc_html($title) . '</option>';
                        }
                    }
                    ?>
                </select> 
            </div>

            <?php
        }
        /**
         * Save coupon banner settings
         * @since 1.2.9
         */
        function save_coupon_banner_settings() {
            if( isset( $_POST['update_wt_smart_coupon_banner_settings']) ) {

                set_transient('wt_active_tab_genral_settings','coupon_banner',30);

                $coupon_banner_options = array(
                    'display_banner'       => array(
                        'banner_type'           =>  (isset( $_POST['wt_coupon_banner_type'] ) ) ? $_POST['wt_coupon_banner_type'] : 'banner',
                        'height'                =>  (isset( $_POST['wt_smart_coupon_banner_height'] ) ) ? $_POST['wt_smart_coupon_banner_height'] : '',
                        'width'                 =>  (isset( $_POST['wt_smart_coupon_banner_width'] ) ) ? $_POST['wt_smart_coupon_banner_width'] : '',
                        'bg_color'              =>  (isset( $_POST['wt_smart_coupon_banner_bg_color'] ) ) ? $_POST['wt_smart_coupon_banner_bg_color'] : '#3389ff',
                        'is_border'             =>  (isset( $_POST['wt_smart_coupon_enable_border'] ) ) ? $_POST['wt_smart_coupon_enable_border'] : false,
                        'border_color'          =>  (isset( $_POST['wt_banner_border_color'] ) ) ? $_POST['wt_banner_border_color'] : '#ffffff',
                        'banner_postion'        =>  (isset( $_POST['wt_coupon_banner_position'] ) ) ? $_POST['wt_coupon_banner_position'] : 'top',
                        'widget_postion'        =>  (isset( $_POST['wt_coupon_widget_position'] ) ) ? $_POST['wt_coupon_widget_position'] : 'bottom_left',
                        'allow_dismissable'     =>  (isset( $_POST['wt_smart_coupon_banner_allow_dismissable'] ) && 'on' == $_POST['wt_smart_coupon_banner_allow_dismissable'] ) ? true : false,
                        'dismissable_color'     =>  (isset( $_POST['wt_banner_dismissable_color'] ) ) ? $_POST['wt_banner_dismissable_color'] : '#ffffff',
                        'action_on_click'       =>  (isset( $_POST['wt_smart_coupon_action_on_applyign_coupon'] ) ) ? $_POST['wt_smart_coupon_action_on_applyign_coupon'] : '',
                        'redirect_url'          =>  (isset( $_POST['wt_banner_redirect_url'] ) ) ? $_POST['wt_banner_redirect_url'] : '',
                        'url_open_in_another_tab'   =>  ( isset( $_POST['wt_banner_url_open_in_another_tab'] ) && 'on' == $_POST['wt_banner_url_open_in_another_tab'] ) ? true : false,
                    ),
                    'banner_title'              =>  array(
                        'enable_banner_title'   => ( isset( $_POST['wt_smart_coupon_enable_banner_title'] ) && 'on' == $_POST['wt_smart_coupon_enable_banner_title'] ) ? true : false,
                        'title'                 => (isset( $_POST['wt_smart_coupon_title_content'] ) ) ? $_POST['wt_smart_coupon_title_content'] : 'Limited-time offer!',
                        'font-size'             => (isset( $_POST['wt_smart_coupon_title_font_size'] ) ) ? $_POST['wt_smart_coupon_title_font_size'] : 20,
                        'font-color'            => (isset( $_POST['wt_smart_coupon_enable_title_color'] ) ) ? $_POST['wt_smart_coupon_enable_title_color'] : '#f8f8f8'
                    ),
                    'banner_description'            => array(
                        'enable_banner_description' => ( isset( $_POST['wt_smart_coupon_enable_banner_description'] ) && 'on' == $_POST['wt_smart_coupon_enable_banner_description'] ) ? true : false,
                        'title'                     => (isset( $_POST['wt_smart_coupon_description_content'] ) ) ? $_POST['wt_smart_coupon_description_content'] : 'Limited-time offer!',
                        'font-size'                 => (isset( $_POST['wt_smart_coupon_description_font_size'] ) ) ? $_POST['wt_smart_coupon_description_font_size'] : 20,
                        'font-color'                => (isset( $_POST['wt_smart_coupon_enable_description_color'] ) ) ? $_POST['wt_smart_coupon_enable_description_color'] : '#f8f8f8',
                    ),
                    'coupon_section'            => array(
                        'enable_coupon_section' => (isset( $_POST['wt_smart_coupon_enable_coupon_section'] ) && 'on' == $_POST['wt_smart_coupon_enable_coupon_section'] ) ? true : false,
                        'font-size'             => (isset( $_POST['wt_smart_coupon_coupon_block_font_size'] ) ) ? $_POST['wt_smart_coupon_coupon_block_font_size'] : 20,
                        'font-color'            => (isset( $_POST['wt_smart_coupon_enable_coupon_block_color'] ) ) ? $_POST['wt_smart_coupon_enable_coupon_block_color'] : '#ffffff',
                        'border-color'          => (isset( $_POST['wt_smart_coupon_enable_coupon_block_border_color'] ) ) ? $_POST['wt_smart_coupon_enable_coupon_block_border_color'] : '#f8f8f8',
                        'bg-color'              => (isset( $_POST['wt_smart_coupon_enable_coupon_block_bg_color'] ) ) ? $_POST['wt_smart_coupon_enable_coupon_block_bg_color'] : '#f8f8f8',
                    ),
                    'coupon_timer'              => array(
                        'enable_coupon_timer'   => (isset( $_POST['wt_smart_coupon_enable_coupon_timer'] ) &&  'on' ==  $_POST['wt_smart_coupon_enable_coupon_timer'] ) ? true : false,
                        'font-size'             => (isset( $_POST['wt_smart_coupon_coupon_timer_font_size'] ) ) ? $_POST['wt_smart_coupon_coupon_timer_font_size'] : 20,
                        'font-color'            => (isset( $_POST['wt_smart_coupon_enable_coupon_timer_color'] ) ) ? $_POST['wt_smart_coupon_enable_coupon_timer_color'] : '#ffffff',
                        'bg-color'              => (isset( $_POST['wt_smart_coupon_enable_coupon_timer_bg_color'] ) ) ? $_POST['wt_smart_coupon_enable_coupon_timer_bg_color'] : '#f8f8f8',
                        'border-color'          => (isset( $_POST['wt_smart_coupon_coupon_timer_border_color'] ) ) ? $_POST['wt_smart_coupon_coupon_timer_border_color'] : '#f8f8f8',
                        'action_on_expiry'      => (isset( $_POST['wt_smart_coupon_action_on_expiry_coupon'] ) ) ? $_POST['wt_smart_coupon_action_on_expiry_coupon'] : '',
                        'expiry_text'           => (isset( $_POST['wt_expiry_date_text_to_display'] ) ) ? $_POST['wt_expiry_date_text_to_display'] : '',
                    ),
                    'inject_coupon'             => array (
                        'enable_inject_coupon'  => (isset( $_POST['wt_smart_coupon_enable_inject_coupon'] ) &&  'on' ==  $_POST['wt_smart_coupon_enable_inject_coupon'] ) ? true : false,
                        'inject_coupon'         => (isset( $_POST['master_coupon_for_inject_coupon'] ) ) ? $_POST['master_coupon_for_inject_coupon'] : '',
                        'inject_into_pages'     => (isset( $_POST['wt_smart_coupon_inject_pages'] ) ) ?  implode(',',$_POST['wt_smart_coupon_inject_pages'] ) : '',
                    )
                );
                $this->update_option( $coupon_banner_options );
            }
        }

    }

}

