(function($){
    function menuTabs(){
      var menu_item = $('.menu-item'),
          article_item = $('.article-item'),
          current_id = 0;
      $(article_item[current_id]).css('display', 'block');
      $('#menu-article-' + current_id).addClass('show');
      $('.menu-item').on('click', function(e){
        var _c = $(e.currentTarget),
            _id = _c.attr('for-article');
        if (_id != current_id) {
          $(menu_item[current_id]).removeClass('actived');
          $(article_item[current_id]).removeClass('show');
          var _c_id = current_id;
          setTimeout(function(){
            $(article_item[_c_id]).css('display', 'none');
          }, 300);
          current_id = _id;
          $(menu_item[_id]).addClass('actived');
          $(article_item[current_id]).css('display', 'block');
          setTimeout(function(){
            $(article_item[_id]).addClass('show');
          }, 0);
        }
      })
    }
    setTimeout(function(){
      menuTabs()
    }, 200)
})($)