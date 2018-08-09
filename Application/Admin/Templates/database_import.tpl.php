<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<div class="table-list">
<form method="post" id="myform" name="myform" >
<input type="hidden" name="pdoname" value="<?php echo $pdoname; ?>">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="10%" align="left"><input type="checkbox" value="" id="check_box" onclick="selectall('filenames[]');"></th>
            <th width="15%">文件名称</th>
            <th width="15%">文件大小</th>
            <th width="15%">备份时间</th>
            <th width="15%">卷号</th>
            <th width="15%">操作</th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($list)){
	foreach($list as $t => $r){
?>   
	<tr>
	<td width="10%">
	<input type="checkbox" name="filenames[]" value="<?php echo $r['filename']?>">
	</td>
	<td  width="15%" align="center"><?php echo $r['filename']?></td>
	<td width="15%" align="center"><?php echo $r['filesize']?></td>
	<td width="15%" align="center"><?php echo $t?></td>
	<td width="15%" align="center"><?php echo $r['part']?></td>
	<td width="15%" align="center">
	<a href="javascript:confirmurl('<?php echo U('import', array('dosubmit' => 1, 'time' => $r['time'])) ?>', '确认恢复数据库吗？')">数据恢复</a>
	</td>
	</tr>
<?php 
	}
}
?>
    </tbody>
    </table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> 
<input type="submit" class="button" name="dosubmit" value="删除备份文件" onclick="document.myform.action='<?php echo U('delete') ?>';return confirm('<?php echo L('bakup_del_confirm')?>')"/>
</div>
</form>
</div>
</div>

</body>
</html>
<script type="text/javascript">
<!--
function show_tbl(obj) {
	var pdoname = $(obj).val();
	location.href='?m=admin&c=database&a=import&pdoname='+pdoname+'&menuid='+<?php echo $_GET['menuid']?>+'&pc_hash=<?php echo $_SESSION['pc_hash']?>';
}
//-->
</script>