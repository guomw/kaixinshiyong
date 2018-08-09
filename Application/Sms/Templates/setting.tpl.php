<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-10">
<?php $smsnotice = get_smsnotice(); ?>
<?php if ($smsnotice): ?>
<div class="explain-col search-form">
<?php echo $smsnotice; ?>
</div>
<?php endif ?>
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME); ?>" method="post" id="myform">
<table width="100%" class="table_form">
<tr>
<td  width="120"><?php echo L('sms_enable')?></td> 
<td><input name="setting[sms_enable]" value="1" type="radio" id="sms_enable" <?php if($sms_setting['sms_enable'] == 1) {?>checked<?php }?>> <?php echo L('open')?>  
<input name="setting[sms_enable]" value="0" type="radio" id="sms_enable" <?php if($sms_setting['sms_enable'] == 0) {?>checked<?php }?>> <?php echo L('close')?></td>
</tr>

<tr>
<td>短信接口</td>
<td>
<?php if ($files): ?>
	<select name="setting[type]" onchange="get">
	<?php foreach ($files as $file): ?>
		<option value="<?php echo $file ?>" <?php if ($sms_setting['type'] == $file): ?>selected<?php endif ?>><?php echo $file ?></option>
	<?php endforeach ?>
	</select>
	&nbsp;&nbsp;Webchinese:网建短信平台，&nbsp;&nbsp;Ali:阿里大鱼短信平台 阿里大鱼不支持个人
<?php else: ?>
暂无任何可用接口
<?php endif ?>
</td>
</tr>

<tr>
	<td>用户名</td>
	<td><input type="text" name="setting[username]" value="<?php echo $sms_setting['username'] ?>"> &nbsp;&nbsp;(中国网建请填写用户名，阿里大鱼请填写：appkey)</td>
</tr>

<tr>
	<td>密钥</td>
	<td><input type="text" name="setting[password]" value="<?php echo $sms_setting['password'] ?>"> &nbsp;&nbsp;(中国网建请填写api密钥，阿里大鱼请填写：secret)</td>
</tr>

<tr>
	<td>签名</td>
	<td><input type="text" name="setting[target]" value="<?php echo $sms_setting['target'] ?>">&nbsp;&nbsp;(中国网建无需填写签名，阿里大鱼请设置签名 且必须与阿里大鱼平台一致) </td>
</tr>

</table>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button" id="dosubmit">
</form>
<div class="bk15"></div>
<div class="explain-col">
<font color="red">注意：</font>接口的存放目录为： <?php echo MODULE_PATH ?>Api/Driver <br>您可进行定制拓展，相关平台的账号或密钥（码）请打开该文件进行修改配置保存即可。</div>
</div>
</body>
</html>