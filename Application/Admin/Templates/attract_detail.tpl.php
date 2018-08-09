<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
	<input type="hidden" name="info[userid]" value="<?php echo $memberinfo['userid']?>"></input>
	<input type="hidden" name="info[username]" value="<?php echo $memberinfo['username']?>"></input>
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		 <tr>
			<td width="120">专员id</td> 
			<td><?php echo $admininfo['userid']?></td>
		</tr>
		

		
		<tr>
			<td>姓名</td> 
			<td><?php echo $admininfo['username']?></td>
		</tr>

		<tr>
			<td>联系电话</td> 
			<td><?php echo $admininfo['phone']?></td>
		</tr>
		
		
	
		<tr>
			<td>qq</td>
			<td>
			<?php echo $admininfo['qq'] ?>
			</td>
		</tr>

		<tr><td colspan="2">本月累计获得提成<font color="red"><?php echo $admininfo['month_money'] ?></font>元，基本工资<font color="red"><?php echo $admininfo['fee_money'] ?></font>元，预计月发放工资<font color="red"><?php echo sprintf("%.2f",$admininfo['month_money']+$admininfo['fee_money']) ?></font>元。</td></tr>

		
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend>新增商家</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120">今日新增商家</td> 
			<td><?php echo $admininfo['today_seller'];?> 名 </td>  
		</tr>
		<tr>
			<td width="120">本周新增商家</td> 
			<td><?php echo $admininfo['week_seller'];?> 名 </td>  
		</tr>
		<tr>
			<td width="120">本月新增商家</td> 
			<td><?php echo $admininfo['month_seller'];?> 名 </td>  
		</tr>
		<tr>
			<td width="120">累计商家</td> 
			<td>
			<?php echo $admininfo['total_seller'];?> 名 </td> 
		</tr>
		
		
		
		
	</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend>充值/续费VIP</legend>
	<table>
		<tr>
			<td width="120">今日升级/续费钻石商家:</td>
			<td>
				<?php echo $admininfo['day_group']; ?> 名
			</td>
		</tr>
		
		<tr>
			<td width="120">本周升级续费/续费钻石商家：</td>
			<td>
				<?php echo $admininfo['week_group']; ?> 名

			</td>
		</tr>
		
		<tr>
			<td width="120">本月升级/续费钻石商家:</td>
			<td>
				<?php echo $admininfo['month_group']; ?> 名

			</td>
		</tr>
		
		<tr>
			<td width="120">累计升级/续费钻石商家  :</td> 
			<td><?php echo $admininfo['total_zuan_group']?$admininfo['total_zuan_group']:0; ?> 
 名</td>  
		</tr>

		<tr>
			<td width="120">今日升级/续费皇冠商家:</td>
			<td>
				<?php echo $admininfo['day_group_3']; ?> 名
			</td>
		</tr>
		
		<tr>
			<td width="120">本周升级续费/续费皇冠商家：</td>
			<td>
				<?php echo $admininfo['week_group_3']; ?> 名

			</td>
		</tr>
		
		<tr>
			<td width="120">本月升级/续费皇冠商家:</td>
			<td>
				<?php echo $admininfo['month_group_3']?$admininfo['month_group_3']:0; ?> 名

			</td>
		</tr>
		
		<tr>
			<td width="120">累计升级/续费皇冠商家:</td> 
			<td><?php echo $admininfo['total_huang_group']?$admininfo['total_huang_group']:0; ?> 
名</td>  
		</tr>

</tbody>
</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend>订单信息</legend>
	<table>
		<tr>
			<td width="120">今日新增已完成订单</td> 
			<td><?php echo $admininfo['day_order'];?></td>  
		</tr>
		
		<tr>
			<td width="120">本周新增已完成订单</td> 
			<td><?php echo $admininfo['week_order'];?></td>  
		</tr>
		
		<tr>
			<td width="120">本月新增已完成订单</td> 
			<td><?php echo $admininfo['month_order'];?></td>  
		</tr>
		
		
	</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend>工资/提成</legend>
	<table>
		<tr>
			<td width="120">基本工资</td> 
			<td><font color="red"><?php echo $admininfo['fee_money'];?></font> /元</td>  
		</tr>
		
		<tr>
			<td width="120">提成模式</td> 
			<td><?php if ($admininfo['fee_type'] == 1):?>按照缴纳保证金提成<?php elseif($admininfo['fee_type'] == 2): ?>按照已完成订单下单价提成<?php elseif($admininfo['fee_type'] == 3): ?>按照已完成订单服务费提成 <?php endif; ?></td>  
		</tr>
		
		<tr>
			<td width="120">提成比例</td> 
			<td><font color="red"><?php echo $admininfo['service_fee'];?> </font>%</td>  
		</tr>
		<?php $_name = string2array($admininfo['company_config']); ?>

		<tr>
			<td width="120">升级/续费 钻石商家</td> 
			<td><font color="red"><?php echo $_name['service_zuan_fee'];?></font> /元</td>  
		</tr>

		<tr>
			<td width="120">升级/续费 皇冠商家</td> 
			<td><font color="red"><?php echo $_name['service_huang_fee'];?></font> /元</td>  
		</tr>
		
		
	</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend>今日提成统计</legend>
	<table>
		<tr>
			<td width="120">今日已获得充值/续费 钻石vip提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['day_money_zuan']);?> </font>/元</td>  
		</tr>
		
		<tr>
			<td width="120">今日已获得充值/续费 皇冠vip</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['day_money_huang']);?> </font>/元</td>  
		</tr>
		<?php if ($admininfo['fee_type'] == 2): ?>
		
		<tr>
			<td width="120">今日获得已完成订单提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['day_one_order']);?> </font>/元</td>  
		</tr>

		<tr>
			<td width="120">累计今日已获得提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f", $admininfo['day_money_zuan']+$admininfo['day_money_huang']+$admininfo['day_one_order']);?> </font>/元</td>  
		</tr>

	   <?php endif ?>

		<?php if ($admininfo['fee_type'] == 1): ?>

		<tr>
			<td width="120">今日获得缴纳保证金提成</td> 
			<td><font color="red"><?php echo $admininfo['day_goods_service'];?></font> /元</td>  
		</tr>

		<tr>
			<td width="120">累计今日已获得提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['day_money_zuan']+$admininfo['day_money_huang']+$admininfo['day_goods_service']);?></font> /元</td>  
		</tr>

	    <?php endif ?>
		
	</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend>本月提成统计</legend>
	<table>
		<tr>
			<td width="120">本月已获得充值/续费 钻石vip提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['month_money_zuan']) ;?> </font> /元</td>  
		</tr>
		
		<tr>
			<td width="120">本月已获得充值/续费 皇冠vip</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['month_money_huang']);?></font> /元</td>  
		</tr>
		<?php if ($admininfo['fee_type'] == 2): ?>
		
		<tr>
			<td width="120">本月获得已完成订单提成</td> 
			<td><font color="red"><?php echo $admininfo['month_one_order']?$admininfo['month_one_order']:0;?></font> /元</td>  
		</tr>

		<tr>
			<td width="120">累计本月已获得提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['month_money_zuan']+$admininfo['month_money_huang']+$admininfo['month_one_order']) ;?></font> /元</td>  
		</tr>

	   <?php endif ?>

		<?php if ($admininfo['fee_type'] == 1): ?>

		<tr>
			<td width="120">本月获得缴纳保证金提成</td> 
			<td><font color="red"><?php echo $admininfo['month_goods_service']?$admininfo['month_goods_service']:0;?> </font> /元</td>  
		</tr>

		<tr>
			<td width="120">累计本月已获得提成</td> 
			<td><font color="red"><?php echo sprintf("%.2f",$admininfo['month_money_zuan']+$admininfo['month_money_huang']+$admininfo['month_goods_service']) ;?></font> /元</td>  
		</tr>

	    <?php endif ?>
		
	</table>
</fieldset>
</div>	
<input type="button"  name="dosubmit" id="dosubmit" value="返回" onclick="javascript:history.go(-1);"/>
</div>
</div>
</body>
</html>