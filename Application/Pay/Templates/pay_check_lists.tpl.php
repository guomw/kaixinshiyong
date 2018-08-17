<?php
	defined('IN_ADMIN') or exit('No permission resources.');
	$show_dialog = 1;
	include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="myform" action="<?php echo __APP__;?>" method="get" name="searchform">
<input type="hidden" name="m" value="<?php echo MODULE_NAME;?>" />
<input type="hidden" name="c" value="<?php echo CONTROLLER_NAME;?>" />
<input type="hidden" name="a" value="<?php echo ACTION_NAME;?>" />
<div class="explain-col search-form">
<select name="info[type]">
	<option value="1"><?php echo L('order_check_no')?> </option>
	<option value="2"><?php echo '昵称';?></option>
</select>
<input type="text" value="" class="input-text" name="info[keyword]">
<?php echo L('addtime')?>  <?php echo $form::date('info[start_addtime]',$start_addtime)?><?php echo L('to')?>   <?php echo $form::date('info[end_addtime]',$end_addtime)?>
<select name="status">
	<option value="-2" <?php if($status == -2){?>selected<?php }?>>全部</option>
	<option value="0"  <?php if($status == 0){?>selected<?php }?>>未审核</option>
	<option value="1"  <?php if($status == 1){?>selected<?php }?>>已审核</option>
	<option value="-1" <?php if($status == -1){?>selected<?php }?>>未通过</option>
</select>
<input type="hidden" value="<?php echo MENUID?>" name="menuid">
<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
</div>
</form>

<form name="myform" id="myform" action="<?php echo U('delete');?>" method="post" >
<div class="table-list">
    <table width="100%" cellspacing="0" id="table">
        <thead>
            <tr>
            	<!-- <th  align="left" width="2%"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th> -->
	            <th width="5%"><?php echo 'ID';?></th>
	            <th width="5%"><?php echo '会员ID';?></th>
	             <th width="5%"><?php echo '会员组';?></th>
	            <th width="10%"><?php echo '店铺名称';?></th>
	            <th width="5%"><?php echo '联系人';?></th>
	            <th width="10%"><?php echo L('order_check_no')?></th>
	            <th width="10%"><?php echo L('order_time')?></th>
	            <th width="10%"><?php echo L('deposit_amount')?></th>
	            <th width="10%"><?php echo L('check_status')?></th>
	            <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody  id="test">
<?php
if(is_array($pay_lists)){
	foreach ($pay_lists as $info){
?>
	<tr>
		<!-- <td align="left"><input type="checkbox" value="<?php echo $info['id']?>" name="id[]"></td> -->
		<td align="center"><?php echo $info['id']?></td>
		<td align="center"><?php echo $info['userid']?></td>
		<td align="center"><?php echo member_group_name($info['userid']);?></td>
		<td align="center"><?php echo $info['store_name']?></td>
		<td align="center"><?php echo $info['contact_name']?></td>
		<td align="center"><?php echo $info['tran_number']?></td>
		<td align="center"><?php echo date('Y-m-d H:i:s',$info['inputtime'])?></td>
		<td align="center">$<?php echo $info['money'];?>美元</td>
		<td align="center"><a><?php if ( $info['status'] == 0){echo '未审核';}elseif ($info['status'] == 1){echo '<font color="green">已审核</font>';}else{echo '<font color="red">未通过</font>';}?> </a>
		<td align="center">
			<!-- <a href="<?php echo U('Pay/PayCheck/payinfo',array('id'=>$info['id']));?>"><?php echo '详细充值信息';?></a> | -->
			<?php if($info['status'] == 0){?>
			<a href="javascript:;" data-id="<?php echo $info['id'];?>" id="passed"><?php echo '通过';?></a> |
			<a href="<?php echo U('Pay/PayCheck/unpassed',array('id'=>$info['id']));?>" onclick="javascript:unpass(this,'<?php echo $info['userid']?>');return false;"><?php echo '不通过';?></a> |
			<?php }?>
			<a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$info['userid']));?>"><?php echo '查看用户';?></a> 
		</td>
	</tr>
<?php
	}
}
?>
    </tbody>
    </table>
 <div id="pages"><?php echo $pages;?></div>
 </form>
</div>
</div>
<script type="text/javascript">
<!--
$(function(){
	$("#test tr").each(function(){
		$(this).children('td').eq(-1).children('#passed').click(function (){
			//判断当前的状态
			var id = $(this).attr("data-id");
			$.post("<?php echo U('Pay/PayCheck/check_pass');?>",{'id':id},function(data){
				if(data.status == 0){
					alert(data.info);
				}else{
					location.href = data.url;
				}
			});
		});
	});
});
function checkuid() {
	var ids='';
	$("input[name='id[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

//查看详情
function detail(id, name) {
	window.top.art.dialog({id:'detail'}).close();
	window.top.art.dialog({title:'<?php echo '商家详情';?>《'+name+'》',id:'detail',iframe:'?m=Pay&c=PayCheck&a=detail&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'detail'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'detail'}).close()});
}
function unpass(obj, name){
	window.top.art.dialog({id:'unpass'}).close();
	window.top.art.dialog({title:'<?php echo '审核会员'?>《'+name+'》',id:'unpass',iframe:obj.href,width:'400',height:'150'}, function(){var d = window.top.art.dialog({id:'unpass'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'unpass'}).close()});
}
//-->
</script>
</body>
</html>