<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package QUIM
 */

?>

	</div><!-- #content -->
</div><!-- #page -->

		<div class="footer-wiggle">
			<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2200.21 129.07" style="fill: #1A283F;"><path d="M0,82.63c176.54,54.14,358.53,76.86,542.21,46.79C729.54,98.75,910.34,19,1100.11,23.52,1289.88,19,1470.67,98.75,1658,129.42c183.68,30.07,365.68,7.35,542.21-46.79V152.4H0Z" transform="translate(0 -23.34)"/></svg>
		</div>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="footer-columns">
				<?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
					<?php dynamic_sidebar( 'footer-widgets' ); ?>
				<?php endif; ?>
			</div>
			
			<div class="site-info">
				<?php echo date("Y"); ?> &copy; Quim Rock. All Rights Reserved. Website by <a aria-label="Designsake Studio" href="http://designsakestudio.com" target="_blank">Designsake Studio</a>


				<?php if ( is_active_sidebar( 'footer-disclaimer' ) ) : ?>
					<?php dynamic_sidebar( 'footer-disclaimer' ); ?>
				<?php endif; ?>
			</div>

			<span id="extole_zone_global_footer"></span>
			
		</div>
	</footer>

<?php wp_footer(); ?>

</body>
</html>
