var jsloader = {

  a: function(scripts,callback) {
     if(typeof(scripts) != "object") var scripts = [scripts];
      //var _scripts = window.document.getElementsByTagName('script');
     var HEAD = document.getElementsByTagName("head").item(0) || document.documentElement, s = new Array(), loaded = 0;
     for(var i=0; i<scripts.length; i++) {
          //var _test = 0;
          //[].map.call(_scripts, function(j){if(j.src==scripts[i]){_test += 1}})
          //if(_test>0) continue;
         s[i] = document.createElement("script");
         s[i].setAttribute("type","text/javascript");
         s[i].onload = s[i].onreadystatechange = function() { //Attach handlers for all browsers
             if(!/*@cc_on!@*/0 || this.readyState == "loaded" || this.readyState == "complete") {
                 loaded++;
                 this.onload = this.onreadystatechange = null; this.parentNode.removeChild(this); 
                 if(loaded == scripts.length && typeof(callback) == "function") callback();
             }
         };
         s[i].setAttribute("src",scripts[i]);
         HEAD.appendChild(s[i]);
     }
  },

  b: function(scripts,callback) {
     if(typeof(scripts) != "object") var scripts = [scripts];
     var HEAD = document.getElementsByTagName("head").item(0) || document.documentElement;
     var s = new Array(), last = scripts.length - 1, recursiveLoad = function(i) {  //递归
         s[i] = document.createElement("script");
         s[i].setAttribute("type","text/javascript");
         s[i].onload = s[i].onreadystatechange = function() { //Attach handlers for all browsers
             if(!/*@cc_on!@*/0 || this.readyState == "loaded" || this.readyState == "complete") {
                 this.onload = this.onreadystatechange = null; this.parentNode.removeChild(this); 
                 if(i != last) recursiveLoad(i + 1); else if(typeof(callback) == "function") callback();
             }
         }
         s[i].setAttribute("src",scripts[i]);
         HEAD.appendChild(s[i]);
     };
     recursiveLoad(0);
  }

};

var common_funcs = {
  loadJs: function(js){
    if (js.scripts!=undefined&&js.scripts.length>0) {
      jsloader.a(js.scripts, function(){eval(js.code)})
    }
    if(js.code.length>0){
      if((typeof js.code) == "string"){
        eval(js.code)
      }else if((typeof js.code) == "object"){
        [].map.call(js.code, function(i){eval(i)})
      }
    }
  },
  loadCss: function(css){
    if (css.links!=undefined && (css.links.length>0||(css.style!=undefined&&css.style.length>0))) {
      [].map.call(css.links,function(i){loadcss(i)})
    }
  }
};

var S4 = function() {return (((1+Math.random())*0x10000)|0).toString(16).substring(1);};
var guid = function() {return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());};

;$(document).ready(function() {

  //Init the carousel

  $("#owl-slides").owlCarousel({
    items: 1,
    loop: true,
    nav: true,
    navText: ['', ''],
    dotsEach: true,
    //onInitialize : start,
    //onDrag: pauseOnDragging,
    //onChanged: start,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    smartSpeed: 500,
    //autoplayTimeout: 5000,
    //autoplay: true,
  });

  var ui = {

    data: {
      avaibleHeight: $(window).height(),
      currentSlidesHeight: this.avaibleHeight,
      settingSlidesHeight: false,
      target: $('#slides'),
      header_btn_arr: $('.header-btn>a'),
      header_btn_list: {},
      _current_btn: null,
      title_default: $('#echo-section').data('defaultTitle'),
      background_default: $('#echo-section').data('defaultImg'),
      theater: null
    },

    setSlides: function(openSlides){
      if (openSlides) {
        if(this.data.avaibleHeight != this.data.currentSlidesHeight){
          this.data.target.css('display', 'block');
          this.data.currentSlidesHeight = this.data.avaibleHeight;
          this.data.target.height(this.data.avaibleHeight);
          $('section.bg .ribbon').css('display', 'block');
        }
      } else{
        if (this.data.currentSlidesHeight != 0) {
          this.data.target.height(0);
          this.data.currentSlidesHeight = 0;
          $('section.bg .ribbon').css('display', 'none');
          setTimeout("$('#slides').css('display', 'none');", 500); //!!!!!!!!!!!!!!!!
        };
      };
      scrollTo($('body'), 0);
    },

    intBgSection: function(data){
      data = data!=undefined?data:{};
      this.initEcho(data.title);
      if(data.img!=undefined&&data.img.length>0){
        var img = new Image();
        img.onload = function(){$('#echo-section').css('background-image','url(' + data.img + ')')}
        img.src = data.img;
      }else{
        $('#echo-section').css('background-image','url(' + this.data.background_default + ')')
      }
      
    },

    initEcho: function(words){
      if(this.data.theater==null){this.data.theater = theaterJS();this.data.theater.addActor('echoline', { speed: 0.8, accuracy: 0.6 })}
      if(words==undefined||words.length==0) {words = this.data.title_default;}
      this.data.theater
             .addScene('echoline:' + words);
      this.data.theater.play();
      
    },

    initHeader: function(){
      if(Object.keys(this.data.header_btn_list).length==0){
        var _h_a = {};
        [].map.call(this.data.header_btn_arr, function(i){
          _h_a[i.href] = i
        });
        this.data.header_btn_list = _h_a
      }
      if(this.data._current_btn){
        $(this.data._current_btn).removeClass('header-btn-actived')
      }
      var _now_btn = this.data.header_btn_list[window.location.href];
      this.data._current_btn = _now_btn;
      $(_now_btn).addClass('header-btn-actived')
    },

    init: function(target){
      this.data.target = typeof target === 'string' ? $(target) : target;
      $.webkitSmoothScroll(); // Smooth Scroll
      this.initEvents();
    },

    initEvents: function(){
      $('#close-btn').on('click', function(e){$('body').removeClass('view');history.back()});
      $(window).scroll(function(){
        if($(window).scrollTop() >= 100){
          $('#header').addClass('fixed fadeInDown')
        }else{
          $('#header').removeClass('fixed fadeInDown')
        }
      });
      $(window).resize(function(){
        ui.data.avaibleHeight = $(window).height();
        if (ui.data.currentSlidesHeight != 0) {
          ui.data.target.height(ui.data.avaibleHeight);
          ui.data.currentSlidesHeight = ui.data.avaibleHeight;
        };
      })
    }

  }

    function spa(){

      var main_container = document.getElementById('main-container'),
      view_page_container = document.getElementById('view-page-container'),
      apiUrls = {
        contact: './index.php/api/page/contact.json',
        projects: './index.php/api/page/projects.json',
        recruitment: './index.php/api/page/recruitment.json',
        products: './index.php/api/page/products.json',
        about: './index.php/api/page/about.json',
        news: './index.php/api/page/news.json',
        home: './index.php/api/page/home.json',
        view: './index.php/api/post/'
      },

      RouterApp = {
        controller: function(args){
          var type = 'home';
          if (args.type!=undefined){ type = args.type };
          return {
            data: m.request({method: 'GET', url: apiUrls[type], deserialize: function(data){return data}})
                    .then(function(content){
                      var data = JSON.parse(content);
                      var tp = data.type;
                      if (tp==='json') {
                        return data
                      } else if(tp==='html'){
                        /*return m.request({method: 'GET', url: data.data, deserialize: function(data){return data}})
                          .then(function(content){
                            data.data = content;
                            return data;
                        })*/
                        return data
                      }else{
                        data.data = "<h2>数据错误</h2>"
                        return data;
                      }
                    })
          }
        },
        view: function(ctrl, args){
          $('body').removeClass('view');
          var data = ctrl.data();
          ui.setSlides(args.type=='home'?true:false);
          if(args.after!=undefined){args.after()}

          ui.initHeader();

          if(data.css!=undefined&&Object.keys(data.css).length>0){
            common_funcs.loadCss(data.css)
          }
          if(data.js!=undefined&&Object.keys(data.js).length>0){
            common_funcs.loadJs(data.js)
          }
          // R
          ui.intBgSection(data.banner);

          if(typeof(data.data)=="object"){
            return {}
          }else{
            return m.trust(data.data)
          }
        }
      },

      router = {
        view_content: {
          controller: function(){
            var view_id = m.route.param('id');
            console.log(apiUrls.view + view_id);
            return {
              data: m.request({method: 'GET', url: apiUrls.view + view_id, deserialize: function(data){return data}})
            }
          },
          view: function(ctrl){
            ui.setSlides(false);
            m.render(view_page_container, m.trust(ctrl.data()));
            scrollTo(0, 0);
            $('body').addClass('view');
            return ''
          }
        }
      },

      init = function(){
        //if(window.config!=undefined&&window.config.apiUrls!=undefined&&Object.keys(window.config.apiUrls).length>0){this.apiUrls = window.config.apiUrls}
        m.route.mode = "hash";
        m.route(main_container, "/", {
          "/": m.component(RouterApp, {type: 'home'}),
          "/about": m.component(RouterApp, {type: 'about'}),
          "/news": m.component(RouterApp, {type: 'news'}),
          "/products": m.component(RouterApp, {type: 'products'}),
          "/projects": m.component(RouterApp, {type: 'projects'}),
          "/recruitment": m.component(RouterApp, {type: 'recruitment'}),//router.recruitment,
          "/contact": m.component(RouterApp, {type: 'contact'}),
          "/view/:id": router.view_content,
        });
      };

      init()
    }


    Module = {

      spa: spa,
      ui: ui,

    };

    Module.spa(); // Single Page App
    Module.ui.init($('#slides')); // Something with the ui
    $('body').removeClass('loading')
});