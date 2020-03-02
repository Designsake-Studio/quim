<?php
/**
Template Name: Learn
 */

get_header(); ?>


    <?php if( have_rows('vaginal_q') ): 
        while( have_rows('vaginal_q') ): the_row(); 
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $image = get_sub_field('image');
        $questions = get_sub_field('questions');
        ?>

        <div class="question-section vaginal">
            <div class="container">

                <div class="story">
                    <div class="content wow fadeIn">
                        <h3 class="section-title"><?php echo $title; ?></h3>
                        <p><?php echo $description; ?></p>
                    </div>
                    <div class="img-container wow fadeIn">
                        <div class="wiggle-frame" style="background: #EBDCCD url('<?php echo $image; ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame-2.png"></div>
                    </div>
                </div>

                <div class="question-list">
                    <?php if( have_rows('questions') ):?>
                        <ul class="questions">
                        <?php while( have_rows('questions') ): the_row(); 
                            // vars
                            $title = get_sub_field('title');
                            $description = get_sub_field('description');
                        ?>
                        <div class="q-a wow fadeIn">
                            <h4><?php echo $title; ?></h4>
                            <p><?php echo $description; ?></p>
                        </div>
                      <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php endwhile; ?>
    <?php endif; ?>


    <!---<div class="testimonial-spotlight">
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
    </div> -->


    <?php if( have_rows('purchase_q') ): 
        while( have_rows('purchase_q') ): the_row(); 
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $image = get_sub_field('image');
        $questions = get_sub_field('questions');
        ?>

        <div class="question-section purchase">
            <div class="container">

                <div class="story">
                    <div class="content wow fadeIn">
                        <h3 class="section-title"><?php echo $title; ?></h3>
                        <p><?php echo $description; ?></p>
                    </div>
                    <div class="img-container wow fadeIn">
                        <div class="wiggle-frame" style="background: #EBDCCD url('<?php echo $image; ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame.png"></div>
                    </div>
                </div>

                <div class="question-list">
                    <?php if( have_rows('questions') ):?>
                        <ul class="questions">
                        <?php while( have_rows('questions') ): the_row(); 
                            // vars
                            $title = get_sub_field('title');
                            $description = get_sub_field('description');
                        ?>
                        <div class="q-a wow fadeIn">
                            <h4><?php echo $title; ?></h4>
                            <p><?php echo $description; ?></p>
                        </div>
                      <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <?php endwhile; ?>
    <?php endif; ?>



<?php get_footer(); ?>
