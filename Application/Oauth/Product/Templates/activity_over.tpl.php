<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = false;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>dialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo THEME_STYLE_PATH ?>style/js/taobao.js"></script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('activity_over'); ?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $info['id'] ?>">
<fieldset>
	<legend>基本信息</legend>
	<table width="100%" class="table_form">
		<tr>
			<td>商品标题：</td> 
			<td><?php echo $info['title']; ?></td>
		</tr>
		<tr>
			<td>活动周期</td> 
			<td><?php echo dgmdate($info['start_time']) ?> - <?php echo dgmdate($info['end_time'])?></td>
		</tr>
		<tr>
			<td>当前状态</td> 
			<td><span style="color:red;"><?php echo $this->activity_status[$info['status']]; ?></span></td>
		</tr>
		<tr>
			<td>商品份数</td>
			<td><?php echo $info['goods_number'] ?> 份</td>
		</tr>
		<tr>
			<td>已售份数</td>
			<td><?php echo $info['already_num'] ?> 份</td>
		</tr>
		<tr>
			<td>商品下单价</td>
			<td><?php echo $info['goods_price'] ?>美元</td>
		</tr>
		<tr>
			<td>赠送红包</td>
			<td><?php echo $info['goods_bonus'] ?>美元</td>
		</tr>
		<?php if ($info['mod'] == 'rebate') { ?>
		<tr>
			<td>商品折扣</td>
			<td><?php echo $info['goods_discount'] ?>折</td>
		</tr>
		<?php } ?>
		<?php if ($info['mod'] == 'rebate') { ?>
			<tr>
				<td>单笔平台损耗费</td>
				<td><?php echo $info['goods_service']*$info['goods_price']/100 ?></td>
			</tr>
		<?php }elseif ($info['mod'] == 'trial'){ ?>		
			<tr>
				<td>收费方式</td>
				<td><?php if ($info['goods_charge_way'] == 1 ){echo '按单场:';}else{echo '按单份:';};echo '('.$info['goods_service'].'美元)' ?></td>
			</tr>
			<tr>
				<td>剩余推广费退还</td>
				<td><?php if (C_READ('seller_give_back') == 1 ){echo '退还';}else{echo '不退还';} ?></td>
			</tr>
			<tr>
				<td>总推广费</td>
				<td>
					<?php if ($info['goods_charge_way'] == 0){echo $info['goods_service_total'];}else{echo $info['goods_service'];} ?> 美元；
					<span style='margin-left:10px;'>
						<?php if ($info['goods_charge_way'] == 0) { ?>
							总推广费 = 单份推广费 * 已售份数
						<?php }else{ ?>
							总推广费 = 单场推广费
						<?php } ?>
					</span>
				</td>
			</tr>
		<?php } ?>		
		<tr> 
			<td>已返还总额</td>
			<td><?php echo $info['goods_member_total'] ?> 美元；已返给会员的总金额</td>
		</tr>
		<?php if ($info['mod'] == 'rebate'){ ?>
			<tr>
				<td>总平台损耗费</td>
				<td><?php echo $info['goods_service_total'] ?> 美元；<span style='margin-left:10px;'>总保证金 = 总份数 - (总份数 * 下单价 * 单笔平台损耗费)</span></td>
			</tr>
		<?php }elseif ($info['mod'] == 'trial'){ ?>
			<tr>
				<td>总保证金</td>
				<td><?php echo $info['goods_deposit'] ?> 美元； 
					<span style='margin-left:10px;'>
						<?php if ($info['goods_charge_way'] == 0){ ?>
							<!-- 按单份 -->
							总保证金 = 总份数 * (下单价+单份推广费)
						<?php }else{ ?>
							总保证金 = 总份数 * 下单价 + 单场推广费
						<?php } ?>
					</span>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td>应退还保证金</td>
			<td><?php echo $info['goods_company_return']; ?> 美元；
				<span style='margin-left:10px;'>
					<?php if ($info['mod']== 'rebate' ) { ?>
						应退还保证金=总保证金-已售份数*(返还会员金额+单笔平台损耗费)
					<?php }elseif($info['mod']=='trial'){ ?>
						应退还费用 = (总份数 - 已售份数) * 下单价
					<?php } ?>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan="2"><span style="color:red;">* 请仔细核对好信息后再进行确认结算哦~该操作不可逆</span></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>