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
<input type='hidden' name='activity_type' value="rebate"/>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',4, 'base');">商家设置</li>
            <li id="tab_setting_safe" <?php if ($this->groupid == 'safe'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',4, 'safe');">会员设置</li>
        </ul>
        <!-- 商家设置 -->
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
        	<tr>
                <th>活动状态</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[rebate_isopen]" value="1" <?php if($setting['rebate_isopen'] == 1){?>" checked <?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[rebate_isopen]" value="0" <?php if($setting['rebate_isopen'] == 0){?>" checked <?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr>
            
            <tr>
                <th>活动价名称</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[rebate_price_name]" value="<?php echo $setting['rebate_price_name'];?>"/>&nbsp;例如：划算价，用于前台展示
                </td>
            </tr>
            
             <tr>
                <th>活动名称</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[rebate_name]" value="<?php if(!$setting['rebate_name']){echo '购物返利';}else{echo $setting['rebate_name'];}?>"/>&nbsp;将展示前台活动名称(默认：购物返利)
                </td>
            </tr>
            <tr>
                <th>下单方式</th>
                <td class="y-bg">
                    普通下单：
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_general_order]" value="1" <?php if($setting['single_mode']['seller_general_order']=='1'||!$setting['single_mode']['seller_general_order']){echo 'checked';}?>/>打开</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_general_order]" value="0" <?php if($setting['single_mode']['seller_general_order']=='0'){echo 'checked';}?>/>关闭</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="y-bg">
                    搜索下单：
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_search_order]" value="1" <?php if($setting['single_mode']['seller_search_order']=='1'||!$setting['single_mode']['seller_search_order']){echo 'checked';}?>/>打开</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_search_order]" value="0" <?php if($setting['single_mode']['seller_search_order']=='0'){echo 'checked';}?>/>关闭</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="y-bg">
                    答案下单：
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_answer_order]" value="1" <?php if($setting['single_mode']['seller_answer_order']=='1'||!$setting['single_mode']['seller_answer_order']){echo 'checked';}?>/>打开</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_answer_order]" value="0" <?php if($setting['single_mode']['seller_answer_order']=='0'){echo 'checked';}?>/>关闭</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="y-bg">
                    二维码下单
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_qrcode_order]" value="1" <?php if($setting['single_mode']['seller_qrcode_order']=='1'||!$setting['single_mode']['seller_general_order']){echo 'checked';}?>/>打开</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[single_mode][seller_qrcode_order]" value="0" <?php if($setting['single_mode']['seller_qrcode_order']=='0'){echo 'checked';}?>/>关闭</label>
                </td>
            </tr>
            <tr>
                <th>参与条件</th>
                <td class="y-bg">
                    <label><input type="checkbox" name="setting[seller_join_condition][phone]" class="input-radio" <?php if(in_array(1, $setting['seller_join_condition'])){echo 'checked';}?> value='1'>需完成手机验证</label>&nbsp;
                    <label><input type="checkbox" name="setting[seller_join_condition][email]" class="input-radio" <?php if(in_array(2, $setting['seller_join_condition'])){echo 'checked';}?> value='2'>需完成邮箱验证</label>&nbsp;
                    <label><input type="checkbox" name="setting[seller_join_condition][realname]" class="input-radio" <?php if(in_array(3, $setting['seller_join_condition'])){echo 'checked';}?> value='3'>需完成实名认证</label>&nbsp;
                  <!--   <label><input type="checkbox" name="setting[seller_join_condition][shop]" class="input-radio" <?php if(in_array(4, $setting['seller_join_condition'])){echo 'checked';}?> value='4'>需完成店铺绑定</label>&nbsp; -->
                    <label><input type="checkbox" name="setting[seller_join_condition][brand]" class="input-radio" <?php if(in_array(5, $setting['seller_join_condition'])){echo 'checked';}?> value='5'>需完成品牌认证</label>
                    <label><input type="checkbox" name="setting[seller_join_condition][information]" class="input-radio" <?php if(in_array(6, $setting['seller_join_condition'])){echo 'checked';}?> value='6'>需完善资料</label>
                </td>
            </tr>

            <tr>
                <th>保证金缴纳方式:</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[service_type]" value="1" <?php if($setting['service_type']=='1' || !$setting['service_type']){echo 'checked';}?>/>全额缴纳</label>&nbsp;&nbsp;
                    <label><input type="radio" class="input-text" name="setting[service_type]" value="2" <?php if($setting['service_type']=='2'){echo 'checked';}?>/>只缴纳返还会员部分</label>&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <th>商品折扣范围</th>
                <td class="y-bg">
                    最低~最高
                    <label><input type="text" class="input-text" name="setting[seller_discount_range][min]" value="<?php if($setting['seller_discount_range'][min]){echo $setting['seller_discount_range'][min];}else{echo "1";} ?>" size='3' maxlength='3'/>~
                    <input type="text" class="input-text" name="setting[seller_discount_range][max]" value="<?php if($setting['seller_discount_range'][max]){echo $setting['seller_discount_range'][max];}else{echo "1";} ?>" size='3' maxlength='3'/></label>
                    &nbsp;&nbsp;提示：请输入范围内的值,最低0.1折，最高9.9折
                </td>
            </tr>
            <tr>
                <th>商家审核时间</th>
                <td class="y-bg"><input type="text" class="input-text" id='check_time' name="setting[seller_check_time]" size="3" value="<?php if($setting['seller_check_time']){echo $setting['seller_check_time'];}else{echo '5';} ?>" maxlength="3" />天
                &nbsp;&nbsp;提示：如果在该时间内未审核订单，订单将根据设置自动审核
                </td>
            </tr>
            <tr>
                <th>到期未审核订单:</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_no_check_order]" value="1" <?php if($setting['seller_no_check_order']=='1' || !$setting['seller_no_check_order']){echo 'checked';}?>/>自动审核成功</label>&nbsp;&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_no_check_order]" value="2" <?php if($setting['seller_no_check_order']=='2'){echo 'checked';}?>/>自动审核失败</label>&nbsp;&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_no_check_order]" value="3" <?php if($setting['seller_no_check_order']=='3'){echo 'checked';}?>/>转平台客服人工审核</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="y-bg">
                    提示：自动审核成功将划算金自动返回给会员(建议)；
                    自动审核失败将拒绝会员订单(不建议选择)；
                    转平台人工审核只有平台客服人工审核成功之后才将划算金返还给会员。
                </td>
            </tr>
            <tr>
                <th>商家申诉</th>
                <td class="y-bg">
                    当收到会员申诉商家在<input type="text" class="input-text" id="get_appeal" name="setting[seller_get_appeal][time]"  value="<?php if(!$setting['seller_get_appeal'][time]){echo '48';}else{echo $setting['seller_get_appeal'][time];};?>" size='4'/>小时内未处理申诉:
                    <label><input type="radio" class="input-text" name="setting[seller_get_appeal][check]" value="1" <?php if($setting['seller_get_appeal'][check]=='1'||!$setting['seller_get_appeal'][check]){echo 'checked';};?>/>将自动审核</label>&nbsp;&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_get_appeal][check]" value="2" <?php if($setting['seller_get_appeal'][check]=='2'){echo 'checked';};?>/>转平台人工审核</label>
                </td>
            </tr>
            <tr>
                <th>活动开始时间</th>
                <td class="y-bg">
                <?php echo $form::date('setting[seller_start_time]',$setting['seller_start_time'])?>活动报名结束时间
                <?php echo $form::date('setting[seller_end_time]',$setting['seller_end_time'])?> 
                    
                </td>
            </tr>
            <tr>
                <th>最长追加天数</th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[seller_push_days]" size="3" value="<?php if($setting['seller_push_days']){echo $setting['seller_push_days'];}else{echo '7';} ?>" maxlength="3" />天
                &nbsp;&nbsp;提示：商家追加库存后活动延长最长天数
                </td>
            </tr>
            <tr>
                <th>最少追加份数</th>
                <td class="y-bg"><input type="text" class="input-text" name="setting[seller_push_nums]" size="5" value="<?php if($setting['seller_push_nums']){echo $setting['seller_push_nums'];}else{echo '1';} ?>" maxlength="5" />份
                &nbsp;&nbsp;提示：商家最少追加库存份数
                </td>
            </tr>
            <tr>
                <th>活动简述</th>
                <td class="y-bg">
                 <textarea style="margin: 0px; width: 267px; height: 78px;" name="setting[seller_activity_desc]"><?php echo $setting['seller_activity_desc']; ?></textarea>
                </td>
            </tr>
            <tr>
                <th>活动详细内容</th>
                <td class="y-bg">
                 <textarea style="margin: 0px; width: 652px;" name="setting[seller_detail_desc]" id="setting[seller_detail_desc]"><?php echo stripslashes($setting['seller_detail_desc']); ?></textarea>
                 <?php echo $form::editor("setting[seller_detail_desc]", "full");?>
                </td>
            </tr>
        </table>
        </div>

        <!-- 会员设置 -->
        <div id="div_setting_safe" class="contentList pad-10 hidden" style="display:none;">
        <table width="100%"  class="table_form">
            <tr>
                <th width="120">人工审核晒单</th>
                <td class="y-bg">
                    <input name="setting[buyer_artificial_check]" value="1" type="radio" <?php if($setting['buyer_artificial_check']=='1' || !$setting['buyer_artificial_check']) echo ' checked';?>> 开启&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="setting[buyer_artificial_check]" value="0" type="radio" <?php if($setting['buyer_artificial_check']=='0') echo ' checked';?>> 关闭
                    &nbsp;&nbsp;提示：开启审核之后需等平台客服人工审核;关闭审核之后将自动通过
                </td>
            </tr>
            <tr>
                <th width="120">参与条件</th>
                <td class="y-bg">
                	<label><input type="checkbox" name="setting[buyer_join_condition][information]" class="input-radio" <?php if(in_array(6, $setting['buyer_join_condition'])){echo 'checked';}?> value='6'>需完善基本资料</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][phone]" class="input-radio" <?php if(in_array(1, $setting['buyer_join_condition'])){echo 'checked';}?> value='1'>需完成手机认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][email]" class="input-radio" <?php if(in_array(2, $setting['buyer_join_condition'])){echo 'checked';}?> value='2'>需完成邮箱认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][realname]" class="input-radio" <?php if(in_array(3, $setting['buyer_join_condition'])){echo 'checked';}?> value='3'>需完成实名认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][bind_taobao]" class="input-radio" <?php if(in_array(4, $setting['buyer_join_condition'])){echo 'checked';}?> value='4'>需绑定淘宝账号</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][bind_alipay]" class="input-radio" <?php if(in_array(5, $setting['buyer_join_condition'])){echo 'checked';}?> value='5'>需绑定支付宝账号</label>
                </td>
            </tr>
            <tr>
                <th width="120">每个商品参与限制</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="buy_times" name="setting[buyer_good_buy_times]" size="3" maxlength="3" value="<?php if($setting['buyer_good_buy_times']){echo $setting['buyer_good_buy_times'];}else{echo '1';} ?>"/>次
                    &nbsp;提示：会员对每个商品可以购买的次数（默认：1次）
                </td>
            </tr>
            <tr>
                <th>每天参与次数限制</th>
                <td class="y-bg">
                    <label><input name="setting[buyer_day_buy_times]" value="0" type="radio" <?php if($setting['buyer_day_buy_times']=='0' || !$setting['buyer_day_buy_times']) echo 'checked';?>>不限</label>&nbsp;&nbsp;
                    <label><input name="setting[buyer_day_buy_times]" type="radio" value="1" <?php if($setting['buyer_day_buy_times'] > '0') echo 'checked';?>>自定义</label>
                    &nbsp;
                    <span id='buyer_day_buy_times'><input type="text" class="input-text" id="day_buy_times" name="setting[buyer_day_buy_times]" size="5" value="<?php if($setting['buyer_day_buy_times']){echo $setting['buyer_day_buy_times'];} ?>" maxlength='5'/>次</span>
                    &nbsp;提示：不限则不限制参与次数；如果自定义了次数则当天内只能参与多少次（默认：不限）
                </td>
            </tr>

            <tr>
                <th>每件商品抢购时间限制</th>
                <td class="y-bg">
                    <label><input name="setting[buyer_buy_time_limit]" value="0" type="radio" <?php if($setting['buyer_buy_time_limit']=='0' || !$setting['buyer_buy_time_limit']) echo 'checked';?>>不限</label>&nbsp;&nbsp;
                    <label><input name="setting[buyer_buy_time_limit]" type="radio" <?php if($setting['buyer_buy_time_limit']>'0') echo 'checked';?>>自定义</label>
                    &nbsp;
                    <span id='buyer_buy_time_limit'><input type="text" class="input-text" id="buy_time_limit" name="setting[buyer_buy_time_limit]" size="7" value="<?php if($setting['buyer_buy_time_limit']){echo $setting['buyer_buy_time_limit'];} ?>" maxlength='7'/>秒</span>
                    &nbsp;提示：不限则不限制该时间段内参与次数；如果自定义了时间则该时间段内只能参与一次
                </td>
            </tr>

            <tr>
                <th>填写订单号时间</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="write_order_time" name="setting[buyer_write_order_time]" size="6" value="<?php echo $setting['buyer_write_order_time'] ?>"/>分钟
                    &nbsp;提示：会员未在该时间内填写订单号，订单号自动关闭(默认30分钟)
                </td>
            </tr>
            <tr>
                <th>会员填写订单号之后</th>
                <td class="y-bg">
                    <input name="setting[buyer_update_order_type]" value="0" type="radio" <?php if($setting['buyer_update_order_type']=='0' || !$setting['buyer_update_order_type']) echo 'checked';?>>不可修改订单号&nbsp;&nbsp;
                    <input name="setting[buyer_update_order_type]" type="radio" <?php if($setting['buyer_update_order_type']>'0') echo 'checked';?>> 自定义&nbsp;
                    <span id='buyer_update_order_type'><input type="text" class="input-text" id="update_order_type" name="setting[buyer_update_order_type]"  value="<?php if($setting['buyer_update_order_type']){echo $setting['buyer_update_order_type'];}else{echo '0';} ?>" size='4'/>小时内可修改</span>
                </td>
            </tr>
            <tr>
                <th>商家审核失败后会员可申诉时间</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="update_order_sn" name="setting[buyer_check_update_order_sn]"  value="<?php if($setting['buyer_check_update_order_sn']){echo $setting['buyer_check_update_order_sn'];}else{echo '48';};?>" size='4'/>
                    小时以内可以申诉订单，到期将自动关闭订单（默认48小时）
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
$(function(){
	if($("input[name='setting[buyer_day_buy_times]']:radio:checked == 0")){
		 $("input[name='setting[buyer_day_buy_times]']:text").attr('value','0');
	}
	if($("input[name='setting[buyer_buy_time_limit]']:radio:checked == 0")){
		$("input[name='setting[buyer_buy_time_limit]']:text").attr('value','0');
	}
	if($("input[name='setting[buyer_update_order_type]']:radio:checked == 0")){
		 $("input[name='setting[buyer_update_order_type]']:text").attr('value','0');
	}
});
// 每天参与次数限制
$("input[name='setting[buyer_day_buy_times]']:radio").click(function(){
    if ($("input[name='setting[buyer_day_buy_times]']:radio:checked").val()=='0'){
        $('#buyer_day_buy_times').attr('style','display:none;');
        $("input[name='setting[buyer_day_buy_times]']:text").attr('value','0');
    }else{
        $('#buyer_day_buy_times').attr('style','display:');
        $("input[name='setting[buyer_day_buy_times]']:text").attr('value','<?php echo $setting["buyer_day_buy_times"] ?>');
    }
})
//  每件商品抢购时间限制
$("input[name='setting[buyer_buy_time_limit]']:radio").click(function(){
    if ($("input[name='setting[buyer_buy_time_limit]']:radio:checked").val()=='0'){
        $('#buyer_buy_time_limit').attr('style','display:none;');
        $("input[name='setting[buyer_buy_time_limit]']:text").attr('value','0');
    }else{
        $('#buyer_buy_time_limit').attr('style','display:;');
        $("input[name='setting[buyer_buy_time_limit]']:text").attr('value','<?php echo $setting["buyer_buy_time_limit"] ?>');
    }
})
// 会员填写订单号之后
$("input[name='setting[buyer_update_order_type]']:radio").click(function(){
    if ($("input[name='setting[buyer_update_order_type]']:radio:checked").val()=='0'){
        $('#buyer_update_order_type').attr('style','display:none;');
        $("input[name='setting[buyer_update_order_type]']:text").attr('value','0');
    }else{
        $('#buyer_update_order_type').attr('style','display:;');
        $("input[name='setting[buyer_update_order_type]']:text").attr('value','<?php echo $setting["buyer_update_order_type"] ?>');
    }
})
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