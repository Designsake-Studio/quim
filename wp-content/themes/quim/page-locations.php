<?php
/**
Template Name: Location
 */

get_header(); ?>

	<?php if( have_rows('thc_retail') ): 
        while( have_rows('thc_retail') ): the_row(); 
        // vars
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        $image = get_sub_field('image');
        ?>

    	<div class="where-to-buy-thc">
    		<a id="thc"></a>
    	    <div class="story container wow fadeIn">
                <div class="content">
    	           <h3><?php echo $title; ?></h3>
    	           <p><?php echo $description; ?></p>
                    <?php if( have_rows('links') ): ?>
                        <ul class="thc-links">
                            <?php while( have_rows('links') ): the_row(); 
                            // vars
                            $title = get_sub_field('title');
                            $icon = get_sub_field('icon');
                            $link = get_sub_field('link');
                            ?>
                            
                            <li class="link-item">
                                <a href="<?php echo $link; ?>">
                                    <img src="<?php echo $icon; ?>" alt="<?php echo $title; ?>">
                                    <?php echo $title; ?>
                                </a>
                            </li>
                            
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="img-container">
                    <div class="wiggle-frame" style="background: #EBDCCD url('<?php echo $image; ?>') no-repeat center center; background-size: cover;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/wiggle-frame.png"></div>
                </div>
    	    </div>
    	</div>

        <?php endwhile; ?>
    <?php endif; ?>


		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'location' );

		endwhile; // End of the loop.
		?>


    <?php if ( get_edit_post_link() ) : ?>
        <footer class="entry-footer">
            <div class="container">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __( 'Edit <span class="screen-reader-text">%s</span>', 'quim' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
            </div>
        </footer><!-- .entry-footer -->
    <?php endif; ?>

<?php get_footer(); ?>
