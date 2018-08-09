<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'Admin');
?>
<script type="text/javascript" src="<?php echo JS_PATH ?>task.js"></script>
<div class="pad-lr-10">
<div class="content-menu ib-a blue">
<a class="add fb" href="<?php echo U('add') ?>"><em>发布日赚任务</em></a><span>|</span>

<a <?php if ($status == -3): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => -3)) ?>"><em>待审核（待支付）</em></a><span>|</span>
<a <?php if ($status == -2): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => -2)) ?>"><em>待审核（已支付）</em></a><span>|</span>
<a <?php if ($status == -1): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => -1)) ?>"><em>审核通过（待上线）</em></a><span>|</span>
<a <?php if ($status == 0): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => 0)) ?>"><em>审核失败（已退款）</em></a><span>|</span>
<a <?php if ($status == 1): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => 1)) ?>"><em>进行中</em></a><span>|</span>
<a <?php if ($status == 2): ?>class="fb"<?php endif ?> href="<?php echo U('init', array('status' => 2)) ?>"><em>已结束</em></a>
</div>
<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
    <table width="100%">
		<thead>
		<tr>
			<th width="16"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <!-- <th width="37">排序</th> -->
            <th width="40">ID</th>
			<th  width="100">任务名称</th>
			<th width="100">任务份数</th>
			<th width="100">剩余份数</th>
			<th width="100">任务单价</th>
			<th width="100">所属商家</th>
            <th width="40">浏览量</th>
            <th width="118">报名时间</th>
			<th width="72">管理操作</th>
		</tr>
        </thead>
		<tbody>

		<?php foreach($lists as $r) : ; ?>
        <tr>
			<td align="center"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>
			<td align='center' ><?php echo $r['id'];?></td>
			<td valign="top">
				<div class="iptd">
					<strong><a href="<?php echo $r['url']?>" rel="nofollow" target="_blank"><?php echo $r['title']?></a></strong>
					<p class="cps_ipt">
						<span style="color:red;">活动状态：<?php echo $this->task_status[$r['status']]; ?></span><br/>
						上线时间：<?php echo dgmdate($r['start_time']) ?> - <?php echo dgmdate($r['end_time'])?>
					</p>
				</div>
			</td>
			<td align='center'><?php echo $r['goods_number'] ?></td>
			<td align='center'><?php echo $r['goods_number']-$r['already_num'];?></td>
			<td align='center'><?php echo $r['goods_price'] ?></td>
			<td align='center'><?php echo $r['store_name'] ?></td>
			<td align='center'><?php echo $r['hits'];?></td>
			<td align='center'><?php echo dgmdate($r['inputtime'], 'Y/m/d H:i:s')?></td>
			<td align='center'>
				<?php if($r['status'] == -2){?>
				<a href="javascript:;" onclick="task.pass(<?php echo $r['id'] ?>)">通过</a> | 
                <a href="javascript:;" onclick="task.refuse(<?php echo $r['id'] ?>);">拒绝</a><br/>
				<?php }elseif ($r['status'] == -3){?>
				<a href="javascript:;" onclick="javascript:enter(<?php echo $r['id'] ?>);">确认付款</a> <br/>
				<?php }elseif($r['status'] == 1 || $r['status'] == 2){?>
				<a href="javascript:;" onclick="task.record(<?php echo $r['id'] ?>);">用户记录</a> <br/>
				<?php }?>
				<a href="<?php echo U('edit', array('id' => $r['id']))?>">编辑</a> | 
				<a href="<?php echo U('delete', array('dosubmit' => 1, 'ids[]' => $r['id'])); ?>" onclick="return confirm('您确定要删除本商品？');">删除</a>
			</td>
		</tr>
		</tbody>
		<?php endforeach ?>
	</table>
    <div class="btn">
		<input type="button" class="button" value="批量删除" onclick="myform.action='<?php echo U('delete', array('dosubmit' => 1)) ?>';myform.submit();"/>
	</div>
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</div>
<script type="text/javascript">
function enter(id) {
  	window.top.art.dialog({
  		title:'提示', 
  		id:'edit',   
  		height:'80px',
  		content: '您确认已收到商家的付款？<br/><br/>本操作会将商品状态变更为待审核(已付款)并不可逆！',
  	}, 	function(){
  		location.href = "<?php echo __ROOT__ ?>/index.php?m=Task&c=Task&a=pay&task_id="+id;
  		window.top.art.dialog({id:'edit'}).close()
  		return false;
  	}, function(){
  		window.top.art.dialog({id:'edit'}).close()
  	});
}

function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:obj.href,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='linkid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operations')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=link&c=link&a=listorder_up&linkid='+id,null,function (msg) { 
	if (msg==1) { 
	//$("div [id=\'option"+id+"\']").remove(); 
		alert('<?php echo L('move_success')?>');
	} else {
	alert(msg); 
	} 
	}); 
} 
</script>
</body>
</html>