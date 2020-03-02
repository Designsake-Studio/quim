<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?> style="background-color: <?php the_field('hero_background'); ?>; color: <?php the_field('hero_primary'); ?>;">
	<style>
		.summary h1,
		.summary h2,
		.summary h3,
		.summary h4 { color: <?php the_field('hero_secondary'); ?>; }
		.entry-summary form.cart button, 
		.product-type-external button,
		a.button { background: transparent; color: <?php the_field('hero_primary'); ?>; border-color: <?php the_field('hero_primary'); ?>; }

	</style>
	<div class="hero-image" style="background: url('<?php the_field('hero_image'); ?>') no-repeat top right; background-size: 55% auto;"></div>
	<div class="summary entry-summary container">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );?>

		<span id="extole_zone_product"></span>

		<ul class="feature-list">
		<?php $thc = get_field('features_alt'); ?>
			<li class="feature-<?php echo esc_attr($thc['value']); ?>"><?php echo esc_html($thc['label']); ?></li>
		<?php $feat = get_field('features'); ?>
			<li class="feature-<?php echo esc_attr($feat['value']); ?>"><?php echo esc_html($feat['label']); ?></li>
		<?php $latex = get_field('features_alt_alt'); ?>
			<li class="feature-<?php echo esc_attr($latex['value']); ?>"><?php echo esc_html($latex['label']); ?></li>
		</ul>

		<?php do_action( 'woocommerce_after_single_product_summary' ); ?>

		<div class="back-to-products">
			<a href="/shop">Back To All Products</a>
		</div>

	</div>

	<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2200.21 129.55" style="fill: <?php the_field('ingredient_background'); ?>"><path d="M0,83.27C176.54,29.13,358.53,6.41,542.21,36.48c187.33,30.67,368.13,110.45,557.9,105.9,189.77,4.55,370.56-75.23,557.89-105.9,183.68-30.07,365.68-7.35,542.21,46.79v69.38H0Z" transform="translate(0 -23.09)"/></svg>

</div>

	<?php if( get_field('ingredients') ): ?>

		<?php if( get_field('ingredient_background') ): ?>
			<div class="ingredients" style="background-color: <?php the_field('ingredient_background'); ?>; color: <?php the_field('ingredient_primary'); ?>">
		<?php else: ?>
			<div class="ingredients">
		<?php endif; ?>

		<div class="container">

				<?php if( have_rows('ingredients') ): ?>
					<div class="ingredients-items">
            			<ul class="ingredients-list">
							<?php while( have_rows('ingredients') ): the_row(); 
							// vars
							$name = get_sub_field('name');
							$details = get_sub_field('details');
							$icon = get_sub_field('icon');
							?>
						
							<li class="ingredients-item">
								<img class="icon" src="<?php echo $icon; ?>">
								<strong><?php echo $name; ?></strong>
								<p style="color:<?php the_field('ingredient_secondary'); ?>;"><?php echo $details; ?></p>
							</li>
						
  							<?php endwhile; ?>
  						</ul>
  					</div>
  				<?php endif; ?>

  				<div class="ingredient-image">
  					<img src="<?php the_field('ingredients_image'); ?>" alt="<?php the_field('ingredients_title'); ?>">
  				</div>

				<?php if( have_rows('features_a') ): ?>
					<div class="features-list">
            			<ul class="ingredients-list">
							<?php while( have_rows('features_a') ): the_row(); 
							// vars
							$name = get_sub_field('name');
							$details = get_sub_field('details');
							$icon = get_sub_field('icon');
							$testimonial = get_sub_field('testimonial');
							?>
						
							
							<li class="ingredients-item">
								<img class="icon" src="<?php echo $icon; ?>">
								<strong><?php echo $name; ?></strong>
								<p style="color:<?php the_field('ingredient_secondary'); ?>;"><?php echo $details; ?></p>
							</li>
							<div class="testimonial-quote">
								<?php echo $testimonial; ?>
							</div>
						
  							<?php endwhile; ?>
  						</ul>
  					</div>
  				<?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

	<?php if( get_field('fullwidth_image') ): ?>
		<div class="fullwidth-image" style="background: #000000 url('<?php the_field('fullwidth_image'); ?>') fixed no-repeat center center; background-size: cover;">
		</div>
	<?php endif; ?>

	<?php if( get_field('product_questions') ): ?>
		<?php if( get_field('background') ): ?>
			<div class="additional-info" style="background: <?php the_field('background'); ?> url('<?php the_field('additional_illustration'); ?>') no-repeat center right; background-size: 35% auto; color: <?php the_field('primary_color'); ?>;">
		<?php else: ?>
			<div class="additional-info">
		<?php endif; ?>
				<div class="details">
					<?php if( have_rows('product_questions') ): ?>
            				<ul class="product-question-list">
								<?php while( have_rows('product_questions') ): the_row(); 
								// vars
								$question = get_sub_field('question');
								$answer = get_sub_field('answer');
								?>
							
								<li class="product-q wow fadeIn">
									<strong><?php echo $question; ?></strong>
									<p style="color: <?php the_field('secondary_color'); ?>;"><?php echo $answer; ?></p>
								</li>
							
  								<?php endwhile; ?>
  							</ul>
  					<?php endif; ?>
        		</div>
        	</div>
	<?php endif; ?>


	<?php if( get_field('testimonial_quote') ): ?>
		<div class="testimonial" style="background-color: <?php the_field('testimonial_background'); ?>; color: <?php the_field('testimonial_primary'); ?>;">
			<div class="testimonial-description">
            	<div class="quote">
            		
            <script>
                jQuery(document).ready(function($) {
                        new CircleType(document.getElementById('tagline'));
                });
            </script>
            		<div id="tagline" class="testimonial-tag"style=" color: <?php the_field('testimonial_secondary'); ?>;">What They're Sayin About Quim◦</div>
            		<h4 style="color: <?php the_field('testimonial_primary'); ?>"><?php the_field('testimonial_quote'); ?></h4>
            	</div>
            </div>
            <div class="testimonial-image" style="background:url('<?php the_field('testimonial_image'); ?>') no-repeat top center; background-size: cover;">
			</div>
        </div>
	<?php endif; ?>


	<?php do_action( 'woocommerce_after_single_product' ); ?>



