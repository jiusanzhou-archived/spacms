(function(){
var baiduMap = function(data){
    var map = new BMap.Map("dituContent");//在百度地图容器中创建一个地
    var point = new BMap.Point(data.point.x, data.point.y);//定义一个中心点坐标
    map.centerAndZoom(point,18);//设定地图的中心点和坐标并将地图显示在地图容器中
    window.map = map;//将map变量存储在全局

    map.enableDragging();//启用地图拖拽事件，默认启用(可不写)
    map.enableScrollWheelZoom();//启用地图滚轮放大缩小
    map.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
    map.enableKeyboard();//启用键盘上下左右键移动地图

    //向地图中添加缩放控件
    var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
    map.addControl(ctrl_nav);
          //向地图中添加缩略图控件
    var ctrl_ove = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:1});
    map.addControl(ctrl_ove);
          //向地图中添加比例尺控件
    var ctrl_sca = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
    map.addControl(ctrl_sca);

    var content = data.content?data.content:'';
    var title = data.title?data.title:'';
    var searchInfoWindow = new BMapLib.SearchInfoWindow(map, content, {
      title  : title,      //标题
      width  : 290,             //宽度
      height : 105,              //高度
      panel  : "panel",         //检索结果面板
      enableAutoPan : true,     //自动平移
      searchTypes   :[
        BMAPLIB_TAB_SEARCH,   //周边检索
        BMAPLIB_TAB_TO_HERE,  //到这里去
        BMAPLIB_TAB_FROM_HERE //从这里出发
      ]
    });

    var marker = new BMap.Marker(point); //创建marker对象
    var label = new BMap.Label("肥西县繁华西路与文山路交口");
    label.setStyle({
       color : "#ed2d2d",
       fontSize : "1rem",
       height : "2rem",
       lineHeight : "2rem",
       marginLeft: "2rem",
       border: "1px solid #ed2d2d",
       padding: "0 1rem",
       fontFamily: "STHeiti,微软雅黑,Microsoft YaHei"
    });
    marker.setLabel(label);
    marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
    marker.addEventListener("click", function(e){
      searchInfoWindow.open(marker);
    });

    label.addEventListener("click", function(e){
      searchInfoWindow.open(marker);
    });
    map.addOverlay(marker); //在地图中添加marker
    setTimeout(function(){
      $('.anchorBL').css('display', 'none');
    }, 1000)
};
var data = {
    point: {x: 117.151993, y: 31.788907},
    title: '玄武科技',
    content: '<div style="margin:0;line-height:20px;padding:2px;">' +
        '地址：北京市海淀区上地十街10号<br/>电话：(010)59928888<br/>简介：百度大厦位于北京市海淀区西二旗地铁站附近，为百度公司综合研发及办公总部。' +
      '</div>'
};
baiduMap(data)
})()