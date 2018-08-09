<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<!--后台通知模板配置 -->
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
        <legend>通知模板配置</legend>    
        <table width="100%"  class="table_form">
            <tr>
                <td width="120">可用变量</td>
                <td>
                <label>账户名：<input type="text" value="{accout}" size="15" class="input-botton input-text"></label>
                <label>网站名：<input type="text" value="{site_name}" size="15" class="input-botton input-text"></label>
                <label>网站地址：<input type="text" value="{site_url}" size="15" class="input-botton input-text"></label>
                <label>链接：<input type="text" value="{url}" size="15" class="input-botton input-text"></label>
                <label>IP地址：<input type="text" value="{ip}" size="15" class="input-botton input-text"></label>
                </td>
            </tr>
            <tr>
                <th>激活邮件</th>
                <td>
                    <textarea cols="60" rows="8" name="configs[member_email_activate]"><?php echo dstripslashes($configs['member_email_activate']['content']) ?></textarea>
                </td>
            </tr>
            <tr>
                <th>欢迎邮件</th>
                <td>
                    <textarea cols="60" rows="8" name="configs[member_email_webcome]"><?php echo dstripslashes($configs['member_email_webcome']['content']) ?></textarea>
                </td>
            </tr>
            <tr>
                <th>找回密码</th>
                <td>
                    <textarea cols="60" rows="8" name="configs[member_email_retpwd]"><?php echo dstripslashes($configs['member_email_retpwd']['content']) ?></textarea>
                </td>
            </tr>
            <tr>
                <th>充值处理结果</th>
                <td>
                    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="message" <?php if(preg_match("/message/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="email" <?php if(preg_match("/email/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="sms" <?php if(preg_match("/sms/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>

                  <!--    <label><input type="checkbox" name="configs[pay_recharge_check][]" value="push" <?php if(preg_match("/push/",$configs['pay_recharge_check']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label> -->
                    <!-- &nbsp;<em style="vertical-align:middle;">普通充值审核结果通知</em> -->
                </td>
            </tr>


            <tr>
                <th>提现处理结果</th>
                <td>
                    <label><input type="checkbox" name="configs[pay_cash_check][]" value="message" <?php if(preg_match("/message/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[pay_cash_check][]" value="email" <?php if(preg_match("/email/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[pay_cash_check][]" value="sms" <?php if(preg_match("/sms/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                     <!-- label><input type="checkbox" name="configs[pay_cash_check][]" value="push" <?php if(preg_match("/push/",$configs['pay_cash_check']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label> -->
                </td>
            </tr>
            <tr>
                <th width="120">商品审核结果通知</th>
                <td>
                    <label><input type="checkbox" name="configs[product_check][]" value="message" <?php if(preg_match("/message/",$configs['product_check']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[product_check][]" value="email" <?php if(preg_match("/email/",$configs['product_check']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[product_check][]" value="sms" <?php if(preg_match("/sms/",$configs['product_check']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>
            <tr>
                <th>商品被屏蔽通知</th>
                <td>
                    <label><input type="checkbox" name="configs[product_lock][]" value="message" <?php if(preg_match("/message/",$configs['product_lock']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[product_lock][]" value="email" <?php if(preg_match("/email/",$configs['product_lock']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[product_lock][]" value="sms" <?php if(preg_match("/sms/",$configs['product_lock']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>
            <tr>
                <th>商品结算通知</th>
                <td>
                    <label><input type="checkbox" name="configs[product_balance][]" value="message" <?php if(preg_match("/message/",$configs['product_balance']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[product_balance][]" value="email" <?php if(preg_match("/email/",$configs['product_balance']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[product_balance][]" value="sms" <?php if(preg_match("/sms/",$configs['product_balance']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>
            <tr>
                <th>用户填写订单号</th>
                <td>
                    <label><input type="checkbox" name="configs[order_fill_trade_no][]" value="message" <?php if(preg_match("/message/",$configs['order_fill_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_fill_trade_no][]" value="email" <?php if(preg_match("/email/",$configs['order_fill_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_fill_trade_no][]" value="sms" <?php if(preg_match("/sms/",$configs['order_fill_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>
            <tr>
                <th>用户填写试用报告</th>
                <td>
                    <label><input type="checkbox" name="configs[order_fill_report][]" value="message" <?php if(preg_match("/message/",$configs['order_fill_report']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_fill_report][]" value="email" <?php if(preg_match("/email/",$configs['order_fill_report']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_fill_report][]" value="sms" <?php if(preg_match("/sms/",$configs['order_fill_report']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>

            <tr>
                <th>用户试用资格审核成功</th>
                <td>
                    <label><input type="checkbox" name="configs[order_check_zige][]" value="message" <?php if(preg_match("/message/",$configs['order_check_zige']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_check_zige][]" value="email" <?php if(preg_match("/email/",$configs['order_check_zige']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_check_zige][]" value="sms" <?php if(preg_match("/sms/",$configs['order_check_zige']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>

            <tr>
                <th>用户订单号审核结果</th>
                <td>
                    <label><input type="checkbox" name="configs[order_check_trade_no][]" value="message" <?php if(preg_match("/message/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_check_trade_no][]" value="email" <?php if(preg_match("/email/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_check_trade_no][]" value="sms" <?php if(preg_match("/sms/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                    <!--  <label><input type="checkbox" name="configs[order_check_trade_no][]" value="push" <?php if(preg_match("/push/",$configs['order_check_trade_no']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label> -->
                </td>
            </tr>

            <tr>
                <th>订单结算通知</th>
                <td>
                    <label><input type="checkbox" name="configs[order_balance][]" value="message" <?php if(preg_match("/message/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_balance][]" value="email" <?php if(preg_match("/email/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_balance][]" value="sms" <?php if(preg_match("/sms/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                     <!-- <label><input type="checkbox" name="configs[order_balance][]" value="push" <?php if(preg_match("/push/",$configs['order_balance']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label> -->
                </td>
            </tr>
            <tr>
                <th>用户发起申诉</th>
                <td>
                    <label><input type="checkbox" name="configs[order_appeal][]" value="message" <?php if(preg_match("/message/",$configs['order_appeal']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_appeal][]" value="email" <?php if(preg_match("/email/",$configs['order_appeal']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_appeal][]" value="sms" <?php if(preg_match("/sms/",$configs['order_appeal']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                </td>
            </tr>
            <tr>
                <th>申诉仲裁结果</th>
                <td>
                    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="message" <?php if(preg_match("/message/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="email" <?php if(preg_match("/email/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="sms" <?php if(preg_match("/sms/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
                     <!-- <label><input type="checkbox" name="configs[order_appeal_arbitration][]" value="push" <?php if(preg_match("/push/",$configs['order_appeal_arbitration']['type'])) : ?>checked="checked"<?php endif ?>> 极光推送</label> -->

                </td>
            </tr>

             <tr>
                <th>补仓功能提醒</th>
                <td>
                    <label><input type="checkbox" name="configs[goods_notify][]" value="message" <?php if(preg_match("/message/",$configs['goods_notify']['type'])) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[goods_notify][]" value="email" <?php if(preg_match("/email/",$configs['goods_notify']['type'])) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[goods_notify][]" value="sms" <?php if(preg_match("/sms/",$configs['goods_notify']['type'])) : ?>checked="checked"<?php endif ?>> 手机短信</label>
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