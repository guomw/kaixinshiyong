<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
include $this->admin_tpl('header','Admin');
?>
<div class="pad_10">
<form action="<?php echo U('add'); ?>" method="post" name="myform" id="myform">                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="25%"><?php echo L('nav_name');?> :</th>
		<td><input type="text" name="info[name]" id="name" size="20"></td>
	</tr>
	<tr>
		<th><?php echo L('nav_url');?> :</th>
		<td><input type="text" name="info[url]" value=""  id="url"></td>
	</tr>
	<tr>
		<th><?php echo L('nav_new');?> :</th>
		<td>
			<label><input type="radio" name="info[isblank]" value="1"  id="isblank" checked>是</label>
			<label><input type="radio" name="info[isblank]" value="0"  id="isblank">否</label>
		</td>
	</tr>
	<tr>
		<th><?php echo L('status');?> :</th>
		<td>
			<label><input type="radio" name="info[status]" value="1"  id="status" checked>有效</label>
			<label><input type="radio" name="info[status]" value="0"  id="status">无效</label>
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('add');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript"> 
<!--
$.formValidator.initConfig({
	formid:"myform",
	autotip:true,
	onerror:function(msg,obj){
		$(obj).focus();
	}
});
$("#name").formValidator({
	empty:false,
	onempty:'导航名称不能为空',
	onshow:"请输入导航名称",
	onfocus:"请输入导航名称"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"导航名称输入错误"
});
$("#url").formValidator({
	empty:false,
	onempty:'导航地址不能为空',
	onshow:"请输入导航地址",
	onfocus:"请输入导航地址"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"导航地址输入错误"
});
//-->
</script>
</body>
</html> 