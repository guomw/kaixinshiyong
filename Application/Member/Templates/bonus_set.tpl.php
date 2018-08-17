<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
    SwapTab('setting','on','',2, 'base');
})
//--> 
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="bonus"/>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',4, 'base');"><?php echo L('红包设置')?></li>
        </ul>
        <!-- 红包设置 -->
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
        	<tr>
                <th><?php echo L('活动状态')?></th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[bonus_isopen]" value="1" <?php if($setting['bonus_isopen'] == 1){?>" checked <?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[bonus_isopen]" value="0" <?php if($setting['bonus_isopen'] == 0){?>" checked <?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('活动名称')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[bonus_name]" value="<?php echo $setting['bonus_name'];?>"/>&nbsp;用于前台展示，例如红包试用。
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('赠送红包金额范围')?></th>
                <td class="y-bg">
                    <label>最少<input type="text" size="6" class="input-text" name="setting[bonus_price][min]" value="<?php if($setting['bonus_price']['min']){ echo $setting['bonus_price']['min']; }else{?>1 <?php }?>"/></label>
                    <label>最多<input type="text" size="6" class="input-text" name="setting[bonus_price][max]" value="<?php echo $setting['bonus_price']['max'];?>"/>美元</label>
                    &nbsp;例如：1-50美元
                </td>
            </tr>
            
            <!-- <tr>
                <th><?php echo L('红包兑换现金方式')?></th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[bonus_exchange]" value="1" checked/>直接到账</label>&nbsp;&nbsp;
                    <label><input type="radio" class="input-text" name="setting[bonus_exchange]" value="2"/>需兑换</label>&nbsp;&nbsp;
                     <label><input type="radio" class="input-text" name="setting[bonus_exchange]" value="3"/>不可兑换</label>&nbsp;&nbsp;
                </td>
            </tr>
            
            <tr>
                <th><?php echo L('红包与现金兑换比例')?></th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[bonus_ratio]" value="<?php if($setting['bonus_ratio']){echo $setting['bonus_ratio'];}else{?>1:1 <?php }?>"/>&nbsp;红包可兑换成现金，然后提现。 例如：1:1
                </td>
            </tr> -->
          
            <tr>
                <th><?php echo L('  红包用途介绍')?></th>
                <td class="y-bg">
                   <textarea rows='5' cols='30' name="setting[bonus_content]"><?php echo $setting['bonus_content'];?></textarea>&nbsp;介绍红包的使用方式
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
<script type="text/javascript">
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}
// 表单校验
$(function(){
    $.formValidator.initConfig({
        formid:"myform",
        autotip:true,
        onerror:function(msg,obj){
            $(obj).focus();
        }
    });
    // 商家审核时间
    $("#check_time").formValidator({
        empty:false,
        onempty:'商家审核时间不能为空',
        onshow:'请输入商家审核时间(纯数字)' ,
        onfocus:"请输入商家审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'商家审核时间只能为正数'
    });
    // 商家申诉
    $("#get_appeal").formValidator({
        empty:false,
        onempty:'商家申诉不能为空',
        onshow:'请输入商家申诉(纯数字)' ,
        onfocus:"请输入商家申诉(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'商家申诉只能为正数'
    });
    // 每个商品参与限制
    $("#buy_times").formValidator({
        empty:false,
        onempty:'每个商品参与限制不能为空',
        onshow:'请输入每个商品参与限制(纯数字)' ,
        onfocus:"请输入每个商品参与限制(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'每个商品参与限制只能为正数'
    });
    // 每天参与次数限制
    $("#day_buy_times").formValidator({
        empty:false,
        onempty:'每天参与次数限制不能为空',
        onshow:'请输入每天参与次数限制(纯数字)' ,
        onfocus:"请输入每天参与次数限制(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'每天参与次数限制只能为数字'
    });
    // 每件商品抢购时间限制
    $("#buy_time_limit").formValidator({
        empty:false,
        onempty:'每件商品抢购时间限制不能为空',
        onshow:'请输入每件商品抢购时间限制(纯数字)' ,
        onfocus:"请输入每件商品抢购时间限制(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'每件商品抢购时间限制只能为数字'
    });
    // 填写订单号时间
    $("#write_order_time").formValidator({
        empty:false,
        onempty:'填写订单号时间限制不能为空',
        onshow:'请输入填写订单号时间限制(纯数字)' ,
        onfocus:"请输入填写订单号时间限制(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'填写订单号时间限制只能为正数'
    });
    // 会员填写订单号之后
    $("#update_order_type").formValidator({
        empty:false,
        onempty:'会员填写订单号之后不能为空',
        onshow:'请输入会员填写订单号之后(纯数字)' ,
        onfocus:"请输入会员填写订单号之后(纯数字)" 
    }).regexValidator({
        regexp:'num',
        datatype:'enum',
        onerror:'会员填写订单号之后只能为数字'
    });
    // 商家审核失败后会员可修改订单号时间
    $("#update_order_sn").formValidator({
        empty:false,
        onempty:'商家审核失败后会员可修改订单号时间不能为空',
        onshow:'请输入商家审核失败后会员可修改订单号时间(纯数字)' ,
        onfocus:"请输入商家审核失败后会员可修改订单号时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'商家审核失败后会员可修改订单号时间只能为正数'
    });
})
</script>
</html>