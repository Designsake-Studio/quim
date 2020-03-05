<?php
/**
Template Name: Ad Landing
 */

get_header('landing');

if(have_rows('hero')) :
    while( have_rows('hero') ) : the_row();

    $text = get_sub_field('hero_text');
    $bg = get_sub_field('hero_image');
    $form = get_sub_field('form');
    $f_label = $form['label'];
    $f_text = $form['text'];
    $shortcode = $form['shortcode'];
    ?>

    <section class="landing hero">
        <div class="bg-img wow fadeIn" style="background-image:url('<?php echo $bg['sizes']['1536x1536']; ?>');"></div>
        <div class="container">
            <?php if(!empty($text)) { ?>
            <div class="content wow fadeIn">
                <h1><?php echo $text; ?></h1>
            </div>
            <?php } ?>
            <div class="action wow fadeInDown">
                <div class="email-wrap">
                    <?php
                        if(!empty($f_label)) { echo '<h5>'.$f_label.'</h5>'; }
                        if(!empty($f_text)) { echo '<h3>'.$f_text.'</h3>'; }
                        if(!empty($shortcode)) { echo '<div class="landing-form-wrap">'.do_shortcode($shortcode).'</div>'; }
                    ?>
                </div>
            </div>
        </div>

    </section>

<?php
endwhile; endif;
// INTRO
if(have_rows('intro')) :
    while( have_rows('intro') ) : the_row();
        $label = get_sub_field('label');
        $text = get_sub_field('text');
        ?>
        <section class="landing intro">
            <div class="container wow fadeIn">
                <div class="content">
                    <?php
                        if(!empty($label)) { echo '<h5>'.$label.'</h5>'; }
                        if(!empty($text)) { echo '<p>'.$text.'</p>'; }
                    ?>
                </div>
            </div>
        </section>
<?php
endwhile; endif;
// CARDS
if(have_rows('card')) : while( have_rows('card') ) : the_row();
    echo '<section class="landing cards">';

        $c1 = get_sub_field('card_one');
        $c1_title = $c1['title'];
        $c1_text = $c1['text'];
        $c1_image = $c1['image'];
        $c2 = get_sub_field('card_two');
        $c2_title = $c2['title'];
        $c2_text = $c2['text'];
        $c2_image = $c2['image'];

        ?>
        <div class="landing promo img-left">
            <div class="container wow fadeIn">
                <div class="image">

                        <?php
                            if(!empty($c1_image)) { ?>
                            <div class="wiggle-frame">
                                <div class="img-bg" style="background: #EBDCCD url('<?php echo $c1_image; ?>') no-repeat center center; background-size: cover;"></div>
                                <img class="flip-me" src="https://itsquim.com/wp-content/themes/quim/images/wiggle-frame.png">
                            </div>
                            <?php }
                        ?>


                </div>
                <div class="content">
                    <?php
                        if(!empty($c1_title)) { echo '<h3>'.$c1_title.'</h3>'; }
                        if(!empty($c1_text)) { echo '<p>'.$c1_text.'</p>'; }
                    ?>
                    <p><a href="/shop/" class="button">Shop Now</a></p>
                </div>
            </div>
        </div>
        <div class="landing promo img-right">
            <div class="container wow fadeIn">
                <div class="image">

                        <?php
                            if(!empty($c2_image)) { ?>
                            <div class="wiggle-frame">
                                <div class="img-bg" style="background: #EBDCCD url('<?php echo $c2_image; ?>') no-repeat center center; background-size: cover;"></div>
                                <img src="https://itsquim.com/wp-content/themes/quim/images/wiggle-frame.png">
                            </div>
                            <?php }
                        ?>


                </div>
                <div class="content">
                    <?php
                        if(!empty($c2_title)) { echo '<h3>'.$c2_title.'</h3>'; }
                        if(!empty($c2_text)) { echo '<p>'.$c2_text.'</p>'; }
                    ?>
                </div>
            </div>
        </div>

    </section><!-- /landing cards -->

<?php
endwhile; endif;
// SIGNOFF
if(have_rows('product_line')) :
    while( have_rows('product_line') ) : the_row();
        $img = get_sub_field('bg_img');
        ?>
        <section class="landing product-line">
            <img src="<?php echo $img['sizes']['2048x2048']; ?>" alt="A self-care line for people with vaginas and people without vaginas who love vaginas." style="width: 100%; display: block;"/>

            <div class="press-spotlight">
                <div class="wiggle-wrap">
                <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2200.21 129.07" style="fill: #1A283F;"><path d="M0,82.63c176.54,54.14,358.53,76.86,542.21,46.79C729.54,98.75,910.34,19,1100.11,23.52,1289.88,19,1470.67,98.75,1658,129.42c183.68,30.07,365.68,7.35,542.21-46.79V152.4H0Z" transform="translate(0 -23.34)"/></svg>
            </div>
            <div class="container">
                <h4>Featured In</h4>
                <ul class="press-listing">

                    <li class="press-item">
                        <a href="https://www.civilized.life/articles/quim-rock-is-evolving-the-conversation-about-weed-sex-and-vaginas/" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/civilized-logo.png" alt="Civilized"></a>
                    </li>


                    <li class="press-item">
                        <a href="https://www.viceland.com/en_us/video/slutever-stoned-sex/5a7892a1f1cdb3119235c7b4" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/viceland-logo.png" alt="Viceland"></a>
                    </li>


                    <li class="press-item">
                        <a href="https://www.theguardian.com/society/2018/jul/09/cannabis-marijuana-sex-aphrodisiac" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/the_gaurdian.png" alt="The Guardian"></a>
                    </li>


                    <li class="press-item">
                        <a href="https://weedmaps.com/news/2019/05/cbd-oil-sex-satisfying-less-painful/" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/Weedmaps_logo.png" alt="Weedmaps"></a>
                    </li>


                    <li class="press-item">
                        <a href="http://nymag.com/strategist/article/the-best-products-at-the-indie-beauty-expo.html" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/thestrategist.png" alt="NY Mag The Strategist"></a>
                    </li>


                    <li class="press-item">
                        <a href="https://www.refinery29.com/en-us/2019/04/229937/weed-lube-reviews" target="_blank"><img src="https://itsquim.com/wp-content/uploads/2019/10/Refinery29.png" alt="Refinery 29"></a>
                    </li>

                </ul>
            </div>

    </section>
<?php endwhile; endif;
// FOOTER FORM
if(have_rows('signoff')) :
    while( have_rows('signoff') ) : the_row();
        $img = get_sub_field('background');
?>
<section class="landing signoff" style="background-image:url('<?php echo $img['sizes']['2048x2048']; ?>');">
    <div class="container wow fadeIn">
        <div class="email-wrap">
            <?php
                if(!empty($f_label)) { echo '<h5>'.$f_label.'</h5>'; }
                if(!empty($f_text)) { echo '<h3>'.$f_text.'</h3>'; }
                if(!empty($shortcode)) { echo '<div class="landing-form-wrap">'.do_shortcode($shortcode).'</div>'; }
            ?>
        </div>
    </div>
</section>
<?php endwhile; endif;


get_footer('landing');
