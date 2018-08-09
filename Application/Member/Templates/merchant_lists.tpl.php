<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get" name="searchform">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $modelid; ?>" name="modelid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">			
			所属专员：
			<select name="attract">
				<option value="-99">全部</option>
				<?php foreach($attract_lists as $k=>$v){?>
				<option value="<?php echo $v['userid'];?>" <?php if($attract == $v['userid']){?>selected<?php }?>><?php echo $v['username'];?></option>
				<?php }?>
			</select>
			<?php echo L('regtime')?>：
			<?php echo $form::date('start_time', $start_time)?>-
			<?php echo $form::date('end_time', $end_time)?>	
			<select name="status">
				<option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>><?php echo L('status')?></option>
				<option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>><?php echo L('lock')?></option>
				<option value='2' <?php if(isset($_GET['status']) && $_GET['status']==2){?>selected<?php }?>><?php echo L('normal')?></option>
			</select>
			<?php echo $form::select($grouplist, $groupid, 'name="groupid"', L('member_group'))?>				
			<select name="type">
				<option value='5' <?php if(isset($_GET['type']) && $_GET['type']==5){?>selected<?php }?>>商家id</option>
				<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>店铺旺旺</option>
				<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>联系人</option>
				<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>>邮箱</option>
				<option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>>手机号</option>
			</select>				
			<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
			<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('Member/Member/delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
			<th align="left"><?php echo '商家id'?></th>
			<th align="left"><?php echo '商家等级';?></th>
			<th align="left"><?php echo '所属专员';?></th>
			<th align="left"><?php echo '店铺名称'?></th>
			<th align="left"><?php echo '联系人';?></th>
			<th align="left"><?php echo '手机';?></th>
			<th align="left"><?php echo L('email')?></th>
			<th align="left"><?php echo '活动商品';?></th>
			<th align="left"><?php echo '冻结中保证金'?></th>
			<th align="left" width="70"><?php echo '注册时间';?></th>
			<th align="left" width="30"><?php echo '登录次数';?></th>
			<th align="left" width="70"><?php echo '最近登录';?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($manage_lists)){
	foreach($manage_lists as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['userid']?>" name="userid[]"></td>
		<!-- <td align="left"><?php if($v['islock']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?></td> -->
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><?php echo member_group_name($v['userid']);?></td>
		<td align="left"><?php echo $v['admin_name']?></td><!-- 所属专员 -->
		<td align="left"><?php echo $v['store_name']? $v['store_name']:$v['new_store_name']['store_name']?><?php if ($v['new_store_name']){ ?>(已绑定<?php echo $v['count'] ?>个店铺)<?php } ?></td>
		<td align="left"><?php echo $v['contact_name']? $v['contact_name']:$v['new_contact_name']['contact_name'];?></td>
		<td align="left"><?php echo $v['phone'];?></td>
		<td align="left"><?php echo $v['email']?></td>
		<td align="left"><?php echo $v['activity_count'];?></td>
		<td align="left"><?php echo $v['frozen_deposit']?></td>
		<td align="left"><?php echo dgmdate($v['regdate'],'Y-m-d'); ?></td>
		<td align="left"><?php echo $v['loginnum'] ?></td>
		<td align="left"><?php echo dgmdate($v['lastdate'], 'Y-m-d');?></td>
		<td align="left">
		    <a href="javascript:;" onclick="user_jr(<?php echo $v['userid'] ?>)" >[<?php echo '进入商家中心';?>]</a> 
			<a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$v['userid']));?>" >[查看]</a> 
			<a href="<?php echo U('Member/Member/addvip',array('userid'=>$v['userid']));?>" onclick="javascript:addvip(this,'<?php echo $v['userid']?>');return false;">[<?php echo '赠送vip';?>]</a>
			<a href="<?php echo U('Member/Member/edit', array('userid' => $v['userid'])) ?>" onclick="javascript:edit('<?php echo $v['userid'];?>', '<?php echo $v['username']?>');return false;">[<?php echo L('edit')?>]</a>
			<?php if($v['islock'] == 1){?>
			<a href="<?php echo U('Member/Member/unlock', array('userid' => $v['userid'])) ?>" >[<?php echo '解锁';?>]</a>
			<?php }else{?>
			<a href="<?php echo U('Member/Member/lock', array('userid' => $v['userid'])) ?>" onclick="javascript:lock(this,'<?php echo $v['nickname']?>');return false;">[<?php echo '冻结';?>]</a>
			<?php }?>
			<a href="<?php echo U('Member/Member/detail', array('userid' => $v['userid'],'modelid'=>2)) ?>">[<?php echo '账户明细';?>]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>

<script type="text/javascript" src="/templates/cloud3/style/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/dialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript">

var r ='温馨提示：<br/>';
    r +='本功能用于平台人员辅助平台商家操作 <br/>';
    r +='未经商家允许：切勿对商家的活动.资金进行操作！<br/>';

function user_jr(userid) {

 $.getJSON('/index.php?m=member&c=member&a=user_jinru&uid='+userid);
	    art.dialog({
	        content: r,
	        title:'温馨提示：',
	        id: 'EF893L',
	        ok: function () {
	        	window.open("/user/");    
	        	this.title('3秒后自动关闭').time(3);
	            return false;
	        },
	        cancelVal: '关闭',
	        cancel: true //为true等价于function(){}
	    });


}


<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member_group')?>《'+name+'》',id:'edit',iframe:'?m=member&c=member&a=edit&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function lock(obj, name){
	window.top.art.dialog({id:'lock'}).close();
	window.top.art.dialog({
        title:'<?php echo L('lock').'会员'?>《'+name+'》',id:'lock',iframe:obj.href,width:'400',height:'150'
    }, function(){
        var d = window.top.art.dialog({id:'lock'}).data.iframe;
        d.document.getElementById('dosubmit').click();return false;
    }, function(){
        window.top.art.dialog({id:'lock'}).close()
    });
}

function addvip(obj, name){
    window.top.art.dialog({id:'addvip'}).close();
    window.top.art.dialog({
        title:'赠送商家会员',id:'addvip',iframe:obj.href,width:'400',height:'150'
    }, function(){
        var d = window.top.art.dialog({id:'addvip'}).data.iframe;
        d.document.getElementById('dosubmit').click();return false;
    }, function(){
        window.top.art.dialog({id:'addvip'}).close()
    });
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

function member_infomation(userid) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

function aword(obj ,name){
	window.top.art.dialog({id:'aword'}).close();
	window.top.art.dialog({title:'<?php echo L('aword').L('member')?>《'+name+'》',id:'aword',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}
//-->
</script>
</body>
</html>