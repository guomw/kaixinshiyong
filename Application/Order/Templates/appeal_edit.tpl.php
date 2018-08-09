<?php defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<div class="pad-10">
<div class="common-form">
	<input type="hidden" name="userid" id="userid" value="<?php echo $memberinfo['userid']?>">
<fieldset>
	<legend>申诉资料</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">会员昵称</td> 
			<td><?php echo $appeal['buyer']['nickname'] ?></td>
		</tr>
		<tr>
			<td width="80">商品标题</td> 
			<td><a href='<?php echo $appeal['goods']['url'] ?>' target="_blank" rel='nofollow' style='color:blue;'><?php echo $appeal['goods']['title'] ?></a></td>
		</tr>
		<tr>
			<td>申诉时间</td>
			<td><?php echo dgmdate($appeal['buyer_time']); ?></td>
		</tr>
		<tr>
			<td>申诉类型</td> 
			<td><?php echo $appealtype[$appeal['appeal_type']] ?></td>
		</tr>
		<tr>
			<td>抢购订单号</td>
			<td><?php echo $appeal['order_sn'] ?></td>
		</tr>
		<tr>
			<td>申诉凭证</td> 
			<td>
				<?php foreach ($appeal['buyer_imgs_url'] as $k => $v) {  ?>
					<a href='<?php echo $v ?>' rel="nofollow" target="_blank"><img src="<?php echo $v;?>" height="60" width="60"></a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>申诉理由</td>
			<td><?php echo $appeal['buyer_cause']; ?></td>
		</tr>
		<?php if ($appeal['buyer_phone']){ ?>
		<tr>
			<td>联系电话</td>
			<td><?php echo $appeal['buyer_phone']; ?></td>
		</tr>
		<?php } ?>
		<?php if ($appeal['buyer_qq']){ ?>
		<tr>
			<td>Q  Q</td>
			<td><?php echo $appeal['buyer_qq'];?></td>
		</tr>
		<?php } ?>
		<?php if ($appeal['buyer_ww']){ ?>
		<tr>
			<td>旺旺</td>
			<td><?php echo $appeal['buyer_ww'];?></td>
		</tr>
		<?php } ?>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend>商家资料</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">店铺名称</td> 
			<td><?php echo $appeal['store']['store_name'] ?></td>
		</tr>
		<tr>
			<td width="80">商家名称</td> 
			<td><?php echo $appeal['seller']['nickname'] ?></td>
		</tr>
		<tr>
			<td width="80">商家邮箱</td> 
			<td><?php echo $appeal['seller']['email'] ?></td>
		</tr>
		<?php if ($appeal['seller']['phone']){ ?>
		<tr>
			<td width="80">商家电话</td> 
			<td><?php echo $appeal['seller']['phone'] ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td width="80">联系QQ</td> 
			<td><?php echo $appeal['store']['store_qq'] ?></td>
		</tr>
		<tr>
			<td>商家凭证</td> 
			<td>
				<?php foreach ($appeal['seller_imgs_url'] as $k => $v) {  ?>
					<a href='<?php echo $v ?>' rel="nofollow" target="_blank"><img src="<?php echo $v;?>" height="60" width="60"></a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>商家理由</td> 
			<td><?php echo $appeal['seller_cause'] ?></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<form name="myform" action="<?php echo U('appeal_do'); ?>" method="post" id="myform">
<input type='hidden' name="id" value="<?php echo $appeal['id']; ?>" />
<input type='hidden' name="order_id" value="<?php echo $appeal['order']['id']; ?>" />
<fieldset>
	<legend>处理结果</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">审核结果</td>
			<td>
				<select name="order_status">
					<option value="-99">请  选  择</option>
					<option value="2">重新填写订单号</option>
					<option value="4">重新发起申述</option>
					<option value="7">订单完成并返款</option>
					<option value="0">直接关闭订单</option>
				</select>
				<span style='margin-left:20px;color:red;'>* 请仔细核对申诉类型及申诉信息后再进行选项操作，该操作不可逆转</span>
			</td>
		</tr>
		<tr>
			<td>审核描述</td> 
			<td><textarea rows="2" cols="80" name="admin_cause" id="admin_cause" onblur="if(this.value.match(/^\s*$/)){this.value = '* 请输入审核描述'}" onfocus="if(this.value == '* 请输入审核描述'){this.value = ''}">* 请输入审核描述</textarea></td>
		</tr>
	</table>
</fieldset>
    <div class="bk15"></div>
    <input name="dosubmit" id="dosubmit" type="submit" value="提交" class="dialog">
</form>
</div>
</div>
</body> 
<script type="text/javascript">
var cause = $("#admin_cause");
$("#myform").submit(function(){
	if (cause.val() =='' || cause.val() == '* 请输入审核描述') {
		alert('请输入审核描述');
		return false;
	}else{
		return true;
	}
})
</script>
<script language="JavaScript">
<!--
	function changemodel(modelid) {
		redirect('?m=member&c=member&a=edit&userid=<?php echo $memberinfo[userid]?>&modelid='+modelid+'&pc_hash=<?php echo $_SESSION['pc_hash']?>');
	}
//-->
</script>
</html>