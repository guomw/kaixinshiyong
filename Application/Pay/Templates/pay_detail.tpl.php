<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header', 'admin');
?>

<div class="pad-10">
<div class="common-form">
<fieldset>
<legend><?php echo L('basic_config')?></legend>
<table width="100%" class="table_form">
<tr>
<td  width="120"><?php echo L('username')?></td> 
<td><?php echo $order_list['username']?>[<?php echo $order_list['userid']?>]</td>
</tr>
<tr>
<td  width="120"><?php echo L('contact_email')?></td> 
<td><?php echo $order_list['email']?></td>
</tr>
<tr>
<td  width="120"><?php echo L('contact_phone')?></td> 
<td><?php echo $order_list['telephone']?></td>
</tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
<legend><?php echo L('order_info')?></legend>
<table width="100%" class="table_form">
<tr>
<td  width="120"><?php echo L('order_sn')?></td> 
<td><?php echo $order_list['trade_sn']?></td>
</tr>
<tr>
<td  width="120"><?php echo L('order_name')?></td> 
<td><?php echo $order_list['subject']?></td>
</tr>

<tr>
<td  width="120"><?php echo L('order_price')?></td> 
<td><?php echo $order_list['total_fee']?> <?php echo L('yuan')?></td>
</tr>
<tr>
<td  width="120"><?php echo L('order_discount')?></td> 
<td><?php echo $discount?> <?php echo L('yuan')?></td>
</tr>

<tr>
<td  width="120"><?php echo L('order_addtime')?></td> 
<td><?php echo date('Y:m:d H:i:s',$order_list['dateline'])?></td>
</tr>
<!-- 
<tr>
<td  width="120"><?php // echo L('order_ip')?></td> 
<td><?php //echo $ip?></td>
</tr> -->

<tr>
<td  width="120"><?php echo L('payment_type')?></td> 
<td><?php echo $order_list['payment']?></td>
</tr>
<!-- 
<tr>
<td  width="120"><?php echo L('order').L('usernote')?></td> 
<td><?php echo $order_list['usernote']?></td>
</tr>
<?php //if($adminnote) {?>
<!--  <tr>
<td  width="120"><?php echo L('adminnote')?></td> 
<td><?php echo $adminnote?></td>
</tr>-->
<?php //}?>
</table>
</fieldset>
</div>
</body>
</html>
<script type="text/javascript">

$(document).ready(function() {
	$("#paymethod input[type='radio']").click( function () {
		if($(this).val()== 0){
			$("#rate").removeClass('hidden');
			$("#fix").addClass('hidden');
			$("#rate input").val('0');
		} else {
			$("#fix").removeClass('hidden');
			$("#rate").addClass('hidden');
			$("#fix input").val('0');
		}	
	});
});
function category_load(obj)
{
	var modelid = $(obj).attr('value');
	$.get('?m=admin&c=position&a=public_category_load&modelid='+modelid,function(data){
			$('#load_catid').html(data);
		  });
}
</script>


