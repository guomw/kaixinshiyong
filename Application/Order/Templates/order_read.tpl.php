<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<fieldset>
	<legend>订单详细信息</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">订单ID</td> 
			<td><?php echo $order['id'] ?></td>
		</tr>
		<?php if ($order['order_sn']){ ?>
		<tr>
			<td width="80">订单号</td> 
			<td><?php echo $order['order_sn'] ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="80">活动类型</td> 
			<td><?php if ($order['act_mod'] == 'rebate'){echo '购物返利';}elseif($order['act_mod']=='trial'){echo '免费试用';}else{echo '九块九包邮';} ?></td>
		</tr>
		<tr>
			<td width="80">商品标题</td> 
			<td><a href="<?php echo $product['url'] ?>" target="_blank"><?php echo $product['title'] ?></a></td>
		</tr>
		<tr>
			<td width="80">会员昵称</td> 
			<td><?php echo $buyer['nickname'] ?></td>
		</tr>
		<tr>
			<td width="80">商家昵称</td> 
			<td><?php echo $seller['nickname'] ?></td>
		</tr>
		<tr>
			<td width="80">下单时间</td> 
			<td><?php echo dgmdate($order['create_time']) ?></td>
		</tr>
		<?php if ($order['check_time']){ ?>
		<tr>
			<td width="80">审核时间</td> 
			<td><?php echo dgmdate($order['check_time']) ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="80">当前状态</td> 
			<td><?php echo $state[$order['status']] ?></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<div class="bk15"></div>
<input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
</div>
</div>
</body>

<script language="JavaScript">
	$(function(){
	
})
<!--
	function changemodel(modelid) {
		redirect('?m=member&c=member&a=edit&userid=<?php echo $memberinfo[userid]?>&modelid='+modelid+'&pc_hash=<?php echo $_SESSION['pc_hash']?>');
	}
//-->
</script>
</html>