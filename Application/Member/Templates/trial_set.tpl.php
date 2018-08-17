<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
    SwapTab('setting','on','',3, 'base');
})
//--> 
</script>
<form action="<?php echo U('update');?>" method="post" id="myform">
<input type='hidden' name='activity_type' value="trial"/>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base" <?php if ($this->groupid == 'base'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'base');">商家设置</li>
            <li id="tab_setting_safe" <?php if ($this->groupid == 'safe'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'safe');">会员设置</li>
            <li id="tab_setting_bonus" <?php if ($this->groupid == 'bonus'): ?>class="on"<?php endif ?> onclick="SwapTab('setting','on','',3, 'bonus');">红包设置</li>
        </ul>
        <!-- 商家设置 -->
        <div id="div_setting_base" class="contentList pad-10">
        <table width="100%"  class="table_form">
			<tr>
                <th>活动状态</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[trial_isopen]" value="1" <?php if($setting['trial_isopen'] == 1){?>checked<?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[trial_isopen]" value="0" <?php if($setting['trial_isopen'] == 0){?>checked<?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr>
            
            <tr>
                <th>活动价名称</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[trial_price_name]" value="<?php echo $setting['trial_price_name'];?>"/>&nbsp;例如：试用价，用于前台展示
                </td>
            </tr>
            
            <tr>
                <th>活动别名</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[trial_name]" value="<?php if(!$setting['trial_name']){echo '免费试用';}else{echo $setting['trial_name'];}?>"/>&nbsp;将展示前台活动名称(默认：免费试用)
                </td>
            </tr>
			
            <tr>
                <th>收费名目</th>
                <td class="y-bg">
                    <input type="text" class="input-text" name="setting[seller_charge_name]" value="<?php if(!$setting['seller_charge_name']){echo '推广费';}else{echo $setting['seller_charge_name'];}?>"/>&nbsp;收取费用的名目(默认：推广费)
                </td>
            </tr>

            <tr>
                <th>收费方式</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_charge_money]" value="0" <?php if($setting['seller_charge_money']=='0'||!$setting['seller_charge_money']){echo 'checked';}?>/>按单份收取</label>&nbsp;
                </td>
            </tr>
            
            <tr>
                <th>剩余推广费退还</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_give_back]" value="1" <?php if($setting['seller_give_back']=='1'||!$setting['seller_give_back']){echo 'checked';}?>/>退还</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_give_back]" value="0" <?php if($setting['seller_give_back']=='0'){echo 'checked';}?>/>不退还</label>
                    &nbsp;<font color="red">(建议按单份收取的时候设置为退还，按单场活动收取的时候设置不退还。)</font>
                </td>
            </tr>
            
            <tr>
                <th>是否开启拍A发B</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_pat_isopen]" value="1" <?php if($setting['seller_pat_isopen']=='1'){echo 'checked';}?>/>是</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_pat_isopen]" value="0" <?php if($setting['seller_pat_isopen']=='0'){echo 'checked';}?>/>否</label>
                </td>
            </tr>
            
            <tr>
                <th>是否开启红包试用</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_bonus_isopen]" value="1" <?php if($setting['seller_bonus_isopen']=='1'){echo 'checked';}?>/>是</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_bonus_isopen]" value="0" <?php if($setting['seller_bonus_isopen']=='0'){echo 'checked';}?>/>否</label>
                </td>
            </tr>

            <tr>
                <th>是否开启VIP免审</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_vip_isopen]" value="1" <?php if($setting['seller_vip_isopen']=='1'){echo 'checked';}?>/>是</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_vip_isopen]" value="0" <?php if($setting['seller_vip_isopen']=='0'){echo 'checked';}?>/>否</label>
                </td>
            </tr>


             <tr>
                <th>是否开启积分兑换</th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[seller_point_isopen]" value="1" <?php if($setting['seller_point_isopen']=='1'){echo 'checked';}?>/>是</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_point_isopen]" value="0" <?php if($setting['seller_point_isopen']=='0'){echo 'checked';}?>/>否</label>
                </td>
            </tr>

             <tr>
                <th>兑换规则</th>
                <td class="y-bg">
                    
                    1美元 =
                    <input type="text" class="input-text" name="setting[trial_point]" size='6' value="<?php if(!$setting['trial_point']){echo '0.1';}else{echo $setting['trial_point'];}?>"/>积分
                    (示例：后台设置1美元 = 0.1 如果某试用品价值100美元 则兑换这件商品需要10积分 根据运营自行设定积分大小值 如果换算后不足1分，按照1分算 )
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
                    <label><input type="checkbox" name="setting[seller_join_condition][phone]" class="input-radio" <?php if(in_array(1, $setting['seller_join_condition'])){echo 'checked';}?> value='1'>需完成手机验证 </label>&nbsp;
                    <label><input type="checkbox" name="setting[seller_join_condition][email]" class="input-radio" <?php if(in_array(2, $setting['seller_join_condition'])){echo 'checked';}?> value='2'>需完成邮箱验证</label>&nbsp;
                    <label><input type="checkbox" name="setting[seller_join_condition][realname]" class="input-radio" <?php if(in_array(3, $setting['seller_join_condition'])){echo 'checked';}?> value='3'>需完成实名认证</label>&nbsp;
<!--                     <label><input type="checkbox" name="setting[seller_join_condition][shop]" class="input-radio" <?php if(in_array(4, $setting['seller_join_condition'])){echo 'checked';}?> value='4'>需完成店铺绑定</label>&nbsp;
 -->                    <label><input type="checkbox" name="setting[seller_join_condition][brand]" class="input-radio" <?php if(in_array(5, $setting['seller_join_condition'])){echo 'checked';}?> value='5'>需完成品牌认证</label>&nbsp;
                     <label><input type="checkbox" name="setting[seller_join_condition][information]" class="input-radio" <?php if(in_array(6, $setting['seller_join_condition'])){echo 'checked';}?> value='6'>需完善资料</label>
                   
                </td>
            </tr>
			
            <tr>
                <th>申请资格审核时间</th>
                <td class="y-bg">
                    最长&nbsp;<input type="text" class="input-text" id='check_time' name="setting[seller_check_time]" value="<?php if(!$setting['seller_check_time']){echo '7';}else{echo $setting['seller_check_time'];}?>" size='3' maxlength='3'/>&nbsp;天，过期将自动失效&nbsp;&nbsp;(默认：7天)
                </td>
            </tr>

            <tr>
                <th>订单号审核时间</th>
                <td class="y-bg">
                    最长&nbsp;<input type="text" class="input-text" id='order_check_time' name="setting[seller_order_check_time]" value="<?php if(!$setting['seller_order_check_time']){echo '48';}else{echo $setting['seller_order_check_time'];}?>" size='3' maxlength='3'/>&nbsp;小时，过期将自动失效&nbsp;&nbsp;(默认：48小时)
                </td>
            </tr> 
			
            <tr>
                <th>试用报告审核时间</th>
                <td class="y-bg">
                    最长&nbsp;
                    <input type="text" class="input-text" id="trialtalk_check" name="setting[seller_trialtalk_check][value]" value="<?php if(!$setting['seller_trialtalk_check']['value']){echo '15';}else{echo $setting['seller_trialtalk_check']['value'];}?>" size='3' maxlength='3'/>&nbsp;天，过期将转为 
                    <label><input type="radio" class="input-text" name="setting[seller_trialtalk_check][chose]" value="1" <?php if($setting['seller_trialtalk_check']['chose']=='1'||!$setting['seller_trialtalk_check']['chose']){echo 'checked';}?>/>自动审核通过</label>&nbsp;
                    <label><input type="radio" class="input-text" name="setting[seller_trialtalk_check][chose]" value="0" <?php if($setting['seller_trialtalk_check']['chose']=='0'){echo 'checked';}?>/>平台审核</label>
                    &nbsp;&nbsp;(默认：15天,并自动审核；备注：用户填写试用报告之后的审核时间)
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
                <td class="y-bg"><input type="text" class="input-text" id="push_days" name="setting[seller_push_days]" size="3" value="<?php if($setting['seller_push_days']){echo $setting['seller_push_days'];}else{echo '7';} ?>" maxlength="3" />天
                &nbsp;&nbsp;提示：商家追加库存后活动延长最长天数
                </td>
            </tr>

            <tr>
                <th>最少追加份数</th>
                <td class="y-bg"><input type="text" class="input-text" id="push_nums" name="setting[seller_push_nums]" size="5" value="<?php if($setting['seller_push_nums']){echo $setting['seller_push_nums'];}else{echo '1';} ?>" maxlength="5" />份
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
                <th width="120">参与条件</th>
                <td class="y-bg">
                    <label><input type="checkbox" name="setting[buyer_join_condition][information]" class="input-radio" <?php if(in_array(6, $setting['buyer_join_condition'])){echo 'checked';}?> value='6'>需完善基本资料</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][phone]" class="input-radio" <?php if(in_array(1, $setting['buyer_join_condition'])){echo 'checked';}?> value='1'>需完成手机认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][email]" class="input-radio" <?php if(in_array(2, $setting['buyer_join_condition'])){echo 'checked';}?> value='2'>需完成邮箱认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][realname]" class="input-radio" <?php if(in_array(3, $setting['buyer_join_condition'])){echo 'checked';}?> value='3'>需完成实名认证</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][bind_taobao]" class="input-radio" <?php if(in_array(4, $setting['buyer_join_condition'])){echo 'checked';}?> value='4'>需绑定淘宝账号</label>&nbsp;
                    <label><input type="checkbox" name="setting[buyer_join_condition][bind_alipay]" class="input-radio" <?php if(in_array(5, $setting['buyer_join_condition'])){echo 'checked';}?> value='5'>需绑定支付宝账号</label>
                     <label><input type="checkbox" name="setting[buyer_join_condition][reason]" class="input-radio" <?php if(in_array(7, $setting['buyer_join_condition'])){echo 'checked';}?> value='7'>需申请理由</label>

                </td>
            </tr>
            <tr>
                <th width="120">每个商品参与限制</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="good_buy_times" name="setting[buyer_good_buy_times]" size="3" maxlength="3" value="<?php if($setting['buyer_good_buy_times']){echo $setting['buyer_good_buy_times'];}else{echo '1';} ?>"/>次
                    &nbsp;提示：设置每个商品每个会员可参与次数（默认：1次）
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
                <th>填写订单号时间</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="order_time" name="setting[buyer_write_order_time]" size="6" maxlength="6" value="<?php if($setting['buyer_write_order_time']){echo $setting['buyer_write_order_time'];}else{echo '48';} ?>"/>小时
                    &nbsp;提示：设置每件商品用户获得抢购资格之后，填写订单号时间(默认：48小时)
                </td>
            </tr>
            <tr>
                <th>填写报告时间</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="write_talk_time" name="setting[buyer_write_talk_time]" size="3" maxlength="3" value="<?php if($setting['buyer_write_talk_time']){echo $setting['buyer_write_talk_time'];}else{echo '15';} ?>"/>天
                    &nbsp;提示：设置每件商品用户填写订单号，商家审核通过之后，填写试用报告时间(默认：15天)
                </td>
            </tr>
            <tr>
                <th>商家审核失败后会员可申诉时间</th>
                <td class="y-bg">
                    <input type="text" class="input-text" id="update_order_sn" name="setting[buyer_check_update_order_sn]"  value="<?php if($setting['buyer_check_update_order_sn']){echo $setting['buyer_check_update_order_sn'];}else{echo '48';};?>" size='4' maxlength='4'/>
                    小时以内可以申诉;到期将自动关闭（默认：48小时）
                </td>
            </tr>
          
             <tr >
                <th>是否收取会员费用</th>
                <td class="y-bg">
                    <label><input name="setting[buyer_order_fee]" value="0" type="radio" <?php if($setting['buyer_order_fee']=='0' || !$setting['buyer_order_fee']) echo 'checked';?>>否</label>&nbsp;&nbsp;
                    
                    <label><input name="setting[buyer_order_fee]" type="radio" value="1" <?php if($setting['buyer_order_fee'] > '0') echo 'checked';?>>是</label>

                  
                    &nbsp;提示：用户完成单笔订单，是否从中收取单笔服务费
                </td>
            </tr>

            <tr >
              <th>收费规则</th>
              <td  class="y-bg"><input type="button" id="type_add" value="新增" style="width:50px;height:25px;" />&nbsp;&nbsp;提示：订单价大于等于100.00美元，收取1.00美元平台服务费</td>
            </tr>

               <?php foreach ($trial_data['trial_fee_price'] as $k=>$v) {?>

                  <tr>
                <th></th>
                <td class="y-bg">
                    下单价大于等于：<input type="text" class="input-text" id='check_time' name="setting[config][trial][trial_fee_price][<?php echo $k;?>][min]" size="3" value="<?php echo $v['min']?>" />
                    <label>收取<input type="text" class="input-text" id='check_time' name="setting[config][trial][trial_fee_price][<?php echo $k;?>][trial]" size="3" value="<?php echo $v['trial']?>" />/美元平台服务费</label>
                    <?php if ($k > 1): ?>
                    <a class="delete" style="color:red;cursor:pointer">删除</a>
                    <?php endif ?>
                </td>
            </tr>
            <?php }?>

            <tr id="trial"></tr>


        </table>
        <script type="text/javascript">
                $(document).ready(function(){
                    var num = '<?php echo $trial_count-1;?>';
                    $("#type_add").click(function(){
                        num++;
                        var html = '';
                        html += '<tr>';
                        html += '<th></th>';
                        html += '<td class="y-bg">';
                        html += '下单价大于等于：<input type="text" class="input-text" id="check_time" name="setting[config][trial][trial_fee_price]['+num+'][min]" size="3" value="" />';
                        html += '<label> 收取：<input type="text" class="input-text" id="check_time" name="setting[config][trial][trial_fee_price]['+num+'][trial]" size="3" value="" />/美元平台服务费</label>';
                        html += '<span class="delete" style="color:red;cursor:pointer">删除</span>';
                        html += '</td>';
                        html += '</tr>';
                        $("#trial").before(html);
                        //删除
                        $(document).on("click",".delete",function(){
                            //判断用餐时段的个数 
                            $(this).parents('tr').remove();
                        });
                    });

                   
                });
            </script>
        </div>
        <div id="div_setting_bonus" class="contentList pad-10" style="display:none;">
        <table width="100%"  class="table_form">
        	<!-- <tr>
                <th><?php echo L('活动状态')?></th>
                <td class="y-bg">
                    <label><input type="radio" class="input-text" name="setting[bonus_isopen]" value="1" <?php if($setting['bonus_isopen'] == 1){?>" checked <?php }?>/>开启&nbsp;</label>
                    <label><input type="radio" class="input-text" name="setting[bonus_isopen]" value="0" <?php if($setting['bonus_isopen'] == 0){?>" checked <?php }?>/>关闭&nbsp;</label>&nbsp;<font color="red">关闭之后在前台将无法使用此功能</font>
                </td>
            </tr> -->
            
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

// 表单值验证
$(function(){
    $.formValidator.initConfig({
        formid:"myform",
        autotip:true,
        onerror:function(msg,obj){
            $(obj).focus();
        }
    });
    // 申请资格审核时间
    $("#check_time").formValidator({
        empty:false,
        onempty:'申请资格审核时间不能为空',
        onshow:'请输入申请资格审核时间(纯数字)' ,
        onfocus:"请输入申请资格审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'申请资格审核时间只能为数字'
    });
    // 订单号审核时间
    $("#order_check_time").formValidator({
        empty:false,
        onempty:'订单号审核时间不能为空',
        onshow:'请输入订单号审核时间(纯数字)' ,
        onfocus:"请输入订单号审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'订单号审核时间只能为数字'
    });
    // 最长追加天数
    $("#push_days").formValidator({
        empty:false,
        onempty:'最长追加天数不能为空',
        onshow:'请输入最长追加天数(纯数字)' ,
        onfocus:"请输入最长追加天数(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'最长追加天数只能为数字'
    });

    // 最少追加份数
    $("#push_nums").formValidator({
        empty:false,
        onempty:'最少追加份数不能为空',
        onshow:'请输入最少追加份数(纯数字)' ,
        onfocus:"请输入最少追加份数(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'最少追加份数只能为数字'
    });
    // 订单号审核时间
    $("#ordersn_check").formValidator({
        empty:false,
        onempty:'订单号审核时间不能为空',
        onshow:'请输入订单号审核时间(纯数字)' ,
        onfocus:"请输入订单号审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'填写订单号审核时间只能为数字'
    });
    // 试用报告审核时间
    $("#trialtalk_check").formValidator({
        empty:false,
        onempty:'试用报告审核时间不能为空',
        onshow:'请输入试用报告审核时间(纯数字)' ,
        onfocus:"请输入试用报告审核时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'试用报告审核时间只能为数字'
    });
    // 每个商品参与限制
    $("#good_buy_times").formValidator({
        empty:false,
        onempty:'每个商品参与限制不能为空',
        onshow:'请输入每个商品参与限制(纯数字)' ,
        onfocus:"请输入每个商品参与限制(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'每个商品参与限制只能为数字'
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
    // 填写订单号时间
    $("#order_time").formValidator({
        empty:false,
        onempty:'填写订单号时间不能为空',
        onshow:'请输入填写订单号时间(纯数字)' ,
        onfocus:"请输入填写订单号时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'填写订单号时间只能为正整数'
    });
    // 填写报告时间
    $("#write_talk_time").formValidator({
        empty:false,
        onempty:'填写报告时间不能为空',
        onshow:'请输入填写报告时间(纯数字)' ,
        onfocus:"请输入填写报告时间(纯数字)" 
    }).regexValidator({
        regexp:'intege1',
        datatype:'enum',
        onerror:'填写报告时间只能为正整数'
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
        onerror:'商家审核失败后会员可修改订单号时间只能为正整数'
    });
})
</script>
</html>