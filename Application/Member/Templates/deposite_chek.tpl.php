<?php defined('IN_ADMIN') or exit('No permission resources.');?>

<?php
$show_header = TRUE;
 include $this->admin_tpl('header', 'admin');?>

<div class="pad_10">
	<form action="<?php echo U('check'); ?>" method="post" name="myform" id="myform" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $id;?>" />                                                  
		<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
				<tr>
					<td width="120">实际支付给会员金额：</td> 
					<td> <?php echo $rs['totalmoney'];?> ( 提现总额:<?php echo $rs['money'];?> - 手续费<?php echo $rs['fee'];?>)</td>
				</tr>
				<tr>
					<td width="120">备注银行交易成功单号:</td> 
					<td><input type="text" name="success_order" value=""></td>
					<td><input type="hidden" name="forward" value="<?php echo U('check');?>"> <input
					type="submit" name="dosubmit" id="dosubmit" class="dialog"
					value="<?php echo L('submit')?> "></td>
				</tr>
		</table>
	</form>
</div>
</body>
</html>