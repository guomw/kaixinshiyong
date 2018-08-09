<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get" name="searchform">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<br/>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">				
				<?php echo L('晒单时间')?>：
				<?php echo $form::date('start_time', $start_time)?>-
				<?php echo $form::date('end_time', $end_time)?>	
				<select name="status">
					<option value='-99' <?php if(isset($_GET['status']) && $_GET['status']==-99){?>selected<?php }?>>状态</option>
					<option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>>待审核</option>
					<option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>>已审核</option>
					<option value='2' <?php if(isset($_GET['status']) && $_GET['status']==2){?>selected<?php }?>>已屏蔽</option>
				</select>
				<select name="type">
					<option value='0' <?php if(isset($_GET['type']) && $_GET['type']==0){?>selected<?php }?>>商品标题</option>
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>会员id</option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>邮箱</option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>>昵称</option>
				</select>				
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
				<br/>
				<br/>
				当前状态： <?php if ($enabled == 1) {echo '<span style="color:red;">已开启</span>人工审核';}else{echo '<span style="color:red;">已关闭</span>人工审核';} ?>
				<a href="/index.php?m=Member&c=Activity&a=activity_set&type=rebate" style="color:blue;">点此去设置</a>&nbsp;&nbsp;( 或者：活动管理 -> 购物返利设置 -> 会员设置 )
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('report_do');?>" method="post" onsubmit="checkuid();return false;">
<input type='hidden' name='type' value="delete"/>
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th align="left">晒单ID</th>
			<th align="left">商品名称</th>
			<th align="left">商家名称</th>
			<th align="left">晒单会员</th>
			<th align="left">当前状态</th>
			<th align="left">晒单时间</th>
			<th align="left">操作</th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($reports)){
	foreach($reports as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><a href='<?php echo $v['product']['url'] ?>' target='_blank'><?php echo $v['product']['title']?></a></td>
		<td align="left"><?php echo $v['seller']['nickname'] ?></td>
		<td align="left"><?php echo $v['buyer']['nickname']?></td>		
		<td align="left"><?php if($v['status']==0){echo '待审核';}elseif($v['status']==1){echo '已审核';}else{echo '<span style="color:red;">已屏蔽</span>';} ?></td>
		<td align="left"><?php echo dgmdate($v['reporttime'])?></td>
		<td align="left">
			<a href="javascript:;" onclick="javascript:read(<?php echo $v['id'];?>)">[查看]</a>
			<a href="<?php echo U('aword', array('userid' => $v['userid'])) ?>" onclick="javascript:aword(this, '晒单额外奖励');return false;">[奖励]</a>
			<?php if ($enabled == 1) { ?>
				<a href="<?php echo U('report_do', array('ids' => array('0' => $v['id']),'type'=>'pass')) ?>" onclick="return confirm('确定通过？该操作不可逆转')">[通过]</a>
				<a href="<?php echo U('report_do', array('ids' => array('0' => $v['id']),'type'=>'shield')) ?>" onclick="return confirm('确定屏蔽？该操作不可逆转')">[屏蔽]</a>
			<?php } ?>	
			<a href="<?php echo U('report_do', array('ids' => array('0' => $v['id']),'type'=>'delete')) ?>" onclick="return confirm('确定删除？该操作不可逆转')">[删除]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box">全选/取消</label>
<input type="submit" class="button" name="dosubmit" value="删除" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function checkuid() {
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'请勾选要删除的记录',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function read(id) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({
			title:'查看晒单详情',
			id:'modelinfo',
			okVal : '关闭',
			iframe:'?m=Member&c=appeals&a=report_read&id='+id,width:'500',height:'400'
		},
		function(){
			window.top.art.dialog({id:'modelinfo'}).close()
		});
}

function aword(obj ,name){
	window.top.art.dialog({id:'aword'}).close();
	window.top.art.dialog({title:'<?php echo L('aword').L('member')?>《'+name+'》',id:'aword',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}

//-->
</script>
</body>
</html>