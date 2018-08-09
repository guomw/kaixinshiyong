<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
    <div class="table-list">
        <div class="explain-col">注意：升级程序有可能覆盖模版文件，请注意备份！linux服务器需检查文件所有者权限和组权限，确保WEB SERVER用户有文件写入权限</div>
        <div class="bk15"></div>

<form name="myform" action="<?php echo U('index')?>" method="post" id="myform">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" width="300">当前版本<?php if(empty($pathlist)) {?>为最新版<?php }?></th>
			<th align="left">更新日期</th>
		</tr>
	</thead>
<tbody>
    <tr>
		<td align="left"><?php echo $current_version['system_version'];?></td>
		<td align="left"><?php echo $current_version['system_release'];?></td>
    </tr>

</tbody>
</table>
<?php if(!empty($pathlist)) {?>
<div class="bk15"></div>
<table width="100%" cellspacing="0">
<thead>
	<tr>
		<th align="left" width="300">可升级版本列表</th>
		<th align="left">更新日期</th>
	</tr>
</thead>
<tbody>
	<?php foreach($pathlist as $v) { ?>
	<tr>
		<td><?php echo $v;?></td>
		<td><?php echo substr($v, 15, 8);?></td>
	</tr>
	<?php }?>
</tbody>
</table>
    <div class="bk15"></div>
<!-- 	<label for="cover"><font color="red">覆盖模板</font></label><input name="cover" id="cover" type="checkbox" value=1> -->
    <input name="dosubmit" type="submit" id="dosubmit" value="开始升级" class="button">
<?php }?>
</form>
</div>
</div>
</body>
</html>