<?php
	defined('IN_ADMIN') or exit('No permission resources.');
	$show_dialog = 1;
	include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="myform" action="<?php echo U('pay_list',array('menuid' => MENUID));?>" method="post" >
<div class="explain-col search-form">
<!--<?php echo L('order_sn')?>  <input type="text" value="" class="input-text" name="info[trade_sn]">
<?php echo L('username')?>  <input type="text" value="" class="input-text" name="info[username]">-->
支付时间  <?php echo $form::date('info[start_addtime]',$start_addtime)?><?php echo L('to')?>   <?php echo $form::date('info[end_addtime]',$end_addtime)?>
<?php echo  $form::select(array('1'=>L('用户名'), '2'=>L('支付订单号')), $type, 'name="info[type]"')?>： <input type="text" value="<?php echo $username?>" class="input-text" name="info[keywords]"> 
<?php echo $form::select($trade_status,$status,'name="info[status]"', L('all_status'))?>
<input type="hidden" value="<?php echo MENUID?>" name="menuid">
<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
</div>
</form>
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="2%"><?php echo L('ID')?></th>
            <th width="4%"><?php echo '会员ID';?></th>
             <th width="5%"><?php echo '会员类型';?></th>
            <th width="6%"><?php echo '充值人姓名'?></th>
            <th width="18%"><?php echo L('order_sn')?></th>
            <th width="10%"><?php echo L('payment_mode')?></th>
            <th width="8%"><?php echo '支付平台账号'?></th>
            <th width="8%"><?php echo L('deposit_amount')?></th>
            <th width="10%"><?php echo L('pay_status')?></th>
            <th width="15%"><?php echo L('order_time')?></th>
            <th width="20%"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php
if(is_array($pay_list)){
	$sum_point = $sum_point_succ = '0';
	foreach ($pay_list as $info){
		$sum_point += $info['total_fee'];
		if($info['status'] == 1) $sum_point_succ += $info['total_fee'];
?>
	<tr>
	<td align="center"><?php echo $info['id']?></td>
	<td align="center"><?php echo $info['userid']?></td>
	<td align="center"><?php if($info['modelid'] == 1){?>会员<?php }else{?>商家<?php }?></td>
	<td align="center"><?php echo $info['real_name']?></td>
	<td align="center"><?php echo $info['trade_sn']?> <a href="javascript:void(0);" onclick="detail('<?php echo $info['trade_sn']?>', '<?php echo $info['username']?>')"><img src="<?php echo IMG_PATH?>admin_img/detail.png"></a></td>
	<td align="center"><?php echo '快速支付(支付宝)';?></td>
	<td align="center"><?php echo $info['buyer_email'];?></td>
	<td align="center">
	<?php if($info['total_fee'] != 0) {echo $info['total_fee'];?> <?php echo L('yuan') ?><?php }else{ echo $info['point']?> <?php echo L('points')?><?php }?>
	</td>
	
	<td align="center"><a><?php if ( $info['status'] == 0){echo '未支付';}elseif ($info['status'] == 1){echo '支付成功';}else{echo '支付取消';}?> </a>
	<td align="center"><?php echo date('Y-m-d H:i:s',$info['dateline'])?></td>
	<td align="center">
	<a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$info['userid']));?>"><?php echo '账户信息';?></a>
	</td>
	</tr>
<?php
	}
}
?>
    </tbody>
    </table>
<div class="btn text-r">
<?php echo L('thispage').L('totalize')?>  <span class="font-fixh green"><?php echo $pay_count?></span> <?php echo L('bi').L('trade')?>，总金额：<span class="font-fixh green"><?php if($sum_point == 0){echo '0';} echo $sum_point;?></span><?php echo L('yuan')?> ,<?php echo L('trade_succ').L('trade')?>：<span class="font-fixh green"><?php if($sum_point_succ == 0){echo '0';} echo $sum_point_succ?></span><?php echo L('yuan')?>
</div>
 <div id="pages"> <?php echo $pages;?></div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
	function discount(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=Pay&c=Pay&a=discount&id='+id ,width:'500px',height:'200px'}, 	function(){var d = window.top.art.dialog({id:'discount'}).data.iframe;
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'discount'}).close()});
}
function detail(id, name) {
	window.top.art.dialog({title:'<?php echo L('discount')?>--'+name, id:'discount', iframe:'?m=Pay&c=Pay&a=public_pay_detail&id='+id ,width:'500px',height:'550px'});
}
//-->
</script>