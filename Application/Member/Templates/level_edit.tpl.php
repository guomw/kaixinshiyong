<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php $show_header = TRUE; ?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#name").formValidator({onshow:"<?php echo L('input')."会员等级"?>",onfocus:"<?php echo "会员等级".L('between_2_to_8')?>"}).inputValidator({min:2,max:15,onerror:"<?php echo "会员等级".L('between_2_to_8')?>"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"<?php echo "会员等级".L('format_incorrect')?>"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"m=Member&c=MemberType&a=public_checkename_ajax&id=<?php echo $id ?>",
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
	}).defaultPassed();
<!--	$("#group_point").formValidator({tipid:"pointtip",onshow:"--><?php //echo L('input').L('point')?><!--",onfocus:"--><?php //echo L('point').L('between_1_to_8_num')?><!--"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"--><?php //echo L('point').L('between_1_to_8_num')?><!--"}).defaultPassed();-->
<!--	$("#group_starnum").formValidator({tipid:"starnumtip",onshow:"--><?php //echo L('input')."经验值"?><!--",onfocus:"--><?php //echo "经验值".L('between_1_to_8_num')?><!--"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"--><?php //echo "经验值".L('between_1_to_8_num')?><!--"}).defaultPassed();-->
<!--	$("#price_m").formValidator({-->
<!--				empty:false,-->
<!--				onempty:'该会员等级价格不能为空',-->
<!--				onshow:'请输入该会员等级价格' ,-->
<!--				onfocus:"请输入该会员等级价格" -->
<!--			}).regexValidator({-->
<!--				regexp:'decmal4',-->
<!--				datatype:'enum',-->
<!--				onerror:'请输入该会员等级价格'-->
<!--			}).defaultPassed();-->
<!---->
<!--	$("#price_p").formValidator({-->
<!--				empty:false,-->
<!--				onempty:'该会员等级价格不能为空',-->
<!--				onshow:'请输入该会员等级价格' ,-->
<!--				onfocus:"请输入该会员等级价格" -->
<!--			}).regexValidator({-->
<!--				regexp:'decmal4',-->
<!--				datatype:'enum',-->
<!--				onerror:'请输入该会员等级价格'-->
<!--			}).defaultPassed();-->
<!---->
<!--	$("#price_y").formValidator({-->
<!--				empty:false,-->
<!--				onempty:'该会员等级价格不能为空',-->
<!--				onshow:'请输入该会员等级价格' ,-->
<!--				onfocus:"请输入该会员等级价格" -->
<!--			}).regexValidator({-->
<!--				regexp:'decmal4',-->
<!--				datatype:'enum',-->
<!--				onerror:'请输入该会员等级价格'-->
<!--			}).defaultPassed();-->

    $("#day_count").formValidator({
        empty:false,
        onempty:'该会员等级每日免审次数不能为空',
        onshow:'请输入该会员等级每日免审次数' ,
        onfocus:"请输入该会员等级每日免审次数"
    }).inputValidator({
        min:1,
        max:5,
        onerror:"请输入适当的次数，不能大于5位数"
    }).regexValidator({
        regexp:'num1',
        datatype:'enum',
        onerror:'请输入数字'
    }).defaultPassed();

    $("#month_count").formValidator({
        empty:false,
        onempty:'该会员等级每日免审次数不能为空',
        onshow:'请输入该会员等级每日免审次数' ,
        onfocus:"请输入该会员等级每日免审次数"
    }).inputValidator({
        min:1,
        max:5,
        onerror:"请输入适当的次数，不能大于5位数"
    }).regexValidator({
        regexp:'num1',
        datatype:'enum',
        onerror:'请输入数字'
    }).defaultPassed();

});
//-->
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="<?php echo U('edit') ?>" method="post" id="myform">
<input type="hidden" name="info[groupid]" value="<?php echo $info['groupid']?>">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="120"><?php echo "会员等级名称"?></td> 
			<td><input type="text" name="info[name]"  class="input-text" id="name" value="<?php echo $info['name']?>"></td>
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
            <td width="80"><?php echo L('member_group_icon')?></td>
            <td><?php echo $form::images('info[icon]', 'image', $info['icon'], 'document');?></td>
        </tr>

        <tr>
            <td width="120"><?php echo "VIP每日免审次数"?></td>
            <td><input type="text" name="info[day_count]"  class="input-text" id="day_count" value="<?php echo $info['day_count']?>" style="width: 40px;">次</td>
        </tr>

        <tr>
            <td width="120"><?php echo "VIP每月免审次数"?></td>
            <td><input type="text" name="info[month_count]"  class="input-text" id="month_count" value="<?php echo $info['month_count']?>" style="width: 40px;">次</td>
        </tr>

        <tr>
            <td width="80"><?php echo L('member_group_description')?></td>
            <td>
                <textarea name="info[description]"> <?php echo $info['description']?> </textarea>
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