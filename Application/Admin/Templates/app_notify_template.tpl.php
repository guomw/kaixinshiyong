<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script  type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<style type="text/css">
.input-botton {
    border:none;
    border-bottom:1px dotted #E1A035;
    background:none;
}
</style>
<script type="text/javascript">
$(function(){
    $('.input-botton').click(function(){
        $(this).select();
    })
})
</script>
<form action="<?php echo U('config');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <fieldset>
        <legend>app通知模板配置</legend>    
        <table width="100%"  class="table_form">
           
           
           
            <tr>
                <th>充值处理结果</th>
                <td>
                     <div style="display:none;"><label><input type="checkbox" name="configs[pay_recharge_check][]" value="message" <?php if(preg_match("/message/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="email" <?php if(preg_match("/email/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="sms" <?php if(preg_match("/sms/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label></div>

                     <label><input type="checkbox" name="configs[pay_recharge_check][]" value="push" <?php if(preg_match("/push/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label>
                </td>
            </tr>
            <tr>
                <th>提现处理结果</th>
                <td>
                   <div style="display:none;"> <label><input type="checkbox" name="configs[pay_cash_check][]" value="message" <?php if(preg_match("/message/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[pay_cash_check][]" value="email" <?php if(preg_match("/email/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[pay_cash_check][]" value="sms" <?php if(preg_match("/sms/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label></div>
                     <label><input type="checkbox" name="configs[pay_cash_check][]" value="push" <?php if(preg_match("/push/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label>
                </td>
            </tr>
           
            <tr>
                <th>用户订单审核结果</th>
                <td>
                   <div style="display:none;">   <label><input type="checkbox" name="configs[order_check_trade_no][]" value="message" <?php if(preg_match("/message/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_check_trade_no][]" value="email" <?php if(preg_match("/email/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_check_trade_no][]" value="sms" <?php if(preg_match("/sms/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label></div>
                     <label><input type="checkbox" name="configs[order_check_trade_no][]" value="push" <?php if(preg_match("/push/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label>
                </td>
            </tr>
            <tr>
                <th>订单结算通知</th>
                <td>
                  <div style="display:none;">    <label><input type="checkbox" name="configs[order_balance][]" value="message" <?php if(preg_match("/message/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_balance][]" value="email" <?php if(preg_match("/email/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_balance][]" value="sms" <?php if(preg_match("/sms/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label></div>
                     <label><input type="checkbox" name="configs[order_balance][]" value="push" <?php if(preg_match("/push/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label>
                </td>
            </tr>
            
            <tr>
                <th>申诉仲裁结果</th>
                <td>
                      <div style="display:none;">    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="message" <?php if(preg_match("/message/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="email" <?php if(preg_match("/email/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="sms" <?php if(preg_match("/sms/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label></div>
                     <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="push" <?php if(preg_match("/push/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label>

                </td>
            </tr>

           
        </table>
        </fieldset>
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
</script>
</html>