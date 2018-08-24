<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<form action="<?php echo U('api');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="postal"/>
<div class="pad-10">
    <div class="explain-col">
         1.亚马逊api 申请地址 http://www.alimama.com/<br>
         2.亚马逊api 主要用途: 1 获取亚马逊.天猫商品信息 2.可用于后续亚马逊商品<br>
         3.官方自带的api因多有多平台使用，有可能造成不稳定情况，建议自行申请。
    </div>
    <div class="bk10"></div>
    <div class="col-tab">
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
            <tr>
                <th>App Key</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[api_key]" value="<?php echo C('API_KEY')?>"/>&nbsp;
                </td>
            </tr>
            
            <tr>
                <th>App Secret</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[api_secret]" value="<?php echo C('API_SECRET') ?>"/>&nbsp;
                </td>
            </tr>

            <tr>
                <th>亚马逊 pid</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[api_pid]" value="<?php echo C('API_PID')?>"/>&nbsp;
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
    /*$("#charge_money").formValidator({
        empty:false,
        onempty:'收费价格不能为空',
        onshow:'请输入收费价格(纯数字)' ,
        onfocus:"请输入收费价格(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'收费价格只能为正数'
    });*/
})
</script>
