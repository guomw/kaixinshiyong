<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'Admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#name").formValidator({onshow:"<?php echo L('input').'商家类型名称，例如：钻石商家'?>",onfocus:"<?php echo '商家类型名称'.L('between_2_to_8')?>"}).inputValidator({min:2,max:15,onerror:"<?php echo '商家类型名称'.L('between_2_to_8')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo '商家类型名称'.L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "<?php echo U('Member/MerchantGroup/public_checkname_ajax') ?>",
		data :"",
		datatype : "html",
		async:'false',
		success : function(data){	
            if( data == "1" ) {
                return true;
			} else {
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "<?php echo L('groupname_already_exist')?>",
		onwait : "<?php echo L('connecting_please_wait')?>"
	});
	$("#service_price").formValidator({onshow:"<?php echo '平台服务费用于购物返利活动，例如 8%';?>",onfocus:"<?php echo '请输入平台服务费用';?>"}).regexValidator({regexp:"num",datatype:"enum",onerror:"<?php echo '只能为数字';?>"});

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
			<td width="120"><?php echo L('merchant_group_name')?></td> 
			<td><input type="text" name="info[name]"  class="input-text" id="name" value="" /></td>
		</tr>
		<tr>
			<td><?php echo L('merchant_group_image')?></td> 
			<td><?php echo $form::images('info[image]', 'image', $image, 'document','','20');?></td>
		</tr>
		<tr>
			<td width="80"><?php echo '收费标准';?></td> 
			<td>
				<select name="info[pricetype][]">
					<option value="1">按月</option>
					<option value="2">按季</option>
					<option value="3">按年</option>
				</select>
				<input type="text" name="info[pricetype][]" class="input-text" size="6">/美元
			</td>
		</tr>
		
		<tr>
			<td><?php echo '下单类型';?></td> 
			<td>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="1" checked />
					<?php echo '普通下单';?></label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="2" <?php if($groupinfo['allowpostverify']){?>checked<?php }?>>
					<?php echo '搜索下单';?></label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="3" <?php if($groupinfo['allowupgrade']){?>checked<?php }?> />
					<?php echo '答案下单';?> </label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="4" <?php if($groupinfo['allowsendmessage']){?>checked<?php }?>>
					<?php echo "二维码下单"?> </label>
				</span>
			</td>
		</tr>
		<tr>
			<td><?php echo '专享权利';?></td>
			<td><textarea cols="50" rows="" name="info[eclusive]"></textarea></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo '购物返利设置';?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与购物返利活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][rebate][isopen]" checked>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][rebate][isopen]">否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '平台服务费';?></td> 
			<td><input type="text" name="info[config][rebate][service]" class="input-text" id="service_price" size="5" value="8">%</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品次数';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][apply][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][apply][radio]">自定义</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="info[config][rebate][apply][times]" size="6">
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品间隔时间';?></td> 
			<td>
 				<label><input type="radio" value="0" name="info[config][rebate][distime][radio]" checked>不限</label>&nbsp;&nbsp; 
				<label><input type="radio" value="1" name="info[config][rebate][distime][radio]">自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config][rebate][distime][times]" size="6">
					<select name="info[config][rebate][distime][type]">
						<option value="1">天</option>
						<option value="2">小时</option>
						<option value="3">分</option>
					</select>
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名活动天数';?></td> 
			<td>
				<input type="text" name="info[config][rebate][activitydays]" size="20" value="<?php echo $r_days;?>">/天
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品件数';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][goods_number][radio]" <?php if($r_minunit == 0 && $r_maxunit == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][goods_number][radio]" <?php if($r_minunit != 0 && $r_maxunit != 0){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][rebate][goods_number][min]" value="<?php echo $r_minunit;?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $r_maxunit;?>" name="info[config][rebate][goods_number][max]" />
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品价格范围';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][price_range][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][price_range][radio]">&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][rebate][price_range][min]" value="" />&nbsp;最多&nbsp;<input type="text" size="6" value="" name="info[config][rebate][price_range][max]" />
			</td>
		</tr>
		
	</table>
</fieldset>
 <div class="bk15"></div>
<fieldset>
	<legend><?php echo '免费试用设置';?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与免费试用活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][trial][isopen]" checked>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][trial][isopen]">否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<!-- tr>
			<td width="80"><?php echo '单份商品费用';?></td> 
			<td><input type="text" name="info[config][trial][product_cost]" class="input-text" id="f_part" size="20" value="10">/美元</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '单场活动费用';?></td> 
			<td><input type="text" name="info[config][trial][activity_cost]" class="input-text" id="f_field" size="20" value="100">/美元</td>
		</tr> -->
		
		<tr>
			<td width="80"><?php echo '推广费用';?></td> 
			<td>
					<select name="info[config][trial][cost][check]">
						<option value="1">单份商品</option>
						<option value="2">单场活动</option>
					</select>
				<label><input type="text" value="0" name="info[config][trial][cost][price]" size="6">/美元</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品次数';?></td> 
			<td>
				<label><input type="radio" name="info[config][trial][apply][radio]" value="0" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][trial][apply][radio]" value="1">自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][free][apply][times]">
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品间隔时间';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][trial][distime][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][distime][radio]">自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config]trial][distime][times]" size="6">
					<select name="info[config][trial][distime][type]">
						<option value="1">天</option>
						<option value="2">小时</option>
						<option value="3">分</option>
					</select>
				
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名活动天数';?></td> 
			<td>
				<input type="text" name="info[config][trial][activitydays]" size="20" value="7">/天
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品件数';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][trial][goods_number][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][goods_number][radio]">&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][trial][goods_num][min]" value="" />&nbsp;最多&nbsp;<input type="text" size="6" value="" name="info[config][trial][goods_num][max]" />
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品价格范围';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][trial][price_range][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][price_range][radio]">&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][trial][price_range][min]" value="" />&nbsp;最多&nbsp;<input type="text" size="6" value="" name="info[config][trial][price_range][max]" />
			</td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo '9.9包邮设置';?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与9.9包邮活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][postal][isopen]" checked>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][postal][isopen]">否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品次数';?></td> 
			<td>
				<label><input type="radio" name="info[config][postal][apply][radio]" value="0" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][postal][apply][radio]" value="1">自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][postal][apply][times]">
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品间隔时间';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][postal][distime][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][postal][distime][radio]">自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config][postal][distime][times]" size="6">
					<select name="info[config][postal][distime][type]">
						<option value="1">天</option>
						<option value="2">小时</option>
						<option value="3">分</option>
					</select>
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名活动天数';?></td> 
			<td>
				<input type="text" name="info[config][postal][activitydays]" size="20" value="7">/天
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名商品件数';?></td> 
			<td>
				<label><input type="radio" value="0" name="info[config][postal][goods_number][radio]" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][postal][goods_number][radio]">&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][postal][goods_number][min]" value="" />&nbsp;最多&nbsp;<input type="text" size="6" value="" name="info[config][postal][goods_number][max]" />
			</td>
		</tr>
	</table>
</fieldset>
    <div class="bk15"></div>
<fieldset>
	<legend><?php echo '品牌折扣设置';?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与品牌折扣活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][brand][isopen]" checked>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][brand][isopen]">否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '活动商品发布数量';?></td> 
			<td>
				<label><input type="radio" name="info[config][brand][apply][radio]" value="0" checked>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][brand][apply][radio]" value="1">自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][brand][apply][times]">
			</td>
		</tr>
		
		<tr>
			<td width="80"><?php echo '报名活动天数';?></td> 
			<td>
				<input type="text" name="info[config][brand][activitydays]" size="20" value="7"> /天
			</td>
		</tr>
	</table>
</fieldset>
 <div class="bk15"></div>
 <fieldset>
	<legend><?php echo '专场活动设置';?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与品牌折扣活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][special][isopen]" checked>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][special][isopen]">否</label>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>