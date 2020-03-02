<?php
/**
 * Register all actions and filters for Coupon banner
 *
 * @link       http://www.webtoffee.com
 * @since      1.2.9
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/coupon-banner
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( ! class_exists ( 'Wt_Smart_Coupon_Banner_Hooks' ) ) {
    class Wt_Smart_Coupon_Banner_Hooks extends Wt_Smart_Coupon_Hooks {
        
        function load_hooks() {
            $coupon_banner = new Wt_Smart_Coupon_Banner( );
            $this->add_filter( 'wt_smart_coupon_settings_tabs', $coupon_banner,'add_coupon_banner_settings', 11, 1 );
            $this->add_filter('wt_smart_coupon_default_options',$coupon_banner,'coupon_banner_option',15,1);
            $this->add_action( 'wt_smart_coupon_after_genral_settings_tab_content', $coupon_banner,'banner_coupon_settings_content', 10, 0 );
            $this->add_action('wt_smart_coupon_after_banner_settings_content',$coupon_banner,'inject_required_script',10,1);
            $this->add_action('wt_smart_coupon_banner_section_show-us',$coupon_banner,'show_us_tab_content' );
            $this->add_action('wt_smart_coupon_banner_section_banner_title',$coupon_banner,'title_tab_content' );
            $this->add_action('wt_smart_coupon_banner_section_banner_description',$coupon_banner,'short_description_content' );
            $this->add_action('wt_smart_coupon_banner_section_coupon_section',$coupon_banner,'coupon_content' );
            $this->add_action('wt_smart_coupon_banner_section_coupon_timer',$coupon_banner,'coupon_timer_content' );
            $this->add_action('wt_before_general_settings_coupon_tabs',$coupon_banner,'save_coupon_banner_settings');
            $this->add_action('wt_smart_coupon_banner_section_inject_coupon',$coupon_banner,'inject_coupon_content' );

            $this->add_action( 'admin_enqueue_scripts', $coupon_banner, 'enqueue_styles',12,0 );
			$this->add_action( 'admin_enqueue_scripts', $coupon_banner, 'enqueue_scripts',12,0 );

            $coupon_banner_short_code = new Wt_Smart_Coupon_Banner_Shortcode();
            $this->add_action( 'wp_enqueue_scripts', $coupon_banner_short_code, 'enqueue_styles' );
            $this->add_action( 'wp_enqueue_scripts', $coupon_banner_short_code, 'enqueue_scripts' );
            $this->add_action('wp_footer',$coupon_banner_short_code,'inject_banner');
            $this->add_action('wt_smart_coupon_after_banner_preview',$coupon_banner_short_code,'display_short_code_guidelines',10);

            
        }
    }
    $combo_coupon = new  Wt_Smart_Coupon_Banner_Hooks();
    $combo_coupon->run();

  
}