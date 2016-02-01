;(function(){
'use strict'
    console.log('Hello Projects....');
  var animEndEventNames = { 'WebkitAnimation' : 'webkitAnimationEnd', 'OAnimation' : 'oAnimationEnd', 'msAnimation' : 'MSAnimationEnd', 'animation' : 'animationend' };
  var ft = {
    support: { animations : Modernizr.cssanimations },
    //animEndEventNames: { 'WebkitAnimation' : 'webkitAnimationEnd', 'OAnimation' : 'oAnimationEnd', 'msAnimation' : 'MSAnimationEnd', 'animation' : 'animationend' },
    animEndEventName: animEndEventNames[ Modernizr.prefixed( 'animation' ) ],//"animationend"
    onEndAnimation: function( el, callback ) {
      var onEndCallbackFn = function( ev ) {
        if( support.animations ) {
          if( ev.target != this ) return;
          this.removeEventListener( animEndEventName, onEndCallbackFn );
        }
        if( callback && typeof callback === 'function' ) { callback.call(); }
      };
      if( support.animations ) {
        el.addEventListener( animEndEventName, onEndCallbackFn );
      }
      else {
        onEndCallbackFn();
      }
    },
    throttle: function(fn, delay) {
      var allowSample = true;
      return function(e) {
        if (allowSample) {
          allowSample = false;
          setTimeout(function() { allowSample = true; }, delay);
          fn(e);
        }
      };
    },


    // array where the flickity instances are going to be stored
    flkties: [],
    // isotope instance
    iso: null,
    sliders: [],
    grid: [],
    filterCtrls: [],

    initElement: function(){
      this.sliders = [].slice.call(document.querySelectorAll('.slider'));
      // filter ctrls
      // grid element
      this.grid = document.querySelector('.grid');
      this.filterCtrls = [].slice.call(document.querySelectorAll('.filter > button'));
    },


    init: function() {
      this.initElement();
      this.initIsotope();
      this.initEvents(this.iso);
    },

    initFlickity: function() {
      this.sliders.forEach(function(slider){
        var flkty = new Flickity(slider, {
          prevNextButtons: false,
          wrapAround: true,
          cellAlign: 'lethis',
          contain: true,
          resize: false
        });

        // store flickity instances
        this.flkties.push(flkty);
      });
    },

    initEvents: function(iso) {
      this.filterCtrls.forEach(function(filterCtrl) {
        filterCtrl.addEventListener('click', function() {
          classie.remove(filterCtrl.parentNode.querySelector('.filter__item--selected'), 'filter__item--selected');
          classie.add(filterCtrl, 'filter__item--selected');
          iso.arrange({
            filter: filterCtrl.getAttribute('data-filter')
          });
          //this.recalcFlickities();
          iso.layout();
        });
      });

      // window resize / recalculate sizes for both flickity and isotope/masonry layouts
      window.addEventListener('resize', this.throttle(function(ev) {        
        iso.layout();
      }, 50));

    },

    initIsotope: function() {
      this.iso = new Isotope( this.grid, {
        isResizeBound: false,
        itemSelector: '.grid__item',
        percentPosition: true,
        masonry: {
          // use outer width of grid-sizer for columnWidth
          columnWidth: '.grid__sizer'
        },
        transitionDuration: '0.6s'
      });
      classie.remove(this.grid, 'grid--loading');
    }
  };
  setTimeout(function(){
    ft.init()
  }, 500);
})()