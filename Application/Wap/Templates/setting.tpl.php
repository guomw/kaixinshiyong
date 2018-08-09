<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U(ACTION_NAME); ?>" method="post" id="myform">
<table width="100%" class="table_form">
<tr>
<td  width="120">开启移动端</td> 
<td>
	<label><input name="setting[wap_enable]" value="1" type="radio" id="wap_enable" <?php if($setting['wap_enable'] == 1) {?>checked<?php }?>> 开启</label>
	<label><input name="setting[wap_enable]" value="0" type="radio" id="wap_enable" <?php if($setting['wap_enable'] == 0) {?>checked<?php }?>> 关闭</label>
</td>
</tr>

<tr>
<td>域名绑定</td>
<td>
<input type="text" name="setting[wap_domain]" value="<?php echo $setting['wap_domain'] ?>">&nbsp;请输入移动端使用的域名（请以http开头，结尾不要加“/”）
</td>
</tr>
</table>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button" id="dosubmit">
</form>
<div class="bk15"></div>
<div class="explain-col">
<font color="red">注意：</font>请将您设置的移动端域名解析到您的主机。</div>
</div>
</body>
</html>