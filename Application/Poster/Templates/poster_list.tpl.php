<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" action="<?php echo U('public_listorder');?>" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" class="contentWrap">
        <thead>
            <tr>
            <th width="30" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th width="70">排序</th>
            <th width="35">ID</th>
			<th align="center">广告位名称</th>
			<th width="70" align="center">广告类型</th>
			<th width="50" align="center">状态</th>
			<th width='50' align="center"><?php echo L('hits')?></th>
			<th width="130" align="center">更新时间</th>
			<th width="150" align="center">管理操作</th>
            </tr>
        </thead>
        <?php if ($lists): ?>
        <tbody>
        <?php foreach ($lists as $r): ?>
        <tr>
        	<td align="center"><input type="checkbox" name="ids[]" value="<?php echo $r['id']?>"></td>
        	
        	<td width="70"><input type="text" class="input-text-c" size="5" name="listorders[<?php echo $r['id']?>]" value="<?php echo $r['listorder']?>" id="listorder"></td>
            <td align="center"><?php echo $r['id']?></td>
        	<td><?php echo $r['name']?></td>
        	<td align="center"><?php echo $types[$r['type']]?></td>
        	<td align="center">
        	<?php if ($r['disabled']): ?>
        		关闭
        	<?php else: ?>
        		开启
        	<?php endif ?>
        	</td>
        	<td align="center"><?php echo $r['clicks']?></td>
        	<td align="center"><?php echo dgmdate($r['dateline']);?></td>
            <td align="center">

                <a href="javascript:call(<?php echo $r['id']?>);void(0);">调用代码</a> |
                <a href="<?php echo U('edit', array('id' => $r['id'])) ?>">编辑</a> |
                <a href="<?php echo U('delete', array('ids[]' => $r['id'], 'dosubmit' => 1)) ?>">删除</a>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    <?php endif ?>
    </table>
  
    <div class="btn">
    	<label for="check_box">全选/取消</label>
    	<input name='dosubmit' type='submit' class="button" value='更新排序'>&nbsp;
		<input name="dosubmit" type="submit" class="button" value="批量删除" onClick="document.myform.action='<?php echo U('delete') ?>';return confirm('确认删除所选广告？')">&nbsp;&nbsp;
	</div>
</div>
<div id="pages"><?php echo $pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit_ads')?>--'+name, id:'edit', iframe:'?m=poster&c=poster&a=edit&id='+id ,width:'600px',height:'430px'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;// 使用内置接口获取iframe对象
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
};

function call(id) { 
    window.top.art.dialog({id:'call'}).close();
    window.top.art.dialog({title:'广告位调用代码', id:'call', iframe:'?m=Poster&c=Admin&a=public_call&id='+id, width:'600px', height:'300px'}, function(){window.top.art.dialog({id:'call'}).close();}, function(){window.top.art.dialog({id:'call'}).close();})
}
//-->
</script>