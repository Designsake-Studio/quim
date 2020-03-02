<?php
/**
Template Name: Contact
 */

get_header(); ?>


<div class="story contact container">
    <div class="content">
        <h3><?php the_field('contact_title'); ?></h3>
        
        <?php if( get_field('contact_description') ): ?>
            <p><?php the_field('contact_description'); ?></p>
        <?php endif; ?>

        <?php if( get_field('contact_form') ): ?>
           <div class="contact-form"><?php the_field('contact_form'); ?></div>
        <?php endif; ?>

    </div>
    <div class="img-container">
        <div class="wiggle-frame" style="background: #EBDCCD url(' <?php the_field('contact_image'); ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame.png"></div>
    </div>
</div>





<?php get_footer(); ?>
