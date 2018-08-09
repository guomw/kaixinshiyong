<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">				
			<?php echo L('regtime')?>：
			<?php echo $form::date('start_time', $start_time)?>-
			<?php echo $form::date('end_time', $end_time)?>		
			<select name="type">
				<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>
				用户账号
				</option>
				<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>
				支付宝账号
				</option>
			</select>			
			<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
			<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('cashid[]');"></th>
			<th align="left"><?php echo L('ID')?></th>
			<th align="left"><?php echo L('用户账号')?></th>
			<th align="left"><?php echo L('支付宝账号')?></th>
			<th align="left"><?php echo L('提现金额(元)')?></th>
			<th align="left"><?php echo L('审核状态')?></th>
			<th align="left"><?php echo L('申请时间')?></th>
			<th align="left"><?php echo L('审核时间')?></th>
			<th align="left"><?php echo L('提交IP')?></th>			
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($cash_list)){
	foreach($cash_list as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['cashid']?>" name="cashid[]"></td>
		<td align="left"><?php echo $v['cashid']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['cash_alipay_username']?></td>
		<td align="left"><?php echo $v['money']?></td>
		<td align="left"><?php if ($v['status']=='-1'){echo '待审核';}elseif($v['status']=='1'){echo '审核通过';}else{echo '审核未通过';}?></td>
		<td align="left"><?php echo dgmdate($v['inputtime'])?></td>
		<td align="left"><?php if($v['check_time']!='0'){echo dgmdate($v['check_time']);}else{echo '--';} ?></td>
		<td align="left"><?php echo $v['ip']?></td>
		<td align="left">
			<a href="<?php echo U('edit', array('cashid' => $v['cashid'])) ?>" onclick="javascript:edit(this, '<?php echo $v['username']?>');return false;">[<?php echo L('审核')?>]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
<!-- <input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('lock'); ?>'" value="<?php echo L('lock')?>"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('unlock'); ?>'" value="<?php echo L('unlock')?>"/> -->
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('审核会员提现')?>【'+name+'】',id:'edit',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='cashid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('请勾选要删除的记录')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

/*function member_infomation(obj, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}*/
function member_infomation(userid) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}
//-->
</script>
</body>
</html>