<?php
$product_name = 'wtsmartcoupon'; // name should match with 'Software Title' configured in server, and it should not contains white space
$product_version = '1.3.1';
$product_slug = WT_SMARTCOUPON_BASE_NAME; //product base_path/file_name
$serve_url = 'https://www.webtoffee.com/';
$plugin_settings_url = admin_url('?page=wt-smart-coupon&tab=genral_settings');
//include api manager
include_once ( 'wf_api_manager.php' );
new WF_API_Manager($product_name, $product_version, $product_slug, $serve_url, $plugin_settings_url);
?>