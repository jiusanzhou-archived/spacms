(function(){
    /* Home page, What we do slides. */
    var wwdSlides = function(){
      $("#wwd-slides").owlCarousel({
          items: 5,
          loop: true,
          nav: true,
          itemsDesktop : [1199,5],
          itemsDesktopSmall : [979,2],
          navText: ['', ''],
          dotsEach: true,
          animateOut: 'fadeOutDown',
          animateIn: 'fadeInUp',
          smartSpeed: 500,
      });
    };
    setTimeout(function(){
        wwdSlides()
    }, 500)
})()