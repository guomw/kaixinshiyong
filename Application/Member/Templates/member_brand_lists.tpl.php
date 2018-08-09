<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'Admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID;?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
        <td>
        <div class="explain-col">               
                <?php echo "认证时间"?>：
                <?php echo $form::date('start_time', $start_time)?>-
                <?php echo $form::date('end_time', $end_time)?> 

                <select name="status">
                     <option value='5' <?php if(isset($_GET['status']) && $_GET['status']==5){?>selected<?php }?>><?php echo "审核状态"?></option>
                    <option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>><?php echo "未审核"?></option>
                    <option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>><?php echo "审核成功"?></option>
                    <option value='2' <?php if(isset($_GET['status']) && $_GET['status']==3){?>selected<?php }?>><?php echo "审核失败"?></option>
                </select>
                <input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
                <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
    </div>
        </td>
        </tr>
    </tbody>
</table>
</form>

	<div class="table-list">
		<form name="myform" id="myform" action="<?php echo U('delete'); ?>" method="post">
		<table width="100%" cellspacing="0">
		<thead>
		<tr>
			<th align="left" width="30px"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th align="left">ID</th>
			<th align="left"><?php echo '用户名';?></th>
			<th align="left"><?php echo '商家logo';?></th>
			<th align="left"><?php echo '授权文件图片'?></th>
			<th align="left"><?php echo '品牌简介';?></th>
			<th align="left"><?php echo '状态';?></th>
			<th><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	foreach($lists as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]" /></td>
		<td align="left"><?php echo $v['id']?></td>
 		<td align="left"><?php echo $v['username']?></td>
 		<td align="left"><a href="<?php echo $v['img_logo']?>" rel="nofollow"><img src="<?php echo $v['img_logo']?>" width="50" height="50"/></a></td>
		<td align="left"><a href="<?php echo $v['img_auth']?>" rel="nofollow"><img src="<?php echo $v['img_auth']?>" width="50" height="50"/></a></td>
		<td align="left"><?php echo $v['content']?></td>
		<td align="left"><?php if($v['status'] == 0){echo '未审核';}else if ($v['status'] == 1){echo '已审核';}else{echo '未通过';}?></td>
		<td align="center">
		<a href="javascript:confirmurl('<?php echo U('delete', array('ids[]' => $v['id']));?>','<?php echo L('sure_delete')?>');"><?php echo L('delete')?></a> 
		</td>
    </tr>
<?php
	}
?>
</tbody>
</table>
<div class="btn"><label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label><input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('check');?>'" value="<?php echo L('check')?>"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='<?php echo U('uncheck');?>'" value="<?php echo L('uncheck')?>"/>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<div id="PC__contentHeight" style="display:none">160</div>
<script language="JavaScript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member_model')?>《'+name+'》',id:'edit',iframe:'?m=member&c=member_model&a=edit&modelid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function move(id, name) {
	window.top.art.dialog({id:'move'}).close();
	window.top.art.dialog({title:'<?php echo L('move')?>《'+name+'》',id:'move',iframe:'?m=member&c=member_model&a=move&modelid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'move'}).data.iframe;d.$('#dosubmit').click();return false;}, function(){window.top.art.dialog({id:'move'}).close()});
}

function check() {
	if(myform.action == '<?php echo U("delete"); ?>') {
		var ids='';
		$("input[name='id[]']:checked").each(function(i, n){
			ids += $(n).val() + ',';
		});
		if(ids=='') {
			window.top.art.dialog({content:'<?php echo L('plsease_select').L('member_model')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
			return false;
		}
	}
	myform.submit();
}
//-->
</script>
</body>
</html>