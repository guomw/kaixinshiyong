<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript" src="/static/js/dialog/jquery.artDialog.js?skin=default"></script>
<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
	<input type="hidden" name="info[userid]" value="<?php echo $memberinfo['userid']?>"></input>
	<input type="hidden" name="info[username]" value="<?php echo $memberinfo['username']?>"></input>
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		 <tr>
			<td width="120"><?php echo '会员ID'?></td> 
			<td><?php echo $memberinfo['userid']?></td>
		</tr>
		<?php if ($memberinfo['modelid'] == 1){?>
		<tr>
			<td><?php echo L('avatar')?></td> 
			<td><img src="<?php echo $memberinfo['avatar']?>" onerror="this.src='<?php echo IMG_PATH?>member/nophoto.gif'" height=90 width=90></td>
		</tr>
		
		<?php }?>

		<?php if ($memberinfo['modelid'] == 1){?>
			<tr>
			<td><?php echo L('nickname')?></td> 
			<td><?php echo $memberinfo['nickname']?></td>
		</tr>
		<?php }else{?>
		<tr>
			<td>联系人</td> 
			<td><?php echo $member_infos['contact_name']?></td>
		</tr>
		<?php }?>
		
		<?php if ($memberinfo['modelid'] == 1){?>
		<tr>
			<td><?php echo L('member_group')?></td>
			<td>
			<?php echo member_group_name($memberinfo['userid']);?>
			</td>
		</tr>
		<?php }else{?>
		<tr>
			<td><?php echo '商家品牌';?></td>
			<td>
			<?php echo $memberinfo['brand_name'];?>&nbsp;&nbsp;
			<?php if($memberinfo['brand_attesta'] == 1){?><span class="green">已认证</span><?php }else{echo '未认证';}?>
			</td>
		</tr>
		<tr>
			<td><?php echo '店铺名称';?></td>
			<td>
			<?php echo $memberinfo['store_name'];?>&nbsp;&nbsp;<?php if($memberinfo['shop_attesta'] == 1){?><span class="green">已认证</span><?php }else{echo '未认证';}?>
			</td>
		</tr>

		<tr>
			<td><?php echo '店铺地址';?></td>
			<td>
			<?php echo $memberinfo['store_address'];?>&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td><?php echo L('member_group')?></td>
			<td>
			<?php echo member_group_name($memberinfo['userid']);?>
			</td>
		</tr>
			<?php }?>
		<?php if($memberinfo['vip']) {?>
		<tr>
			<td><?php echo L('vip').L('overduedate')?></td>
			<td>
			 <?php echo date('Y-m-d H:i:s',$memberinfo['overduedate']);?>
			</td>
		</tr>
		<?php }?>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('more_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120">注册时间：</td> 
			<td><?php echo dgmdate($memberinfo['regdate'],'Y-m-d');?></td>  
		</tr>
		<tr>
			<td width="120">注册ip：</td> 
			<td><?php echo $memberinfo['regip'];?></td>  
		</tr>
		<tr>
			<td width="120">最近登陆时间：</td> 
			<td><?php echo dgmdate($memberinfo['lastdate'],'Y-m-d');?></td>  
		</tr>
		<tr>
			<td width="120">最近登陆IP：</td> 
			<td><?php echo $memberinfo['lastip'];?></td>  
		</tr>
		<tr>
			<td width="120">登陆次数：</td> 
			<td><?php echo $memberinfo['loginnum'];?></td>  
		</tr>
		<tr>
			<td width="120">支付宝账号：</td> 
			<td><?php echo $memberinfo['alipay_account']?></td>  
		</tr>
		<tr>
			<td width="120">银行账号：</td> 
			<td><?php echo $memberinfo['band_address'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $memberinfo['bank_account'];?></td>  
		</tr>
		<tr>
			<td width="120">账户目前余额：</td>
			<td><?php echo $memberinfo['money'];?></td>
		</tr>
		<?php if($memberinfo['modelid'] == 1){?>
		<tr>
			<td width="120">账户积分：</td>
			<td><?php echo $memberinfo['point'];?></td>
		</tr>
		<tr>
			<td width="120">用户成长值等级：</td>
			<td><?php echo $memberinfo['exp'];?></td>
		</tr>
		<tr>
			<td width="120">推荐人ID：</td>
			<td><?php echo $memberinfo['agent_id'];?></td>
		</tr>
		<tr>
			<td width="120">本人推荐会员数量：</td>
			<td><?php echo $memberinfo['agent_count'];?></td>
		</tr>
		<?php }else{?>
		
		<?php }?>
		<tr>
			<td width="120">身份证号：</td> 
			<td><?php echo $memberinfo['id_number']?>&nbsp;&nbsp;
				<?php if ($memberinfo['id_number_status'] == 0): ?>
					未认证
				<?php else:?>
					<span class="green">已认证</span>
				<?php endif ?>
				
			</td>  
		</tr>
	</table>
</fieldset>

<div class="bk15"></div>
<fieldset>
	<legend><?php echo '联系信息';?></legend>
	<table>
	<tr>
			<td width="120">邮箱：</td>
			<td><?php echo $memberinfo['email'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if($memberinfo['email_status'] == 1){?><span class="green">已认证</span><?php }else{?>未认证<?php }?>
			</td>
		</tr>
		
		<tr>
			<td width="120">手机：</td>
			<td><?php echo $memberinfo['phone'];?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if($memberinfo['phone_status'] == 1){?><span class="green">已认证</span><?php }else{?>未认证<?php }?>
			</td>
		</tr>
		
		<tr>
			<td width="120">状态：</td>
			<td>
			<?php if($memberinfo['status'] == -2){?>已忽略<?php }else if($memberinfo['status'] == -1){?>拒绝<?php }else if($memberinfo['status'] == 0){?>待审核<?php }else{?>正常<?php }?>
			</td>
		</tr>
		
		<tr>
			<td width="120">联系地址：</td> 
			<td> <?php echo get_linkage($memberinfo['province'],1,'',2).get_linkage($memberinfo['city'],1,'',2).$memberinfo['contact_address']?></td>  
		</tr>

		
		<tr>
			<td width="120">联系人：</td> 
			<td><?php echo $memberinfo['contact_name']?></td>  
		</tr>
		
		<tr>
			<td width="120">联系方式：</td> 
			<td><?php echo $memberinfo['contact_tel']?></td>  
		</tr>
		<?php if($memberinfo['modelid'] == 1){?>
		<tr>
			<td width="120">联系QQ：</td> 
			<td><?php echo $memberinfo['qq']?></td>  
		</tr>
		
		<?php }?>
		<?php if($memberinfo['modelid'] == 2){?>
		<tr>
			<td width="120">联系QQ：</td> 
			<td><?php echo $memberinfo['store_qq']?></td>  
		</tr>
		<tr>
			<td width="120">联系人旺旺：</td> 
			<td><?php echo $memberinfo['contact_want']?></td>  
		</tr>
		<?php }?>
	</table>
</fieldset>
<div class="bk15"></div>

<?php if($memberinfo['modelid'] == 1){?>
<fieldset>
	<legend><?php echo '已绑定亚马逊亚马逊帐号';?></legend>
	<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left"><?php echo '亚马逊帐号'?></th>
			<th align="left"><?php echo '绑定时间';?></th>
			<th align="left"><?php echo '买家信誉';?></th>
			<th align="left"><?php echo '亚马逊截图'?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($taobaoinfo)){
	foreach($taobaoinfo as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['account']?></td>
		<td align="left"><?php echo dgmdate($v['inputtime']);?></td>
		<td align="left"><img src="<?php echo $v['account_level']?>" /></td>
		<td align="left"><img onclick="get_toabao_img('<?php echo $v['taobao_img']; ?>')"   height="30px" src="<?php echo $v['taobao_img']; ?>" /></td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
</fieldset>
<?php }?>



<?php if($memberinfo['modelid'] == 2){?>
<fieldset>
	<legend><?php echo '店铺绑定';?></legend>
	<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left"><?php echo '商家id'?></th>
			<th align="left"><?php echo '商家等级';?></th>
			<th align="left"><?php echo '店铺地址';?></th>
			<th align="left"><?php echo '店铺名称'?></th>
			<th align="left"><?php echo '店铺logo'?></th>

			<th align="left"><?php echo '联系人';?></th>
			<th align="left"><?php echo '店铺旺旺';?></th>
			<th align="left"><?php echo '所属类型';?></th>
			<th align="left"><?php echo '所属行业';?></th>
		
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($manage_lists)){
	foreach($manage_lists as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><?php echo member_group_name($v['userid']);?></td>
		<td align="left"><?php echo $v['store_address']?></td>

		<td align="left"><?php echo $v['store_name']?></td>
				<td align="left"><img src="<?php echo $v['store_logo']?>" width="60px" height="60px"></td>

		<td align="left"><?php echo $v['contact_name'];?></td>
		<td align="left"><?php echo $v['contact_want'];?></td>
		<td align="left"><?php if( $v['store_type'] == 1){echo '亚马逊';}else{echo '亚马逊';};?></td>
	    <td align="left"><?php echo $v['type'];?></td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
</fieldset>
<?php }?>

<div class="bk15"></div>
<fieldset>
	<legend><?php echo '活动信息';?></legend>
	<table>
		<?php if($memberinfo['modelid'] == 1){?>
		<tr>
			<td width="120">参与次数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1);?></td>  
		</tr>
		
		<tr>
			<td width="120">订单失效次数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,-1);?></td>  
		</tr>
		
		<tr>
			<td width="120">订单关闭次数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,0); ?></td>  
		</tr>
		
		<tr>
			<td width="120">订单抢购条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,1);?></td>  
		</tr>
		
		<tr>
			<td width="120">订单确认条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,2);?></td>  
		</tr>
		
		<tr>
			<td width="120">待审核订单条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,3);?></td>  
		</tr>
		
		<tr>
			<td width="120">审核失败订单条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,4);?></td>  
		</tr>
		
		<tr>
			<td width="120">审核通过订单条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,5);?></td>  
		</tr>
		
		<tr>
			<td width="120">申诉中订单条数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,6);?></td>  
		</tr>
		
		<tr>
			<td width="120">订单完成次数：</td> 
			<td><?php echo order_count($memberinfo['userid'],1,7);?></td>  
		</tr>
		<?php }else{?>
		<tr>
			<td width="20%">活动信息总数：</td> 
			<td><?php echo activity_count($memberinfo['userid']);?></td>  
		</tr>
		
		<tr>
			<td width="20%">购物返利总条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'rebate');?></td>  
		</tr>
		
		<tr>
			<td width="20%">免费试用总条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'trial');?></td>  
		</tr>
		
		<tr>
			<td width="20%">9.9包邮总条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'postal');?></td>  
		</tr>
		
		<tr>
			<td width="20%">待审核（待付款）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',-3);?></td>  
		</tr>
		
		<tr>
			<td width="20%">待审核（已付款）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',-2);?></td>  
		</tr>
		
		<tr>
			<td width="20%">审核通过（待上线）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',-1);?></td>  
		</tr>
		
		<tr>
			<td width="20%">审核失败（已退款）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',0);?></td>  
		</tr>
		
		<tr>
			<td width="20%">活动进行中条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',1);?></td>  
		</tr>
		
		<tr>
			<td width="20%">活动结束（结算中）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',2);?></td>  
		</tr>
		
		<tr>
			<td width="20%">活动结束（已结算）条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',3);?></td>  
		</tr>
		
		<tr>
			<td width="20%">已撤销条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',4);?></td>  
		</tr>
		
		<tr>
			<td width="20%">已屏蔽条数：</td> 
			<td><?php echo activity_count($memberinfo['userid'],'',5);?></td>  
		</tr>
		<?php }?>
	</table>
</fieldset>
</div>	
<input type="button"  name="dosubmit" id="dosubmit" value="返回" onclick="javascript:history.go(-1);"/>
</div>
</div>
</body>

<script type="text/javascript">
	 /*查看已上传的信誉截图*/
	 function get_toabao_img(img){
		art.dialog({
			lock: true,fixed: true,
			title: '已上传的信誉截图',
			time:5,
			content:'<img src="'+img+'" />',
			ok: function (){

				}
			});
	 }
</script>
</html>