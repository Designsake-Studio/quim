<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * The admin-Settings functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      1.1.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin
 * 
 */

if( ! class_exists ( 'WT_smart_Coupon_Settings' ) ) {

    
	class WT_smart_Coupon_Settings {

        public static $option_prefix;

        public function __construct( ) {
            add_action('wt_smart_coupon_tab_content_settings',array($this,'general_settings_tab_content'),10);
            add_action('wt_before_general_settings_coupon_tabs',array($this,'save_gernal_settings'),10);


        }

        /**
         * General settings tab content
         * @since 1.2.1
         */

         function general_settings_tab_content(  ) {
             $this->display_general_settings_form();
         }

        /**
         * Sub section for settings
         * @since 1.1.0
         */
        function get_sections() {

            $sections = array(
                'styling' => __('Style', 'wt-smart-coupons-for-woocommerce-pro'),
                'general-settings' => __('General', 'wt-smart-coupons-for-woocommerce-pro')
            );

            return apply_filters( 'wt_smart_coupon_settings_tabs', $sections );
        }


        /**
         * Save Genral settings
         * @since 1.2.9 
         * Moved as function from display genral settings
         */

         function save_gernal_settings() {
            $updated = false;

            if( isset( $_POST[ 'update_wt_smart_coupon_settings'] )) {
                set_transient('wt_active_tab_genral_settings','styling',30);
                check_admin_referer('wt_smart_coupons_settings');

                
                $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();

                $coupon_types = $this->get_coupon_style_types();

                foreach( $coupon_types as $coupon_type => $coupon_type_name  ) {
                    if( isset( $_POST['wt_'.$coupon_type.'_style'] ) && '' != $_POST['wt_'.$coupon_type.'_style']  ) {
                        $smart_coupon_style['style'] = sanitize_text_field( $_POST['wt_'.$coupon_type.'_style'] );
                    }
                    if( isset( $_POST['wt_'.$coupon_type.'_color_0'] ) && '' != $_POST['wt_'.$coupon_type.'_color_0']  ) {
                        $smart_coupon_style['color'][] = sanitize_text_field( $_POST['wt_'.$coupon_type.'_color_0'] );
                    }
                    if( isset( $_POST['wt_'.$coupon_type.'_color_1'] ) && '' != $_POST['wt_'.$coupon_type.'_color_1']  ) {
                        $smart_coupon_style['color'][] = sanitize_text_field( $_POST['wt_'.$coupon_type.'_color_1'] );
                    }
                    if( isset( $_POST['wt_'.$coupon_type.'_color_2'] ) && '' != $_POST['wt_'.$coupon_type.'_color_2']  ) {
                        $smart_coupon_style['color'][] = sanitize_text_field( $_POST['wt_'.$coupon_type.'_color_2'] );
                    }

                    $smart_coupon_options['wt_coupon_styles'][$coupon_type] = $smart_coupon_style;
                    unset( $smart_coupon_style );

                }
                update_option("wt_smart_coupon_options", $smart_coupon_options);
                $updated = true;
                do_action('wt_smart_coupon_style_settings_updated');

            }

            if( isset( $_POST['update_wt_smart_coupon_general_settings'] ) ) {
                check_admin_referer('wt_smart_coupons_general_settings');
                set_transient('wt_active_tab_genral_settings','general-settings',30);
                $smart_coupon_options = Wt_Smart_Coupon_Admin::get_options();

                if( isset( $_POST['wt_display_used_coupons_in_my_account'] ) && 'on' == $_POST['wt_display_used_coupons_in_my_account'] ) {
                    $wt_copon_general_settings['display_used_coupons_my_acount'] = true ;
                } else {
                    $wt_copon_general_settings['display_used_coupons_my_acount'] = false ;
                }

                if( isset( $_POST['wt_display_expired_coupons_in_my_account'] ) && 'on' == $_POST['wt_display_expired_coupons_in_my_account'] ) {
                    $wt_copon_general_settings['display_expired_coupons_my_acount'] = true ;
                } else {
                    $wt_copon_general_settings['display_expired_coupons_my_acount'] = false ;
                }


                if( isset( $_POST['_wt_coupon_prefix'] ) && '' != $_POST['_wt_coupon_prefix'] ) {
                    $wt_copon_general_settings['wt_coupon_prefix'] = $_POST['_wt_coupon_prefix'] ;
                } else {
                    $wt_copon_general_settings['wt_coupon_prefix'] = '';
                }

                if( isset( $_POST['_wt_coupon_suffix'] ) && '' != $_POST['_wt_coupon_suffix'] ) {
                    $wt_copon_general_settings['wt_coupon_suffix'] = $_POST['_wt_coupon_suffix'] ;
                } else {
                    $wt_copon_general_settings['wt_coupon_suffix'] = '';
                }

                if( isset( $_POST['_wt_coupon_length'] ) && '' != $_POST['_wt_coupon_length'] ) {
                    $wt_copon_general_settings['wt_coupon_length'] = $_POST['_wt_coupon_length'] ;
                } else {
                    $wt_copon_general_settings['wt_coupon_length'] = 12;
                }

                $smart_coupon_options['wt_copon_general_settings'] = $wt_copon_general_settings;



                update_option("wt_smart_coupon_options", $smart_coupon_options);
                $updated = true;
                do_action('wt_smart_coupon_general_settings_updated');

            }
        ?>
            <div id="message-settings">
            <?php  if( $updated) { ?>
                
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( 'Done! Updated Smart Coupon settings.', 'wt-smart-coupons-for-woocommerce-pro' ); ?></p>
                </div>
            <?php } ?>
            </div>
        <?php

         }

        /**
         * Display general settings
         * @since 1.1.0
         */
        function display_general_settings_form() { ?>

            
                <div id="normal-sortables-2" class="meta-box-sortables ui-sortable">
                    <div class="wt_coupon_settings_form_content">
                        <ul class="wt_sub_tab">
                            
                            <?php
                                do_action('wt_before_general_settings_coupon_tabs');
                                $sections = $this->get_sections();
                                $active_tab = get_transient( 'wt_active_tab_genral_settings' );
                                $i = 0;
                                foreach( $sections as $id => $section ) {
                                    $class = '';
                                    if( isset( $active_tab ) && $active_tab ) {
                                        if( $active_tab == $id ) {
                                            $class="active";
                                        }
                                    } elseif( $i++ == 0 ) {
                                        $class="active";
                                    }
                                    ?>
                                    <li class="<?php echo $class; ?>">
                                        <a href = "#<?php echo $id; ?>" >
                                        <?php  echo $section ; ?>
                                        
                                        </a>
                                    </li>

                                    <?php
                                }
                            
                            ?>
                            
                        </ul>

                        <div id="wt_smart_coupon_settings" >
                            <div class="wt_smart_coupon_settings_content">

                                <div class="wt_sub_tab_container">
                                    <?php
                                        $active_tab = get_transient( 'wt_active_tab_genral_settings' );
                                        $class = ' ';
                                        if( isset( $active_tab ) &&  $active_tab == 'general-settings' ) {
                                            $class = 'active';
                                        }
                                    ?>
                                    <div class="wt_sub_tab_content <?php echo  $class; ?>" id="general-settings"  >
                                        <?php $this->general_settings_form(  ); ?>
                                    </div>
                                    <?php
                                       $class = ' ';
                                       if( ! isset( $active_tab ) || '' == $active_tab || $active_tab == 'styling' ) {
                                           $class = 'active';
                                       }
                                    ?>
                                    <div  class="wt_sub_tab_content <?php echo $class; ?>" id="styling" >
                                        <?php $this->general_settings_styling_form( ); ?>
                                    </div>

                                    <?php do_action('wt_smart_coupon_after_genral_settings_tab_content'); ?>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        /**
         * Style Coupon settings
         * @since 1.1.0
         */
        function general_settings_styling_form(  ) {
            $admin_options = Wt_Smart_Coupon_Admin::get_option('wt_coupon_styles');
            ?>
            <form name="wt_smart_coupon_settings" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" name="wt_smart_coupon_settings">
                            <?php wp_nonce_field('wt_smart_coupons_settings'); ?>
                            <div class="wt_settings_section">
                                <h2><?php _e('Coupon Styles','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                                <p><?php _e('Choose a style for the coupon types(status based: Available, Expired, Used), from the available templates.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                            </div>
                            

                            <div class="wt-form-item ">
                                <label> <?php _e("Coupon Type",'wt-smart-coupons-for-woocommerce-pro');  ?></label>
                                <div class="wt-form-field">
                                    <select name="wt_coupon_style_type" id="wt_coupon_style_type" >
                                            <?php 
                                                    $get_coupon_style_types = $this->get_coupon_style_types();
                                                    foreach( $get_coupon_style_types as $style_type => $style ) {
                                                       
                                                        echo ' <option value="'.$style_type.'"  >'.$style .'</option>';
                                                    }
                                                ?>

                                    </select>
                                </div>

                            </div>


                            <?php
                            
                            foreach( $get_coupon_style_types as $style_type => $style_type_name ) {  ?>

                                <div id="<?php echo $style_type; ?>_item" class="wt_coupon_style_type wt-form-item ">
                                    <label></label>
                                    <div class="wt-form-field">
                                        <div class="coupon_styling_settings <?php echo $style_type; ?>">
                                            <div class="section-sub-title">
                                                <h4><?php _e( $style_type_name ,'wt-smart-coupons-for-woocommerce-pro') ?></h4>
                                            </div>

                                            <div class="form-item-wrapper">


                                                <div class="wt-form-item ">
                                                
                                                    <div class="wt-form-field">
                                                        <div class="coupon-themes">
                                                            <div class="wt-coupon-button-wrapper">
                                                                <button type="button" name="" class="button-primary wt_modal_btn <?php echo $style_type; ?>_modal_btn" target='#wt_<?php echo $style_type; ?>_modal' >
                                                                    <span class="dashicons dashicons-admin-appearance" style="line-height: 28px;"></span>
                                                                    <?php _e('Select style','wt-smart-coupons-for-woocommerce-pro'); ?>
                                                                </button>
                                                            </div>
                                                            <div class="coupon_preview <?php echo $style_type; ?>_preview"></div>
                                                                <div class="wt_coupon_elements">
                                                                    
                                                                    <div class="wt_coupon_colors">
                                                                        <?php
                                                                            $colors = $admin_options[$style_type]['color'];
                                                                            $i = 0;
                                                                            if( $colors ) {
                                                                                 foreach( $colors as $color ) {
                                                                                    ?>
                                                                                    <div class="form-element">
                                                                                        <input name="wt_<?php echo $style_type; ?>_color_<?php echo $i; ?>" id="wt_<?php echo $style_type; ?>_color_<?php echo $i++; ?>" type="text" value="<?php echo $color; ?>" class="wt_colorpick wt_<?php echo $style_type; ?>_color" />
                                                                                    </div>
                                                                                <?php
                                                                                }
                                                                            }
                                                                           
                                                                            ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                    $coupon_styles = Wt_Smart_Coupon_Admin::coupon_styles();
                                                                    $selected_style = $admin_options[$style_type]['style'];

                                                                ?>
                                                        
                                                                <div id="wt_<?php echo $style_type; ?>_modal" class="wt_modal wt_<?php echo $style_type; ?>_modal_item" style="display:none" current-style="<?php echo $selected_style; ?>" coupon_type="<?php echo $style_type; ?>">
                                                                    <div class="wt-modal-header">
                                                                            <div class="wt-modal-title">
                                                                                <?php _e($style_type_name.' styles','wt-smart-coupons-for-woocommerce-pro'); ?>
                                                                            </div>
                                                                            <div class="wt-modal-close" >
                                                                                <span class="dashicons dashicons-no-alt" style="line-height:20px;"></span>
                                                                            </div>
                                                                    </div>
                                                                    <div class="wt_modal_content">
                                                                        <?php
                                                                            foreach( $coupon_styles as $style_name => $style ) {
                                                                                
                                                                                if( isset( $selected_style ) && $style_name == $selected_style ) {
                                                                                    $selected = 'checked';
                                                                                } else {
                                                                                    $selected = '';
                                                                                }

                                                                                echo '<div class="wt_coupon_style_item"> <label>';
                                                                                
                                                                                echo '<input type="radio" name="wt_'.$style_type.'_style" style="'.$style['name'].'" class="wt_hidden_radio" value="'.$style_name.'" '.$selected.' >';

                                                                                
                                                                                echo $this->get_coupn_html( $style_name,$style['name'] );

                                                                                echo '</label></div>';
                                                                            }

                                                                        ?>
                                                                    </div>
                                                                    <div class="wt_modal_footer">
                                                                        <div class="wt_choose_style button button-primary" coupon_type="<?php echo $style_type; ?>"> <?php _e('Choose style','wt-smart-coupons-for-woocommerce-pro'); ?> </div>
                                                                    </div>
                                                                </div>
                        
                                                            </div>
                                                        </div>
                    
                                                    </div> 
                                                </div>

                                        </div>


                                    </div> <!-- Coupons section  -->


                                    
                                </div>



                            <?php  } ?>
                            <!-- General settings -->


                            
                            
                            <?php do_action('wt_smart_coupon_after_coupon_style_settings_form'); ?>

                            <div class="wt_form_submit">
                                <div class="form-submit">
                                    <button id="update_wt_smart_coupon_settings" name="update_wt_smart_coupon_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                                </div>
                            </div>
                        </form>

            <?php
        }

        /**
         * General settings form
         * @since 1.1.0
         */
        function general_settings_form(  ) {
            $general_settings_options = Wt_Smart_Coupon_Admin::get_option('wt_copon_general_settings');
            ?>
            <form name="wt_coupon_general_Settings" method="post" action="<?php echo esc_attr($_SERVER["REQUEST_URI"]); ?>" >
                <?php wp_nonce_field('wt_smart_coupons_general_settings'); ?>
                <div class="wt_settings_section">
                    <h2><?php _e('General Settings','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                    <p><?php _e('Configure options from the settings below to suit your business needs.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                </div>

                <div class="wt_section_title">
                    <h2>
                        <?php _e('Coupon visibility','wt-smart-coupons-for-woocommerce-pro') ?>
                        <?php echo wc_help_tip( __('Control the pages in which the chosen coupon type should be displayed for view','wt-smart-coupons-for-woocommerce-pro') ); ?>
                    </h2>
                </div>

                <table class="form-table">
                    <tbody>
                        <tr  valign="top">
                            <?php 
                                $wt_display_used_coupons_in_my_account =  ( isset( $general_settings_options['display_used_coupons_my_acount'] ) )? $general_settings_options['display_used_coupons_my_acount'] : false; 
                                $checked = '';
                                if( $wt_display_used_coupons_in_my_account ) {
                                    $checked = 'checked = checked';
                                }
                            ?>
                            <td scope="row" class="titledesc"> 
                                <?php _e('Show Used Coupons under My Account','wt-smart-coupons-for-woocommerce-pro'); ?>
                            </td>
                            <td class="forminp forminp-checkbox">
                                <input type="checkbox" id="wt_display_used_coupons_in_my_account" name="wt_display_used_coupons_in_my_account" <?php echo $checked; ?>>

                            </td>
                        </tr>

                        <tr  valign="top">
                            <?php 
                                $wt_display_expired_coupons_in_my_account =  ( isset( $general_settings_options['display_expired_coupons_my_acount'] ) )? $general_settings_options['display_expired_coupons_my_acount'] : false; 
                                $checked = '';
                                if( $wt_display_expired_coupons_in_my_account ) {
                                    $checked = 'checked = checked';
                                }
                            ?>
                            <td scope="row" class="titledesc"> 
                            <?php _e('Show Expired Coupons under My Account','wt-smart-coupons-for-woocommerce-pro'); ?>
                            </td>
                            <td class="forminp forminp-checkbox">
                            <input type="checkbox" id="wt_display_expired_coupons_in_my_account" name="wt_display_expired_coupons_in_my_account" <?php echo $checked; ?>>

                            </td>
                        </tr>
                    </tbody>
                </table>


                <?php
                /**
                 * Default coupon format
                 * @since 1.2.6
                 */
                ?>
                
                <div class="wt_section_title">
                    <h2><?php _e('Coupon format','wt-smart-coupons-for-woocommerce-pro') ?>
                        <a class="thickbox" href="<?php echo esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ).'/assets/images/coupon-format-small.jpg' )  ); ?>?TB_iframe=true&width=100&height=100"><small>[<?php _e('Preview','wt-smart-coupons-for-woocommerce-pro'); ?>]</small></a>
                        <?php echo wc_help_tip( __('This coupon format will be used only for dynamic coupons(that are generated automatically) except bulk coupons and store credit(configured under their respective settings).','wt-smart-coupons-for-woocommerce-pro') ); ?>
                    
                    </h2>
                </div>

                <table class="form-table">
                    <tbody>
                        <tr  valign="top">
                            <?php  $coupon_prefix = isset( $general_settings_options['wt_coupon_prefix'] )? $general_settings_options['wt_coupon_prefix']: ''; ?>
                            <td scope="row" class="titledesc">
                                <?php _e('Prefix','wt-smart-coupons-for-woocommerce-pro'); ?>
                                        
                            </td>
                            <td class="forminp forminp-text">
                                <input type="text" name="_wt_coupon_prefix"  value="<?php echo $coupon_prefix ; ?>" />

                            </td>
                        </tr>

                        <tr  valign="top">
                            <?php  $coupon_suffix = isset( $general_settings_options['wt_coupon_suffix'] )? $general_settings_options['wt_coupon_suffix']: ''; ?>
                            <td scope="row" class="titledesc">
                                <?php _e('Suffix','wt-smart-coupons-for-woocommerce-pro'); ?> 
                            </td>
                            <td class="forminp forminp-text">
                                <input type="text" name="_wt_coupon_suffix"  value="<?php echo $coupon_suffix ; ?>" />
                            </td>

                        </tr>

                        <tr  valign="top">
                            <?php  $coupon_length = isset( $general_settings_options['wt_coupon_length'] )? $general_settings_options['wt_coupon_length']: 12; ?>
                                        
                            <td scope="row" class="titledesc">
                                <?php  _e('Length of the coupon code','wt-smart-coupons-for-woocommerce-pro'); ?> 
                            </td>
                            <td  class="forminp forminp-text">   
                                <input type="number" name="_wt_coupon_length" min="7"   value="<?php echo $coupon_length ; ?>" />
                            </td>
                        </tr>

                    </tbody>
                </table>

                <?php  // end default coupon format. ?>

                <?php do_action('wt_smart_coupon_general_settings') ?>

                <div class="wt_form_submit">
                    <div class="form-submit">
                        <button id="update_wt_smart_coupon_general_settings" name="update_wt_smart_coupon_general_settings" type="submit" class="button button-primary button-large"><?php _e( 'Save','wt-smart-coupons-for-woocommerce-pro'); ?></button>
                    </div>
                </div>

                <?php do_action('wt_smart_coupon_after_general_settings_form'); ?>


            </form>
            <?php
        }
        /**
         * Get Coupon Stles
         * @since 1.1.0
         */
        public static function get_coupon_style_types() {
            $coupon_style_types = array(
                'available_coupon'  =>  'Available Coupon',
                'used_coupon'       =>  'Used Coupon',
                'expired_coupon'    =>  'Expired Coupon'
            );
    
            return apply_filters( 'wt_coupon_style_types', $coupon_style_types );
        }

        /**
         * Get Coupon HTML for admin
         * @since 1.1.0
         */
        function get_coupn_html( $style_name,$style  ) {
            switch( $style_name ) {
    
                case 'stitched_padding' :
    
                        $coupon_style =  '
                        <div class="wt-single-coupon '. $style_name .'"   >
                            <div class="wt-coupon-content">
                                <div class="wt-coupon-amount">
                                    <span class="amount">$10</span><span> CART DISCOUNT</span>
                                </div>
                                <div class="wt-coupon-code"> 
                                    <code>' .$style.'</code>
                                </div>
                            </div>
                        </div>';
                        break;
                case 'stitched_edge' :
                    $coupon_style = '<div class="wt-single-coupon '.$style_name.'" >
                        <div class="wt-coupon-content">
                            <div class="wt-coupon-amount">
                                <span class="amount">10$</span><span> CART DISCOUNT</span>
                            </div>
                            <div class="wt-coupon-code"> 
                                <code>'.$style.'</code>
                            </div>
                        </div>
                    </div>';
                    break;
                case 'ticket_style' : 
                   
                    $coupon_style ='<div class="wt-single-coupon '.$style_name.'" >
                                <div class="wt-coupon-content">
                                    <div class="wt-coupon-amount"  >
                                        <span class="amount">10$</span>
                                    </div>
                                    <div class="wt-coupon-code"> 
                                        <span class="discount_type">CART DISCOUNT</span>
                                        <code>' .$style. '</code>
                                    </div>
                                </div>
                            </div>';
                        break;
    
                case 'plane_coupon' :
                   
                    $coupon_style = '<div class="wt-single-coupon active-coupon '.$style_name.'" >
                                    <div class="wt-coupon-content">
                                        <div class="wt-coupon-amount">
                                            <span class="amount">10$</span><span> CART DISCOUNT</span>
                                        </div>
                                        <div class="wt-coupon-code"> 
                                            <code>' .$style. '</code>
                                        </div>
                                    </div>
                                </div>';
                    break;
                default : 
                    $coupon_style = `
                        <div class="wt-single-coupon active-coupon '.$style_name.'" >
                            <div class="wt-coupon-content">
                                <div class="wt-coupon-amount">
                                    <span class="amount">$10</span><span> CART DISCOUNT</span>
                                </div>
                                <div class="wt-coupon-code"> 
                                    <code>'.$style.'</code>
                                </div>
                            </div>
                        </div>`;
                        break;
                
    
            }
            return $coupon_style;
        } 

    } // End Class.

    $settings = new WT_smart_Coupon_Settings();

}