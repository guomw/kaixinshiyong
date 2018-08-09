<?php defined('IN_ADMIN') or exit('No permission resources.');
$show_header = false;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
	<input type="hidden" name="userid" id="userid" value="<?php echo $memberinfo['userid']?>" />
<fieldset>
	<legend><?php echo L('晒单详细信息')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">商品名称</td> 
			<td><a href="<?php echo $report['product']['url']; ?>" target="_blank"><?php echo $report['product']['title'] ?></a></td>
		</tr>
		<tr>
			<td>商家名称</td>
			<td><?php echo $report['seller']['nickname']; ?></td>
		</tr>
		<tr>
			<td>晒单会员</td>
			<td><?php echo $report['buyer']['nickname']; ?></td>
		</tr>
		<tr>
			<td>当前状态</td>
			<td><?php if($report['status']==0){echo '待审核';}elseif($report['status']==1){echo '已审核';}else{echo '<span style="color:red;">已屏蔽</span>';} ?></td>
		</tr>
		<tr>
			<td>晒单时间</td>
			<td><?php echo dgmdate($report['reporttime']) ?></td>
		</tr>
		<tr>
			<td>晒单图片</td> 
			<td><a href="<?php echo $report['report_imgs'];?>" target="_blank" rel='nofollow'><img src="<?php echo $report['report_imgs']?>" height=90 width=90></a></td>
		</tr>
		<tr>
			<td>晒单内容</td> 
			<td><?php echo $report['content'];?></td>
		</tr>
	</table>
</fieldset>
</div>
</div>
</body>
<script language="JavaScript">
<!--
	function changemodel(modelid) {
		redirect('?m=member&c=member&a=edit&userid=<?php echo $memberinfo[userid]?>&modelid='+modelid+'&pc_hash=<?php echo $_SESSION['pc_hash']?>');
	}
//-->
</script>
</html>