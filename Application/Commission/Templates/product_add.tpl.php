<?php
defined('IN_ADMIN') or exit('No permission resources.');$addbg=1;
include $this->admin_tpl('header','admin');?>
<style>
	.addContent{ width: auto;}
	tr.tr_type_box {background-color: #eee}
</style>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = 'http://127.0.0.1/phpcms_v9/uploadfile/';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<form name="myform" id="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" enctype="multipart/form-data">
<div class="addContent">
	<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
        		<h6>商品主图</h6>
        		<div class="upload-pic img-wrap">
        		<input type="hidden" name="info[thumb]" id="thumb" value=""><a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,0','product','1','d3164f4df2ee417a7d84fee7eb51748c');return false;">
        		<img src="<?php echo IMG_PATH; ?>icon/upload-pic.png" id="thumb_preview" width="135" height="113" style="cursor:hand"></a>
        		<input type="button" style="width: 66px;" class="button" onclick="crop_cut_thumb($('#thumb').val());return false;" value="裁切图片">
        		<input type="button" style="width: 66px;" class="button" onclick="$('#thumb_preview').attr('src','<?php echo IMG_PATH ?>icon/upload-pic.png');$('#thumb').val(' ');return false;" value="取消图片">
        		<script type="text/javascript">
        		function crop_cut_thumb(id) {
        			if (id=='') {
        				alert('请先上传缩略图');return false;
        			}
        			window.top.art.dialog({
        				title:'裁切图片', 
        				id:'crop',
        				iframe:'index.php?m=Document&c=Document&a=public_crop&module=document&catid='+catid+'&picurl='+encodeURIComponent(id)+'&input=thumb&preview=thumb_preview',
        				width:'680px',
        				height:'480px'
        			}, 	function(){
        				var d = window.top.art.dialog({id:'crop'}).data.iframe;
        				d.uploadfile();
        				return false;
        			}, function(){
        				window.top.art.dialog({id:'crop'}).close()});
        			};
        		</script>
        		</div>
        		<h6>活动状态</h6>
        		<input type="hidden" name="info[status]" value="-3">
        		<?php echo $this->activity_status[-3]?>
        		<h6>允许参与用户组</h6>
        		<?php $member_group = getcache('member_group', 'member');?>
        		<?php
        		$member_groups = array();
				foreach ($member_group as $groupid => $group) {
					$member_groups[$groupid] = $group['name'];
				}
        		?>
        		<?php echo $form::checkbox('info[allow_groupid]', '', $member_groups);?>


        		
          </div>
        </div>
    </div>
    <a title="展开与关闭" class="r-close" hidefocus="hidefocus" style="outline-style: none; outline-width: medium;" id="RopenClose" href="javascript:;"><span class="hidden">展开</span></a>
    <div class="col-auto">
    	<div class="col-1">
        	<div class="content pad-6">

<table width="100%" cellspacing="0" class="table_form">
	<tbody>	
	<tr>
      <th width="80">商品标题：<font color="red">*</font></th>
      <td><input type="text" name="info[title]" id="title" class="measure-input" style="width:400px;" /></td>
    </tr>

    <tr>
    	<th>下单暗号</th>
      <td><input type="text" name="info[goods_tips]" id="title" class="measure-input" style="width:400px;" /></td>
    </tr>

    <tr>
		<th>投放渠道：</th>
		<td>
			<label class="ib " style=""><input type="checkbox"  value="1" name="info[c_type][]">pc端&nbsp;</label>
			<label class="ib " style=""><input type="checkbox"  value="2" name="info[c_type][]">手机端&nbsp;</label>

		</td>
	</tr>

	<tr>
		<th>商品来源：</th>
		<td><?php echo $form::radio('info[source]', 1, $this->source)?></td>
    </tr>
	
	<tr>
		<th>搜索关键字：<font color="red">*</font></th>
		<td><input type="text" name="info[keyword]" id="keyword"/>&nbsp;请设置搜索关键字，多个关键字用逗号分隔</td>
	</tr>
	<tr>
		<th>搜索排序要求：<font color="red">*</font></th>
		<td>
			<label><input type="radio" value="1" name="info[sort]" checked="">综合</label>
	      	<label><input type="radio" value="2" name="info[sort]">人气</label>
	      	<label><input type="radio" value="3" name="info[sort]">销量</label>
	      	<label><input type="radio" value="4" name="info[sort]">信用</label>
	      	<label><input type="radio" value="5" name="info[sort]">最新</label>
	      	<label><input type="radio" value="6" name="info[sort]">价格</label>

		</td>
	</tr>

	<tr>
		<th>商品位置：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_address]" id="goods_address"/>&nbsp;</td>
	</tr>

	<tr>
		<th>商家旺旺：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_wangwang]" id="goods_wangwang"/>&nbsp;</td>
	</tr>		
	
	
	<script type="text/javascript">
	$("input[name='info[type]']").click(function() {
		$("tr.tr_type_box").hide();
		if($(this).val() != 'general') {
			$("tr#type_" + $(this).val()).show();
		}
	})
	</script>
	<tr>
		<th>下单地址：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_url]" id="goods_url"/></td>
	</tr>
	<tr>
		<th>所属商家：<font color="red">*</font></th>
		<td>
			<input name="info[company_id]" type="hidden" id="company_id"/>
			<input type="text" id="company_id_text" value="" disabled/>
			<input type="button" class="button" onclick="javascript:omnipotent('field_company_id', '<?php echo U('Member/Member/dialog', array('result_id' => 'company_id'));?>', '选择商家', 1);" value="选择商家">
		</td>
	</tr>

	<tr>
      <th>产品分类：</th>
      <td><?php echo $form::select_product_category('catid',0);?></td>
    </tr>

	<tr>
		<th>下单价格：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_price]" id="goods_price" /></td>
		<input type="hidden" value="" id="bonus">
	</tr>



	<tr>
		<th>试客佣金：<font color="red">*</font></th>
		<td><input type="text" id="bonus_price" value="" name="info[bonus_price]" /></td>
	</tr>

	<tr>
		<th>佣金份数：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_number]" id="goods_number" /></td>
	</tr>	
	<script type="text/javascript">
		/*$(document).ready(function(){//每份佣金
			$("#goods_price").on('blur', function(){
				var company_id = $("#company_id").val();
				var goods_price = $("#goods_price").val();
				$.ajax({
					url:'<?php echo U('reward');?>',
					type:'post',
					dataType:'json',
					data:{company_id:company_id,goods_price:goods_price},
					success:function(data){
						$("#goods_commission").val(data);
					}
				});
			});
		});*/
	</script>
	
	
	
	<tr>
		<th>活动时间：<font color="red">*</font></th>
		<td><input type="text" name="info[activity_days]" id="activity_days" class="input-text" style="width: 50px;"/>天，从活动上线开始持续开放的活动时间。</td>
	</tr>


	<tr>
		<th>搜索流程图：</th>
		<td>
			<input name="info[goods_search_albums]" type="hidden" value="1">
			<fieldset class="blue pad-10">
				<legend>图片列表</legend>
				<div class="onShow" id="nameTip">您最多可以同时上传 <font color="red">5</font> 张</div>
				<div id="goods_search_albums" class="picList"></div>
			</fieldset>
			<div class="bk10"></div>
			<script type="text/javascript" src="<?php echo JS_PATH;?>swfupload/swf2ckeditor.js"></script>
			<div class="picBut cu"><a herf="javascript:void(0);" onclick="javascript:flashupload('goods_search_albums', '附件上传','goods_search_albums',change_images,'5,,','product','2','c7ca0ab7bba2e9e20f87b86214ff27c1')"> 选择图片 </a></div>
		</td>
	</tr>
	
   <!--  <tr>
		<th>显示提示：</th>
		<td>
            <input type="checkbox" name="info[goods_tips][]" value="1" />货比三家
            <input type="checkbox" name="info[goods_tips][]" value="2" />停留截图
            <input type="checkbox" name="info[goods_tips][]" value="3" />聊天截图
        </td>
	</tr> -->
    
	
	
	<!-- <tr>
		<th>下单流程与要求：</th>
		<td>
            <textarea name="info[goods_content]" id="info[goods_content]"></textarea>
			<?php echo $form::editor("info[goods_content]", "full");?>
		</td>
	</tr>		 -->
    
    </tbody>
</table>
                </div>
        	</div>
        </div>
        
    </div>
</div>

<div class="fixed-bottom">
	<div class="fixed-but text-c">
    <div class="button"><input value="<?php echo L('save_close');?>" type="submit" name="dosubmit" class="cu" style="width:145px;" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('c_close');?>" type="button" name="close" onclick="refersh_window();close_window();" class="cu" style="width:70px;"></div>
      </div>
</div>
</form>

</body>
</html>
<script type="text/javascript"> 
<!--
$.formValidator.initConfig({
	formid:"myform",
	autotip:true,
	onerror:function(msg,obj){
		$(obj).focus();
	}
});

$("#title").formValidator({
	onshow:"请输入10到30个汉字",
	onfocus:"请输入10到30个汉字"
}).inputValidator({
	min:20,
	max:60,
	onerror:"标题为10到30个汉字"
});

$("#goods_url").formValidator({
	empty:false,
	onempty:"宝贝链接不能为空",
	onshow:"请输入宝贝链接地址",
	onfocus:"请输入宝贝链接地址"
}).regexValidator({
	regexp:'url',
	datatype:'enum',
	onerror:'宝贝链接地址合适不合法'	
})

/* 发布份数 */
$("#goods_number").formValidator({
	empty:false,
	onempty:'发布份数不能为空',
	onshow:'请输入发布份数',
	onfocus:'请输入发布份数'
}).regexValidator({
	regexp:'intege1',
	datatype:'enum',
	onerror:'发布份数只能为正数'
});

$("#goods_price").formValidator({
	empty:false,
	onempty:'下单价不能为空',
	onshow:'请输入下单价',
	onfocus:'请输入下单价'
}).regexValidator({
	regexp:'num',
	datatype:'enum',
	onerror:'下单价输入格式错误'
}).ajaxValidator({
    url : "<?php echo U('reward'); ?>",
    datatype:'JSON',
    data:$("#company_id").val(),
    async:false,
    success:function(ret) {
        if(ret) {
            $("#bonus_price").val(ret);
            $("#bonus").val(ret);

            return true;
        } else {
            return false;
        }
    },
    onerror:'请输入下单价'
});


var goods_price = $("#goods_price").val();
$("#bonus_price").formValidator({
	empty:true,
	onshow:'请输入赠送试客佣金',
	onfocus:'请输入赠送试客佣金'
}).functionValidator({
	fun:function(val,elem){
		var goods_bonus =  $("#bonus").val();
		if(parseInt(val) < parseInt(goods_bonus)){
			return '赠送用户红包的最低'+goods_bonus+'美元起';
		}else{
			return true;
		}
	}
}).regexValidator({
	regexp:'num',
	datatype:'enum',
	onerror:'用户红包只能为正数'
});

/* 商品位置 */
$("#goods_address").formValidator({
	empty:false,
	onempty:'商品位置不能为空',
	onshow:'请输入商品位置',
	onfocus:"请输入商品位置"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:'商品位置不能为空'
});


/* 活动天数 */
$("#activity_days").formValidator({
	empty:false,
	onempty:'活动天数不能为空',
	onshow:'请输入活动天数',
	onfocus:'请输入活动天数'		
}).regexValidator({
	regexp:'intege1',
	datatype:'enum',
	onerror:'活动天数只能为正整数'	
});
/*产品分类*/
$("#linkage_input_catid").formValidator({
	empty:false,
	onshow: "请选择产品分类",
    onfocus: "请选择产品分类"
}).inputValidator({
    min: 1,
    onerror: "产品分类不能不选择"
}).regexValidator({
	regexp:'intege1',
	datatype:'enum',
	onerror:'请选择产品分类'
});

/*旺旺*/
$("#goods_wangwang").formValidator({
	empty:false,
	onempty:'掌柜旺旺不能为空',
	onshow:'请输入掌柜旺旺',
	onfocus:"请输入掌柜旺旺"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:'掌柜旺旺不能为空'
});

function refersh_window() {
	setcookie('refersh_time', 1);
}
//-->
</script>