<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * API接口插件
 * 
 * @package API
 * @author John
 * @version 0.0.1
 * @link http://jiusanzhou.github.io
 */
class API_Plugin implements Typecho_Plugin_Interface
{

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        self::doTable();
        Helper::addAction("api", "API_Action");
        Typecho_Plugin::factory('admin/custom-fields.php')->before  = array('API_Plugin', 'addParser');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array('API_Plugin', 'processContent');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishSave = array('API_Plugin', 'processContent');
        Helper::addRoute("ChildApi","/api/[arg1]/[arg2].json/","API_Action","detailAction");
        Helper::addRoute("MainApi","/api/[type].json/","API_Action","action");
        return(_t('开启网站API接口,访问路径为：' . "/api"));
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
        //Helper::removeRoute("ChildApi");
        Helper::removeRoute("MainApi");
        //Helper::removeAction("api");
        return(_t('关闭网站API接口'));
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){

        $apiUrl = new Typecho_Widget_Helper_Form_Element_Text('url', null, '/api', _t('路径'), _t('API访问路径，目前还不能确保与默URL冲突！'));
        $form->addInput($apiUrl);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 从$PageContentEditClass里的$request，
     * 获取Resources
     * 
     * @access public
     * @param array $request 数据结构体
     * @return array
     */
    public static function getResource($request){
        $resources = array();
        $_contents = $request->getArray('fieldContents');
        $_types    = $request->getArray('fieldTypes');
        $_isLinks  = $request->getArray('fieldIsLinks');

        if (!empty($_contents)) {
            foreach ($_isLinks as $key => $val) {
                $data = array(
                    'islink'  => intval($val),
                    'type'    => $_types[$key],
                    'content' => htmlspecialchars($_contents[$key], ENT_QUOTES)
                );
                $resources[] = $data;
            }
        }
        return $resources;
    }

    public static function saveBanner($request){

    }

    /**
     * 在将添加的资源加入数据库
     * 
     * @access public
     * @param array $post 数据结构体
     * @param Widget_Contents_Page_Edit $wcpe
     * @return array
     */
    public static function processContent($post, $PageContentEditClass){
        $db = Typecho_Db::get();
        $_cid = $PageContentEditClass->cid;
        $_img = $PageContentEditClass->request->bannerImg;
        $_title = $PageContentEditClass->request->bannerTitle;
        if(isset($_img)||isset($_title)){
            $_img = htmlspecialchars($_img, ENT_QUOTES);
            $_title = htmlspecialchars($_title, ENT_QUOTES);
            $_bid = $db->fetchRow($db->select('bid')
                    ->from('table.banners')
                    ->where('img = ? AND title = ?', $_img, $_title));
            if(empty($_bid)){
                $_bid = $db->query($db->insert('table.banners')
                        ->rows(array('img'=>$_img, 'title'=>$_title)));
            }else{
                $_bid = $_bid['bid'];
            }
            $db->query($db->update('table.contents')
                        ->rows(array('bid'=>$_bid))
                        ->where('cid = ?', $_cid));
        }else{
            /* 临时的处理方法 */
            $db->query($db->update('table.contents')
                        ->rows(array('bid'=>null))
                        ->where('cid = ?', $_cid));
        }
        $resources = self::getResource($PageContentEditClass->request);
        /* 临时的处理方法 */
        $db->query($db->delete('table.rls_resources')
            ->where('cid = ?', $_cid));
        foreach ($resources as $value) {
            if($value['content']){
                $_rid = $db->fetchRow($db->select('id')
                            ->from('table.resources')
                            ->where('content = ?', $value['content'])
                            ->limit(1));
                //file("http://127.0.0.1:8888/ssss/" . 'search_rid_is_' . $_rid['id']);
                //$_f = fopen('debug_file.txt', 'w');
                //fwrite($_f, $_rid[0] . json_encode($_rid));
                //fclose($_f);
                $_rid = $_rid['id'];
                if(empty($_rid)) {
                    $_rid = $db->query($db->insert('table.resources')
                            ->rows($value));
                }
                if(isset($_cid)&&isset($_rid)){
                    $_exits = $db->fetchRow($db->select('id')
                                ->from('table.rls_resources')
                                ->where('cid = ? AND rid = ?',$_cid,$_rid));
                    if(empty($_exits)) {
                        $db->query($db->insert('table.rls_resources')
                            ->rows(array('cid'=>$_cid,'rid'=>$_rid)));
                    }
                }
            }
        }
        return $post;
    }

    /**
     * 在发布页面页添加资源规则
     * 
     * @access public
     * @return void
     */
    public static function addParser($page){
        $resourceFields = isset($page) ? $page->getResourceFieldItems() : array();
        $banner = isset($page) ? $page->getBanner() : $post->getBanner();
        ?>
                    <section id="cf-resource" class="typecho-post-option">
                        <label id="cf-resource-expand" class="typecho-label"><a href="##"><i class="i-caret-right"></i> <?php _e('封面图文'); ?></a></label>
                        <table class="typecho-list-table mono">
                            <colgroup>
                                <col width="50%"/>
                                <col width="50%"/>
                            </colgroup>
                            <tr>
                                <td>
                                    <label for="fieldvalue"><?php _e('图片地址'); ?></label>
                                    <textarea name="bannerImg" id="banerImg" class="text-s w-100" rows="2"><?php echo empty($banner)?'':$banner['img']; ?></textarea>
                                </td>
                                <td>
                                    <label for="fieldvalue"><?php _e('文字说明'); ?></label>
                                    <textarea name="bannerTitle" id="banerTitle" class="text-s w-100" rows="2"><?php echo empty($banner)?'':$banner['title']; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </section>
                    <section id="cf-resource" class="typecho-post-option<?php if (empty($defaultResourceFields) && empty($resourceFields)): ?> fold<?php endif; ?>">
                        <label id="cf-resource-expand" class="typecho-label"><a href="##"><i class="i-caret-right"></i> <?php _e('异步资源'); ?></a></label>
                        <table class="typecho-list-table mono">
                            <colgroup>
                                <col width="10%"/>
                                <col width="10%"/>
                                <col width="0"/>
                                <col width="10%"/>
                            </colgroup>
                            <?php foreach ($resourceFields as $field): ?>
                            <tr>
                                <td>
                                    <label for="fieldislink" class="sr-only"><?php _e('是否是链接'); ?></label>
                                    <select name="fieldIsLinks[]" id="fieldislink">
                                        <option value="1"<?php if ('1' == $field['islink']): ?> selected<?php endif; ?>><?php _e('外链文件'); ?></option>
                                        <option value="0"<?php if ('0' == $field['islink']): ?> selected<?php endif; ?>><?php _e('源代码'); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <label for="fieldtype" class="sr-only"><?php _e('资源类型'); ?></label>
                                    <select name="fieldTypes[]" id="fieldtype">
                                        <option value="js"<?php if ('js' == $field['type']): ?> selected<?php endif; ?>><?php _e('JavaScript脚本'); ?></option>
                                        <option value="css"<?php if ('css' == $field['type']): ?> selected<?php endif; ?>><?php _e('CSS样式'); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <label for="fieldvalue" class="sr-only"><?php _e('内容'); ?></label>
                                    <textarea name="fieldContents[]" id="fieldcontent" class="text-s w-100" rows="10"><?php echo htmlspecialchars_decode($field['content']); ?></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs"><?php _e('删除'); ?></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($defaultResourceFields) && empty($resourceFields)): ?>
                            <tr>
                                <td>
                                    <label for="fieldislink" class="sr-only"><?php _e('是否是链接'); ?></label>
                                    <select name="fieldIsLinks[]" id="fieldislink">
                                        <option value="1"><?php _e('外链文件'); ?></option>
                                        <option value="0" selected><?php _e('源代码'); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <label for="fieldtype" class="sr-only"><?php _e('资源类型'); ?></label>
                                    <select name="fieldTypes[]" id="fieldtype">
                                        <option value="js" selected><?php _e('JavaScript脚本'); ?></option>
                                        <option value="css"><?php _e('CSS样式'); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="fieldContents[]" id="fieldcontent" class="text-s w-100" rows="2">console.log('Hello John!')</textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-xs"><?php _e('删除'); ?></button>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        <div class="description clearfix">
                            <button type="button" class="btn btn-xs operate-add"><?php _e('+添加资源'); ?></button>
                        </div>
                    </section>
        <?php

    }

    /**
     * 
     * @access private
     * @return void
     *
     * 创建资源表
     * +------+--------+---------+
     * | type | islink | content |
     * +------+--------+---------+
     * |  js  |    1   |    ""   |
     * +------+--------+---------+
     * |  css |    0   |    ""   |
     * +------+--------+---------+
     *
     * 创建资源-Relationships表
     * +------+------+
     * |  rid |  cid |
     * +------+------+
     * |  1   |  1   |
     * +------+------+
     */
    private function doTable(){
        $db            = Typecho_Db::get();
        $adapter       = $db->getAdapterName();
        $resources     = $db->getPrefix() . 'resources';
        $rls_resources = $db->getPrefix() . 'rls_resources';
        $banners     = $db->getPrefix() . 'banners';
        $rls_banners = $db->getPrefix() . 'rls_banners';

        $_query1_1       = "";
        $_query1_2       = "";
        $_query2_1       = "";
        $_query2_2       = "";
        switch (str_replace("Pdo_", "", $adapter)) {
            case 'SQLite':
                $_query1_1 = "CREATE TABLE IF NOT EXISTS ". $resources ." (
                           id INTEGER PRIMARY KEY, 
                           type TEXT,
                           islink INT,
                           content TEXT)";
                $_query1_2 = "CREATE TABLE IF NOT EXISTS ". $rls_resources ." (
                           id INTEGER PRIMARY KEY, 
                           cid INT,
                           rid INT)";
                $_query2_1 = "CREATE TABLE IF NOT EXISTS ". $banners ." (
                           bid INTEGER PRIMARY KEY, 
                           title TEXT,
                           img TEXT,
                           content TEXT)";
                /*$_query2_2 = "CREATE TABLE IF NOT EXISTS ". $rls_banners ." (
                           id INTEGER PRIMARY KEY, 
                           cid INT,
                           bid INT)";*/
                break;
            
            case 'Mysql':
                $_query1_1 = "CREATE TABLE IF NOT EXISTS ". $resources ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `type` varchar(10) NOT NULL,
                           `islink` tinyint(1) DEFAULT 0,
                           `content` text,
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                $_query1_2 = "CREATE TABLE IF NOT EXISTS ". $rls_resources ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `rid` int(10),
                           `cid` int(10),
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                $_query2_1 = "CREATE TABLE IF NOT EXISTS ". $banners ." (
                           `bid` int(8) NOT NULL AUTO_INCREMENT,
                           `img` varchar(255) NOT NULL,
                           `title` varchar(255),
                           PRIMARY KEY (`bid`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                /*$_query2_2 = "CREATE TABLE IF NOT EXISTS ". $rls_resources ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `bid` int(10),
                           `cid` int(10),
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";*/
                break;

            default:
                $_query1_1 = "CREATE TABLE IF NOT EXISTS ". $resources ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `type` varchar(10) NOT NULL,
                           `islink` tinyint(1) DEFAULT 0,
                           `content` text,
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                $_query1_2 = "CREATE TABLE IF NOT EXISTS ". $rls_resources ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `rid` int(10),
                           `cid` int(10),
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                $_query2_1 = "CREATE TABLE IF NOT EXISTS ". $banners ." (
                           `bid` int(8) NOT NULL AUTO_INCREMENT,
                           `img` varchar(255),
                           `title` varchar(255),
                           PRIMARY KEY (`bid`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
                /*$_query2_2 = "CREATE TABLE IF NOT EXISTS ". $rls_banners ." (
                           `id` int(8) NOT NULL AUTO_INCREMENT,
                           `bid` int(10),
                           `cid` int(10),
                           PRIMARY KEY (`id`)
                           ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";*/
                break;
        }
        if($_query1_1) $db->query($_query1_1);
        if($_query1_2) $db->query($_query1_2);
        if($_query2_1) $db->query($_query2_1);
        //if($_query2_2) $db->query($_query1_2);
        $_cls = $db->fetchRow($db->select('*')
                ->from("table.contents")
                ->where(1));
        if(!array_key_exists("bid", $_cls)){
            $db->query("ALTER TABLE " .$db->getPrefix() ."contents" ." ADD COLUMN `bid` int(10)");
        }
    }
}
