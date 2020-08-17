<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package QUIM
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<script src="https://code.jquery.com/jquery-latest.min.js"></script>

  	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico" type="image/x-icon">
	<link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.svg" color="#000000">
	<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.png">

	<!-- Unbranded -->
	<script type="text/javascript" src="https://quim.extole.io/core.js" async></script>

	<!-- HubSpot -->
	<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/6616270.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,600,700,800&display=swap" rel="stylesheet">

  <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/circletype.min.js"></script>
  <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.lettering-0.6.min.js"></script>

	<!-- Facebook Pixel -->
	<script>
	  !function(f,b,e,v,n,t,s)
	  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	  n.queue=[];t=b.createElement(e);t.async=!0;
	  t.src=v;s=b.getElementsByTagName(e)[0];
	  s.parentNode.insertBefore(t,s)}(window, document,'script',
	  'https://connect.facebook.net/en_US/fbevents.js');
	  fbq('init', '2015494611929557');
	  fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	  src="https://www.facebook.com/tr?id=2015494611929557&ev=PageView&noscript=1"
	/></noscript>
	<!-- End Facebook Pixel Code -->

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-150093503-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-150093503-1');
	</script>

  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
  	<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/wow.min.js"></script>
    <script>
    	new WOW().init();
    </script>

	<script>
		$(document).ready(function(){
		 	// Add smooth scrolling to all links
		 	$("a").on('click', function(event) {
		 	  	if (this.hash !== "") {
		 	  	  // Prevent default anchor click behavior
		 	  	  event.preventDefault();
		 	  	  // Store hash
		 	  	  var hash = this.hash;
		 	  	  $('html, body').animate({
		 	  	    scrollTop: $(hash).offset().top
		 	  	  }, 1200, function(){
		 	 		        window.location.hash = hash;
		 	  	  });
		 	  	} // End if
		  });
		});
	</script>

	<?php wp_head(); ?>


</head>

<body <?php body_class(); ?>>

<!-- Extole  Global Header
<span id="extole_zone_global_header"></span> -->

<?php if ( is_active_sidebar( 'announcement-bar' ) ) : ?>
	<div id="announcement">
		<?php dynamic_sidebar( 'announcement-bar' ); ?>
	</div>
<?php endif; ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'quim' ); ?></a>
	<header id="masthead" class="site-header">
		<div class="container">


			<div class="site-branding">
				<div class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div>
			</div>


		</div>
	</header><!-- #masthead -->


	<div id="content" class="site-content">
