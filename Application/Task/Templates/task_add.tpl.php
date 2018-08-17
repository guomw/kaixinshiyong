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
<script type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
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
        		<input type="hidden" name="info[status]" value="-3" />
        		<?php echo $this->task_status[-3]?>
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
      <th width="80">任务问题：<font color="red">*</font></th>
      <td><input type="text" name="info[title]" id="title" class="measure-input" style="width:400px;" /></td>
    </tr>
    
	<tr>
      <th>问题答案：<font color="red">*</font></th>
      <td><input type="text" name="info[answer]" class="input-text" id="answer" style="width:300px" /></td>
    </tr>    
    
	<tr>
		<th>商品来源：<font color="red">*</font></th>
		<td><?php echo $form::radio('info[source]', 1, $this->source)?></td>
    </tr>

	<script type="text/javascript">
		$("input[name='info[source]']").click(function() {
			if($(this).val() <= 2) {
				$("tr#field_taobaoke").show();
			} else {
				$("tr#field_taobaoke").hide();
			}
		})
	</script>
	
	<tr>
      <th>搜索关键字：<font color="red">*</font></th>
      <td><input type="text" name="info[keyword]" class="input-text" id="keyword" style="width:300px" />&nbsp;请输入商品关键字，多个用英文逗号（,）分割</td>
    </tr>
    
    <tr>
      <th>排序要求：<font color="red">*</font></th>
      <td>
      	<label><input type="radio" value="1" name="info[sort]" checked/>综合</label>
      	<label><input type="radio" value="2" name="info[sort]"/>人气</label>
      	<label><input type="radio" value="3" name="info[sort]"/>销量</label>
      	<label><input type="radio" value="4" name="info[sort]"/>信用</label>
      	<label><input type="radio" value="5" name="info[sort]"/>最新</label>
      	<label><input type="radio" value="6" name="info[sort]"/>价格</label>
      </td>
    </tr>
    
    <tr>
		<th>商品位置：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_address]" id="goods_address" /></td>
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
		<th>掌柜旺旺：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_wangwang]" id="goods_wangwang" /></td>
	</tr>
	
	<tr>
		<th>任务份数：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_number]" id="goods_number" />份</td>
	</tr>
	
	<tr>
		<th>每份佣金：<font color="red">*</font></th>
		<td><input type="text" name="info[goods_price]" id="goods_price" value=""/>美元</td>
	</tr>	
	
	<tr>
		<th>流程图集：<font color="red">*</font></th>
		<td>
			<input name="info[goods_albums]" type="hidden" value="1">
			<fieldset class="blue pad-10">
				<legend>图片列表</legend>
				<div class="onShow" id="nameTip">您最多可以同时上传 <font color="red">20</font> 张</div>
				<div id="goods_albums" class="picList"></div>
			</fieldset>
			<div class="bk10"></div>
			<script type="text/javascript" src="<?php echo JS_PATH;?>swfupload/swf2ckeditor.js"></script>
			<div class="picBut cu"><a href="javascript:void(0);" onclick="javascript:flashupload('goods_albums', '附件上传','goods_albums',change_images,'20,,','product','1','c7ca0ab7bba2e9e20f87b86214ff27c1')"> 选择图片 </a></div>
		</td>
	</tr>	
    </tbody>
</table>
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

$("#answer").formValidator({
	onshow:"请输入答案",
	onfocus:"请输入答案"
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:'答案输入错误'
});

$("#goods_price").formValidator({
	onshow:"请输入佣金",
	onfocus:"请输入佣金"
}).regexValidator({
	regexp:'money',
	datatype:'enum',
	onerror:'佣金输入错误'
});

$("#company_id").formValidator({
	onshow:"请选择商家",
}).regexValidator({
	regexp:'num',
	datatype:'enum',
	onerror:'商家选择错误'
});

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