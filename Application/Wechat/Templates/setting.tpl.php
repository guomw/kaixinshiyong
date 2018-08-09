<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
    $show_header = FALSE;
	include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-6">
<div class="common-form">
    <div class="explain-col">
        微信端所有功能均针对买家用户开发，不支持商家用户任何操作及通知。在使用本功能前，请确认您已购买 专业版 授权并已通过微信服务号。<br>
        <b class="red">请在自定义配置微信菜单前，充分了解所配置项所代表的含义。以免带来意外导致不可用。</b><br>
        <b class="green">由于微信限制，默认最多只能添加3个一级分类（各分类可添加5个二级分类），若您需要添加自己的菜单，需要禁止内置菜单后再添加。</b>
    </div>

<form name="myform" action="<?php echo U(ACTION_NAME); ?>" method="post" id="myform">
<table width="100%" class="table_form">
    <tr>
        <td  width="200">开启微信端</td>
        <td>
            <label><input name="setting[enable]" value="1" type="radio" id="wap_enable" <?php if($setting['enable'] == 1) {?>checked<?php }?>> 开启</label>
            <label><input name="setting[enable]" value="0" type="radio" id="wap_enable" <?php if($setting['enable'] == 0) {?>checked<?php }?>> 关闭</label>
        </td>
    </tr>
    
    <tr>
        <td>Token(令牌)</td>
       <td><input type="text" name="setting[options][token]" value="<?php echo $setting['options']['token']?>" style="width:300px"></td>
    </tr>
    
    <tr>
        <td>EncodingAESKey(消息加解密密钥)</td>
        <td><input type="text" name="setting[options][encodingaeskey]" value="<?php echo $setting['options']['encodingaeskey']?>" style="width:300px"></td>
    </tr>
    
    <tr>
        <td>AppID(应用ID)</td>
        <td><input type="text" name="setting[options][appid]" value="<?php echo $setting['options']['appid']?>" style="width:300px"></td>
    </tr>   
    
    <tr>
        <td>AppSecret(应用密钥)</td>
        <td><input type="text" name="setting[options][appsecret]" value="<?php echo $setting['options']['appsecret']?>" style="width:300px"></td>
    </tr>
    
    <tr>
        <td>微信即时通知<br/><a href="http://help. .cn/?/question/14" target="_blank" rel="nofollow" class="blue">微信模板配置教程</a></td>
        <td class="notify">
            <label><input type="checkbox" data-id="account_bind" name="setting[notify][account_bind][enabled]" value="1" <?php if($setting['notify']['account_bind']['enabled'] == 1) {?>checked<?php }?>/>&nbsp;帐户绑定通知</label>&nbsp;
            <label id="label_account_bind" <?php if(!$setting['notify']['account_bind']['enabled']) {?>style="display:none;"<?php }?>>（模板消息ID）<input type="text" name="setting[notify][account_bind][template_id]" value="<?php echo $setting['notify']['account_bind']['template_id'] ?>" style="width:300px;"></label>
            <br>
            <label><input type="checkbox" data-id="pay_cash_check" name="setting[notify][pay_cash_check][enabled]" value="1" <?php if($setting['notify']['pay_cash_check']['enabled'] == 1) {?>checked<?php }?>/>&nbsp;提现审核通知</label>&nbsp;
            <label id="label_pay_cash_check" <?php if(!$setting['notify']['pay_cash_check']['enabled']) {?>style="display:none;"<?php }?>>（模板消息ID）<input type="text" name="setting[notify][pay_cash_check][template_id]" value="<?php echo $setting['notify']['pay_cash_check']['template_id'] ?>" style="width:300px;"></label>
            <br>
            <label><input type="checkbox" data-id="order_check_trade_no" name="setting[notify][order_check_trade_no][enabled]" value="1" <?php if($setting['notify']['order_check_trade_no']['enabled']) {?>checked<?php }?>/>&nbsp;订单审核通知</label>&nbsp;
            <label id="label_order_check_trade_no" <?php if(!$setting['notify']['order_check_trade_no']['enabled']) {?>style="display:none;"<?php }?>>（模板消息ID）<input type="text" name="setting[notify][order_check_trade_no][template_id]" value="<?php echo $setting['notify']['order_check_trade_no']['template_id'] ?>" style="width:300px;"></label>
            <br>
            <label><input type="checkbox" data-id="order_balance" name="setting[notify][order_balance][enabled]" value="1" <?php if($setting['notify']['order_balance']['enabled']) {?>checked<?php }?>/>&nbsp;订单结算通知</label>&nbsp;
            <label id="label_order_balance" <?php if(!$setting['notify']['order_balance']['enabled']) {?>style="display:none;"<?php }?>>（模板消息ID）<input type="text" name="setting[notify][order_balance][template_id]" value="<?php echo $setting['notify']['order_balance']['template_id'] ?>" style="width:300px;"></label>
            <br>
            <label><input type="checkbox" data-id="order_appeal_arbitration" name="setting[notify][order_appeal_arbitration][enabled]" value="1" <?php if($setting['notify']['order_appeal_arbitration']['enabled']) {?>checked<?php }?>/>&nbsp;申诉处理通知</label>&nbsp;
            <label id="label_order_appeal_arbitration" <?php if(!$setting['notify']['order_appeal_arbitration']['enabled']) {?>style="display:none;"<?php }?>>（模板消息ID）<input type="text" name="setting[notify][order_appeal_arbitration][template_id]" value="<?php echo $setting['notify']['order_appeal_arbitration']['template_id'] ?>" style="width:300px;"></label>
        </td>
    </tr>
    
    <tr>
        <td>微信菜单配置</td>
        <td>
            <table width="100%" cellspacing="0" class="table-list">
                <thead>
                    <tr>
                        <th width="38" style="text-align:center;">排序</th>
                        <th style="text-align:left;">名称</th>
                        <th width="120" style="text-align:center;">类型</th>
                        <th width="200" style="text-align:center;">返回信息</th>
                        <th width="80" style="text-align:center;">状态</th>
                        <th width="80" style="text-align:center;">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($setting['menu']['button'] as $key => $value): ?>
                    <?php if ($value['type'] == 'click' && in_array($value['key'], $clicks)): ?>
                    <input type="hidden" name="setting[menu][button][<?php echo $key ?>][type]" value="<?php echo $value['type'] ?>">
                    <input type="hidden" name="setting[menu][button][<?php echo $key ?>][key]" value="<?php if ($value['type'] == 'click'): ?><?php echo $value['key'] ?><?php else: ?><?php echo $value['url'] ?><?php endif ?>">
                    <?php endif ?>
                    <?php $value['status'] = (int) $value['status'] ?>           
                    <tr class="menu" data-menu="<?php echo $key ?>" data-status="<?php echo $value['status'] ?>">
                        <td align="center">↑ ↓</td>
                        <td><input type="text" name="setting[menu][button][<?php echo $key ?>][name]" value="<?php echo $value['name'] ?>" class="input-text"> <a href="javascript:;" onclick="addsubmenu(this);">添加子菜单</a> </td>
                        <td align="center" class="menu_type">
                            <select name="setting[menu][button][<?php echo $key ?>][type]" <?php if ($value['sub_button']): ?>style="display:none;"<?php endif ?> <?php if ($value['type'] == 'click' && in_array($value['key'], $clicks)): ?>disabled<?php endif ?>>
                                <option value="click"<?php if ($value['type'] == 'click'): ?>selected<?php endif ?>>事件回复</option>
                                <option value="view" <?php if ($value['type'] == 'view'): ?>selected<?php endif ?>>跳转地址</option>
                            </select>
                        </td>
                        <td align="center" class="menu_key"><input type="text" name="setting[menu][button][<?php echo $key ?>][key]" style="width:98%;<?php if ($value['sub_button']): ?>display:none;<?php endif ?>" value="<?php if ($value['type'] == 'click'): ?><?php echo $value['key'] ?><?php else: ?><?php echo $value['url'] ?><?php endif ?>" <?php if ($value['type'] == 'click' && in_array($value['key'], $clicks)): ?>disabled<?php endif ?>></td>
                        <td align="center">
                            <a href="javascript:;" onclick="setstate(this)" <?php if ($value['status'] == 0): ?>class="red"<?php endif ?>><?php if ($value['status'] == 1): ?>启用<?php else: ?>禁用<?php endif ?></a>
                            <input type="hidden" class="status" name="setting[menu][button][<?php echo $key ?>][status]" value="<?php echo $value['status'] ?>">
                        </td>
                        <td align="center">
                        <?php if (($value['type'] == 'click' && in_array($value['key'], $clicks)) || !empty($value['sub_button'])): ?>
                            <font color="#cccccc">删除</font>
                        <?php else: ?>
                            <a href="javascript:;" onclick="delsubmenu(this)">删除</a>
                        <?php endif ?>
                        </td>
                        <?php foreach ($value['sub_button'] as $k => $v): ?>
                        <?php if ($v['type'] == 'click' && in_array($v['key'], $clicks)): ?>
                        <input type="hidden" name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][type]" value="<?php echo $v['type'] ?>">
                        <input type="hidden" name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][key]" value="<?php if ($v['type'] == 'click'): ?><?php echo $v['key'] ?><?php else: ?><?php echo $v['url'] ?><?php endif ?>">
                        <?php endif ?>
                        <?php $v['status'] = (int) $v['status']; ?>
                    <tr class="submenu" data-submenu="<?php echo $key ?>" data-status="<?php echo $v['status'] ?>">
                        <td align="center">↑ ↓</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <input type="text" name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][name]" value="<?php echo $v['name'] ?>" class="input-text"></td>
                        <td align="center">
                            <select name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][type]" <?php if ($v['type'] == 'click' && in_array($v['key'], $clicks)): ?>disabled<?php endif ?>>
                                <option value="click" <?php if ($v['type'] == 'click'): ?>selected<?php endif ?>>事件回复</option>
                                <option value="view" <?php if ($v['type'] == 'view'): ?>selected<?php endif ?>>跳转地址</option>
                            </select>
                        </td>
                        <td align="center"><input type="text" name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][key]" value="<?php if ($v['type'] == 'click'): ?><?php echo $v['key'] ?><?php else: ?><?php echo $v['url'] ?><?php endif ?>" class="input-text" style="width:98%" <?php if ($v['type'] == 'click' && in_array($v['key'], $clicks)): ?>disabled<?php endif ?>></td>
                        <td align="center">
                            <a href="javascript:;" onclick="setstate(this)" <?php if ($v['status'] == 0): ?>class="red"<?php endif ?>><?php if ($v['status'] == 1): ?>启用<?php else: ?>禁用<?php endif ?></a>
                            <input type="hidden" class="status" name="setting[menu][button][<?php echo $key ?>][sub_button][<?php echo $k ?>][status]" value="<?php echo $v['status'] ?>">
                        </td>
                        <td align="center">
                        <?php if ($v['type'] == 'click' && in_array($v['key'], $clicks)): ?>
                            <font color="#cccccc">删除</font>
                        <?php else: ?>
                            <a href="javascript:;" onclick="delsubmenu(this)">删除</a>
                        <?php endif ?>
                        </td>
                    </tr>                            
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
                    <tr class="end">
                        <td colspan="6">
                            <a href="javascript:;" onclick="addmenu(this);">添加一级菜单</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<script type="text/javascript">
var $options = '<option value="click">事件回复</option><option value="view" selected>跳转地址</option>';
var $clicks = <?php echo json_encode($clicks) ?>;
function addmenu (o) {
    var tr = $(o).parents('tr.end');
    var menu_num = $('tr.menu').length;
    if($('tr.menu[data-status=1]').length >= 3) {
        alert('您最多只能添加3个一级菜单');
        return false;
    }
    var _html = '<tr class="menu" data-menu="'+ menu_num +'" data-status="1">';
    _html += '<td align="center">↑ ↓</td>';
    _html += '<td><input name="setting[menu][button]['+ menu_num +'][name]" type="text" class="input-text"><a href="javascript:;" onclick="addsubmenu(this);">添加子菜单</a></td>';
    _html += '<td align="center" class="menu_type"><select name="setting[menu][button]['+ menu_num +'][type]">'+ $options +'</select></td>';
    _html += '<td align="center" class="menu_key"><input type="text" name="setting[menu][button]['+ menu_num +'][key]" class="input-text" style="width:98%"></td>';
    _html += '<td align="center"><a href="javascript:;" onclick="setstate(this)">启用</a><input type="hidden" class="status" name="setting[menu][button]['+ menu_num +'][status]" value="1"></td>';
    _html += '<td align="center">删除</td>';       
    _html += '</tr>';
    $(tr).before(_html);
}

function addsubmenu (o) {
    var tr = $(o).parents('tr.menu');
    var menu_id = tr.attr('data-menu');
    var menu_num = $("tr.submenu[data-submenu='"+ menu_id +"']").length;
    if($("tr.submenu[data-submenu='"+ menu_id +"'][data-status=1]").length >= 5) {
        alert('您最多只能添加5个二级菜单');
        return false;
    }
    var _html = '<tr class="submenu" data-submenu="'+ menu_id +'" data-status="1">';
    _html += '<td align="center">↑ ↓</td>';
    _html += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─ <input type="text" name="setting[menu][button]['+menu_id+'][sub_button]['+ menu_num +'][name]" value="" class="input-text"></td>';
    _html += '<td align="center"><select name="setting[menu][button]['+menu_id+'][sub_button]['+ menu_num +'][type]">'+ $options +'</select></td>';
    _html += '<td align="center"><input type="text" name="setting[menu][button]['+menu_id+'][sub_button]['+ menu_num +'][key]" class="input-text" style="width:98%"></td>';
    _html += '<td align="center"><a href="javascript:;" onclick="setstate(this)">启用</a><input type="hidden" class="status" name="setting[menu][button]['+ menu_id +'][sub_button]['+ menu_num +'][status]" value="1"></td>';
    _html += '<td align="center"><a href="javascript:;" onclick="delsubmenu(this)">删除</a></td>';             
    _html += '</tr>';
    if(menu_num > 0) {
        $("tr.submenu[data-submenu='"+ menu_id +"']:last").after(_html);
    } else {
        tr.find('.menu_type').children('select').fadeOut();
        tr.find('.menu_key').children('input').fadeOut();
        tr.after(_html);
    }
}

function delsubmenu (o) {
    var tr = $(o).parents('tr.submenu');
    var menu_id = tr.attr('data-submenu');
    var menu_num = $("tr.submenu[data-submenu='"+ menu_id +"']").length;
    $(o).parents('tr.submenu').fadeOut(function(){$(this).remove()});
    if(menu_num == 1) {
        $("tr.menu[data-menu='"+ menu_id +"']").find('.menu_type').children('select').fadeIn();
        $("tr.menu[data-menu='"+ menu_id +"']").find('.menu_key').children('input').fadeIn();        
    }
}

function setstate (o) {
    var tr = $(o).parent().parent('tr');
    if(tr.hasClass('submenu')) {
        var menu_id = tr.attr('data-submenu');
        if(tr.attr('data-status') == 1) {
            tr.attr('data-status', 0);
            $(o).addClass('red').text('禁用');
            tr.find("input.status").attr("value", 0);
        } else {
            if($("tr.submenu[data-submenu='"+ menu_id +"'][data-status=1]").length >= 5) {
                alert('您最多只能启用5个二级菜单');
                return false;
            }
            tr.attr('data-status', 1);
            $(o).removeClass('red').text('启用');
            tr.find("input.status").attr("value", 1);
        }
    } else {
        var menu_id = tr.attr('data-menu');
        if(tr.attr('data-status') == 1) {
            tr.attr('data-status', 0);
            tr.find("input.status").attr("value", 0);
            $("tr.submenu[data-submenu='"+ menu_id +"']").find("input.status").attr("value", 0);
            $("tr.submenu[data-submenu='"+ menu_id +"']").attr('data-status', 0);
            $(o).addClass('red').text('禁用');
            $("tr.submenu[data-submenu='"+ menu_id +"']").find('td:eq(4) > a').addClass('red').text('禁用'); 
        } else {
            if($("tr.submenu[data-submenu='"+ menu_id +"']").length > 5) {
                alert('当前二级分类超过5项，您不能批量启用');
                return false;
            }
            tr.attr('data-status', 1);
            tr.find("input.status").attr("value", 1);
            $("tr.submenu[data-submenu='"+ menu_id +"']").find("input.status").attr("value", 1);
            $("tr.submenu[data-submenu='"+ menu_id +"']").attr('data-status', 1);
            $(o).removeClass('red').text('启用');
            $("tr.submenu[data-submenu='"+ menu_id +"']").find('td:eq(4) > a').removeClass('red').text('启用');
        }
    }
}

$(function(){
    $(".notify input[type=checkbox]").click(function(){
        if($(this).attr('checked') == 'checked') {
            $("label#label_" + $(this).attr('data-id')).show();
        } else {
            $("label#label_" + $(this).attr('data-id')).hide();
        }
    })
})
</script>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="保存微信设置" class="button" id="dosubmit">
</form>
</div>
</body>
</html>