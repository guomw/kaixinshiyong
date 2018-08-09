
<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
	<form action="<?php echo __APP__;?>" method="get" name="searchform">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $userid; ?>" name="userid">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">			
			操作时间：
			<?php echo $form::date('start_time', $start_time)?>-
			<?php echo $form::date('end_time', $end_time)?>	
			类型选择：
			<select name="type">
				<option value='0' <?php if(isset($_GET['type']) && $_GET['type']==0){?>selected<?php }?>>默认</option>
				<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>保证金</option>
				<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>单笔成交</option>
				<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>>钻石商家</option>
				<option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>>皇冠商家</option>

			</select>				
<!-- 			<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
 -->			<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%"><?php echo L('id')?></th>
		<th width="10%" align="left" > 招商专员姓名</th>
		<th width="10%" align="left" > 类型</th>
		<th width="10%" align="left" > 金额</th>
		<th width="10%" align="left" > 描述</th>
		<th width="10%" align="left" > 操作时间</th>
		</tr>
        </thead>
        <tbody>
<?php 
if(is_array($infos)){
	foreach($infos as $info){
?>
<tr>
<td width="10%" align="center"><?php echo $info['id']?></td>
<td width="10%" ><?php echo $info['username']?></td>
<td width="10%" >
<?php if ($info['type'] == 1): ?>
	保证金
<?php elseif ($info['type'] == 2): ?>
	单笔成交金额
<?php elseif ($info['type'] == 5): ?>
	单笔成交服务费
<?php elseif ($info['type'] == 3): ?>
	商家（钻石商家）
<?php elseif ($info['type'] == 4): ?>
	商家（皇冠商家）	
<?php endif ?>

</td>

<td width="10%" ><?php echo $info['money']?></td>
<td width="20%" ><?php echo $info['body']?></td>

<td width="10%" ><?php echo dgmdate($info['time'],'Y-m-d');?></td>

</tr>
<?php 
	}
}
?>
</tbody>
</table>
 <div id="pages"><?php echo $pages?></div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({
		title:'<?php echo L('edit')?>--'+name, 
		id:'edit', 
		iframe: obj.href,
		width:'500px',
		height:'400px'
	}, 	function(){
		var d = window.top.art.dialog({id:'edit'}).data.iframe;
		var form = d.document.getElementById('dosubmit');form.click();
		return false;
	}, function(){
		window.top.art.dialog({id:'edit'}).close()
	});
}
//-->
</script>