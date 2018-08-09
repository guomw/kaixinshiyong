<?php defined('IN_ADMIN') or exit('No permission resources.');
$show_header = false;
?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<fieldset>
	<legend>试用报告详细信息</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">商品名称</td> 
			<td><a href="<?php echo $trial['product']['url']; ?>" target="_blank"><?php echo $trial['product']['title'] ?></a></td>
		</tr>
		<tr>
			<td>商家昵称</td>
			<td><?php echo $trial['seller']['nickname']; ?></td>
		</tr>
		<tr>
			<td>会员昵称</td>
			<td><?php echo $trial['buyer']['nickname']; ?></td>
		</tr>
		<tr>
			<td>完成时间</td>
			<td><?php echo dgmdate($trial['complete_time']) ?></td>
		</tr>
		<tr>
			<td>试用打分</td>
			<td><?php echo $trial['trial_report']['star'] ?></td>
		</tr>
		<tr>
			<td>身高</td>
			<td><?php echo $trial['trial_report']['height'] ?></td>
		</tr>
		<tr>
			<td>体重</td>
			<td><?php echo $trial['trial_report']['weight'] ?></td>
		</tr>
		<tr>
			<td>年龄</td>
			<td><?php echo $trial['trial_report']['age'] ?></td>
		</tr>
		<tr>
			<td>职业</td>
			<td><?php echo $trial['trial_report']['job'] ?></td>
		</tr>
		<tr>
			<td>试客背景</td>
			<td><?php echo $trial['trial_report']['background'] ?></td>
		</tr>
		<tr>
			<td>试用报告内容</td> 
			<td><?php echo $trial['content'];?></td>
		</tr>
		<tr>
			<td>当前状态</td>
			<td><?php if ($trials['status'] == -1) {
			echo '审核失败';
		}elseif ($trials['status'] == 0) {
			echo '等待审核';
		}elseif ($trials['status'] == 1) {
			echo '审核成功';
		}?></td>
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