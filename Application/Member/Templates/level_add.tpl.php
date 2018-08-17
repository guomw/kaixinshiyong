<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'Admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#name").formValidator({onshow:"<?php echo L('input')."会员等级"?>",onfocus:"<?php echo "会员等级".L('between_2_to_8')?>"}).inputValidator({min:2,max:15,onerror:"<?php echo "会员等级".L('between_2_to_8')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo "会员等级".L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "<?php echo U('public_checkename_ajax') ?>",
		data :"",
		datatype : "JSON",
		async:'false',
		success : function(data){	
            if( data.status == 1 ) {
                return true;
			} else {
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "<?php echo L('该会员等级已存在')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	});
	$("#group_point").formValidator({tipid:"pointtip",onshow:"<?php echo L('input').L('point')?>",onfocus:"<?php echo L('point').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('point').L('between_1_to_8_num')?>"});
	$("#group_starnum").formValidator({tipid:"starnumtip",onshow:"<?php echo L('input')."经验值"?>",onfocus:"<?php echo "经验值".L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo "经验值".L('between_1_to_8_num')?>"});
	$("#price_m").formValidator({
				empty:false,
				onempty:'该会员等级价格不能为空',
				onshow:'请输入该会员等级价格' ,
				onfocus:"请输入该会员等级价格" 
			}).regexValidator({
				regexp:'decmal4',
				datatype:'enum',
				onerror:'请输入该会员等级价格'
			});

	$("#price_p").formValidator({
				empty:false,
				onempty:'该会员等级价格不能为空',
				onshow:'请输入该会员等级价格' ,
				onfocus:"请输入该会员等级价格" 
			}).regexValidator({
				regexp:'decmal4',
				datatype:'enum',
				onerror:'请输入该会员等级价格'
			});

	$("#price_y").formValidator({
				empty:false,
				onempty:'该会员等级价格不能为空',
				onshow:'请输入该会员等级价格' ,
				onfocus:"请输入该会员等级价格" 
			}).regexValidator({
				regexp:'decmal4',
				datatype:'enum',
				onerror:'请输入该会员等级价格'
			});

});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('add'); ?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120"><?php echo "等级名称"?></td> 
			<td><input type="text" name="info[name]"  class="input-text" id="name"></td>
		</tr>
		<tr>
			<td><?php echo "积分"?></td> 
			<td>
			<input type="text" name="info[point]" class="input-text" id="group_point" value="" size="6"></td>
		</tr>
		<tr>
			<td>经验值</td> 
			<td><input type="text" name="info[exp]" class="input-text" id="group_starnum" size="6"></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('more_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td><?php echo L('member_group_permission')?></td> 
			<td>
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowpost]" value="1">
					<?php echo L('member_group_publish')?>
				</span>
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowpostverify]" value="1">
					<?php echo L('member_group_publish_verify')?>
				</span>
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowupgrade]" value="1">
					<?php echo L('member_group_upgrade')?> 
				</span>
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowsendmessage]" value="1">
					<?php echo L('member_group_sendmessage')?> 
				</span>	
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowattachment]" value="1">
					<?php echo L('allowattachment')?> 
				</span>
				<span class="ik lf" style="width:120px;">
					<input type="checkbox" name="info[allowsearch]" value="1">
					<?php echo L('allowsearch')?> 
				</span>
			</td>

		</tr>

		<tr>
			<td width="80"><?php echo L('member_group_upgradeprice')?></td> 
			<td>
				<span class="ik lf" style="width:120px;">
					<?php echo "包月"?>：
					<input type="text" name="info[price_m]" class="input-text" id="price_m" size="6">美元
				</span>
				<span class="ik lf" style="width:120px;">
					<?php echo "包季"?>：
					<input type="text" name="info[price_p]" class="input-text" id="price_p" size="6">美元
				</span>
				<span class="ik lf" style="width:120px;">
					<?php echo L('member_group_yearprice')?>：
					<input type="text" name="info[price_y]" class="input-text" id="price_y" size="6">美元
				</span>
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo "等级图标"?></td> 
			<td><?php echo $form::images('info[icon]', 'image', $image, 'document');?></td>
		</tr>
		<tr>
			<td width="80"><?php echo L('member_group_description')?></td> 
			<td>
				<!-- <input type="text" name="info[description]" class="input-text" size="60"> -->
				<textarea name="info[description]">  </textarea>
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo "类型"?></td> 
			<td>
				<input type="radio" name="info[issystem]" value="0" >系统
				<input type="radio" name="info[issystem]" value="1" checked>用户

			</td>
		</tr>

		</table>
</fieldset>
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>">
</form>
</div>
</div>
</body>
</html>