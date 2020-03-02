<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if( ! class_exists ( 'Wt_Smart_Coupon_import' ) ) {


    class Wt_Smart_Coupon_import {
        
        protected $id,$coupon_post_fields = array();
        protected $coupon_posts_headers = array();
        protected $email_coupon_on_import;
        protected $header,$map_head,$row_parsed;

        public function __construct( ) {
            $this->coupon_post_fields = array(
                'post_title','post_excerpt','post_status','post_parent','menu_order','post_date'
            );

            $this->coupon_posts_headers = array ( 
                'post_title','post_excerpt','post_status','post_parent','menu_order','post_date'
            );
           
            $email_coupon_on_import = get_transient('email_coupon_on_import');
            if( $email_coupon_on_import ) {
                $this->email_coupon_on_import = 1;
                set_transient( 'email_coupon_on_import',$this->email_coupon_on_import,12 * HOUR_IN_SECONDS );
            } else {
                $this->email_coupon_on_import = 0;
                set_transient( 'email_coupon_on_import',$this->email_coupon_on_import,12 * HOUR_IN_SECONDS );
            }

            
            $row_parsed = get_transient('wt_row_parsed');
            if( ! $row_parsed   ) {
                $this->row_parsed = 0;
                set_transient( 'wt_row_parsed',0, 12 * HOUR_IN_SECONDS );

            } else {
                $this->row_parsed = $row_parsed+1;

            }
        }

        /**
         * Add Import tab into Smart Coupon settings.
         * @since 1.2.1
         */
        function add_admin_tab( $admin_tabs ) {
            $admin_tabs['import_coupon'] = __('Import Coupon','wt-smart-coupons-for-woocommerce-pro');
            return $admin_tabs;
        }

        

        /**
         * Import Coupon tab content
         * @since 1.2.1
         */

        function import_coupon_content() {
            $this->import_coupon_form();
        }


        /**
         * Display imprt Coupon form on import page
         * @since 1.0.0
         */
        public function import_coupon_form() {

            $step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

            switch( $step ) {
                case 0: 
                    $this->display_import_form();
                    break;
                case 1: 
                    check_admin_referer( 'wt_import_smart_coupon' );
                    if( $this->handle_import_coupon() ) {
                        $this->render_csv_row();
                    }
                    break;
                
                case 2:
                    check_admin_referer( 'wt_import_smart_coupon_step_2' );
                    $this->import_coupon_from_csv();

            }

        }

        /**
         * Display the import form
         * @since 1.0.0
         */
        

        /**
         * Display the import form
         * @since 1.0.0
         */
        public function display_import_form() {

            $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
            $sixe_in_mb = $bytes = number_format($bytes / 1048576, 2) . ' MB';

            ?>
            <div id="message"><p></p></div>
            <div id="normal-sortables-1" class="meta-box-sortables ui-sortable">
                <div id="wt_import_coupon_top" class="postbox ">
                    <div class="wt_import_coupon_content">
                        <div class="wt_settings_section wt_section_title">
                            <h2><?php _e('Import coupons','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                            <p><?php _e('This section allows you to import coupons from a CSV(UTF-8 format) file into your store.','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                        </div>
                        <p class="import-instructions"> 
                            <?php 
                            echo esc_html__('For a clean import the CSV must include the header and adhere to the format as indicated in our ','wt-smart-coupons-for-woocommerce-pro');
                            echo  '<a href=" '.esc_url( plugins_url( dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/import/sample-file/wt_smart_coupon1.csv' )  ).'">'.esc_html__('sample file.','wt-smart-coupons-for-woocommerce-pro');
                            echo '</a>';
                            _e(' Columns <i>post_title</i> and <i>discount_type</i> are mandatory for the import. Duplicate coupons will be skipped during import. ','wt-smart-coupons-for-woocommerce-pro'); ?>
                        </p>
                        <form enctype="multipart/form-data" id="import-coupon" class="form import-coupon" action="<?php echo get_admin_url(); ?>admin.php?page=wt-smart-coupon&tab=import_coupon&step=1" method="POST">
                                <?php wp_nonce_field(  'wt_import_smart_coupon' ); ?>
                            
                                <div class="wt-import-input-file-container">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <td scope="row" class="titledesc"><?php _e('Upload the CSV file','wt-smart-coupons-for-woocommerce-pro') ?></td>
                                                <td >
                                                    <label for="upload" class="button button-hero sc-file-container">
                                                        <span class="wt-file-container-label"><?php _e('Upload','wt-smart-coupons-for-woocommerce-pro'); ?>  <span class="dashicons dashicons-upload"></span></span>
                                                        <input type="file" id="wt_smart_coupon_upload" name="import" accept=".csv" size="25" required="">
                                                        
                                                    </label>
                                                    <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>">
                                                    <p><small><?php _e('Maximum file size:','wt-smart-coupons-for-woocommerce-pro');  ?> <?php  echo $sixe_in_mb; ?></small></p>
                                                </td>
                                            </tr>

                                            <tr  class="email-coupon-on-import-1">
                                                <td scope="row" class="titledesc"><?php _e('Email coupon to users upon Import','wt-smart-coupons-for-woocommerce-pro'); ?></td>
                                                <td><input type="checkbox" name="email_coupon_on_import" id="email_coupon_on_import" /></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                    
                                    <input type="submit" class="button button-primary button-hero" value="Map columns for import">
                                    
                                </div>

                                


                        </form>
                    </div>
                </div>
            </div>

        <?php
        }

        /**
         * Handle CSV upload
         * @since 1.0.0
         */
        public function handle_import_coupon() {

            if ( ! empty( $_FILES['import'] ) ) {
                $file = wp_import_handle_upload();

                if ( isset( $file['error'] ) ) {
                    echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wt-smart-coupons-for-woocommerce-pro' ) . '</strong><br />';
                    echo esc_html( $file['error'] ) . '</p>';
                    return false;
                }
                $this->id = (int) $file['id'];

            }
            if(! $this->id ) {
                echo '<p><strong>' . __( 'Sorry, Something went wrong!, Please try again.', 'wt-smart-coupons-for-woocommerce-pro') . '</strong><br />';

                return false;

            } 
            return true;
        }


        /**
         * Render the Given CSV
         * @since 1.0.0
         */
        public function render_csv_row() {
            $j = 0;

            $coupon_posts_headers = $this->coupon_posts_headers;
            if( isset( $_POST['email_coupon_on_import'] ) && $_POST['email_coupon_on_import'] == 'on'  ) {
                $this->email_coupon_on_import = 1;    
                set_transient( 'email_coupon_on_import',true,12 * HOUR_IN_SECONDS ); 
            }


            $default_coupon_meta_fields = apply_filters( 'wt_smart_coupon_default_meta_fields', array(
                '_wt_sc_shipping_methods',
                '_wt_sc_payment_methods',
                '_wt_sc_user_roles',
                '_wt_category_condition',
                '_wt_product_condition',
                '_wt_free_product_ids',
                '_wt_min_matching_product_qty',
                '_wt_max_matching_product_qty',
                '_wt_min_matching_product_subtotal',
                '_wt_max_matching_product_subtotal',
                'discount_type',
                'coupon_amount',
                'individual_use',
                'product_ids',
                'exclude_product_ids',
                '_wt_valid_for_number',
                'minimum_amount',
                'maximum_amount',
                'customer_email',
                'usage_limit',
                'limit_usage_to_x_items',
                'usage_limit_per_user',
                'expiry_date',
                '_wt_need_check_location_in',
                '_wt_coupon_available_location',
                '_wt_coupon_start_date',
                'wt_apply_discount_before_tax_calculation',

            ) );
            $coupon_meta_headers = $this->get_possible_meta_for_coupon();
            $coupon_meta_headers = array_unique( array_merge($default_coupon_meta_fields,$coupon_meta_headers ));


            $coupon_heades = array_merge($coupon_posts_headers,$coupon_meta_headers);
            
            $file = get_attached_file( $this->id );
            
            // Set locale
            $enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
            if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
            @ini_set( 'auto_detect_line_endings', true );

            if ( ( $handle = @fopen( $file, "r" ) ) !== FALSE ) {

                $row = $raw_headers = array();

                $header = fgetcsv( $handle, 0 ); //gets header of the file
                $this->header =  $header;

                while ( ( $postmeta = fgetcsv( $handle, 0 ) ) !== FALSE ) {
                    foreach ( $header as $key => $heading ) {
                        if ( ! $heading ) continue;
                        if( !isset( $coupon_heades[$j] ) ) break;

                        $s_heading = strtolower( $heading );
                        $row[ $coupon_heades[$j] ] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';
                        $raw_headers[ $coupon_heades[$j] ] = $heading;
                        $j++;
                    }
                    break;
                }

                fclose( $handle );
            }


            ?>

            <div id="wt_import_coupon_top-step-2" class="meta-box-sortables ui-sortable">
                <div id="wt_import_coupon_top" class="postbox ">
                    <div class="wt_import_coupon_content">
                        <div class="wt_settings_section wt_section_title">
                            <h2><?php _e('Map coupon columns for import','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                            <p><?php _e('Map the basic columns against your CSV column names respectively. ','wt-smart-coupons-for-woocommerce-pro'); ?></p>
                        </div>
                        <form enctype="multipart/form-data" id="import-coupon" class="form import-coupon" action="<?php echo get_admin_url(); ?>admin.php?page=wt-smart-coupon&tab=import_coupon&step=2" method="POST">
                            <?php wp_nonce_field( 'wt_import_smart_coupon_step_2' ); ?>
                            <input name="wt_import_id" type="hidden" value="<?php echo $this->id; ?>" />
                            <table class="widefat wt_smart_coupon_import_table">   
                                <thead>
                                    <tr>
                                        <th><?php _e( 'Coupon field', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                                        <th><?php _e( 'Map Column to', 'wt-smart-coupons-for-woocommerce-pro'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach( $row as $key=> $value ) { ?>
                                        <tr>
                                        
                                            <td><code> <?php echo $key; ?></code> </td>
                                            <td>
                                                <select name="mapto[<?php echo $key;  ?>]">
                                                    <option value="" > <?php echo  __('select mapping coloumn','wt-smart-coupons-for-woocommerce-pro') ?></option>';

                                                    <?php 
                                                    foreach( $raw_headers as $raw_header ) {
                                                        echo '<option '.selected( $raw_header, $key ).'>'.$raw_header .'</option>';
                                                    }
                                                    ?>
                                                </select>

                                            </td>
                                            <!-- <td> <code><?php //echo ( $value )?$value:'-' ; ?></code> </td> -->
                                        </tr>
                                    <?php }  ?>
                                    
                                </tbody>
                                
                            </table>

                            <input id="wt_import_coupon_btn"  name="wt_import_coupon_btn" type="submit" class="button button-primary button-hero" value="<?php _e('Import coupons','wt-smart-coupons-for-woocommerce-pro'); ?> ">
                            
                        </form>
                    </div>
                </div>
            </div>


            <?php

        }


        /**
         * Format the CSV data
         * @since 1.0.0
         */
        public function format_data_from_csv( $data, $enc ) {
            return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
        }


        /**
         * Import coupons from CSV
         * @since 1.0.0
         */
        public function import_coupon_from_csv( ) {
            global $wpdb;
            $this->id  = (isset( $_POST['wt_import_id']) )?  $_POST['wt_import_id'] : '';
            if( ! $this->id ) {
                    echo '<p><strong>' . __( 'The file does not exist, please try again.', 'wt-smart-coupons-for-woocommerce-pro') . '</strong><br />';
                    die();
                    return;
            }

            ?>

            <div class="meta-box-sortables ui-sortable">
                <div id="wt_import_coupon_top" class="postbox ">
                    <div class="wt_import_coupon_content">
                        <div class="wt_settings_section wt_section_title">
                            <h2><?php _e('Import Complete:','wt-smart-coupons-for-woocommerce-pro'); ?></h2>
                            <ul class="import-result">
                            </ul>
                        </div>


                        <table id="wt_smar_coupon_import_progress" class="widefat_importer widefat">
                            <thead>
                                <tr>
                                    <th class="status">&nbsp;</th>
                                    <th class="row"><?php _e( 'Row', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                                    <th><?php _e( 'Coupon Id', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                                    <th><?php _e( 'Coupon Code', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                                    <th class="reason"><?php _e( 'Status Msg', 'wt-smart-coupons-for-woocommerce-pro' ); ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="wt-importer-loading loading">
                                    <td colspan="5"></td>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
                        



            <?php

            $file = get_attached_file( $this->id );
            $enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
            if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
            @ini_set( 'auto_detect_line_endings', true );

            if ( ( $handle = @fopen( $file, "r" ) ) !== FALSE ) {

                

                $header = fgetcsv( $handle, 0 );

                if( isset( $_POST['mapto'] ) && !empty( $_POST['mapto'] )) {
                    $map_head =  ( $_POST['mapto'] );

                }

                $minimum_header = array( 'post_title','discount_type' );

                $coupon_data = array(
                    'post_type'=>'shop_coupon'
                );
                $coupon_meta_data = array();
                $coupon_post_fields = $this->coupon_post_fields;

                if( count(array_intersect($coupon_post_fields, $header)) != count($coupon_post_fields) ) {
                
                    // echo '<p><strong>' . __( 'headers doesnot match.', 'wt-smart-coupons-for-woocommerce-pro') . '</strong><br />';
                    // return false;

                    $params = array(
                        'show_import_message'       => true,
                        'headers_match'             => 'headers_doesnt_match'
                    );
                    $redirect_url = add_query_arg( $params, admin_url('?page=wt-smart-coupon&tab=import_coupon') );
                    // wp_safe_redirect( $redirect_url );
                    // exit;
                }

            

                $imported =0;
                $skipped =0;
                $duplicate =0;
                

                ?>

                <script type="text/javascript">

                    jQuery(document).ready(function($) {

                        var done_count  =   0;


                        function  wt_import_rows( start_pos, end_pos ) {
                            var data = {
                                action              :   'wt_import_csv_coupon_rows',
                                file                :   '<?php echo addslashes( $file ); ?>',
                                header              :   '<?php echo json_encode($header); ?>',
                                map_head            :   '<?php echo json_encode( $map_head ); ?>',
                                handle              :   '<?php echo $handle; ?>',
                                start_position      :   start_pos,
                                end_position        :   end_pos,
                                email_on_import     :   <?php  echo $this->email_coupon_on_import; ?>
                               
                            };
                            
                            return $.ajax({
                                url     :   '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                                data    :   data,
                                type    :   'POST',
                                success :   function( response ) {
                                    if ( response ) {
                                        var results = $.parseJSON( response );
                                        console.log(results);

                                        $.each(results,function( index, result ) {
                                            if( result.error == true ) {
                                                $('#wt_smar_coupon_import_progress tbody').append( '<tr id="row-' + result.row + '" ><td><mark class="result wt-fail " title="' + result.status + '"> <span class="dashicons dashicons-no-alt"></span></mark></td><td class="row">' + result.row + '</td><td> - </td><td>' + result.coupon_name + '</td><td class="reason">' + result.status + '</td></tr>' );

                                            } else {
                                                $('#wt_smar_coupon_import_progress tbody').append( '<tr id="row-' + result.row + '" ><td><mark class="result wt-success" title="' + result.status + '">  <span class="dashicons dashicons-yes"></span> </mark></td><td class="row">' + result.row + '</td><td>' + result.coupon_id+ '</td><td>' + result.coupon_name + '</td><td class="reason">' + result.status + '</td></tr>' );
                                            }
                                        });

										done_count++;

										$('body').trigger( 'wt_coupon_csv_import_request_complete' );
                                        

                                    }
                                }
                            });
                        }


                        var rows = [];
                        var parse_rows;
                        
                        <?php
                        $count = 1;
                        $position_array = array();
                        $position = 0;
                        $limit  = apply_filters('wt_smart_coupon_import_batch_size',10);
                        $import_count      =0;

                        $delimiter = apply_filters( 'wt_smart_coupon_import_delimiter',  "," );
                        
                            while (($data = fgetcsv($handle, 0, $delimiter, '"', '"')) !== FALSE) {

                                if ($count >= $limit) {
                                    $previous_position = $position;
                                    $position = ftell($handle);
                                    $count = 0;

                                    $import_count++;
                                    ?>
                                    rows.push( [ <?php echo $previous_position; ?>, <?php echo $position; ?> ] );
                                    <?php
                                }
                                $count++;
                            }
        
                            
                            if ($count > 0) {
                                ?>
                                rows.push( [ <?php echo $position; ?>, '' ] );
                                <?php
                                $import_count++;
                            }
                        ?>
                            
                            var parse_rows = rows.shift();
							wt_import_rows( parse_rows[0], parse_rows[1] );


                            $('body').on( 'wt_coupon_csv_import_request_complete', function() {
								if ( done_count == <?php echo $import_count; ?> ) {
									wt_import_done();

								} else {
									parse_rows = rows.shift();
									wt_import_rows( parse_rows[0], parse_rows[1] );
								}
							} );


                            function wt_import_done() {
                                <?php
                                    delete_transient('wt_row_parsed');
                                    delete_transient('email_coupon_on_import'); 
                                ?>
                                $('.wt-importer-loading').removeClass('loading');
                                $('.wt-importer-loading td').append('Completed!');
                            }

                    });
                </script>
                <?php
                    fclose($handle);

                } else {
                    echo '<p><strong>' . __( 'The file does not exist, please try again.', 'wt-smart-coupons-for-woocommerce-pro') . '</strong><br />';
                    die();
                    return;
                }
                


            }

            /**
             * Ajax Callback for importing coupon batch
             * @since 1.2,1
             */
            function import_start( ) {
                $file               = ( isset( $_POST['file'] ) )? $_POST['file'] : $this->id;
                $handle             = ( isset( $_POST['handle'] ) )? $_POST['handle'] : '';
                $header             = ( isset( $_POST['header'] ) )? json_decode(stripslashes( $_POST['header'] ))   :  array(); 
                // var_dump($header );
                // die();
                $map_head           = ( isset( $_POST['map_head'] ) )?  json_decode(stripslashes( $_POST['map_head'] )) :  array(); 
                $start_position     = ( isset( $_POST['start_position'] ) )? $_POST['start_position'] : 0;
                $end_position       = ( isset( $_POST['end_position'] ) )? $_POST['end_position'] : '';
                $flag               = ( isset( $_POST['flag'] ) )? $_POST['flag'] : false;
                $email_on_import    = ( isset( $_POST['email_on_import'] ) &&  $_POST['email_on_import'] )?  true : false;
    
                $map_head = (array) $map_head;

                $enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
                if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
                @ini_set( 'auto_detect_line_endings', true );
    
                if (  ( $handle = @fopen( $file, "r" ) ) !== FALSE ) {
    
                
                
                    $coupon_data = array(
                        'post_type'=>'shop_coupon'
                    );
                    $coupon_meta_data = array();
                    try {
                        fseek($handle,$start_position ) ;
                    }
                    catch(Exception $e) {
                        echo 'Message: ' .$e->getMessage();
                        die();
                    }
        
                    $error = false;
                    $responce = array();
                    $delimiter = apply_filters( 'wt_smart_coupon_import_delimiter',  "," );

                    while (($data = fgetcsv($handle, 0, $delimiter,'"','"' )) !== FALSE) {
                        if( empty($data) ) {
                            echo 'empty rows';
                            continue;
                        }

                        
        
                        if( $this->row_parsed == 0 ) { // Bypass the header
                            $this->row_parsed++;
                            continue;
                        }

                        $num = count($data);
                        $i = 0;
                        foreach( $data as $date_item ) {
                                if(! in_array( $header[ $i ] , $map_head ) ) {
                                    $i++;
                                    continue;
                                }
            
                            $matched_item =  array_search( $header[ $i++ ] , $map_head);
        
                            if( in_array( $matched_item ,$this->coupon_post_fields )) {
                                $coupon_data[ $matched_item ] =  $date_item  ;
                            } else {
                                $coupon_meta_data[ $matched_item ] = $date_item;
                            }
                        }
                        if( ! isset( $coupon_data['post_status'] ) || '' == $coupon_data['post_status']  ) {
                            $coupon_data['post_status'] = 'publish';
                        }
                        if( ! $error && ( !isset( $coupon_data['post_title']  )) || trim( $coupon_data['post_title'] ) =='' ) {
                            $responce[] = array(
                                'row'   => $this->row_parsed,
                                'error' => true,
                                'coupon_name' => $coupon_data['post_title'],
                                'status'    => __('Coupon title empty','wt-smart-coupons-for-woocommerce-pro')
                            );
                            $error = true;
                        }
                        if( ! $error && $this->is_coupon_exist( $coupon_data['post_title']  ) ) {
                            $error = true;
                            $responce[] = array(
                                'row'=> $this->row_parsed,
                                'error' => true,
                                'coupon_name' => $coupon_data['post_title'],
                                'status'    => __('Coupon already exists','wt-smart-coupons-for-woocommerce-pro')
                            );
                        }
                        
                        if( ! $error ) {
        
                            $coupon_id  = wp_insert_post( $coupon_data );
        
                            if( $coupon_id ) {

                                $staus =__('Import success','wt-smart-coupons-for-woocommerce-pro');
                                update_post_meta( $coupon_id, 'wt_bulk_genrated_coupon', true );
                                update_post_meta( $coupon_id, 'wt_bulk_genrated_coupon_csv_import', true );

                                $coupon_obj = new WC_Coupon( $coupon_id );

                                foreach( $coupon_meta_data as $meta_key => $meta_value ) {
                                    update_post_meta( $coupon_id, $meta_key, $meta_value );

                                    /** 
                                     * fix #6586
                                     * Meta key timestamp need to update */
                                    if( $meta_key == '_wt_coupon_start_date' && '' != $meta_value ) {
                                        update_post_meta( $coupon_id, '_wt_coupon_start_date_timestamp', strtotime( $meta_value ) );
                                    }

                                    if( $email_on_import && $meta_key == 'customer_email' && $meta_value !='' ) {
                                        WC()->mailer();
                                        $email_recipients = explode(',',$meta_value);
                                        if( is_array($email_recipients ) ) {
                                            foreach( $email_recipients as $email ) {
                                                do_action('wt_send_coupon_to_customer',$coupon_obj,$coupon_obj->get_code(),$email  );
                                                
                                            }
                                        }

                                            $staus =__('Import success & coupon emailed','wt-smart-coupons-for-woocommerce-pro');

                                        
                                    }
                                }
                                if( $coupon_obj->is_type('store_credit')){
                                    update_post_meta( $coupon_id, '_wt_smart_coupon_credit_activated', true );
                                }

                                // $coupon_obj->save();
        
                                $responce[] = array(
                                    'row'=> $this->row_parsed,
                                    'error' => false,
                                    'coupon_id' => $coupon_id,
                                    'coupon_name' => $coupon_data['post_title'],
                                    'status'    => $staus
                                );
                                
                                
                            } else {
                                $responce[] = array(
                                    'row'=> $this->row_parsed,
                                    'error' => true,
                                    'coupon_name' => $coupon_data['post_title'],
                                    'status'    => __('Skipped','wt-smart-coupons-for-woocommerce-pro')
                                );
                            }
                        } 
        
                        $position = ftell($handle);
                        if ( '' != $end_position && $position >= $end_position ) {
                            break;
                        }
        
                        unset($coupon_data);
                        unset($coupon_meta_data);
                        $coupon_data = array(
                            'post_type'=>'shop_coupon'
                        );
                        $coupon_meta_data = array();
                        $error = false;
                        $this->row_parsed++;
                        
                    }
        
                    set_transient( 'wt_row_parsed', $this->row_parsed, 12 * HOUR_IN_SECONDS );
        
                    fclose( $handle );
                    echo json_encode( $responce );

                }
                die();
            }

        /**
         * Cehck the coupon already exist
         * @since 1.2.1
         */
        function  is_coupon_exist( $coupon ) {
            global $wpdb;
            if( $results = $wpdb->get_row( $wpdb->prepare(   "SELECT ID FROM $wpdb->posts WHERE post_type ='shop_coupon' AND post_status = 'publish' AND post_title = %s ",$coupon ) ) ) {
                return true;
                
            }
            return false;
        }

        /**
         * Return all meta key for Coupon
         * @since 1.0.0
         */
        public function get_possible_meta_for_coupon() {
            global $wpdb;
            $query = "SELECT DISTINCT meta_key FROM $wpdb->posts post INNER JOIN $wpdb->postmeta meta ON post.ID = meta.post_id WHERE post_type='shop_coupon' AND meta.meta_key != '_edit_lock' AND meta.meta_key != '_edit_last' ";
            $meta_results = $wpdb->get_results( $query );
            $coupon_meta = array();
            foreach( $meta_results as $meta_result ) {

                array_push($coupon_meta,$meta_result->meta_key );
            }

            return $coupon_meta;

        }
    }
}
