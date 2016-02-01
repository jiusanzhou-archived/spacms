<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Qiniu 这是七牛云空间的插件
 * 
 * @package Qiniu
 * @author John
 * @version 0.0.1
 * @link http://jiusanzhou.github.io
 */
class Qiniu_Plugin implements Typecho_Plugin_Interface
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
        if (false == Typecho_Http_Client::get()) {
            throw new Typecho_Plugin_Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
        }
        Typecho_Plugin::factory('admin/menu.php')->navBar = array('Qiniu_Plugin', 'render');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $id = new Typecho_Widget_Helper_Form_Element_Textarea('appid', NULL, NULL, _t('认证ID'), _t('此密钥需要向服务提供商注册<br />
        它是一个用于表明您合法用户身份的字符串'));
        $form->addInput($id->addRule('required', _t('您必须填写一个认证ID')));

        $key = new Typecho_Widget_Helper_Form_Element_Textarea('key', NULL, NULL, _t('服务密钥'), _t('此密钥需要向服务提供商注册<br />
        它是一个用于表明您合法用户身份的字符串'));
        $form->addInput($key->addRule('required', _t('您必须填写一个服务密钥')));
        
        $bucket = new Typecho_Widget_Helper_Form_Element_Text('bucket', NULL, NULL,
        _t('容器名称'), _t('必要的保存区间'));
        $form->addInput($bucket->addRule('text', _t('您使用的容器名错误')));

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
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        echo '<span class="message success">'
            . htmlspecialchars(Typecho_Widget::widget('Widget_Options')->plugin('Qiniu')->word)
            . '</span>';
    }
}
