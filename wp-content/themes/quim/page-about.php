<?php
/**
Template Name: About
 */

get_header(); ?>


	<?php if( have_rows('hero') ): 
        while( have_rows('hero') ): the_row(); 
        // vars
        $title = get_sub_field('hero_title');
        $description = get_sub_field('hero_description');
        $background = get_sub_field('hero_image');
        $background_mp4 = get_sub_field('hero_mp4');
        $link = get_sub_field('hero_link');
        $cta = get_sub_field('hero_cta');
        ?>

    	<div class="about hero" style="background: #000 url('<?php echo $background; ?>') fixed no-repeat center center; background-size: cover;">
    	    <div class="container wow fadeIn">
    	        <h2><?php echo $title; ?></h2>
    	        <p><?php echo $description; ?></p>
    	    </div>

            <div class="pulsing-circle"></div>
            <div class="puls-button"><a href="#mission"></a></div>

        <?php if( $background_mp4 ): ?>
             <video preload="auto" autoplay="autoplay" muted="muted" loop="loop" playsinline="playsinline" webkit-playsinline="playsinline" class="preloaded">
                 <source src="<?php echo $background_mp4; ?>" type="video/mp4">
             </video>
        <?php endif; ?>


        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2200.21 129.55" style="fill: #EBDCCD;"><path d="M0,83.27C176.54,29.13,358.53,6.41,542.21,36.48c187.33,30.67,368.13,110.45,557.9,105.9,189.77,4.55,370.56-75.23,557.89-105.9,183.68-30.07,365.68-7.35,542.21,46.79v69.38H0Z" transform="translate(0 -23.09)"/></svg>

    	</div>

        <?php endwhile; ?>
    <?php endif; ?>

    	<div class="about mission">
    		<a id="mission"></a>
            <div class="container">

    		  <div class="entry-content">
                    <?php while ( have_posts() ) :  the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile; ?>
    		  </div>

              <?php if( have_rows('story') ): 
                while( have_rows('story') ): the_row(); 
                // vars
                $title = get_sub_field('title');
                $description = get_sub_field('description');
                $background = get_sub_field('background');
                ?>
                <div class="story">
                    <div class="content wow fadeIn">
                        <h3><?php echo $title; ?></h3>
                        <p><?php echo $description; ?></p>
                    </div>
                    <div class="img-container wow fadeIn">
                        <div class="wiggle-frame" style="background: #EBDCCD url('<?php echo $background; ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame.png"></div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>
    	</div>

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

    
	<?php if( have_rows('fifty') ): 
        while( have_rows('fifty') ): the_row(); 
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $background = get_sub_field('background');
        $image = get_sub_field('image');
        ?>
        <div class="about fifty">
    		<div class="half image" style="
    			background: url('<?php echo $background; ?>') no-repeat top center; background-size: cover;">
    		</div>
    		<div class="half description wow fadeIn">
    		 	<h3><?php echo $title; ?></h3>
    		 	<?php echo $description; ?>
  	    	</div>
    	</div>

        <?php endwhile; ?>
    <?php endif; ?>

<?php get_footer(); ?>
