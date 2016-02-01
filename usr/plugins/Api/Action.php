<?php
class API_Action extends Typecho_Widget implements Widget_Interface_Do {

    const VERSION = "0.0.1";
    const SITE_NAME = "Jiusanzhou";

    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->db = Typecho_Db::get();
        $this->options = Helper::options();
        //$this->pluginRootUrl = Typecho_Common::url('Api/', $this->options->pluginUrl);
        require_once 'Twig/Autoloader.php';
        Twig_Autoloader::register();
    }

    public function execute() {
        //Do nothing
    }

    /**
     * 返回网站信息
     * 
     * @return {}
     */
    public function getSite() {
        $_cats = $this->db->fetchAll($this->db
            ->select('slug, name, mid, count, description')
            ->from('table.metas')
            ->where('type = ?', 'category'));
        $_cat_item = array();
        $res = array();
        foreach ($_cats as $key => $value) {
            $_articles = $this->db->fetchAll($this->db
                        ->select()
                        ->from('table.contents')
                        ->join('table.relationships', 'table.relationships.cid = table.contents.cid')
                        ->where('table.relationships.mid = ?', $value['mid']));
            $_cat_item['articles'] = $_articles;
            $_cat_item['count'] = $value['count'];
            $_cat_item['name'] = $value['name'];
            $_cat_item['description'] = $value['description'];
            $res[$value['slug']] = $_cat_item;
        }
        return $res;
    }

    /**
     * 返回所有的文章
     * news:
     *      count:
     *      articles:
     *             [
     *                  author:
     *                  title:
     *                  time:
     *                  excerp:
     *              ]
     * @return {}
     */
    public function getPosts() {
        $_cats = $this->db->fetchAll($this->db
            ->select('slug, name, mid, count, description')
            ->from('table.metas')
            ->where('type = ?', 'category'));
        $_cat_item = array();
        $res = array();
        foreach ($_cats as $key => $value) {
            $_articles_db = $this->db->fetchAll($this->db
                        ->select('table.contents.slug, table.contents.cid, table.contents.text, table.contents.title, table.contents.created, table.users.screenName')
                        ->from('table.contents')
                        ->join('table.relationships', 'table.relationships.cid = table.contents.cid')
                        ->join('table.users', 'table.users.uid = table.contents.authorId')
                        ->where('table.contents.status = ?', 'publish')
                        ->where('table.contents.type = ?', 'post')
                        ->where('table.relationships.mid = ?', $value['mid']));
            //$_articles = array();
            foreach ($_articles_db as $key => $_value_article) {
                //$_value_article['slug'] = $_value_article[];
                //$_value_article['title'] = $_value_article[];
                $_value_article['author'] = $_value_article['screenName'];
                unset($_value_article['screenName']);
                $_value_article['excerp'] = Typecho_Common::subStr(strip_tags($_value_article['text']), 0, 100, '...');
                unset($_value_article['text']);
                $_value_article['time'] = $_value_article['created'];
                unset($_value_article['created']);
                /* 最好在上面内联选择出来 */
                $_value_article['banner'] = self::getBanner($_value_article['cid']);
                $_articles_db[$key] = $_value_article;
                //$_articles[] = $_value_article;
            }
            $_cat_item['articles'] = $_articles_db;
            $_cat_item['count'] = $value['count'];
            $_cat_item['name'] = $value['name'];
            $_cat_item['description'] = $value['description'];
            $res[$value['slug']] = $_cat_item;
        }
        return $res;
    }

    public function detailAction(){
        $arg1 = $this->request->arg1;
        $arg2 = $this->request->arg2;
        $_res =  self::getDetail($arg1, $arg2);
        if('page'==$arg1){
            $res = array();
            if(!empty($_res)){
                $_text = $_res['text'];
                $_cid = $_res['cid'];
                $_resources = self::getResource($_cid);
                $res['type'] = is_null(json_decode($_text))?'html':'json';
                $loader = new Twig_Loader_Array(array(
                    'page' => $_text,
                ));
                $twig = new Twig_Environment($loader);
                $_pages = array(
                    'posts' => self::getPosts(),
                    'theme' => array(
                        'url' => $this->options->themeUrl,
                    ),
                );
                $res['banner'] = self::getBanner($_cid);
                $res['data'] = $twig->render('page', $_pages);//$_text;
                $res = array_merge($res, $_resources);
            }
            (new Typecho_Response())->throwJson($res);
        }elseif('post'==$arg1){
            if(!empty($_res)) echo $_res['text'];
        }
    }

    public function getBanner($cid){
        $banner = array(
            'img' => '',
            'title' => ''
        );

        $_bid = $this->db->fetchRow($this->db->select('bid')
                ->from('table.contents')
                ->where('cid = ?', $cid));
        if(!empty($_bid)){
            $_banner = $this->db->fetchRow($this->db->select('img, title')
                ->from('table.banners')
                ->where('bid = ?', $_bid['bid']));
            if(!empty($_banner)){
                $banner['img'] = htmlspecialchars_decode($_banner['img'], ENT_QUOTES);
                $banner['title'] = htmlspecialchars_decode($_banner['title'], ENT_QUOTES);
            }
        }
        return $banner;
    }

    public function getResource($cid){
        $js  = array(
            'scripts' => array(),
            'code' => array()
        );
        $css = array(
            'links'     => array(),
            'style'     => array()
        );
        if(isset($cid)){
            $_res = $this->db->fetchAll($this->db
                                ->select('table.resources.type, table.resources.islink, table.resources.content')
                                ->from('table.resources')
                                ->join('table.rls_resources', 'table.rls_resources.rid = table.resources.id')
                                ->where('cid = ?', $cid));
            foreach ($_res as $_resv) {
                switch ($_resv['type']) {
                    case 'css':
                        $_resv['islink']
                        ? $css['links'][]   = htmlspecialchars_decode($_resv['content'], ENT_QUOTES)
                        : $css['style'][]   = htmlspecialchars_decode($_resv['content'], ENT_QUOTES);
                        break;

                    case 'js':
                        $_resv['islink']
                        ? $js['scripts'][] = htmlspecialchars_decode($_resv['content'], ENT_QUOTES)
                        : $js['code'][]    = htmlspecialchars_decode($_resv['content'], ENT_QUOTES);
                        break;

                    default:
                        break;
                }
            }
        }
        $res = array(
            'js' => $js,
            'css' => $css
        );
        return $res;
    }

    public function getDetail($arg1, $arg2, $type='slug'){
        $res = array();
        if('page'==$arg1||'post'==$arg1){
            $_res = $this->db->fetchRow($this->db
                        ->select('text, cid')
                        ->from('table.contents')
                        ->where('type = ?', $arg1)
                        ->where('cid'==$type?'cid = ?' : 'slug = ?', $arg2));
        }
        if(!empty($_res)) {
            $res['text'] = str_replace("<!--markdown-->", "", $_res['text']);
            $res['cid'] = 'cid'==$type?$arg2:$_res['cid'];
        };
        return $res;
    }

    public function action(){
        $type = $this->request->type;
        $res = Array(
            "site" => self::SITE_NAME,
            "version" => self::VERSION,
        );
        switch ($type) {
            case 'config':
                $res["data"] = Array();
                break;
            
            case 'pages':
                $res["data"] = self::getPages(false);
                break;
            
            case 'pagesfull':
                $res["data"] = self::getPages();
                break;
            
            case 'posts':
                $res["data"] = self::getPosts();
                break;

            default:
                break;
        }
        (new Typecho_Response())->throwJson($res);
    }

    /**
     * 获取导航栏上的条目，即独立的页面
     * 需要转义
     * @param bool $isFull 是否全部内容
     * @return Array
     */
    private function getPages($isFull = true){

        $target = $this->db->fetchAll($this->db
                    ->select($isFull
                            ? 'cid, slug, title, text'
                            : 'cid, slug, title')
                    ->from('table.contents')
                    ->where(' type = ?' , "page")
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
}