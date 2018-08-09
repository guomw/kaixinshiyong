<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	})
//-->
</script>
<div class="pad_10">
<form action="<?php echo U('edit'); ?>" method="post" name="myform" id="myform">
<input type="hidden" value="<?php echo $navid;?>" name="navid" />                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="25%"><?php echo L('nav_name');?> :</th>
		<td><input type="text" name="info[name]" id="name" size="20" value="<?php echo $name;?>" /></td>
	</tr>
	<tr>
		<th><?php echo L('nav_url');?> :</th>
		<td><input type="text" name="info[url]" value="<?php echo $url;?>"  id="url"></td>
	</tr>
	<tr>
		<th><?php echo L('nav_new');?> :</th>
		<td>
			<label><input type="radio" name="info[isblank]" value="1"  id="isblank" <?php if($isblank == 1){?>checked<?php }?>>是</label>
			<label><input type="radio" name="info[isblank]" value="0"  id="isblank" <?php if($isblank == 0){?>checked<?php }?>>否</label>
		</td>
	</tr>
	<tr>
		<th><?php echo L('status');?> :</th>
		<td>
			<label><input type="radio" name="info[status]" value="1"  id="status" <?php if($status == 1){?>checked<?php }?>>有效</label>
			<label><input type="radio" name="info[status]" value="0"  id="status" <?php if($status == 0){?>checked<?php }?>>无效</label>
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('edit');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
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
}).defaultPassed();
$("#url").formValidator({
	empty:false,
	onempty:'导航地址不能为空',
	onshow:"请输入导航地址",
	onfocus:"请输入导航地址"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"导航地址输入错误"
}).defaultPassed();
//-->
</script>
</body>
</html> 