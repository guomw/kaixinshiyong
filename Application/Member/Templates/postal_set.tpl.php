<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="postal"/>
<div class="pad-10">
    <div class="col-tab">
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
        <tr>
                <th><?php echo L('活动状态')?></th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[postal_isopen]" value="1" <?php if($setting['postal_isopen'] == 1){?>checked<?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[postal_isopen]" value="0" <?php if($setting['postal_isopen'] == 0){?>checked<?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('活动价名称')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[postal_price_name]" value="<?php echo $setting['postal_price_name'];?>"/>&nbsp;例如：包邮价，用于前台展示
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('活动别名')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[postal_name]" value="<?php if(!$setting['postal_name']){echo '九块九包邮';}else{echo $setting['postal_name'];}?>"/>&nbsp;将展示前台活动名称(默认：九块九包邮)
                </td>
            </tr>
            <tr>
                <th><?php echo L('收费名目')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[seller_charge_name]" value="<?php if(!$setting['seller_charge_name']){echo '推广费';}else{echo $setting['seller_charge_name'];}?>"/>&nbsp;收取费用的名目(默认：推广费)
                </td>
            </tr>
            <tr>
                <th><?php echo L('收费价格')?></th>
                <td class="y-bg">
                    亚马逊 最低佣金不低于
                    <input type="text" class="input-text" id='charge_money' name="setting[seller_charge_money]" size="3" value="<?php if($setting['seller_charge_money']){echo $setting['seller_charge_money'];}else{echo '5';};?>" maxlength="3" />&nbsp;%&nbsp;&nbsp;(默认：5%)
                </td>
            </tr>
            <tr>
                <th><?php echo L('活动开始时间')?></th>
                <td class="y-bg">
                <?php echo $form::date('setting[seller_start_time]',$setting['seller_start_time'])?>活动报名结束时间
                <?php echo $form::date('setting[seller_end_time]',$setting['seller_end_time'])?> 
                    
                </td>
            </tr>
            <tr>
                <th><?php echo L('活动简述')?></th>
                <td class="y-bg">
                 <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[seller_activity_desc]"><?php echo $setting['seller_activity_desc']; ?></textarea>
                    
                </td>
            </tr>
            <tr>
                <th><?php echo L('活动详细内容')?></th>
                <td class="y-bg">
                 <textarea style="margin: 0px; width: 652px;" name="setting[seller_detail_desc]" id="setting[seller_detail_desc]" ><?php echo stripslashes($setting['seller_detail_desc']); ?></textarea>
                 <?php echo $form::editor("setting[seller_detail_desc]", "full");?>
                </td>
            </tr>
        </table>
        </div>
        <div class="bk15"></div>
        <input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
    </div>
</div>
</form>
</body>
</html>
<script type='text/javascript'>
$(function(){
    $.formValidator.initConfig({
        formid:"myform",
        autotip:true,
        onerror:function(msg,obj){
            $(obj).focus();
        }
    });
    $("#charge_money").formValidator({
        empty:false,
        onempty:'收费价格不能为空',
        onshow:'请输入收费价格(纯数字)' ,
        onfocus:"请输入收费价格(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'收费价格只能为正数'
    });
})
</script>
