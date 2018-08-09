<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_validator = 1; 
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="<?php echo U(ACTION_NAME);?>" id="myform">
<input type="hidden" name="id" value="<?php echo $id ?>">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
	<tr>
		<th width="100">广告位名称：</th>
		<td><input name="poster[name]" value="<?php echo $rs['name'] ?>" id="name" class="input-text" type="text" size="25"></td>
	</tr>
	<tr>
    	<th align="right"  valign="top">广告类型：</th>
        <td valign="top" colspan="2"><?php echo $form::select($types, $rs['type'], 'name="poster[type]" id="type" onchange="AdsType(this.value)"', $default);?>
        </td>
    </tr>

    <tr>
		<th width="100">是否开启</th>
		<td><label><input type="radio" name="poster[disabled]" <?php if($rs['disabled'] != 1) {?>  checked <?php } ?> value="0">是</label>
			<label><input type="radio" name="poster[disabled]" <?php if($rs['disabled'] == 1) {?>  checked <?php } ?> value="1">否</label>
		</td>
	</tr>

	<tr>
		<th>上线时间：</th>
		<td><?php echo $form::date('poster[start_time]', dgmdate($rs['start_time']), 1)?></td>
	</tr>
	<tr>
		<th>下线时间：</th>
		<td><?php echo $form::date('poster[end_time]', dgmdate($rs['end_time']), 1)?></td>
	</tr>
	</tbody>
	</table>

	<div class="pad-10 poster_type" id="imagediv" <?php if ($rs['type'] != 'image'): ?>style="display:none;"<?php endif ?>>
		<fieldset>
			<legend>图片设置</legend>
			<table width="100%"  class="table_form">
				<tbody>
					<tr>
						<th width="80">链接地址：</th>
						<td class="y-bg">
						<input type="text" class="input-text" name="setting[image][linkurl]" id="linkurl3" size="30" value="<?php echo $rs['setting']['image']['linkurl'] ?>" /></td>
						<td rowspan="2">
							<a href="javascript:flashupload('imgurl_images', '<?php echo L('upload_photo')?>','imgurl',preview,'1,<?php echo $this->M['ext']?>,1','poster', '', '<?php echo $authkey?>');void(0);">

							<img src="<?php if ($rs['setting']['image']['imageurl']): ?><?php echo $rs['setting']['image']['imageurl'] ?><?php else: ?><?php echo IMG_PATH;?>icon/upload-pic.png<?php endif ?>" id="imgurl_s" width="105" height="88"></a>
							<input type="hidden" id="imgurl" name="setting[image][imageurl]" value="<?php echo $rs['setting']['image']['imageurl'] ?>">
						</td>
					</tr>
					<tr>
						<th>图片说明：</th>
						<td class="y-bg"><input type="text" class="input-text" name="setting[image][alt]" id="alt3" size="30" value="<?php echo $rs['setting']['image']['alt'] ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div class="pad-10 poster_type" id="textdiv" <?php if ($rs['type'] != 'text'): ?>style="display:none;"<?php endif ?>>
	<fieldset>
		<legend>文字链接</legend>
		<table width="100%"  class="table_form">
		<tbody>
			<tr>

				<th width="80">链接标题：</th>
				<td class="y-bg"><input type="text" class="input-text" name="setting[text][title]" value="<?php echo $rs['setting']['text']['title']?>" id="title" size="30" /></td>
			</tr>
			<tr>
				<th>链接地址：</th>
				<td class="y-bg">
					<input type="text" class="input-text" name="setting[text][linkurl]" id="link" size="30" value="<?php echo $rs['setting']['text']['linkurl']?>"  /></td>
			</tr>
		</tbody>
		</table>
	</fieldset>
	</div>

	<div class="pad-10 poster_type" id="codediv" <?php if ($rs['type'] != 'code'): ?>style="display:none;"<?php endif ?>>
	<fieldset>
		<legend>代码广告</legend>
		<table width="100%"  class="table_form">
		<tbody>
			<tr>
				<th width="80">代码内容：</th>
				<td class="y-bg"><textarea name="setting[code]" id="code" cols="55" rows="6"><?php echo $info['setting']['code']?><?php echo $rs['setting']['code'] ?></textarea></td>
			</tr>
		</tbody>
		</table>
	</fieldset>
	</div>

<div class="bk15" style="margin-left:10px; line-height:30px;">
	<input type="submit" name="dosubmit" id="dosubmit" value="提交" class="button">&nbsp;
	<input type="reset" value=" 返回上页 " class="button" onclick="history.go(-1)">
</div>	
</form>
</body>
</html>
<script type="text/javascript">
function AdsType(type) {
	$('.poster_type').hide();
	$('#'+type+'div').show();
}
$(document).ready(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){$(obj).focus();}});
	$('#name').formValidator({
		onshow:"请输入广告位名称",
		onfocus:"请输入广告位名称"
	}).inputValidator({
		min:1,
		onerror:"广告位名称不能为空"
	}).defaultPassed();

	$('#type').formValidator({
		onshow:"请选择广告位类型",
		onfocus:"请选择广告位类型",
		defaultvalue:"<?php echo $rs[type] ?>"
	}).inputValidator({
		min:0,
		onerror: "请选择广告位类型"
	}).defaultPassed();

	$('#start_time').formValidator({
		empty:true,
		onshow:"请选择广告上线时间",
		onfocus:"请选择广告上线时间"
	}).functionValidator({
		fun:isDateTime
	}).defaultPassed();

	$('#end_time').formValidator({
		empty:true,
		onshow:"请选择广告下线时间",
		onfocus:"请选择广告下线时间"
	}).functionValidator({
		fun:isDateTime
	}).defaultPassed();;
});

function preview(uploadid,returnid){
	var d = window.top.art.dialog({id:uploadid}).data.iframe;
	var in_content = d.$("#att-status").html().substring(1);
	$('#'+returnid).val(in_content);
	$('#'+returnid+'_s').attr('src', in_content);
}
</script>
<script type="text/javascript" src="<?php echo JS_PATH?>swfupload/swf2ckeditor.js"></script>