<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	
	$("#type_name").formValidator({onshow:"<?php echo L("input").L('type_name')?>",onfocus:"<?php echo L("input").L('type_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('type_name')?>"}).ajaxValidator({
		type : "get",
		url : "<?php echo U('public_check_name');?>",
		data :{"typename":$("#type_name").val()},
		datatype : "html",
		async:"false",
		success : function(data){
			if( data == "1" ){
				return true;
			}else{
				return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "<?php echo L('type_name').L('exists')?>",
		onwait : "<?php echo L('connecting')?>"});
	 
	})
//-->
</script>
<div class="pad-lr-10">
<form action="<?php echo U('add');?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="100"><?php echo L('type_name')?>：</th>
		<td><input type="text" name="type[typename]" id="type_name"
			size="30" class="input-text" value=""></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('link_type_listorder')?>：</th>
		<td><input type="text" name="type[listorder]" id="listorder"
			size="5" class="input-text" value="0"></td>
	</tr>
	
	<tr>
		<th><?php echo L('type_description')?>：</th>
		<td><textarea name="type[describe]" id="description" cols="50"
			rows="6"></textarea></td>
	</tr>

	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('add_type');?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="button"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html>
