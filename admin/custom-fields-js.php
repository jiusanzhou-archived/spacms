<?php if(!defined('__TYPECHO_ADMIN__')) exit; ?>
<script>
$(document).ready(function () {
    // 自定义字段
    $('#custom-field-expand').click(function() {
        var btn = $('i', this);
        if (btn.hasClass('i-caret-right')) {
            btn.removeClass('i-caret-right').addClass('i-caret-down');
        } else {
            btn.removeClass('i-caret-down').addClass('i-caret-right');
        }
        $(this).parent().toggleClass('fold');
        return false;
    });
    // 异步加载资源
    $('#cf-resource-expand').click(function() {
        var btn = $('i', this);
        if (btn.hasClass('i-caret-right')) {
            btn.removeClass('i-caret-right').addClass('i-caret-down');
        } else {
            btn.removeClass('i-caret-down').addClass('i-caret-right');
        }
        $(this).parent().toggleClass('fold');
        return false;
    });

    function attachDeleteEvent (el) {
        $('button.btn-xs', el).click(function () {
            if (confirm('<?php _e('确认要删除此字段吗?'); ?>')) {
                $(this).parents('tr').fadeOut(function () {
                    $(this).remove();
                });
            }
        });
    }

    $('#custom-field table tbody tr').each(function () {
        attachDeleteEvent(this);
    });
    $('#cf-resource table tbody tr').each(function () {
        attachDeleteEvent(this);
    });

    $('#cf-resource button.operate-add').click(function () {
        var html = '<tr><td><select name="fieldIsLinks[]" id="">'
                + '<option value="1"><?php _e('外链文件'); ?></option>'
                + '<option value="0"><?php _e('源代码'); ?></option>'
                + '</select></td>'
                + '<td><select name="fieldTypes[]" id="">'
                + '<option value="js"><?php _e('JavaScript脚本'); ?></option>'
                + '<option value="css"><?php _e('CSS样式'); ?></option>'
                + '</select></td>'
                + '<td><textarea name="fieldContents[]" placeholder="<?php _e('内容'); ?>" class="text-s w-100" rows="2"></textarea></td>'
                + '<td><button type="button" class="btn btn-xs"><?php _e('删除'); ?></button></td></tr>',
            el = $(html).hide().appendTo('#cf-resource table tbody').fadeIn();

        attachDeleteEvent(el);
    });
    $('#custom-field button.operate-add').click(function () {
        var html = '<tr><td><input type="text" name="fieldNames[]" placeholder="<?php _e('字段名称'); ?>" class="text-s w-100"></td>'
                + '<td><select name="fieldTypes[]" id="">'
                + '<option value="str"><?php _e('字符'); ?></option>'
                + '<option value="int"><?php _e('整数'); ?></option>'
                + '<option value="float"><?php _e('小数'); ?></option>'
                + '</select></td>'
                + '<td><textarea name="fieldValues[]" placeholder="<?php _e('字段值'); ?>" class="text-s w-100" rows="2"></textarea></td>'
                + '<td><button type="button" class="btn btn-xs"><?php _e('删除'); ?></button></td></tr>',
            el = $(html).hide().appendTo('#custom-field table tbody').fadeIn();

        attachDeleteEvent(el);
    });
});
</script>
