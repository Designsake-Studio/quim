<?php
/**
 * QUIM functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package QUIM
 */

if ( ! function_exists( 'quim_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function quim_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on QUIM, use a find and replace
		 * to change 'quim' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'quim', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'main-navigation' => esc_html__( 'Primary', 'quim' ),
			'footer-menu' => esc_html__( 'Footer', 'quim' ),
			'mobile-menu' => esc_html__( 'Mobile Menu', 'quim' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'quim_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'quim_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function quim_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'quim_content_width', 640 );
}
add_action( 'after_setup_theme', 'quim_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function quim_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widgets', 'quim' ),
		'id'            => 'footer-widgets',
		'description'   => esc_html__( 'Add widgets here.', 'quim' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Disclaimer', 'quim' ),
		'id'            => 'footer-disclaimer',
		'description'   => esc_html__( 'Add disclaimer', 'quim' ),
		'before_widget' => '<section id="%1$s" class="disclaimer">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title" style="display:none;">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Description', 'quim' ),
		'id'            => 'blog-description',
		'description'   => esc_html__( 'Content Above Blog Listing', 'quim' ),
		'before_widget' => '<section id="%1$s" class="disclaimer">',
		'after_widget'  => '</section>',
		'before_title'  => '<h1 class="page-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'quim_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function quim_scripts() {
	wp_enqueue_style( 'quim-reset', get_template_directory_uri() . '/reset.css', array(), null, 'all' );
	wp_enqueue_style( 'quim-style', get_stylesheet_uri() );

	wp_enqueue_script( 'quim-custom', get_template_directory_uri() . '/js/custom-rad.js', array(), '20191030', true );
	wp_enqueue_script( 'owl-js', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), '20191027', true );

	wp_enqueue_script( 'quim-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'quim-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
}
add_action( 'wp_enqueue_scripts', 'quim_scripts' );


/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}


function cpt_press() {
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Press', 'quim' ),
		'singular_name'       => _x( 'Press',  'quim' ),
		'menu_name'           => __( 'Press', 'quim' ),
		'parent_item_colon'   => __( 'Parent Press', 'quim' ),
		'all_items'           => __( 'All Press', 'quim' ),
		'view_item'           => __( 'View Press', 'quim' ),
		'add_new_item'        => __( 'Add New Press', 'quim' ),
		'add_new'             => __( 'Add New', 'quim' ),
		'edit_item'           => __( 'Edit Press', 'quim' ),
		'update_item'         => __( 'Update Press', 'quim' ),
		'search_items'        => __( 'Search Press', 'quim' ),
		'not_found'           => __( 'Not Found', 'quim' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'quim' ),
	);
	// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'Press', 'quim' ),
		'description'         => __( 'List of Press', 'quim' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'custom-fields', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 7,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
  		'menu_icon'=> 'dashicons-awards',
	);
	// Registering your Custom Post Type
	register_post_type( 'press', $args );
}
add_action( 'init', 'cpt_press', 0 );



function cpt_testimonial() {
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Testimonial', 'quim' ),
		'singular_name'       => _x( 'Testimonial',  'quim' ),
		'menu_name'           => __( 'Testimonial', 'quim' ),
		'parent_item_colon'   => __( 'Parent Testimonial', 'quim' ),
		'all_items'           => __( 'All Testimonials', 'quim' ),
		'view_item'           => __( 'View Testimonial', 'quim' ),
		'add_new_item'        => __( 'Add New Testimonial', 'quim' ),
		'add_new'             => __( 'Add New', 'quim' ),
		'edit_item'           => __( 'Edit Testimonials', 'quim' ),
		'update_item'         => __( 'Update Testimonials', 'quim' ),
		'search_items'        => __( 'Search Testimonials', 'quim' ),
		'not_found'           => __( 'Not Found', 'quim' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'quim' ),
	);
	// Set other options for Custom Post Type
	$args = array(
		'label'               => __( 'Testimonial', 'quim' ),
		'description'         => __( 'List of Testimonials', 'quim' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'custom-fields', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 7,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
  		'menu_icon'=> 'dashicons-format-chat',
	);
	// Registering your Custom Post Type
	register_post_type( 'testimonial', $args );
}
add_action( 'init', 'cpt_testimonial', 0 );


function remove_menus(){
  remove_menu_page( 'edit-comments.php' );  //Comments
}
add_action( 'admin_menu', 'remove_menus' );


/**
 * Remove the breadcrumbs 
 */
add_action( 'init', 'woo_remove_wc_breadcrumbs' );
function woo_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}

/**
 * Age Gate
 */
add_filter('age_gate_after', 'after_age_gate', 10, 1);
function after_age_gate($after){

  $after .= '<div class="custom-markup">';
  $after .= "	<a class='facebook' href='https://www.facebook.com/quim.rock/'>Facebook</a>
				<a class='instagram' href='https://www.instagram.com/its.quim/'>Instagram</a>
				<a class='twitter' href='https://twitter.com/quimrock'>Twitter</a>";
  $after .= '</div>';

  return $after;

}

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 */
function my_header_add_to_cart_fragment( $fragments ) {
    ob_start();
    $count = WC()->cart->cart_contents_count;
    ?><a class="cart-contents" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php
    if ( $count > 0 ) {
        ?>
        <span class="cart-contents-count"><?php echo esc_html( $count ); ?></span>
        <?php            
    }
        ?></a><?php
    $fragments['a.cart-contents'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );


/**
 * Extole Integration
 */
function queue_extole_confirmation_zone( $orderid ) {
    $order = wc_get_order( $orderid );

    wp_enqueue_script( 'extole_confirmation_zone', get_template_directory_uri() . '/js/extole-confirmation-zone.js' );
     
    # First choice order email? -> https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html#_get_user
    # Second choice? -> https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html#_get_billing_email
    $orderDetails = array(
        'customerEmail'            => $order->get_billing_email();
    );

    wp_localize_script( 'extole_confirmation_zone', 'orderDetails', $orderDetails );
}

add_action( 'woocommerce_thankyou', 'queue_extole_confirmation_zone' );/* Start Extole */
  (function(c,e,k,l,a){c[e]=c[e]||{};for(c[e].q=c[e].q||[];a<l.length;)k(l[a++],c[e])})(window,"extole",function(c,e){e[c]=e[c]||function(){e.q.push([c,arguments])}},["createZone"],0);
/* End Extole */

extole.createZone({
    name: "confirmation"
    email: orderDetails.customerEmail
})


