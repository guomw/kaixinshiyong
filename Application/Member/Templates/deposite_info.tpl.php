<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
	<table width="100%" class="table_form">
		<tr>
			<td width="120">ID：</td> 
			<td><?php echo $cashid;?></td>  
		</tr>
		<tr>
			<td width="120">会员ID：</td> 
			<td><?php echo $userid;?></td>  
		</tr>
		<tr>
			<td width="120">申请时间：</td> 
			<td><?php echo dgmdate($inputtime,'Y-m-d');?></td>  
		</tr>
		<tr>
			<td width="120">状态：</td> 
			<td><?php if($status == 0){echo '待审核';}elseif($status == -1){echo '审核失败';}else{echo '审核通过';}?></td>  
		</tr>
		<?php if($status==-1){?>
		<tr>
			<td width="120">审核失败原因：</td> 
			<td><?php echo $cause;?></td>  
		</tr>
		<?php }?>
		<tr>
			<td width="120">申请ip：</td> 
			<td><?php echo $ip;?></td>  
		</tr>
		<tr>
			<td width="120">提现账号：</td>
			<td><?php echo $cash_alipay_username;?></td>
		</tr>
		<tr>
			<td width="120">提现方式：</td>
			<td><?php if($type == 1){?>提现到银行<?php }else{?>提现到支付宝<?php }?></td>
		</tr>
		
		<tr>
			<td width="120">提现方式：</td>
			<td><?php if($paypal == 1){?>普通提现<?php }else{?>快速提现<?php }?></td>
		</tr>
		
		<tr>
			<td width="120">提现总额：</td> 
			<td><?php echo $money;?></td>  
		</tr>
		<tr>
			<td width="120">手续费：</td> 
			<td><?php echo $fee;?></td>  
		</tr>
		<tr>
			<td width="120">实际金额：</td> 
			<td><?php echo $totalmoney;?></td>  
		</tr>
		<tr>
			<td width="120">提现人姓名：</td>
			<td><?php echo $name;?></td>
		</tr>
		<?php if($type == 1){?>
		<tr>
			<td width="120">提现银行：</td>
			<td><?php echo $bankname;?></td>
		</tr>
		
		<tr>
			<td width="120">开户行所在地：</td>
			<td><?php echo $privince.$city;?></td>
		</tr>
		<tr>
			<td width="120">支行名称：</td>
			<td><?php echo $infos['sub_branch'];?></td>
		</tr>
		
		<?php }else{?>
		
		<?php }?>
		<tr>
			<td width="120">操作人：</td>
			<td><?php echo $operator;?></td>
		</tr>
	</table>

</div>	
<input type="button"  name="dosubmit" id="dosubmit" value="返回" onclick="javascript:history.go(-1);"/>
</div>
</div>
</body>
</html>