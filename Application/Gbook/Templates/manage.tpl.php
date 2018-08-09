<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'Admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="<?php echo U('delete');?>" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="35" align="center">ID</th>
			<th align="center">联系人</th>
			<th width="12%" align="center">电子邮件</th>
			<th width="15%" align="center">手机号码</th>
			<th width="10%" align="center">联系地址</th>
			<th width='10%' align="center">所属公司</th>
			<th align="center">留言内容</th>
			<th width="8%" align="center">拓展信息</th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td align="center"><?php echo $info['id'] ?></td>
		<td align="center"><?php echo $info['linkname'] ?></td>
		<td align="center"><?php echo $info['email'] ?></td>
		<td align="center" width="12%"><?php echo $info['mobile'] ?></td>
		<td align="center" width="15%"><?php echo $info['address'];?></td>
		<td align="center" width="10%"><?php echo $info['cname'];?></td>	
		<td align="center"><?php echo $info['content'] ?></td>
		<td align="center"><?php echo $info['extra'] ?></td>
		<td align="center"><a href="<?php echo U('delete', array('id[]' => $info['id'])) ?>">删除留言</a></td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages;?></div>
</form>
</div>
</body>
</html>
