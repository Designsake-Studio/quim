<?php
/**
Template Name: Home
 */

get_header(); ?>

	<?php if( have_rows('hero') ):
        while( have_rows('hero') ): the_row();
        // vars
        $title = get_sub_field('hero_title');
        $description = get_sub_field('hero_description');
        $background = get_sub_field('hero_image');
        $link = get_sub_field('hero_link');
        $cta = get_sub_field('hero_cta');
        $productimg = get_sub_field('product_image');
        $productimgalt = get_sub_field('product_image_alt');
        $type = get_sub_field('background_type');
        $video = get_sub_field('background_video');
        ?>
        <?php if($type == 'img') : ?>
    	<div class="home hero" style="background: #0a4271 url('<?php echo $background; ?>') fixed no-repeat center center; background-size: contain;">
        <?php elseif($type == 'vid') : ?>
            <div class="home hero">
                <div class="video-wrap">
                    <video autoplay muted loop id="homeHero">
                      <source src="<?php echo $video; ?>" type="video/mp4">
                    </video>
                </div>
        <?php endif; ?>
    	    <div class="container wow fadeIn">
    	        <h2><?php echo $title; ?></h2>
                <?php if( $description ): ?>
    	           <div class="description"><?php echo $description; ?></div>
                <?php endif; ?>
                <?php if( $link ): ?>
    	           <a class="button" href="<?php echo $link; ?>"><?php echo $cta; ?></a>
                <?php endif; ?>
    	    </div>

            <?php if( $productimg ): ?>
                <div class="float-img bounce-1">
                    <img src="<?php echo $productimg; ?>">
                </div>
            <?php endif; ?>
            <?php if( $productimgalt ): ?>
                <div class="float-img right bounce-2">
                    <img src="<?php echo $productimgalt; ?>">
                </div>
            <?php endif; ?>


            <div class="pulsing-circle"></div>
            <div class="puls-button"><a href="#products"></a></div>


        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2200.21 129.55" style="fill: #1A283F;"><path d="M0,83.27C176.54,29.13,358.53,6.41,542.21,36.48c187.33,30.67,368.13,110.45,557.9,105.9,189.77,4.55,370.56-75.23,557.89-105.9,183.68-30.07,365.68-7.35,542.21,46.79v69.38H0Z" transform="translate(0 -23.09)"/></svg>

    	</div>

        <?php endwhile; ?>
    <?php endif; ?>

    <div class="press-spotlight">
        <div class="container">
            <h4>Featured In</h4>
            <ul class="press-listing">
            <?php $query = new WP_Query( array( 'post_type' => 'press', 'orderby' => 'rand', 'posts_per_page' => 6, ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

                <li class="press-item">
                    <a href="<?php the_field('press_link'); ?>" target="_blank"><img src="<?php the_field('press_logo'); ?>" alt="<?php the_title(); ?>"></a>
                </li>

                <?php endwhile; endif; ?>
            <?php wp_reset_query(); ?>
            </ul>
        </div>
    </div>

	<?php if( have_rows('featured_products') ):
        while( have_rows('featured_products') ): the_row();
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        ?>
        <div class="home-product container">
            <a id="products"></a>
    		<div class="product-title">
                <h3><?php echo $title; ?></h3>
    		    <div class="home-prod-desc"><?php echo $description; ?></div>
            </div>
            <div class="featured-products">
                <?php $query = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 4, ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

                        <div class="single-product <?php the_title(); ?>">
                            <div class="product-img-home">
                                <a href="<?php echo get_permalink(); ?>"><img src="<?php the_field('ingredients_image'); ?>" alt="<?php the_title(); ?>"></a>
                            </div>
                            <div class="product-detail-home">
                                <?php the_title( '<h4><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );?>
                                <span class="subtitle"><?php the_field('subtitle'); ?></span>
                            </div>
                        </div>

                    <?php endwhile; endif; ?>
                <?php wp_reset_query(); ?>
            </div>
    	</div>

        <?php endwhile; ?>
    <?php endif; ?>

    <?php if( get_field('home_fullwidth_image') ): ?>
        <div class="fullwidth-image" style="background: #000000 url('<?php the_field('home_fullwidth_image'); ?>') fixed no-repeat center center; background-size: cover;">
        </div>
    <?php endif; ?>

    <?php if( have_rows('card') ):
        while( have_rows('card') ): the_row();
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $background = get_sub_field('background');
        $mp4 = get_sub_field('bg_mp4');
        ?>

        <div class="about card" style="background: #000 url('<?php echo $background; ?>') no-repeat center center; background-size: cover;">
            <div class="floating wow fadeInUp">
                <h3><?php echo $title; ?></h3>
                <p><?php echo $description; ?></p>
            </div>
            <?php if( $mp4 ): ?>
                 <video preload="auto" autoplay="autoplay" muted="muted" loop="loop" playsinline="playsinline" webkit-playsinline="playsinline" class="preloaded">
                     <source src="<?php echo $mp4; ?>" type="video/mp4">
                 </video>
            <?php endif; ?>
        </div>

        <?php endwhile; ?>
    <?php endif; ?>

    <div class="testimonial-spotlight">
        <div class="container">
			<script>
				jQuery(document).ready(function($) {
    					new CircleType(document.getElementById('tagline'));
				});
			</script>
            <div id="tagline" class="testimonial-tag">What They're Sayin About Quim◦</div>
            <ul class="testimonial-list owl-carousel">
            <?php $query = new WP_Query( array( 'post_type' => 'testimonial', 'orderby' => 'rand', 'posts_per_page' => 5, ) ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>

                <li class="testimonial-item">
                    <?php the_field('quote'); ?>
                </li>

                <?php endwhile; endif; ?>
            <?php wp_reset_query(); ?>
            </ul>
        </div>
    </div>

	<?php if( have_rows('fifty') ):
        while( have_rows('fifty') ): the_row();
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $background = get_sub_field('background');
        $image = get_sub_field('image');
        ?>
        <div class="story container">
    		<div class="content wow fadeIn">
    		 	<h3><?php echo $title; ?></h3>
    		 	<p><?php echo $description; ?></p>
  	    	</div>
    		<div class="img-container wow fadeIn">
    			 <div class="wiggle-frame" style="background: #EBDCCD url('<?php echo $background; ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame.png"></div>
    		</div>
    	</div>

        <?php endwhile; ?>
    <?php endif; ?>

<?php get_footer(); ?>
