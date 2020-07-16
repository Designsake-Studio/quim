<?php
if (!defined('WPINC')) {
    die;
}

if( !class_exists('Wt_Smart_Coupon_Banner_Shortcode') ) {
    class Wt_Smart_Coupon_Banner_Shortcode extends Wt_Smart_Coupon_Banner {
        protected $banner_option;
        protected $coupon_banner_count;
        function __construct() {
            $coupon_banner_count = 0;
            add_shortcode( 'wt_smart_coupon_banner', array($this,'smart_coupon_banner_shortcode') );
        }



        /**
         * Add required Scripts
         * @since 1.2.9
         */
        function enqueue_scripts() {

            $script_parameters['ajaxurl'] = admin_url( 'admin-ajax.php' ) ;
            $script_parameters['nonce'] = wp_create_nonce( 'wt_smart_coupons_apply_coupon' );
            wp_enqueue_script('wt-smart-coupon-banner', plugin_dir_url(__FILE__) . 'js/wt-coupon-banner.js', array('jquery'), $this->version, false);
            wp_localize_script('wt-smart-coupon-banner','WTSmartCouponBannerOBJ',$script_parameters );
        }

        /**
         * Add required Scripts
         * @since 1.2.9
         */
        function enqueue_styles() {
            wp_enqueue_style( 'wt-smart-coupon-banner', plugin_dir_url(__FILE__) . 'css/wt-coupon-banner.css', array(), $this->version, 'all');
        }

        /**
         * Shortcode call back and arguments valdation
         * @since 1.2.9
         */
        function smart_coupon_banner_shortcode( $atts ) {
            $banner_options = shortcode_atts( array(
                'coupon_id'                 => '',
                'banner_type'               => 'banner',
                'title'                     => '', 
                'description'               => '',
                'position'                  => '',
                'bg_color'                  => '',
                'border_color'              => '',
                'enable_title'              => true,
                'enable_description'        => true,
                'enable_coupon'             => true,
                'enable_coupon_timer'       => true,
                'is_dismissable'            => true,
                'action_on_click'           => '',
                'redirect_url'              => '',
                'url_open_in_another_tab'   => true,
                'action_on_expiry'          => '',
                'expiry_text'               => '',
            ), $atts );

            if( '' == $banner_options['coupon_id'] ) {
                return '';
            }

            return $this->draw_the_banner( $banner_options );
        }

        /**
         * Draw the banner from givern styles and coupon
         * @since 1.2.9
         */
        public function draw_the_banner( $banner_args = array(), $coupn_id = '' ) {
            if( empty($banner_args)) {
                $banner_args = $this->banner_option;
            }
            

            $coupon_id = ( isset( $banner_args['coupon_id'] ) )? $banner_args['coupon_id'] : '';
            if( !$coupon_id ) {
                return false;
            }
            $coupon_obj  =  new WC_Coupon($coupon_id);
            $coupon_exp = $coupon_obj->get_date_expires();

            $banner_copon_options   = $this->get_option();
            $coupon_timer           = $banner_copon_options['coupon_timer'];
            $banner_settings        = $banner_copon_options['display_banner'];
            $banner_type            = ( isset( $banner_args['banner_type']) && ''!= $banner_args['banner_type'] )? $banner_args['banner_type'] : $banner_settings['banner_type'];
            $postion                = ( isset( $banner_args['position'] ) )? $banner_args['position'] : '';
            

            $banner_settings['action_on_click']         = ( ( isset($banner_args['action_on_click']) ) ? $banner_args['action_on_click'] : ( isset( $banner_settings['action_on_click'] )) ) ?  $banner_settings['action_on_click'] : '' ;
            $banner_settings['redirect_url']            = ( ( isset($banner_args['redirect_url']) ) ? $banner_args['redirect_url'] : ( isset( $banner_settings['redirect_url'] )) ) ?  $banner_settings['redirect_url'] : '' ;
            $banner_settings['url_open_in_another_tab'] = ( ( isset($banner_args['url_open_in_another_tab']) ) ? $banner_args['url_open_in_another_tab'] : ( isset( $banner_settings['url_open_in_another_tab'] )) ) ?  $banner_settings['url_open_in_another_tab'] : '';

            $coupon_timer['action_on_expiry']           = ( ( isset($banner_args['action_on_expiry']) ) ? $banner_args['action_on_expiry'] : ( isset( $coupon_timer['action_on_expiry'] )) ) ?  $coupon_timer['action_on_expiry'] : '' ;
            $coupon_timer['expiry_text']                = ( ( isset($banner_args['expiry_text']) ) ? $banner_args['expiry_text'] : ( isset( $coupon_timer['expiry_text'] )) ) ?  $coupon_timer['expiry_text'] : '' ;

            
           //positioning widget. 
           $positon_style  = '';
           
           if( 'widget' == $banner_type && ( 'custom' != $postion || ( '' == $postion && 'custom' != $banner_settings['widget_postion']  ) )  ) {
                $positon = ( '' != $postion )?  $postion : $banner_settings['widget_postion'];
                switch( $positon ) {
                    case  'top_left' :
                        $positon_style .= 'position:fixed;top:5px;left:5px;';
                        break;
                    case  'top_right' :
                        $positon_style .= 'position:fixed;top:5px;right:5px;';
                        break;
                    case  'bottom_right' :
                        $positon_style .= 'position:fixed;bottom:5px;right:5px;';
                        break;
                    case  'bottom_left' :
                        $positon_style .= 'position:fixed;bottom:5px;left:5px;';
                        break;
                    default :
                        $positon_style .= 'position:fixed;bottom:5px;left:5px;';
                        break;
                }
            } elseif( 'banner' == $banner_type && ( 'custom' != $postion || ( '' == $postion && 'custom' != $banner_settings['banner_postion']  ) ) ) {
                $positon = ( '' != $postion )?  $postion : $banner_settings['banner_postion'];
                switch( $positon ) {
                    case  'top' :
                        $positon_style .= 'position:fixed;top:0px;left:0px;';
                        break;
                    case  'bottom' :
                        $positon_style .= 'position:fixed;bottom:0px;left:0px;';
                        break;
                    default : 
                        $positon_style .= 'position:fixed;top:0px;left:0px;';
                        break;
                }
            }
            
            $banner_settings['bg_color'] = ( isset( $banner_args['bg_color'] ) && '' != $banner_args['bg_color'] )? $banner_args['bg_color'] :  $banner_settings['bg_color'];
            $style = 'background-color:'.$banner_settings['bg_color'].';';
            
            $banner_settings['border_color'] = ( isset( $banner_args['border_color'] ) && '' != $banner_args['border_color'] )? $banner_args['border_color'] :  $banner_settings['bg_color'];
            if( '' != $banner_settings['border_color'] ) {
                $style .= 'border:1px solid '.$banner_settings['border_color'].';';
            }
            if( $banner_type == 'widget' ) {
                $style .= 'height:'.$banner_settings['height'].'px;width:'.$banner_settings['width'].'px;';
            }
            if( '' != $positon_style ) {
                $style .= $positon_style;
            }
            $banner_settings['allow_dismissable'] = ( isset( $banner_args['is_dismissable'] ) )? $banner_args['is_dismissable']  : $banner_settings['allow_dismissable'];
            
            ob_start();
            $this->coupon_banner_count ++;
            $expired_new_setting_flag = false;
            $expired_text = false;
            if( $coupon_exp && $coupon_timer['enable_coupon_timer'] ) {
                $expire_date = $coupon_exp->getTimestamp();

                if( $expire_date <= time() ) {
                    if( isset($coupon_timer['action_on_expiry'] ) && 'hide_banner' == $coupon_timer['action_on_expiry'] ) {
                        $expired_new_setting_flag = true;
                        return false;
                    } elseif( isset($coupon_timer['action_on_expiry'] ) && 'display_text' == $coupon_timer['action_on_expiry'] ) {
                        $expired_new_setting_flag = true;
                        $expired_text = ( isset( $coupon_timer['expiry_text'] ) )? $coupon_timer['expiry_text'] : __('Expired!','wt-smart-coupons-for-woocommerce-pro');
                    }
                }
                if( ! $expired_new_setting_flag ) {
                    $this->coupon_timer_html_content( $expire_date,$coupon_timer,$this->coupon_banner_count );
                }

            }
            $redirect_url = '';
            $apply_coupon = '';
            $custom_attr = '';
            if( isset( $banner_settings['action_on_click'] ) && 'redirect_to_url' === $banner_settings['action_on_click'] ) {
                $redirect_url  = ( isset( $banner_settings['redirect_url'] ) ) ?  $banner_settings['redirect_url'] : '';
                $target = ( isset( $banner_settings['url_open_in_another_tab'] ) && $banner_settings['url_open_in_another_tab'] )? 'target="_blank"': '';
                if( '' != $redirect_url ) {
                    echo '<a href='.$redirect_url.' '.$target.' >';
                }
            } elseif( isset( $banner_settings['action_on_click'] ) && 'apply_coupon' === $banner_settings['action_on_click'] ) {
                $apply_coupon = 'wt_apply_coupon_banner';
                $custom_attr .= 'coupon="'.$coupon_obj->get_code().'"';
            }
            if( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
                $cart_page = wc_get_cart_url();
                $custom_attr .= ' redirect='. $cart_page.'/?wt_coupon='.$coupon_obj->get_code();
                // $custom_attr .= ' redirect='. $cart_page;
            }
            ?>

            <div class="wt_banner show_as_<?php echo $banner_type; ?>  <?php echo $apply_coupon; ?>"  style="<?php echo $style; ?>"  <?php echo $custom_attr; ?>>
                <div class="wt_banner_content" <?php echo ( $banner_settings['allow_dismissable'] ) ? ' style="padding-right:35px;"': ''; ?> >
                    <div class="coupon-items-container">
                        <div class="coupon-banner-items">
                            <?php 
                            
                            $title_tab_details      = $banner_copon_options['banner_title'];

                                if( isset($banner_args['title'] ) && '' != $banner_args['title'] ) {
                                    $title_tab_details['title'] = $banner_args['title'];
                                }
                                $style = 'style="font-size:'.$title_tab_details['font-size'].'px;color:'.$title_tab_details['font-color'].';';
                                
                                $title_tab_details['enable_title'] = ( isset( $banner_args['enable_title'] ) )? $banner_args['enable_title'] : $title_tab_details['enable_banner_title'];                                
                                $style = ( isset( $title_tab_details['enable_title'] )  && ( ! $title_tab_details['enable_title'] || '' == $title_tab_details['title'] ) )?  $style.'display:none;' : $style;                               
                                $style .= '"';
                                ?>
                            <div class="wt_banner_title" <?php echo $style; ?>>
                                <?php echo $title_tab_details['title']; ?>
                            </div>
                            <?php 
                                $description_details    = $banner_copon_options['banner_description'];
                                if( isset($banner_args['description'] )  && '' != $banner_args['description'] ) {
                                    $description_details['title'] = $banner_args['description'];
                                }
                                $style = 'style="font-size:'.$description_details['font-size'].'px;color:'.$description_details['font-color'].';';

                                $description_details['enable_description'] = ( isset( $banner_args['enable_description'] ) )? $banner_args['enable_description'] : $description_details['enable_banner_description'];
                                $style = ( isset( $description_details['enable_description'] ) && (  ! $description_details['enable_description'] || '' == $description_details['title'] ) )?  $style.'display:none;' : $style;
                                
                                $style .= '"';
                                ?>
                            <div class="banner-description" <?php echo $style; ?>>
                                <?php echo $description_details['title']; ?>
                            </div>

                            
                            <?php
                                $style = 'style="font-size:'.$coupon_timer['font-size'].'px;color:'.$coupon_timer['font-color'].';';
                                
                                $coupon_timer['enable_coupon_timer'] = ( isset( $banner_args['enable_coupon_timer'] ) )? $banner_args['enable_coupon_timer'] : $coupon_timer['enable_coupon_timer'];
                                $style = ( $coupon_timer['enable_coupon_timer'] )? $style  : $style.';display:none;';
                                $style .= '"';
                            ?>
                            <div class="banner-coupon-timer" id="banner-coupon-timer_<?php echo $this->coupon_banner_count; ?>" <?php echo $style; ?>>
                                <?php
                                    if( $expired_new_setting_flag && $expired_text ) {
                                        echo $expired_text;
                                    }
                                ?>
                            
                            </div>
                            <?php
                                $coupon_section         = $banner_copon_options['coupon_section'];
                                $style = 'style="font-size:'.$coupon_section['font-size'].'px;color:'.$coupon_section['font-color'].';';

                                $coupon_section['enable_coupon'] = ( isset( $banner_args['display_coupon'] ) )? $banner_args['display_coupon'] : $coupon_section['enable_coupon_section'];
                                $style = ( ! isset( $coupon_section['enable_coupon'] ) ||  ( isset( $coupon_section['enable_coupon'] ) && $coupon_section['enable_coupon'] ) )? $style  : $style.';display:none;';
                                $style = ( isset( $coupon_section['bg-color'] ) && $coupon_section['bg-color'] )?  $style.'background-color:'.$coupon_section['bg-color'].';' : $style ;
                                $style = ( isset( $coupon_section['border-color'] ) && $coupon_section['border-color'] )?  $style.'border-color:'.$coupon_section['border-color'].';' : $style ;
                                $style .= '"';
                            ?>
                            <div class="banner-coupon-code" <?php echo $style; ?>>
                                <?php echo $coupon_obj->get_code(); ?>
                            </div>
                            

                        </div>
                    </div>
                    <?php
                    if ( $banner_settings['allow_dismissable'] && "false" !== $banner_settings['allow_dismissable'] ) { 
                        echo '<div class="wt_dismissable" style="color:'.$banner_settings['dismissable_color'].'"> X </div>';
                    }
                    ?>
                </div>
            </div> 
            <?php
            if( '' != $redirect_url ) {
                echo '</a>';
            }

            return ob_get_clean();
        }

        /**
         * Display on specified page automatically
         * @since 1.2.9
         */
        function inject_banner() {
            $banner_copon_options   = $this->get_option('inject_coupon');
            $display_banner_options = $this->get_option('display_banner');

            if(  $banner_copon_options['enable_inject_coupon'] && '' != $banner_copon_options['inject_coupon'] && '' != $banner_copon_options['inject_into_pages'] ) {
                $banner_options = array(
                    'coupon_id'                 => $banner_copon_options['inject_coupon']
                );

                if( 'banner' == $display_banner_options['banner_type'] && 'custom' == ['banner_postion']  ) {
                    $banner_options['position'] = 'bottom';
                } elseif( 'widget' == $display_banner_options['banner_type'] && 'custom' == ['widget_postion']  ) {
                    $banner_options['position'] = 'top_left';
                }

                $inject_on_pages = explode( ',',$banner_copon_options['inject_into_pages']);
                $current_page_id =  get_the_ID();
                $enable_on_this_page = false;
                if( ( is_front_page() && in_array(0,$inject_on_pages) ) || in_array($current_page_id,$inject_on_pages ) ){
                    $enable_on_this_page = true;
                    echo $this->draw_the_banner( $banner_options );
                }
            }
        }

        /**
         * Guideline for using coupon banner shortcode
         * 
         * @since 1.2.9
         */
        function display_short_code_guidelines() {
            $args = array(
                'coupon_id' => array(
                    'default'       => '',
                    'description'   => __('Coupon ID(coupon post_id) to be displayed in the banner.','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => true
                ),
                'banner_type' => array(
                    'default'       => 'banner',
                    'description'   => __('Displays type. Values{banner,widget}','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'enable_title' => array(
                    'default'       =>'true',
                    'description'   => __('Enables title on banner','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'title' => array(
                    'default'       => 'FINAL HOURS',
                    'description'   => 'Title',
                    'is_required'   => false
                ),
                'enable_description' => array(
                    'default'       => 'true',
                    'description'   => __('Enables description on banner','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'description' => array(
                    'default'       => '20% OFF',
                    'description'   => __('Description','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'position' => array(
                    'default'       => '',
                    'description'   => __('Display position. for banner: {top,bottom,custom} for widget {top_left,top_right,bottom_left,bottom_right,custom}','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'bg_color' => array(
                    'default'       => '#3389ff',
                    'description'   => __('Background color','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'border_color' => array(
                    'default'       => '',
                    'description'   => __('Border color','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'display_coupon' => array(
                    'default'       => 'true',
                    'description'   => __('Displays coupon code on banner','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'enable_coupon_timer' => array(
                    'default'       => 'true',
                    'description'   => __('Enables coupon timer on banner. Assumes the expiry date of the associated coupon as value.','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                ),
                'is_dismissable' => array(
                    'default'       => 'true',
                    'description'   => __('Provisions a close button on the banner.','wt-smart-coupons-for-woocommerce-pro'),
                    'is_required'   => false
                )
            );
            $coupon_short_code_arguments = apply_filters( 'wt_smart_coupon_banner_short_Code_arguments', $args )
           ?>
                <div class="coupon_guidelines">
                    <h3><?php _e('How to use coupon banner shortcode?','wt-smart-coupons-for-woocommerce-pro'); ?></h3>
                    <div class="shortcode">
                        <p> <?php _e('You can use shortcodes to set up a coupon banner on your website. This can be done by embedding the shortcode manually into any of your pages or automatically by using the configuration option "Inject coupons". Either way will ensure a coupon banner announcing the offer to your visitors.','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
                        <p><?php  printf( __( 'To achieve this, simply place the shortcode in the prescribed format %s within the respective page to display the default coupon banner. coupon_id is the post id of the coupon(created prior via Woocommerce->Coupons).','wt-smart-coupons-for-woocommerce-pro'),'<b>[wt_smart_coupon_banner coupon_id=xxx]</b>' ); ?></p>
                        <p> <?php printf( __('Alternatively, you can pass specific %s along with the shortcode to override the default coupon banner appearance. Some of the predefined arguments that can be used along with shortcodes are defined in the list.','wt-smart-coupons-for-woocommerce-pro'),'<a data-wt_popup="wt_short_code_arguments" class="wt_popup_open" >'.__('arguments/parameters','wt-smart-coupons-for-woocommerce-pro').'</a>');  ?></p>
                        
                        
                        <p><b><?php _e('Example:','wt-smart-coupons-for-woocommerce-pro') ?></b></p>
                        <ul class="wt_banner_short_code_example">
                            <li>
                                <p><?php _e('Shortcode for default banner layout','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
                                <p>
                                    <b>[wt_smart_coupon_banner coupon_id=2828]</b>
                                    <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/banner-default.png' )  ); ?>?TB_iframe=true&width=100&height=100"><small>[<?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                                </p>
                                <p><?php _e( 'Displays the banner for the coupon id 2828 with the default coupon specifications.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                            </li>

                            <li>
                                <p><?php _e('Shortcode with arguments','wt-smart-coupons-for-woocommerce-pro'); ?>  </p>
                                <p>
                                    <b>[wt_smart_coupon_banner coupon_id=4545 banner_type="widget" title="End of Season Sale" description="Avail 50%discount" position="bottom_right" bg_color="#8224e3" ]</b>
                                    <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/banner-widget.jpg' )  ); ?>?TB_iframe=true&width=100&height=100"><small>[<?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                                </p>
                                <p> <?php _e('The above shortcode will set the appearance type as a widget with title, description, positioned to bottom right and background color as #8224e3 for a coupon with ID 4545.','wt-smart-coupons-for-woocommerce-pro'); ?> </p>
                            </li>
                        </ul>

                        <p><i><?php _e('Note: The styling will be overridden only for arguments explicitly mentioned within the shortcode, others will follow default settings.','wt-smart-coupons-for-woocommerce-pro'); ?></i></p>
                        <div id="wt_short_code_arguments" class="wt_popup">
                            <div class="wt-popup-content">
                                <div class="wt_popup_head">
                                    <?php _e('Arguments','wt-smart-coupons-for-woocommerce-pro') ?>
                                    <div class="wt_popup_close"></div>
                                </div>
                                <div class="wt_popup_body">
                                    <table class="wp-list-table fixed striped wt-shortcode-args">
                                        <thead>
                                            <tr>
                                                <th><?php _e('Argument','wt-smart-coupons-for-woocommerce-pro'); ?></th>
                                                <th><?php _e('Default value','wt-smart-coupons-for-woocommerce-pro'); ?></th>
                                                <th><?php _e('Description','wt-smart-coupons-for-woocommerce-pro'); ?></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                                foreach( $coupon_short_code_arguments as $argument => $values ) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $argument ; ?></td>
                                                        <td><?php echo $values['default'] ; ?></td>
                                                        <td><?php echo $values['description'] ; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
           <?php
        }
    }

}