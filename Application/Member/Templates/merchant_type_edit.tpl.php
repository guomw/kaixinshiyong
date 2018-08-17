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
	}).defaultPassed();
	$("#service_price").formValidator({onshow:"<?php echo '平台服务费用于购物返利活动，例如 8%';?>",onfocus:"<?php echo '请输入平台服务费用';?>"}).regexValidator({regexp:"num",datatype:"enum",onerror:"<?php echo '只能为数字';?>"}).defaultPassed();;

});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('edit'); ?>" method="post" id="myform">
<input type="hidden" value="<?php echo $groupid;?>" name="info[groupid]">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120"><?php echo L('merchant_group_name')?></td> 
			<td><input type="text" name="info[name]"  class="input-text" id="name" value="<?php echo $name;?>" /></td>
		</tr>
		<tr>
			<td><?php echo L('merchant_group_image')?></td> 
			<td><?php echo $form::images('info[image]', 'image', $image, 'document','','20');?></td>
		</tr>
		<tr>
			<td width="80">收费标准</td> 
			<td>
				<select name="info[pricetype][]">
					<option value="1" <?php if($pricetype[0] == 1){?> selected<?php }?>>按月</option>
					<option value="2" <?php if($pricetype[0] == 2){?> selected<?php }?>>按季</option>
					<option value="3" <?php if($pricetype[0] == 3){?> selected<?php }?>>按年</option>
				</select>
				<input type="text" name="info[pricetype][]" class="input-text" size="6" value="<?php echo $pricetype[1];?>">/美元
			</td>
		</tr>
		
		<tr>
			<td>下单类型</td> 
			<td>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="1" <?php if (in_array('1',$ordertype)) {?>checked <?php }?>/>普通下单</label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="2" <?php if (in_array('2',$ordertype)) {?>checked <?php }?> />搜索下单</label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="3" <?php if (in_array('3',$ordertype)) {?>checked <?php }?> />答案下单</label>
				</span>
				<span class="ik lf" style="width:120px;">
					<label><input type="checkbox" name="info[ordertype][]" value="4" <?php if (in_array('4',$ordertype)) {?>checked <?php }?> />二维码下单</label>
				</span>
			</td>
		</tr>

		<tr>
			<td>店铺绑定个数</td>
			<td><input type="text" name="info[store_num]" value="<?php echo $store_num;?>" size="3">/个</td>
		</tr>
		<tr>
			<td>专享权利</td>
			<td><textarea cols="50" rows="" name="info[eclusive]"><?php echo $eclusive;?></textarea></td>
		</tr>
	</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend>购物返利设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与购物返利活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][rebate][isopen]" <?php if($rebate['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][rebate][isopen]" <?php if($rebate['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80">平台服务费</td> 
			<td><input type="text" name="info[config][rebate][service]" class="input-text" id="service_price" size="5" value="<?php echo $rebate['service'];?>">%</td>
		</tr>
		
		<tr>
			<td width="80">报名商品次数</td>
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][apply][radio]" <?php if($rebate['apply']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][apply][radio]" <?php if($rebate['apply']['radio'] == 1){?>checked<?php }?>>自定义</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="info[config][rebate][apply][times]" size="6" value="<?php echo $rebate['apply']['times']?>"/>
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品间隔时间</td> 
			<td>
 				<label><input type="radio" value="0" name="info[config][rebate][distime][radio]" <?php if($rebate['distime']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp; 
				<label><input type="radio" value="1" name="info[config][rebate][distime][radio]" <?php if($rebate['distime']['radio'] == 1){?>checked<?php }?>>自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config][rebate][distime][times]" size="6" value="<?php echo $rebate['distime']['times'];?>"/>
					<select name="info[config][rebate][distime][type]">
						<option value="1" <?php if($rebate['distime']['type'] == 1){?> selected<?php }?>>天</option>
						<option value="2" <?php if($rebate['distime']['type'] == 2){?> selected<?php }?>>小时</option>
						<option value="3" <?php if($rebate['distime']['type'] == 3){?> selected<?php }?>>分</option>
					</select>
			</td>
		</tr>
		
		<tr>
			<td width="80">报名活动天数</td> 
			<td>
				<input type="text" name="info[config][rebate][activitydays]" size="20" value="<?php echo $rebate['activitydays'];?>">/天
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品件数</td> 
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][goods_number][radio]" <?php if($rebate['goods_number']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][goods_number][radio]" <?php if($rebate['goods_number']['radio'] == 1){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][rebate][goods_number][min]" value="<?php echo $rebate['goods_number']['min'];?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $rebate['goods_number']['max'];?>" name="info[config][rebate][goods_number][max]" />
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品价格范围</td> 
			<td>
				<label><input type="radio" value="0" name="info[config][rebate][price_range][radio]" <?php if($rebate['price_range']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][rebate][price_range][radio]" <?php if($rebate['price_range']['radio'] == 1){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][rebate][price_range][min]" value="<?php echo $rebate['price_range']['min'];?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $rebate['price_range']['max'];?>" name="info[config][rebate][price_range][max]" />
			</td>
		</tr>
		
	</table>
</fieldset>
 <div class="bk15"></div>
<fieldset>
	<legend>免费试用设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">是否允许参与免费试用活动</td>
			<td>
				<label><input type="radio" value="1" name="info[config][trial][isopen]" <?php if($trial['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][trial][isopen]" <?php if($trial['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>

		<tr>
			<td width="80">是否允许使用禁止重复参与功能</td>
			<td>
				<label><input type="radio" value="1" name="info[config][trial][is_join]" <?php if($trial['is_join'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][trial][is_join]" <?php if($trial['is_join'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>

		<tr>
			<td width="80">是否允许使用禁止IP重复功能</td>
			<td>
				<label><input type="radio" value="1" name="info[config][trial][is_ip]" <?php if($trial['is_ip'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][trial][is_ip]" <?php if($trial['is_ip'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>

		<tr>
			<td width="80">实物试用收费规则</td>
			<?php $seller_charge_money = C_READ('seller_charge_money'); ?>
			<td>
				单份商品
				<input type="text" value="<?php if($trial['cost']['product_cost']){echo $trial['cost']['product_cost'];}else{echo '8';};?>" name="info[config][trial][cost][product_cost]" size="6">/美元&nbsp;&nbsp;
			</td>
		</tr>

		<tr>
			<td width="80">是否允许参与拍A发B</td>
			<td>
				<label><input type="radio" class="js_a_b" value="2" name="info[config][trial][is_a_b]" <?php if($trial['is_a_b'] == 2){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" class="js_a_b"  value="1" name="info[config][trial][is_a_b]" <?php if($trial['is_a_b'] == 1){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>

		<tr <?php if ($trial['is_a_b'] == 2){?> ''  <?php }else{ ?> style="display:none;" <?php } ?> class="js_a_show">
			<td width="80">拍A发B是否收费</td> 
			<td>
				<label><input type="radio" value="0" class="js_a_b_check" name="info[config][trial][a_b_cost]" <?php if($trial['a_b_cost'] == 0){?>checked<?php }?>>免费</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" class="js_a_b_check" name="info[config][trial][a_b_cost]" <?php if($trial['a_b_cost'] == 1){?>checked<?php }?>>收费&nbsp;&nbsp;</label>&nbsp;&nbsp;
			</td>
		</tr>

		 <tr <?php if ($trial['a_b_cost'] ==1 && $trial['is_a_b'] == 2){?> ''  <?php }else{ ?> style="display :none;" <?php } ?> class="js_fee_show">
              <th>拍A发B收费规则</th>
              <td  class="y-bg"><input type="button" id="a_add" value="新增" style="width:50px;height:25px;" />&nbsp;&nbsp;提示：下单单价>=100.00美元，金额为7.00美元</td></tr>

               <?php 

               $trial['a_b'] = array_values($trial['a_b']);

               foreach ($trial['a_b'] as $k=>$v) {?>
                   <tr <?php if ($trial['a_b_cost'] ==1  && $trial['is_a_b'] == 2){?> ''  <?php }else{ ?> style="display :none;" <?php } ?> class="js_fee_show">       

               		<th></th>
	                <td class="y-bg">
	                    下单价>=：<input type="text" class="input-text" id='check_time' name="info[config][trial][a_b][<?php echo $k;?>][min]" size="3" value="<?php echo $v['min']?>" />
	                    <label>提取金额：<input type="text" class="input-text" id='check_time' name="info[config][trial][a_b][<?php echo $k;?>][a_b_trial]" size="3" value="<?php echo $v['a_b_trial']?>" />/美元</label>
	                    <?php if ($k > 0): ?>
	                    <a class="delete" style="color:red;cursor:pointer">删除</a>
	                    <?php endif ?>
	                </td>
	             </tr>
	            <?php }?>
	           

			<script type="text/javascript">
			$('.js_a_b').click(function(){
				var a = $('input[name="info[config][trial][is_a_b]"]:checked').val();
				if (a == 2) {
					$('.js_a_show').show();
				}else{
					$('.js_a_show').hide();
					$('.js_fee_show').hide();

				};
			});
			$(".js_a_b_check").click(function(){
                		 var v = $('input[name="info[config][trial][a_b_cost]"]:checked ').val();
	                		if (v == 1) {
	                		$('.js_fee_show').show();
	                	}else{
	                		$('.js_fee_show').hide();
	                	};
                	});
                    var num = '<?php echo $a_b_count-1;?>';
                    $("#a_add").click(function(){
                        num++;
                        var html = '';
                        html += '<tr>';
                        html += '<th></th>';
                        html += '<td class="y-bg">';
                        html += '下单价>=：<input type="text" class="input-text" id="check_time" name="info[config][trial][a_b]['+num+'][min]" size="3" value="" />/美元';
                        html += '<label> 提取金额：<input type="text" class="input-text" id="check_time" name="info[config][trial][a_b]['+num+'][a_b_trial]" size="3" value="" />/美元</label>';
                        html += '<span class="delete" style="color:red;cursor:pointer">删除</span>';
                        html += '</td>';
                        html += '</tr>';
                        $("#a_b").before(html);
                        //删除
                       
                    });
					 $(document).on("click",".delete",function(){
                            //判断用餐时段的个数 
                            $(this).parents('tr').remove();
                        });



			</script>


		<tr id="a_b">
			<td width="80">是否允许参与红包试用</td>
			<td>
				<label><input type="radio" value="2" class="js_join_red" name="info[config][trial][is_red]" <?php if($trial['is_red'] == 2){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" class="js_join_red" name="info[config][trial][is_red]" <?php if($trial['is_red'] == 1){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>

		<tr <?php if ($trial['is_red'] == 2){?> ''  <?php }else{ ?> style="display:none;" <?php } ?> class="js_red_show">
			<td width="80">红包试用是否收费</td> 
			<td>
				<label><input type="radio" value="0" class="js_red_check" name="info[config][trial][red_cost]" <?php if($trial['red_cost'] == 0){?>checked<?php }?>>免费</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" class="js_red_check" name="info[config][trial][red_cost]" <?php if($trial['red_cost'] == 1){?>checked<?php }?>>收费&nbsp;&nbsp;</label>&nbsp;&nbsp;
			</td>
		</tr>

		 <tr <?php if ($trial['red_cost'] == 1 && $trial['is_red'] == 2){?> ''  <?php }else{ ?> style="display:none;" <?php } ?>class="js_fee_red_show">
              <th>红包收费规则</th>
              <td  class="y-bg"><input type="button" id="red_add" value="新增" style="width:50px;height:25px;" />&nbsp;&nbsp;提示：下单单价>=100.00美元，金额为7.00美元</td></tr>
    	     

               <?php 

               $trial['red'] = array_values($trial['red']);
               foreach ($trial['red'] as $k=>$v) {?>
                <tr <?php if ($trial['red_cost'] == 1 && $trial['is_red'] == 2 ){?> ''  <?php }else{ ?> style="display:none;" <?php } ?>class="js_fee_red_show">  
               		<th></th>
	                <td class="y-bg">
	                    下单价>=：<input type="text" class="input-text" id='check_time' name="info[config][trial][red][<?php echo $k;?>][min]" size="3" value="<?php echo $v['min']?>" />
	                    <label>提取金额：<input type="text" class="input-text" id='check_time' name="info[config][trial][red][<?php echo $k;?>][red_trial]" size="3" value="<?php echo $v['red_trial']?>" />/美元</label>
	                    <?php if ($k > 0): ?>
	                    <a class="delete" style="color:red;cursor:pointer">删除</a>
	                    <?php endif ?>
	                </td>
	             </tr>
	            <?php }?>

	      <script type="text/javascript">
			$('.js_join_red').click(function(){
				var a = $('input[name="info[config][trial][is_red]"]:checked').val();
				if (a == 2) {
					$('.js_red_show').show();
				}else{
					$('.js_red_show').hide();
					$('.js_fee_red_show').hide();

				};
			});
			$(".js_red_check").click(function(){
                		 var v = $('input[name="info[config][trial][red_cost]"]:checked ').val();
	                		if (v == 1) {
	                		$('.js_fee_red_show').show();
	                	}else{
	                		$('.js_fee_red_show').hide();
	                	};
                	});
                    var num = '<?php echo $red_count-1;?>';
                    $("#red_add").click(function(){
                        num++;
                        var html = '';
                        html += '<tr>';
                        html += '<th></th>';
                        html += '<td class="y-bg">';
                        html += '下单价>=：<input type="text" class="input-text" id="check_time" name="info[config][trial][red]['+num+'][min]" size="3" value="" />/美元';
                        html += '<label> 提取佣金：<input type="text" class="input-text" id="check_time" name="info[config][trial][red]['+num+'][red_trial]" size="3" value="" />/美元</label>';
                        html += '<span class="delete" style="color:red;cursor:pointer">删除</span>';
                        html += '</td>';
                        html += '</tr>';
                        $("#red").before(html);
                        //删除
                       
                    });
					 $(document).on("click",".delete",function(){
                            //判断用餐时段的个数 
                            $(this).parents('tr').remove();
                        });



			</script>



		
		<tr id="red">
			<td width="80">报名商品次数</td>
			<td>
				<label><input type="radio" name="info[config][trial][apply][radio]" value="0" <?php if($trial['apply']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][trial][apply][radio]" value="1" <?php if($trial['apply']['radio'] == 1){?>checked<?php }?>>自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][trial][apply][times]" value="<?php echo $trial['apply']['times']?>" />
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品间隔时间</td> 
			<td>
				<label><input type="radio" value="0" name="info[config][trial][distime][radio]" <?php if($trial['distime']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][distime][radio]" <?php if($trial['distime']['radio'] == 1){?>checked<?php }?>>自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config][trial][distime][times]" size="6" value="<?php echo $trial['distime']['times'];?>"/> 
					<select name="info[config][trial][distime][type]">
						<option value="1" <?php if($trial['distime']['type'] == 1){?>selected <?php }?>>天</option>
						<option value="2" <?php if($trial['distime']['type'] == 2){?>selected <?php }?>>小时</option>
						<option value="3" <?php if($trial['distime']['type'] == 3){?>selected <?php }?>>分</option>
					</select>
				
			</td>
		</tr>
		
		<tr>
			<td width="80">报名活动天数</td> 
			<td>
				<input type="text" name="info[config][trial][activitydays]" size="20" value="<?php echo $trial['activitydays'];?>">/天
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品件数</td>
			<td>
				<label><input type="radio" value="0" name="info[config][trial][goods_number][radio]" <?php if($trial['product_num']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][goods_number][radio]" <?php if($trial['product_num']['radio'] == 1){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][trial][goods_number][min]" value="<?php echo $trial['goods_number']['min'];?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $trial['goods_number']['max'];?>" name="info[config][trial][goods_number][max]" />
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品价格范围</td> 
			<td>
				<label><input type="radio" value="0" name="info[config][trial][price_range][radio]" <?php if($trial['price_range']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][trial][price_range][radio]" <?php if($trial['price_range']['radio'] == 1){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][trial][price_range][min]" value="<?php echo $trial['price_range']['min'];?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $trial['price_range']['max'];?>" name="info[config][trial][price_range][max]" />
			</td>
		</tr>
	</table>
</fieldset>
<!-- 闪电佣金 -->
<div class="bk15"></div>
<fieldset>
	<legend>闪电佣金设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">是否允许参与闪电佣金活动</td>
			<td>
				<label><input type="radio" value="1" name="info[config][commission][isopen]" <?php if($commission['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][commission][isopen]" <?php if($commission['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>
	
		
		
		
		<tr>
			<td width="80">是否收费</td> 
			<td>
				<label><input type="radio" value="0" class="js_check" name="info[config][commission][cost]" <?php if($commission['cost'] == 0){?>checked<?php }?>>免费</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" class="js_check" name="info[config][commission][cost]" <?php if($commission['cost'] == 1){?>checked<?php }?>>收费&nbsp;&nbsp;</label>&nbsp;&nbsp;
			</td>
		</tr>


        <tr <?php if ($commission['service_price']){?> ''  <?php }else{ ?> style="display:none;" <?php } ?>class="js_show">
              <th>佣金规则</th>
              <td  class="y-bg"><input type="button" id="type_add" value="新增" style="width:50px;height:25px;" />&nbsp;&nbsp;提示：订单价100.00美元，佣金为7.00美元</td>
             </tr>

               <?php foreach ($commission['service_price'] as $k=>$v) {?>

	        <tr <?php if ($commission['service_price']){?> ''  <?php }else{ ?> style="display:none;" <?php } ?> class="js_show">
	                <th></th>
	                <td class="y-bg">
	                    下单价：<input type="text" class="input-text" id='check_time' name="info[config][commission][service_price][<?php echo $k;?>][min]" size="3" value="<?php echo $v['min']?>" /> ~ <input type="text" class="input-text" id='check_time' name="info[config][commission][service_price][<?php echo $k;?>][max]" size="3" value="<?php echo $v['max']?>" />
	                    <label>提取佣金：<input type="text" class="input-text" id='check_time' name="info[config][commission][service_price][<?php echo $k;?>][commission]" size="3" value="<?php echo $v['commission']?>" />/美元</label>
	                    <?php if ($k > 0): ?>
	                    <a class="delete" style="color:red;cursor:pointer">删除</a>
	                    <?php endif ?>
	                </td>
	            </tr>
	            <?php }?>
	         <tr id="commission">
			<td width="80">报名活动天数</td> 
			<td>
				<input type="text" name="info[config][commission][activitydays]" size="20" value="<?php echo $commission['activitydays'];?>">/天
			</td>
		</tr>
		
		
		
	
		
		
	</table>
</fieldset>
<script type="text/javascript">
                $(document).ready(function(){

                	$(".js_check").click(function(){
                		 var v = $('input[name="info[config][commission][cost]"]:checked ').val();
	                		if (v == 1) {
	                		$('.js_show').show();
	                	}else{
	                		$('.js_show').hide();
	                	};
                	});
                    var num = '<?php echo $bonus_count-1;?>';
                    $("#type_add").click(function(){
                        num++;
                        var html = '';
                        html += '<tr>';
                        html += '<th></th>';
                        html += '<td class="y-bg">';
                        html += '下单价：<input type="text" class="input-text" id="check_time" name="info[config][commission][service_price]['+num+'][min]" size="3" value="" /> ~ <input type="text" class="input-text" id="check_time" name="info[config][commission][service_price]['+num+'][max]" size="3" value="" />';
                        html += '<label> 提取佣金：<input type="text" class="input-text" id="check_time" name="info[config][commission][service_price]['+num+'][commission]" size="3" value="" />/美元</label>';
                        html += '<span class="delete" style="color:red;cursor:pointer">删除</span>';
                        html += '</td>';
                        html += '</tr>';
                        $("#commission").before(html);
                        //删除
                       
                    });
					 $(document).on("click",".delete",function(){
                            //判断用餐时段的个数 
                            $(this).parents('tr').remove();
                        });

                   
                });
            </script>


<!-- 闪电佣金 -->


<div class="bk15"></div>
<fieldset>
	<legend>9.9包邮设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">是否允许参与9.9包邮活动</td> 
			<td>
				<label><input type="radio" value="1" name="info[config][postal][isopen]" <?php if($postal['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][postal][isopen]" <?php if($postal['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品次数</td> 
			<td>
				<label><input type="radio" name="info[config][postal][apply][radio]" value="0" <?php if($postal['apply']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][postal][apply][radio]" value="1" <?php if($postal['apply']['radio'] == 1){?>checked<?php }?>>自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][postal][apply][times]" value="<?php echo $postal['apply']['times'];?>"/>
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品间隔时间</td>
			<td>
				<label><input type="radio" value="0" name="info[config][postal][distime][radio]" <?php if($postal['distime']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][postal][distime][radio]" <?php if($postal['distime']['radio'] == 1){?>checked<?php }?>>自定义&nbsp;&nbsp;</label>&nbsp;&nbsp;
					<input type="text" name="info[config][postal][distime][times]" size="6" value="<?php echo $postal['distime']['times'];?>"/>
					<select name="info[config][postal][distime][type]">
						<option value="1" <?php if($postal['distime']['type'] == 1){?>selected<?php }?>>天</option>
						<option value="2" <?php if($postal['distime']['type'] == 2){?>selected<?php }?>>小时</option>
						<option value="3" <?php if($postal['distime']['type'] == 3){?>selected<?php }?>>分</option>
					</select>
			</td>
		</tr>
		
		<tr>
			<td width="80">报名活动天数</td> 
			<td>
				<input type="text" name="info[config][postal][activitydays]" size="20" value="<?php echo $postal['activitydays'];?>">/天
			</td>
		</tr>
		
		<tr>
			<td width="80">报名商品件数</td> 
			<td>
				<label><input type="radio" value="0" name="info[config][postal][goods_number][radio]" <?php if($postal['goods_number']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" value="1" name="info[config][postal][goods_number][radio]" <?php if($postal['goods_number']['radio'] == 1){?>checked<?php }?>>&nbsp;&nbsp;至少</label>&nbsp;<input type="text" size="6" name="info[config][postal][goods_number][min]" value="<?php echo $postal['goods_number']['min'];?>" />&nbsp;最多&nbsp;<input type="text" size="6" value="<?php echo $postal['goods_number']['max'];?>" name="info[config][postal][goods_number][max]" />
			</td>
		</tr>
	</table>
</fieldset>
    <div class="bk15"></div>
<!-- <fieldset>
	<legend>品牌折扣设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80"><?php echo '是否允许参与品牌折扣活动';?></td> 
			<td>
				<label><input type="radio" value="1" name="info[config][brand][isopen]" <?php if($brand['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][brand][isopen]" <?php if($brand['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>
		
		<tr>
			<td width="80">活动商品发布数量</td> 
			<td>
				<label><input type="radio" name="info[config][brand][apply][radio]" value="0" <?php if($brand['apply']['radio'] == 0){?>checked<?php }?>>不限</label>&nbsp;&nbsp;
				<label><input type="radio" name="info[config][brand][apply][radio]" value="1" <?php if($brand['apply']['radio'] == 1){?>checked<?php }?>>自定义</label>&nbsp;&nbsp;<input type="text" size="6" name="info[config][brand][apply][times]" value="<?php echo $brand['apply']['times'];?>"/>
			</td>
		</tr>
		
		<tr>
			<td width="80">报名活动天数</td> 
			<td>
				<input type="text" name="info[config][brand][activitydays]" size="20" value="<?php echo $brand['activitydays'];?>"> /天
			</td>
		</tr>
	</table>
</fieldset> -->
 <div class="bk15"></div>
 <!-- <fieldset>
	<legend>专场活动设置</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">是否允许参与品牌折扣活动</td> 
			<td>
				<label><input type="radio" value="1" name="info[config][special][isopen]" <?php if($special['isopen'] == 1){?>checked<?php }?>>是</label>&nbsp;&nbsp;
				<label><input type="radio" value="0" name="info[config][special][isopen]" <?php if($special['isopen'] == 0){?>checked<?php }?>>否</label>&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</fieldset> -->
<div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>