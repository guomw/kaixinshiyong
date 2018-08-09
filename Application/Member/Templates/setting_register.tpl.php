<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = True;
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
$(function(){
	SwapTab('setting','on','',4, '<?php echo $this->groupid; ?>');
})
//-->
</script>
<form action="<?php echo U(ACTION_NAME);?>" method="post" id="myform">
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_setting_base"><?php echo L('注册设置')?></li>
        </ul>
        <div id="div_setting_base" class="contentList pad-10">
            <table width="100%"  class="table_form">
                <!-- 人工注册审核 -->
                <tr>
                    <th width="120">是否开启注册：</th>
                    <td><?php echo $form::checkbox('setting[setting_register_enable]', $setting['setting_register_enable'], $models); ?>&nbsp;开启注册的会员类型</td>
                </tr>
                <tr>
                    <th>开启邮箱验证：</th>
                    <td><?php echo $form::checkbox('setting[setting_register_email_enable]', $setting['setting_register_email_enable'], $models); ?>&nbsp;开启邮箱验证的会员类型，需配置邮件发送接口</td>
                </tr>
                <tr>
                    <th>开启手机验证：</th>
                    <td><?php echo $form::checkbox('setting[setting_register_sms_enable]', $setting['setting_register_sms_enable'], $models); ?>&nbsp;开启手机短信验证的会员类型，需配置手机短信接口</td>
                </tr>
                <tr>
                    <th>开启验证码：</th>
                    <td><?php echo $form::checkbox('setting[setting_register_verify_enable]', $setting['setting_register_verify_enable'], $models); ?>&nbsp;开启图片验证码的会员类型，需服务器支持GD库</td>
                </tr>  


            </table>

            <br>
            <br>
            <br>

            <table width="100%"  class="table_form">
                <!-- 人工注册审核 -->
                <tr>
                    <th width="120">开启v2版手机注册：</th>
                    <td>

                    <?php echo $form::checkbox('setting[setting_register_v2_phone]', $setting['setting_register_v2_phone'], $models); ?>

                        &nbsp;是否开启手机注册的会员类型</td>
                </tr>
                <tr>
                    <th>开启v2版邮箱注册：</th>
                   <td>
                     <?php echo $form::checkbox('setting[setting_register_v2_email]', $setting['setting_register_v2_email'], $models); ?>
                    &nbsp;是否开启邮箱注册的会员类型</td>
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