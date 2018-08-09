<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get" name="searchform">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $t; ?>" name="modelid">
<input type="hidden" value="<?php echo $t; ?>" name="t">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">				
				<?php echo L('绑定时间')?>：
				<?php echo $form::date('start_time', $start_time)?>-
				<?php echo $form::date('end_time', $end_time)?>	
				
				<select name="type">
					<!-- <option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('username')?></option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('uid')?></option> -->
					<option value='0'>请选择</option>
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('淘宝账号')?></option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('邮箱账号')?></option>
					
				</select>				
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('bind_delete');?>" method="post" onsubmit="check();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left"   width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
			<th align="center" width="5%">id</th>
			<th align="left"   width="5%"><?php echo '会员名称';?></th>
			<th align="left"   width="10%"><?php echo '会员邮箱';?></th>
			<th align="left"   width="5%"><?php echo '会员等级';?></th>
			<th align="left" width="10%">淘宝账号</th>
			<th align="left" width="5%">安全等级</th>
			<th align="left" width="10%">淘宝信誉</th>
			<th align="left" width="10%">淘宝信誉截图</th>
			<th align="left" width="5%">账号等级</th>
			<th align="left" width="10%">绑定状态</th>
			<th align="left" width="10%">绑定时间</th>
			<th align="left" width="10%"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($lists)){
	foreach($lists as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="ids[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo nickname($v['userid'])?></td>
		<td align="left"><?php echo $v['userinfo']['email']?></td>
		<td align="left"><?php echo member_group_name($v['userid'])?></td>
		<td align="left"><?php if(empty($v['account'])) {echo '-';}else{echo $v['account'];}?></td>
		<td align="left">

	<?php 
		if($v['safe_grade'] == 0){
			echo"安全";
		}
		elseif($v['safe_grade'] == 1){
			echo"一般";
		}

		elseif($v['safe_grade'] == 2){
			echo"危险";
		}


		?>


	</td>
	<td align="left"><img src="<?php echo $v['account_level']?>"></td>
	<td align="left"><?php echo $v['taobao_img'] ? '<a href="javascript:;" onclick="get_toabao_img(\''.$v[taobao_img].'\')"><img width="50" src="'.$v['taobao_img'].'" /></a>' : '' ?></td>
	<td align="left"><?php echo $v['bLevel'] ?></td>
	<td align="left">
				<?php 
					if($v['status'] == 0){
							echo"未通过";
						}
						elseif($v['status'] == 1){
							echo"已绑定";
						}

						elseif($v['status'] == 2){
							echo"解绑";
						}


				?>

	</td>
	<td align="left"><?php echo dgmdate($v['inputtime'], 'Y-m-d');?></td>
	<td align="left">
	<a href="javascript:confirmurl('<?php echo U('member/member/bind_delete', array('ids[]' => $v['id']));?>','<?php echo L('sure_delete')?>');"
                >[删除]</a> 
	</td>
    </tr>
    <?php
}}?>

</tbody>
</table>
<div class="btn">
    <label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div> 

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function lock(obj, name){
	window.top.art.dialog({id:'lock'}).close();
	window.top.art.dialog({title:'<?php echo L('lock').'会员'?>《'+name+'》',id:'lock',iframe:obj.href,width:'400',height:'150'}, function(){var d = window.top.art.dialog({id:'lock'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'lock'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}
function check() {
    if(myform.action == "<?php echo U('bind_delete'); ?>") {
        var ids='';
        $("input[name='ids[]']:checked").each(function(i, n){
            ids += $(n).val() + ',';
        });
        if(ids=='') {
            window.top.art.dialog({content:'请选择要删除的选项',lock:true,width:'200',height:'50',time:1.5},function(){});
            return false;
        }
    }
    myform.submit();
}

 /*查看已上传的信誉截图*/
 function get_toabao_img(img){
	art.dialog({
		lock: true,fixed: true,
		title: '已上传的信誉截图',
		time:5,
		content:'<img src="'+img+'" />',
		ok: function (){

			}
		});
 }

//-->
</script>
<script type="text/javascript" src="/static/js/dialog/jquery.artDialog.js?skin=default"></script>
</body>
</html>