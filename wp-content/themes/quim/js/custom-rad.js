jQuery(document).ready(function($) {
    $( '.testimonial-list').owlCarousel({
    loop:true,
    margin:40,
    nav:true,
    smartSpeed:450,
    autoplay: 2500,
    autoplayHoverPause: true,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:1
        }
    }
    })
});