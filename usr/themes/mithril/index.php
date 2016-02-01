<?php
/**
 * Mithril构建的SPA
 * 
 * @package Mithril Theme 
 * @author John
 * @version 1.0
 * @link http://jiusanzhou.github.io
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
require_once '/usr/plugins/Api/Twig/Autoloader.php';
Twig_Autoloader::register();
/**
 * 获取导航栏上的条目，即独立的页面
 * 需要转义
 * @param bool $isFull 是否全部内容
 * @return Array
 */
function getPages($db, $isFull = false){

    $target = $db->fetchAll($db
                ->select($isFull
                        ? 'slug, title, text'
                        : 'slug, title')
                ->from('table.contents')
                ->where(' type = ?' , "page")
                ->where(' slug != ? ', "index")
                ->order('order'));
    if($isFull){
        foreach ($target as $key => $value) {
            $resources = self::getResource( $value['cid']);
            $target[$key] = array_merge($target[$key], $resources);
            $_temp_text = self::getDetail('page', $value['cid'], 'cid');
            if(!empty($_temp_text)) $_temp_text = $_temp_text['text'];
            $target[$key]['type'] = is_null(json_decode($_temp_text))?'html':'json';
            $target[$key]['text'] = $_temp_text;
        }
    }
    return $target;
}
$_index = $this->db->fetchRow($this->db
            ->select('text')
            ->from('table.contents')
            ->where('type = ?', 'page')
            ->where('slug = ?', 'index'));
if(!empty($_index)){
    $loader = new Twig_Loader_Array(array(
            'index' => str_replace("<!--markdown-->", "", $_index['text']),
        ));
        $twig = new Twig_Environment($loader);
        $_headers = getPages($this->db);
        $data = array(
            'headers' => $_headers,
            'theme' => array(
                'url' => $this->options->themeUrl,
            ),
        );
        echo $twig->render('index', $data);//$_text;
}else{
    echo '未找到首页！';
}