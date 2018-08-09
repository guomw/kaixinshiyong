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
<form action="<?php echo U('two_config');?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <fieldset>
        <legend>二次通知通知模板配置</legend>    
        <table width="100%"  class="table_form">

             <tr>
                <th>到期时间设置</th>
                <td>
                  <div>

                  
                <label>到期前： <input type="text" name="configs[two_notice][time]" value="<?php echo C('two_notice.time') ?>" size="5">分钟提醒</label>

                </div>

                    
                </td>
            </tr>
           
           
           
            <tr>
                <th>用户获得试用资格之后，未下单提醒</th>
                <td>
                     <div>

                    <label><input type="checkbox" name="configs[two_trial_pass][message]" value="1" <?php if(C('two_trial_pass.message')  == 1) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[two_trial_pass][email]" value="1" <?php if(C('two_trial_pass.email') == 1 ) : ?> checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[two_trial_pass][sms]" value="1" <?php if(C('two_trial_pass.sms') == 1) : ?>checked="checked"<?php endif ?>> 手机短信</label>
<!--                      <label>到期前： <input type="text" name="configs[two_trial_pass][time]" id="trial_pass" size="5">(系统设置单位：小时)</label>
 -->

                </div>

                    
                </td>
            </tr>
              <tr>
                <th>用户未填写试用报告，提醒</th>
                <td>
                     <div><label><input type="checkbox" name="configs[two_trial_report][message]" value="1" <?php if(C('two_trial_report.message') == 1) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[two_trial_report][email]" value="1" <?php if(C('two_trial_report.email') == 1) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[two_trial_report][sms]" value="1" <?php if(C('two_trial_report.sms') == 1) : ?>checked="checked"<?php endif ?>> 手机短信</label>
<!--                     <label>到期前： <input type="text" name="configs[two_trial_report][time]" id="trial_report" size="5">(系统设置单位：天)</label>
 -->
                </div>

                </td>
            </tr>
           
             <tr>
                <th>抢购返利商品未填写订单号</th>
                <td>
                     <div><label><input type="checkbox" name="configs[two_rebate_order_sn][message]" value="1" <?php if(C('two_rebate_order_sn.message') == 1) : ?>checked="checked"<?php endif ?>> 站内信</label>
                    <label><input type="checkbox" name="configs[two_rebate_order_sn][email]" value="1" <?php if(C('two_rebate_order_sn.email') == 1) : ?>checked="checked"<?php endif ?>> 邮件通知</label>
                    <label><input type="checkbox" name="configs[two_rebate_order_sn][sms]" value="1" <?php if(C('two_rebate_order_sn.sms') == 1) : ?>checked="checked"<?php endif ?>> 手机短信</label>
<!--                     <label>到期前： <input type="text" name="configs[two_rebate_order_sn][time]" size="5">(系统设置单位：分钟)</label>
 -->
                </div>

                    
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

$(function(){
    /*$('#trial_pass').blur(function(){
        var system = "<?php echo C_READ('buyer_write_order_time') ?>";
        if ($(this).val() >= system) {
            alert('该设置超过了系统设置时间');
        };
    });

     $('#trial_report').blur(function(){
        var system = "<?php echo C_READ('buyer_write_talk_time') ?>";
        if ($(this).val() >= system) {
            alert('该设置超过了系统设置时间');
        };
    });*/

});
function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    $('div.contentList').hide();
    $('ul.tabBut > li').attr('class', cls_hide);
    $('#div_'+name+'_'+cur).show();
    $('#tab_'+name+'_'+cur).attr('class',cls_show);
}
</script>
</html>