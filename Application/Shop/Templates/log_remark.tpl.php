<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#remark").formValidator({
		onshow:"请输入备注信息",
		onfocus:"请输入备注信息"
	}).inputValidator({
		min:1,
		max:30,
		onerror:"  请输入备注信息"
	});
});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('check') ?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $id;?>">
<fieldset>
	<legend>备注/(审核原因)</legend>
	<table width="100%" class="table_form">
		<tr>
			<td>状态</td> 
			<td>
				<label><input type="radio" name="status" value="-1" >不通过</label>&nbsp;&nbsp;
				<label><input type="radio" name="status" value="1" checked >通过</label>
			</td>
		</tr>
		<tr>
			<td>审核原因</td> 
			<td><textarea cols="50" rows="5" name="remark" id="remark"></textarea></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>