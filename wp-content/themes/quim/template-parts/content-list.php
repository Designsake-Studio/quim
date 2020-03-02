<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package QUIM
 */

?>
		
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="container">

		<?php quim_post_thumbnail(); ?>

	
		<div class="entry-content">
			<header class="entry-header">
			<?php
				if ( is_singular() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif; ?>
			</header><!-- .entry-header -->
			<?php the_excerpt()?>
		</div><!-- .entry-content -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
