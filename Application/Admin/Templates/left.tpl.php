<?php
defined('IN_ADMIN') or exit('No permission resources.');
foreach($datas as $_value) {
	echo '<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>'.L($_value['name']).'</h3>';
	echo '<ul>';
	$sub_array = $menu->get_child($_value['id']);
	foreach($sub_array as $_key=>$_m) {
		//附加参数
		$data = $_m['data'] ? '&'.$_m['data'] : '';
        $classname = 'class="sub_menu"';
		echo '<li id="_MP'.$_m['id'].'" '.$classname.'><a href="javascript:_MP('.$_m['id'].',\''.U($_m['m'].'/'.$_m['c'].'/'.$_m['a'].$data).'\');" hidefocus="true" style="outline:none;">'.L($_m['name']).'</a></li>';
	}
	echo '</ul>';
}
?>

<?php if ($menuid == 1): ?>
<?php
$quick_links = model('admin_panel')->where(array('userid' => session('userid')))->select();
?>
<?php if ($quick_links): ?>
	<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>快捷操作</h3><ul>
<?php
foreach ($quick_links as $link) { ?>
<li id="_MP<?php echo $link['menuid']?>" class="sub_menu"><a href="javascript:_MP('<?php echo $link['menuid']?>','<?php echo $link['url']?>');" hidefocus="true" style="outline:none;"><?php echo $link['name']?></a></li>
<?php
}
?>
</ul>
<?php endif ?>
<?php elseif($menuid == 99999): ?>
	<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>专业版设置</h3>
	<ul>
		<li class="sub_menu" id="_MP99999-1"><a href="javascript:_MP('99999-1', '<?php echo U('Wap/Admin/setting') ?>')" hidefocus="true" style="outline:none;">移动端设置</a></li>
		<li class="sub_menu" id="_MP99999-2"><a href="javascript:_MP('99999-2', '<?php echo U('Wechat/Admin/setting') ?>')" hidefocus="true" style="outline:none;">微信端设置</a></li>
	</ul>


<?php elseif($menuid == 88888): ?>
	<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>消息设置</h3>
	<ul>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/AppNotifyTemplate/config') ?>')" hidefocus="true" style="outline:none;">app端消息设置</a></li>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/setting/push_set') ?>')" hidefocus="true" style="outline:none;">推送端消息设置</a></li>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/setting/push_message') ?>')" hidefocus="true" style="outline:none;">发送推送消息</a></li>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/setting/message') ?>')" hidefocus="true" style="outline:none;">推送消息记录</a></li>
		
	</ul>

	<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>APP抽奖设置</h3>
	<ul>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/LotteryDraw/LotteryDrawSet') ?>')" hidefocus="true" style="outline:none;">APP抽奖设置</a></li>
		
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/LotteryDraw/LotteryDrawList') ?>')" hidefocus="true" style="outline:none;">APP大转盘抽奖记录</a></li>
		
	</ul>

	<h3 class="f14"><span class="switchs cu on" title="'.L('expand_or_contract').'"></span>APP下载设置</h3>
	<ul>
		<li class="sub_menu" id="_MP8888-1"><a href="javascript:_MP('88888-1', '<?php echo U('Admin/setting/download_set') ?>')" hidefocus="true" style="outline:none;">下载设置</a></li>
		
		
		
	</ul>



<?php endif ?>


<script type="text/javascript">
$(".switchs").each(function(i){
	var ul = $(this).parent().next();
	$(this).click(function(){
		if(ul.is(':visible')){
			ul.hide();
			$(this).removeClass('on');
		}else{
			ul.show();
			$(this).addClass('on');
		}
	})
});
</script>