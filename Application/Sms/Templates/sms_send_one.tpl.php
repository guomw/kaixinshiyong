<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','Admin');
?>
<script type="text/javascript"> 
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	/*$("#subject").formValidator({onshow:"<?php echo L('input').L('subject')?>",onfocus:"<?php echo L('subject').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('subject').L('no_empty')?>"});*/
	$("#con").formValidator({onshow:"<?php echo L('content').L('no_empty')?>",onfocus:"<?php echo L('content').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('content').L('no_empty')?>"});
  	$("#tousername").formValidator({onshow:"<?php echo L('input').L('touserid')?>",onfocus:"<?php echo L('touserid').L('no_empty')?>"}).inputValidator({min:1,onerror:"<?php echo L('input').L('touserid')?>"}).ajaxValidator({type : "get",url : "<?php echo U('public_name') ?>",data :"",datatype : "JSON",async:'true',success : function(result){if( result.status == 1 ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('not_myself')?>! ",onwait : "<?php echo L('connecting')?>"});
})
//-->
</script> 
<div class="pad-lr-10"> 
<form action="<?php echo U(ACTION_NAME) ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<!-- <tr>
		<th width="100"><?php echo L('subject')?>：</th>
		<td><input type="text" name="info[subject]" id="subject"
			size="30" class="input-text" placeholder="私信标题"></td>
	</tr> -->
	
	<tr>
		<th width="100"><?php echo L('touserid')?>：</th>
		<td><input type="text" name="info[send_to_id]" id="tousername"
			size="20" class="input-text" value="" placeholder="输入用户名|email|手机号"></td>
	</tr>
	
	<tr>
		<th><?php echo L('content')?>：</th>
		<td><textarea name="info[content]" id="con" cols="50"
			rows="6" placeholder="私信内容"></textarea></td>
	</tr>

	<tr>
		<th></th>
		<td><input
		type="submit" name="dosubmit" id="dosubmit" class="button"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html>
