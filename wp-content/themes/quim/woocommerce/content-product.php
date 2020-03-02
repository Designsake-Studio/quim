<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;
global $product;
// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<div  style="background: <?php the_field('hero_background'); ?> url('<?php the_field('hero_image'); ?>') no-repeat top right; background-size: auto 620px;" <?php wc_product_class( '', $product ); ?>>
	<div class="container wow fadeIn">
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );
	?>

	<?php if( get_field('subtitle') ): ?>
        <span class="subtitle"><?php the_field('subtitle'); ?></span>
	<?php endif; ?>
	<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?>"><?php echo $product->get_price_html(); ?></p>

	<ul class="feature-list">
		<?php $thc = get_field('features_alt'); ?>
			<li class="feature-<?php echo esc_attr($thc['value']); ?>"><?php echo esc_html($thc['label']); ?></li>
		<?php $feat = get_field('features'); ?>
			<li class="feature-<?php echo esc_attr($feat['value']); ?>"><?php echo esc_html($feat['label']); ?></li>
		<?php $latex = get_field('features_alt_alt'); ?>
			<li class="feature-<?php echo esc_attr($latex['value']); ?>"><?php echo esc_html($latex['label']); ?></li>
	</ul>

	<?php if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly. 
		}
		global $post;
		$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
	
		if ( ! $short_description ) {
			return;
		}
	?>
	<div class="woocommerce-product-details__short-description">
		<?php echo $short_description; // WPCS: XSS ok. ?>
	</div>
	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
	<a class="learn-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">Learn More</a>
	<span id="extole_zone_product"></span>
	</div>
</div>
