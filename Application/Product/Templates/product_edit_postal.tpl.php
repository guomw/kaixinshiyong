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
<input name="id" value="<?php echo $rs['id'];?>" type="hidden">
<input name="info[mod]" value="<?php echo $rs['mod'];?>" type="hidden">
<div class="addContent">
	<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">
        		<h6>商品主图</h6>
        		<div class="upload-pic img-wrap">
        		<input type="hidden" name="info[thumb]" id="thumb" value="<?php echo $rs['thumb'] ?>"><a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,0','product','1','d3164f4df2ee417a7d84fee7eb51748c');return false;">
        		<img src="<?php if ($rs['thumb']): ?><?php echo $rs['thumb'] ?><?php else: ?><?php echo IMG_PATH; ?>icon/upload-pic.png<?php endif ?>" id="thumb_preview" width="135" height="113" style="cursor:hand"></a>
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
        		<input type="hidden" name="info[status]" value="<?php echo $rs['status'];?>">
        		<?php echo $this->activity_status[$rs['status']];?>
        		
        		<h6>是否推荐</h6>
        		<label><input type="radio" name="info[isrecommend]" value="1" <?php if($rs['isrecommend'] == 1){?>checked<?php }?>/>是</label>&nbsp;&nbsp;
        		<label><input type="radio" name="info[isrecommend]" value="0" <?php if($rs['isrecommend'] == 0){?>checked<?php }?>/>否</label>
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
      <td><input type="text" name="info[title]" id="title" class="measure-input" style="width:400px;" value="<?php echo $rs['title'] ?>" /></td>
    </tr>
    
	<tr>
      <th>商品关键字：</th>
      <td><input type="text" name="info[keyword]" class="input-text" style="width:300px" value="<?php echo $rs['keyword'] ?>" />&nbsp;请输入商品关键字，多个用英文逗号（,）分割</td>
    </tr>    

	<tr>
      <th>产品分类：</th>
      <td><?php echo $form::select_product_category('catid', $rs['catid']);?></td>
    </tr> 

	<tr>
		<th>商品来源：</th>
		<td><?php echo $form::radio('info[source]', $rs['source'], $this->source)?></td>
    </tr>

	<tr id="field_taobaoke">
		<th>亚马逊佣金：</th>
		<td>
			<!-- <label><input type="radio" name="info[taobaoke]" value="1" <?php if ($rs['taobaoke'] == 1): ?>checked<?php endif ?>>&nbsp;是</label>
			<label><input type="radio" name="info[taobaoke]" value="0" <?php if ($rs['taobaoke'] == 0): ?>checked<?php endif ?>>&nbsp;否</label> -->
			<input type="text" name="info[taobao_charge]" class="input-text" style="width:50px" id="taobao" value="<?php echo $rs['taobao_charge'];?>"/>
		</td>
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
		<th>商品图集：</th>
		<td>
			<input name="info[goods_albums]" type="hidden" value="1">
			<fieldset class="blue pad-10">
				<legend>图片列表</legend>
				<div class="onShow" id="nameTip">您最多可以同时上传 <font color="red">5</font> 张</div>
				<div id="goods_albums" class="picList">
					<?php foreach ($rs['goods_albums'] as $pic): ?>
					<?php $k = random(6); ?>
					<li id="image<?php echo $k; ?>">
					<input type="text" name="goods_albums_url[]" value="<?php echo $pic['url'] ?>" style="width:310px;" ondblclick="image_priview(this.value);" class="input-text"> <input type="text" name="goods_albums_alt[]" value="<?php echo $pic['alt'] ?>" style="width:160px;" class="input-text" onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;"> <a href="javascript:remove_div('image<?php echo $k;?>')">移除</a> </li>
					<?php endforeach ?>
				</div>
			</fieldset>
			<div class="bk10"></div>
			<script type="text/javascript" src="<?php echo JS_PATH;?>swfupload/swf2ckeditor.js"></script>
			<div class="picBut cu"><a herf="javascript:void(0);" onclick="javascript:flashupload('goods_albums', '附件上传','goods_albums',change_images,'5,,','product','1','c7ca0ab7bba2e9e20f87b86214ff27c1')"> 选择图片 </a></div>
		</td>
	</tr>	

	<tr>
		<th>发布份数：</th>
		<td><input type="text" name="info[goods_number]" id="goods_number" value="<?php echo $rs['goods_number'] ?>" disabled /></td>
	</tr>

	<tr>
		<th>下单价格：</th>
		<td><input type="text" name="info[goods_price]" id="goods_price" value="<?php echo $rs['goods_price'] ?>" disabled /></td>
	</tr>

	<tr>
		<th>下单地址：</th>
		<td><input type="text" name="info[goods_url]" id="goods_url" value="<?php echo $rs['goods_url']?>"/></td>
	</tr>		
	
	<tr>
      <th>活动时间：</th>
      <td><input type="text" name="info[activity_days]" id="activity_days" value="<?php echo $rs['activity_days'];?>"/></td>
    </tr>
    
	<tr>
		<th valign="top">温馨提醒：</th>
		<td>
			<!-- <label><input type="checkbox" name="info[goods_tips][order_tip][]" value="1" <?php if (in_array(1, $rs['goods_tips']['order_tip'])): ?>checked<?php endif ?>/>&nbsp;请不要使用信用卡下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="2" <?php if (in_array(2, $rs['goods_tips']['order_tip'])): ?>checked<?php endif ?>/>&nbsp;请不要催促商家返款</label>
			<div class="bk3"></div> -->
			<label>默认快递：<input type="text" name="info[goods_tips][goods_order][kuaidi]" style="width:250px;" value="<?php echo $rs['goods_tips']['goods_order']['kuaidi'] ?>"></label>
			<div class="bk3"></div>
			<label>拍下须知：<input type="text" name="info[goods_tips][goods_order][remark]" style="width:250px;" value="<?php echo $rs['goods_tips']['goods_order']['remark'] ?>"></label>
			<div class="bk3"></div>
			原价为：<input type="text" name="info[goods_tips][goods_order][price][cost]" style="width:50px;" value="<?php echo $rs['goods_tips']['goods_order']['price']['cost'] ?>">美元，拍下后价格为：<input type="text" name="info[goods_tips][goods_order][price][after]" style="width:50px;" value="<?php echo $rs['goods_tips']['goods_order']['price']['after'] ?>">美元
		</td>
	</tr>	
	
	<tr>
		<th>商品介绍：</th>
		<td>
			<textarea name="info[goods_content]" id="info[goods_content]"><?php echo $rs['goods_content'] ?></textarea>
			<?php echo $form::editor("info[goods_content]", "full");?>
		</td>
	</tr>		
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
   <!--  <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X" onclick="refersh_window()"></div> -->
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
	empty:false,
	onshow:"请输入商品标题",
	onfocus:"请输入商品标题"
}).inputValidator({
	min:1,
	onerror:"请输入商品标题"
}).defaultPassed();
/* 下单地址 */
$("#goods_url").formValidator({
	empty:false,
	onempty:"下单地址不能为空",
	onshow:"请输入用户最终下单页的地址",
	onfocus:"请输入用户最终下单页的地址"
}).regexValidator({
	regexp:'url',
	datatype:'enum',
	onerror:'下单地址合适不合法'	
}).defaultPassed();

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
}).defaultPassed();

/* 下单价 */
$("#goods_price").formValidator({
	empty:false,
	onempty:'下单价不能为空',
	onshow:'请输入用户购买时的下单价',
	onfocus:"请输入用户购买时的下单价"
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'下单价只能为正数'
}).defaultPassed();

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
}).defaultPassed();

$("#goods_content").formValidator({
	onshow:"",
	onfocus:"内容不能为空"
}).functionValidator({
	fun:function(val,elem){
		var oEditor = CKEDITOR.instances.content;
		var data = oEditor.getData();
		if($('#islink').attr('checked')){
			return true;
		} else if(($('#islink').attr('checked')==false) && (data=='')){
			return "内容不能为空";
		} else if (data=='' || $.trim(data)=='') {
			return "内容不能为空";
		}
		return true;
	}
}).defaultPassed();

$("#taobao").formValidator({
	empty:false,
	onempty:'亚马逊佣金不能为空',
	onshow:'亚马逊佣金不能为空',
	onfocus:'请输入亚马逊佣金'
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'亚马逊佣金输入错误'	
}).defaultPassed();

function refersh_window() {
	setcookie('refersh_time', 1);
}
//-->
</script>