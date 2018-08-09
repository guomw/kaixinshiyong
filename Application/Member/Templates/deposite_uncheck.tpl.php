<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = TRUE;
include $this->admin_tpl('header','Admin');
?>
<div class="pad_10">
<form action="<?php echo U('uncheck'); ?>" method="post" name="myform" id="myform" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $id;?>" />                                                  
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
	    <th width="100">快捷选择原因:</th>
	    <td width="204">
		    <select id="kj">
	        			<option value="银行卡号或者支付宝帐号错误" selected>银行卡号或者支付宝帐号错误</option>
	        			<option value="银行在维护">银行在维护</option>
	        			<option value="账户异常">账户异常</option>
	        			<option value="微信未实名认证">微信未实名认证</option>
	        			<option value="微信实名认证与平台实名认证不一致">微信实名认证与平台实名认证不一致</option>
	        			<option value="平台系统维护 暂停提现">平台系统维护 暂停提现</option>
	        </select>
        </td>
	</tr>
	<tr>
    <th width="100">提现未通过原因：</th>
    <td><textarea id="cause" cols="25" rows="5" name="cause"></textarea></td>
	</tr>

	<tr>
		<th></th>
		<td>
		<input type="hidden" name="forward" value="<?php echo U('uncheck');?>"> 
		<input type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
<script type="text/javascript">
	$('#cause').val($('#kj').children('option:selected').val());

	//快捷选择原因
	$('#kj').change(function(){ 
	 var p1=$(this).children('option:selected').val();//这就是selected的值 
      $('#cause').val(p1);
	}) 
</script>
</html> 