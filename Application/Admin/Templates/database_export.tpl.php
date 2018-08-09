<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<div class="table-list">
<form method="post" name="myform" id="myform" action="<?php echo U('export') ?>">
<table width="100%" cellspacing="0">
<thead>
  	<tr>
    	<th class="tablerowhighlight" colspan='4'>备份设置</th>
  	</tr>
</thead>
  	<tr>
	    <td width="200" class="align_r">分卷文件大小</td>
	    <td colspan=3><input type=text name="sizelimit" value="2048" size=5> KB</td>
  	</tr>
   	<tr>
	    <td class="align_r">备份文件压缩</td>
	    <td colspan=3><label><input type="radio" name="compress" value="1" checked> 是</label>&nbsp; <label><input type="radio" name="compress" value="0"> 否</label></td>
  	</tr>
   	<tr>
	    <td class="align_r">强制字符集</td>
	    <td colspan=3><label><input type="radio" name="sqlcharset" value="" checked> 默认</label>&nbsp; <label><input type="radio" name="sqlcharset" value="latin1"> LATIN1</label> &nbsp; <label><input type="radio" name="sqlcharset" value='utf8'> UTF-8</label></td>
  	</tr>
  	<tr>
	    <td></td>
	    <td colspan=3><input type="submit" name="dosubmit" value=" 开始备份数据 " class="button"></td>
  	</tr>
</table>
    <table width="100%" cellspacing="0">
 <?php 
if(is_array($lists)){
?>   
	<thead><tr><th align="center" colspan="8"><strong><?php echo C('DB_NAME') ?></strong></th></tr></thead>
    <thead>
       <tr>
           <th width="8"><input type="checkbox" id="check_box" onclick="selectall('tables[]');"></th>
           <th width="10%">表名</th>
           <th width="10%">类型</th>
           <th width="10%">编码</th>
           <th width="15%">记录数</th>
           <th width="15%">使用空间</th>
           <th width="15%">碎片</th>
           <th width="15%">操作</th>
       </tr>
    </thead>
    <tbody>
	<?php foreach($lists as $v){?>
	<tr title="<?php echo $v['comment'] ?>">
	<td  width="5%" align="center"><input type="checkbox" name="tables[]" value="<?php echo $v['name']?>"/></td>
	<td  width="10%" align="left"><?php echo $v['name']?></td>
	<td  width="10%" align="center"><?php echo $v['engine']?></td>
	<td  width="10%" align="center"><?php echo $v['collation']?></td>
	<td  width="15%" align="center"><?php echo $v['rows']?></td>
	<td  width="15%" align="center"><?php echo $v['size']?></td>
	<td  width="15%" align="center"><?php echo $v['data_free']?></td>
	<td  width="15%" align="center"><a href="<?php echo U('optimize', array('tables[]' => $v['name'])); ?>">优化</a> | <a href="<?php echo U('repair', array('tables[]' => $v['name'])); ?>">修复</a> | <a href="<?php echo U('showcreat', array('table' => $v['name'])) ?>" onclick="showcreat(this,'<?php echo $v['name']?>'); return false;">结构</a></td>
	</tr>
	<?php } ?>
	</tbody>
<?php 
}
?>
</table>
<?php 
if(is_array($lists)){
?>
<div class="btn">
<input type="button" class="button" onclick="reselect()" value="反选"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('optimize'); ?>'" value="批量优化"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('repair'); ?>'" value="批量修复"/>
</div>
<?php 
}
?>
</form>
</div>
</div>
</form>
</body>
<script type="text/javascript">
<!--
window.top.$('#display_center_id').css('display','none');
function showcreat(obj, tblname) {
	window.top.art.dialog({title:tblname, id:'show', iframe: obj.href,width:'500px',height:'350px'});
}
function reselect() {
	var chk = $("input[name='tables[]']");
	var length = chk.length;
	for(i=0;i < length;i++){
		if(chk.eq(i).attr("checked")) chk.eq(i).removeAttr("checked",false);
		else chk.eq(i).attr("checked",true);
	}
}
//-->
</script>
</html>
