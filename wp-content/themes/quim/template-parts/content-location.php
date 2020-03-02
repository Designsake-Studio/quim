<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package QUIM
 */

?>

	<div class="location-list sfbay">
		<a id="sfbay"></a>
		<div class="container">
			<h3>San Francisco / Bay Area</h3>
			<div class="location-columns">
			<?php if( have_rows('san_francisco') ): ?>
					<?php while( have_rows('san_francisco') ): the_row(); 
					// vars
					$region = get_sub_field('region');
					$retailer = get_sub_field('retailer');
					?>
					<div class="location">
            		<h4><?php echo $region; ?></h4>
						<?php if( have_rows('retailer') ): ?>
							<div class="retailer-list">
								<?php while( have_rows('retailer') ): the_row(); 
								// vars
								$name = get_sub_field('name');
								$address = get_sub_field('address');
								$link = get_sub_field('link');
								?>
							
								<li class="locale wow fadeIn">
									<strong><?php echo $name; ?></strong>
									<span class="address"><?php echo $address; ?></span>
									<a href="<?php echo $link; ?>" target="_blank">Website</a>
								</li>
  								<?php endwhile; ?>
  							</div>
  						<?php endif; ?>
  						</div>
  				<?php endwhile; ?>
  			<?php endif; ?>
  			</div>
        </div>
    </div>

	<div class="location-list norcal">
		<a id="norcal"></a>
		<div class="container">
			<h3>Northern California</h3>
			<div class="location-columns">
			<?php if( have_rows('norcal_location') ): ?>
					<?php while( have_rows('norcal_location') ): the_row(); 
					// vars
					$region = get_sub_field('region');
					$retailer = get_sub_field('retailer');
					?>
					<div class="location">
            		<h4><?php echo $region; ?></h4>
						<?php if( have_rows('retailer') ): ?>
							<div class="retailer-list">
								<?php while( have_rows('retailer') ): the_row(); 
								// vars
								$name = get_sub_field('name');
								$address = get_sub_field('address');
								$link = get_sub_field('link');
								?>
							
								<li class="locale wow fadeIn">
									<strong><?php echo $name; ?></strong>
									<span class="address"><?php echo $address; ?></span>
									<a href="<?php echo $link; ?>" target="_blank">Website</a>
								</li>
  								<?php endwhile; ?>
  							</div>
  						<?php endif; ?>
  						</div>
  				<?php endwhile; ?>
  			<?php endif; ?>
  			</div>
        </div>
    </div>

	<div class="location-list socal">
		<a id="socal"></a>
		<div class="container">
			<h3>Southern California</h3>
			<div class="location-columns">
			<?php if( have_rows('socal_location') ): ?>
					<?php while( have_rows('socal_location') ): the_row(); 
					// vars
					$region = get_sub_field('region');
					$retailer = get_sub_field('retailer');
					?>
					<div class="location">
            		<h4><?php echo $region; ?></h4>

						<?php if( have_rows('retailer') ): ?>
							<div class="retailer-list">
								<?php while( have_rows('retailer') ): the_row(); 
								// vars
								$name = get_sub_field('name');
								$address = get_sub_field('address');
								$link = get_sub_field('link');
								?>
							
								<li class="locale wow fadeIn">
									<strong><?php echo $name; ?></strong>
									<span class="address"><?php echo $address; ?></span>
									<a href="<?php echo $link; ?>" target="_blank">Website</a>
								</li>
						
  								<?php endwhile; ?>
  							</div>
  						<?php endif; ?>
  					</div>
  				<?php endwhile; ?>
  			<?php endif; ?>
  			</div>
        </div>
    </div>

	<div class="location-list delivery">
		<a id="delivery"></a>
		<div class="container">
			<h3>Get Quim Delivered</h3>
			<div class="location-columns">
			<?php if( have_rows('delivery_location') ): ?>
					<?php while( have_rows('delivery_location') ): the_row(); 
					// vars
					$region = get_sub_field('region');
					$retailer = get_sub_field('retailer');
					?>
					<div class="location">
            		<h4><?php echo $region; ?></h4>
						<?php if( have_rows('retailer') ): ?>
							<div class="retailer-list">
								<?php while( have_rows('retailer') ): the_row(); 
								// vars
								$name = get_sub_field('name');
								$location = get_sub_field('location');
								$link = get_sub_field('link');
								?>
							
								<li class="locale wow fadeIn">
									<strong><?php echo $name; ?></strong>
									<?php if( $location ): ?><?php echo $location; ?><?php endif; ?>
									<a href="<?php echo $link; ?>" target="_blank">Website</a>
								</li>
  								<?php endwhile; ?>
  							</div>
  						<?php endif; ?>
  						</div>
  				<?php endwhile; ?>
  			<?php endif; ?>
  			</div>
        </div>
    </div>


