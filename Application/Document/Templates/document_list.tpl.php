<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','Admin');?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('center_frame').src = '<?php echo U("public_categorys", array("type" => "add")) ?>';
	window.top.$("#current_pos").data('clicknum',0);
}
//-->
</SCRIPT>
<div class="pad-10">
<div class="content-menu ib-a blue line-x">
<a class="add fb" href="javascript:;" onclick=javascript:openwinx('<?php echo U('add', array('catid' => $catid)) ?>','')><em><?php echo L('add_content');?></em></a>　
<a href="<?php echo U('manage',array('status'=>0,'menuid'=>MENUID,'catid'=>12));?>" <?php if(!isset($_GET['reject'])) echo 'class=on';?>><em><?php echo L('check_unpassed');?></em></a><span>|</span><a href="javascript:;" onclick="javascript:$('#searchid').css('display','');"><em><?php echo L('search');?></em></a> 
<?php if($category['ishtml']) {?>
<span>|</span><a href="<?php echo U('Createhtml/category', array('pagesize'=> 30, 'dosubmit' => 1, 'modelid' =>0, 'catids[]' => $catid, 'referer' => urlencode($_SERVER['QUERY_STRING']))) ?>"><em><?php echo L('update_htmls',array('catname'=>$category['catname']));?></em></a>
<?php }?>
</div>
<div id="searchid" style="display:<?php if(!isset($s)) echo 'none';?>">
<form name="searchform" action="<?php echo __APP__; ?>" method="get">
<input type="hidden" value="<?php echo $catid;?>" name="catid">
<input type="hidden" value="1" name="search">
<input type="hidden" name="m" value="<?php echo MODULE_NAME ?>">
<input type="hidden" name="c" value="<?php echo CONTROLLER_NAME ?>">
<input type="hidden" name="a" value="<?php echo ACTION_NAME ?>">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
 
				<?php echo L('addtime');?>：
				<?php echo $form::date('start_time',$s['start_time'],0,0,'false');?>- &nbsp;<?php echo $form::date('end_time',$s['end_time'],0,0,'false');?>
				
				<select name="posids"><option value='' <?php if($s['posids']=='') echo 'selected';?>><?php echo L('all');?></option>
				<option value="1" <?php if($s['posids']==1) echo 'selected';?>><?php echo L('elite');?></option>
				<option value="2" <?php if($s['posids']==2) echo 'selected';?>><?php echo L('no_elite');?></option>
				</select>				
				<select name="searchtype">
					<option value='0' <?php if($s['searchtype']==0) echo 'selected';?>><?php echo L('title');?></option>
					<option value='1' <?php if($s['searchtype']==1) echo 'selected';?>><?php echo L('intro');?></option>
					<option value='2' <?php if($s['searchtype']==2) echo 'selected';?>><?php echo L('username');?></option>
					<option value='3' <?php if($s['searchtype']==3) echo 'selected';?>>ID</option>
				</select>
				
				<input name="keyword" type="text" value="<?php if(isset($s['keyword'])) echo $s['keyword'];?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search');?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
</div>
<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
			 <th width="16"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th width="37"><?php echo L('listorder');?></th>
            <th width="40">ID</th>
			<th><?php echo L('title');?></th>
			<th><?php echo L('type');?></th>
            <th width="40"><?php echo L('hits');?></th>
            <th width="70"><?php echo L('publish_user');?></th>
            <th width="118"><?php echo L('updatetime');?></th>
			<th width="72"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
<tbody>
    <?php
	if(is_array($datas)) {
		$this->hits_db = D('Hits');	
		foreach ($datas as $r) {
			$hits_r = $this->hits_db->getByHitsid('c-'.$modelid.'-'.$r['id']);
	?>
        <tr>
		<td align="center"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>
        <td align='center'><input name='listorders[<?php echo $r['id'];?>]' type='text' size='3' value='<?php echo $r['listorder'];?>' class='input-text-c'></td>
		<td align='center' ><?php echo $r['id'];?></td>
		<td>
		<?php
		if($status==99) {
			if($r['islink']) {
				echo '<a href="'.$r['url'].'" target="_blank">';
			} elseif(strpos($r['url'],'http://')!==false) {
				echo '<a href="'.$r['url'].'" target="_blank">';
			} else {
				echo '<a href="'.$release_siteurl.$r['url'].'" target="_blank">';
			}
		} else {
			echo '<a href="javascript:;" onclick=\'window.open("'.U('Document/index/show', array('catid' => $catid, 'id' => $r['id'])).'","manage")\'>';
		}?><span<?php echo title_style($r['style'])?>><?php echo $r['title'];?></span></a> <?php if($r['thumb']!='') {echo '<img src="'.IMG_PATH.'icon/small_img.gif" title="'.L('thumb').'">'; } if($r['posids']) {echo '<img src="'.IMG_PATH.'icon/small_elite.gif" title="'.L('elite').'">';} if($r['islink']) {echo ' <img src="'.IMG_PATH.'icon/link.png" title="'.L('islink_url').'">';}?></td>
		
		<td><?php if($r['status'] == 0 || $r['status'] == 99){?><font color="red"><?php echo L('unpassed');?></font><?php }else{?><?php echo L('passed');?><?php }?></td>
		
		<td align='center' title="<?php echo L('today_hits');?>：<?php echo $hits_r['dayviews'];?>&#10;<?php echo L('yestoday_hits');?>：<?php echo $hits_r['yestodayviews'];?>&#10;<?php echo L('week_hits');?>：<?php echo $hits_r['weekviews'];?>&#10;<?php echo L('month_hits');?>：<?php echo $hits_r['monthviews'];?>"><?php echo $hits_r['views'];?></td>
		<td align='center'>
		<?php
		if($r['sysadd']==0) {
			echo "<a href='".U('Member/member/memberinfo', array('username' => urlencode($r['username'])))."' >".$r['username']."</a>"; 
			echo '<img src="'.IMG_PATH.'icon/contribute.png" title="'.L('member_contribute').'">';
		} else {
			echo $r['username'];
		}
		?></td>
		<td align='center'><?php echo dgmdate($r['updatetime']);?></td>
		<td align='center'><a href="javascript:;" onclick="javascript:openwinx('<?php echo U('edit', array('catid' => $catid, 'id' => $r['id'])) ?>','')"><?php echo L('edit');?></a></td>
	</tr>
     <?php }
	}
	?>
</tbody>
     </table>
    <div class="btn"><label for="check_box"><?php echo L('selected_all');?>/<?php echo L('cancel');?></label>
    	<input type="button" class="button" value="<?php echo L('listorder');?>" onclick="myform.action='<?php echo U('listorder', array('catid'=> $catid, 'dosubmit' => 1)) ?>';myform.submit();"/>
		<?php if($category['content_ishtml']) {?>
		<input type="button" class="button" value="<?php echo L('createhtml');?>" onclick="myform.action='<?php echo U('Html/batch_show', array('catid'=> $catid, 'dosubmit' => 1)) ?>';myform.submit();"/>
		<?php }
		if($status!=99) {?>
		<!--<input type="button" class="button" value="<?php echo L('passed_checked');?>" onclick="myform.action='<?php echo U('pass', array('catid'=> $catid, 'dosubmit' => 1)) ?>';myform.submit();"/>
		-->
		<?php }?>
		
		<input type="button" class="button" value="<?php echo L('delete');?>" onclick="myform.action='<?php echo U('delete', array('catid'=> $catid, 'dosubmit' => 1)) ?>';return confirm_delete()"/>
		<?php if(!isset($_GET['reject'])) { ?>
		<!-- <input type="button" class="button" value="<?php echo L('push');?>" onclick="push();"/> -->
		<?php }?>
		<!-- <input type="button" class="button" value="<?php echo L('remove');?>" onclick="myform.action='<?php echo U('remove', array('catid'=>$catid)) ?>';myform.submit();"/> -->
		<?php echo runhook('admin_content_init')?>
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</div>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript"> 
<!--
function push() {
	var str = 0;
	var id = tag = '';
	$("input[name='ids[]']").each(function() {
		if($(this).attr('checked')=='checked') {
			str = 1;
			id += tag+$(this).val();
			tag = '|';
		}
	});
	if(str==0) {
		alert('<?php echo L('you_do_not_check');?>');
		return false;
	}
	window.top.art.dialog({id:'push'}).close();
	window.top.art.dialog({title:'<?php echo L('push');?>：',id:'push',iframe:'?m=document&c=push&action=position_list&catid=<?php echo $catid?>&modelid=<?php echo $modelid?>&id='+id,width:'800',height:'500'}, function(){var d = window.top.art.dialog({id:'push'}).data.iframe;// 使用内置接口获取iframe对象
	var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'push'}).close()});
}
function confirm_delete(){
	if(confirm('<?php echo L('confirm_delete', array('message' => L('selected')));?>')) $('#myform').submit();
}
function view_comment(id, name) {
	window.top.art.dialog({id:'view_comment'}).close();
	window.top.art.dialog({yesText:'<?php echo L('dialog_close');?>',title:'<?php echo L('view_comment');?>：'+name,id:'view_comment',iframe:'index.php?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id,width:'800',height:'500'}, function(){window.top.art.dialog({id:'edit'}).close()});
}

setcookie('refersh_time', 0);
function refersh_window() {
	var refersh_time = getcookie('refersh_time');
	if(refersh_time==1) {
		window.location.reload();
	}
}
setInterval("refersh_window()", 3000);
//-->
</script>
</body>
</html>