<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = TRUE;
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#link_name").formValidator({onshow:"<?php echo L("input").L('link_name')?>",onfocus:"<?php echo L("input").L('link_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('link_name')?>"});
 	$("#link_url").formValidator({onshow:"<?php echo L("input").L('url')?>",onfocus:"<?php echo L("input").L('url')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('url')?>"}).regexValidator({regexp:"^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&]*([^<>])*$",onerror:"<?php echo L('link_onerror')?>"})
	 
	})
//-->
</script>
<div class="pad_10">
<form action="<?php echo U('add'); ?>" method="post" name="myform" id="myform" enctype="multipart/form-data">                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">	

	<tr>
		<th width="100"><?php echo L('card_title')?>：</th>
		<td><input type="text" name="card[name]" id="card_name"
			size="30" class="input-text"></td>
	</tr>
	
	<tr id="logolink">
		<th width="100"><?php echo L('logo')?>：</th>
		<td><?php echo $form::images('card[image]', 'image', $image, 'document');?></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('card_money');?>：</th>
		<td><input type="text" name="card[money]" id="link_url"
			size="30" class="input-text"></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('card_point')?>：</th>
		<td><input type="text" name="card[point]" id="link_username"
			size="30" class="input-text" value=""></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('card_points')?>：</th>
		<td><input type="text" name="card[addpoint]" id="link_username"
			size="30" class="input-text" value=""></td>
	</tr>
	
	<tr>
		<th><?php echo L('card_discount')?>：</th>
		<td><input name="card[discount]" type="text" value="0" size="30" class="input-text">
	</tr>
	
	<tr>
		<th><?php echo L('card_msg')?>：</th>
		<td><textarea name="card[discription]" id="introduce" cols="50"
			rows="6"></textarea></td>
	</tr>
	
	<tr>
		<th><?php echo L('is_hot')?>：</th>
		<td><input name="card[ishot]" type="radio" value="1" checked>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;<input
			name="card[ishot]" type="radio" value="0">&nbsp;<?php echo L('no')?></td>
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
</body>
</html> 