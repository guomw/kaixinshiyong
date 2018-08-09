<?php
defined('IN_ADMIN') or exit('No permission resources.');
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
<form action="<?php echo U('add'); ?>" method="post" name="myform" id="myform">                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="25%"><?php echo L('search_word_name');?> :</th>
		<td><input type="text" name="info[name]" id="word" size="20"></td>
	</tr>
	<tr>
		<th><?php echo '搜素地址';?> :</th>
		<td>
			<select name="info[type]" id="urltype">
				<option value="0">默认</option>
				<option value="1">自定义</option>
			</select>
		</td>
	</tr>
	
	<tr style="display:none;" id="address">
		<th>地址：</th>
		<td>
			<input type="text" name="info[address]" value=""  id="urladdress" />
		</td>
	</tr>
	<script type="text/javascript">
		$(function(){
			$("#urltype").change(function(){
				var _val = $(this).val();
				if(_val == 1){
					$("#address").show();
					$("#urladdress").unFormValidator(false); 
				}else{
					$("#address").hide();
				}
			});
		});
	</script>
	<tr>
		<th><?php echo L('seach_status');?> :</th>
		<td>
			<input type="radio" name="info[isrecommend]" value="1"  id="status" checked>是
			<input type="radio" name="info[isrecommend]" value="0"  id="status">否
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="<?php echo U('add');?>"> 
		<input type="submit" name="dosubmit" id="dosubmit" class="dialog"value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript"> 
<!--
$(function(){
	var urltype = $("#urltype").val();
	if(urltype == 0){
		//解除效验
		$("#urladdress").show().unFormValidator(true); 
	}else{
		$("#urladdress").unFormValidator(false); //恢复校验
	}
});

$.formValidator.initConfig({
	formid:"myform",
	autotip:true,
	onerror:function(msg,obj){
		$(obj).focus();
	}
});
$("#word").formValidator({
	empty:false,
	onempty:'关键字不能为空',
	onshow:"请输入关键字",
	onfocus:"请输入关键字"
}).regexValidator({
	regexp:'chinese',
	datatype:'enum',
	onerror:"关键字输入错误"
});
$("#urladdress").formValidator({
	empty:false,
	onempty:'搜索地址不能为空',
	onshow:"请输入搜索地址",
	onfocus:"请输入搜索地址"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:"搜索地址输入错误"
});
//-->
</script>
</body>
</html> 