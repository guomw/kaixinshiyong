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
<input name="info[mod]" value="<?php echo $mod;?>" type="hidden"/>
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
      <th>商品关键字：</th>
      <td><input type="text" name="info[keyword]" class="input-text" style="width:300px" />&nbsp;请输入商品关键字，多个用英文逗号（,）分割</td>
    </tr>    

	<tr>
      <th>产品分类：</th>
      <td><?php echo $form::select_product_category("catid", 0);?></td>
    </tr>

    <tr>
        <th>获取信息：</th>
        <td><input type="text" name="info[go_link]" class="input-text" style="width:300px" />(请输入商品链接 支持亚马逊 天猫 京东) <input type="button" value="一键获取" class="button keyget" name="keyget"></td>
    </tr>


	<tr>
		<th>商品来源：</th>
		<td><?php echo $form::radio('info[source]', 1, $this->source)?></td>
    </tr>

	<tr id="field_taobaoke">
		<th>亚马逊推广：</th>
		<td>
			<label><input type="radio" name="info[taobaoke]" value="1"/>&nbsp;是</label>
			<label><input type="radio" name="info[taobaoke]" value="0"/>&nbsp;否</label>
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
		<th>下单方式：</th>
		<td>
			<label><input type="radio" name="info[type]" value="general" checked="checked" />&nbsp;普通下单</label>
			<label><input type="radio" name="info[type]" value="search" />&nbsp;搜索下单</label>
			<label><input type="radio" name="info[type]" value="ask" />&nbsp;答案下单</label>
			<label><input type="radio" name="info[type]" value="qrcode" />&nbsp;二维码下单</label>
		</td>
	</tr>
	<tr id="type_search" class="tr_type_box" style="display: none;">
		<th>搜索关键字：</th>
		<td><input type="text" name="info[goods_rule][keyword]"/>&nbsp;请设置搜索关键字，多个关键字用逗号分隔</td>
	</tr>
	<tr id="type_search" class="tr_type_box" style="display: none;">
		<th>搜索提示：</th>
		<td><input type="text" name="info[goods_rule][keyword2]"/>&nbsp;例如：用Ctrl + F 查找店铺关键字，找到活动商品。</td>
	</tr>	
	<tr id="type_ask" class="tr_type_box" style="display: none;">
		<th>答案下单：</th>
		<td>
			<fieldset class="blue pad-10">
				<legend>问答列表</legend>
				<div id="type_ask_list" class="picList">
					<li id="image9611">
						问题：<input type="text" name="info[goods_rule][ask][question]" class="input-text">
						答案：<input type="text" name="info[goods_rule][ask][answer]" style="width:160px;" class="input-text" >
						提示：<input type="text" name="info[goods_rule][ask][tips]" style="width:160px;" class="input-text" >
					</li>
				</div>
			</fieldset>
		</td>
	</tr>
	<tr id="type_qrcode" class="tr_type_box" style="display: none;">
		<th>二维码下单：</th>
		<td>
			<?php echo $form::images('info[goods_rule][qrcode]', 'goods_rule_qrcode', '', 'product', 0, 20, '', '', '', '', '', '二维码上传');?>
			&nbsp;请上传商品页的二维码图片
		</td>
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
		<th>下单地址：</th>
		<td><input type="text" name="info[goods_url]"/></td>
	</tr>
	<tr>
		<th>所属商家：</th>
		<td>
			<input name="info[company_id]" type="hidden" id="company_id"/>
			<input type="text" id="company_id_text" value="" disabled/>
			<input type="button" class="button" onclick="javascript:omnipotent('field_company_id', '<?php echo U('Member/Member/dialog', array('result_id' => 'company_id'));?>', '选择商家', 1);" value="选择商家">
		</td>
	</tr>

	<tr>
		<th>商品图集：</th>
		<td>
			<input name="info[goods_albums]" type="hidden" value="1">
			<fieldset class="blue pad-10">
				<legend>图片列表</legend>
				<div class="onShow" id="nameTip">您最多可以同时上传 <font color="red">5</font> 张</div>
				<div id="goods_albums" class="picList"></div>
			</fieldset>
			<div class="bk10"></div>
			<script type="text/javascript" src="<?php echo JS_PATH;?>swfupload/swf2ckeditor.js"></script>
			<div class="picBut cu"><a herf="javascript:void(0);" onclick="javascript:flashupload('goods_albums', '附件上传','goods_albums',change_images,'5,,','product','1','c7ca0ab7bba2e9e20f87b86214ff27c1')"> 选择图片 </a></div>
		</td>
	</tr>	

	<tr>
		<th>发布份数：</th>
		<td><input type="text" name="info[goods_number]" id="goods_number" /></td>
	</tr>

	<tr>
		<th>下单价格：</th>
		<td><input type="text" name="info[goods_price]" id="goods_price" /></td>
	</tr>		

	<tr>
		<th>价格折扣：</th>
		<td><input type="text" name="info[goods_discount]" id="goods_discount" /></td>
	</tr>
	
	<tr>
		<th>划算价：</th>
		<td><input type="text" id="remal_price" /></td>
	</tr>
	<script type="text/javascript">
	$("#goods_discount").on('keypress keyup blur', function(){
		var remal_price = ($('#goods_price').val() * $(this).val() / 10).toFixed(2);
		$("#remal_price").attr('value', remal_price);
	});

	$("#remal_price").on('keypress keyup blur', function(){
		var goods_discount = (($(this).val() / $('#goods_price').val())  * 10).toFixed(1);
		$("#goods_discount").attr('value', goods_discount); 
	});
	</script>
	
	<tr>
		<th>活动时间：</th>
		<td><input type="text" name="info[activity_days]" id="activity_days" class="input-text" style="width: 50px;"/>天，从活动上线开始持续开放的活动时间。</td>
	</tr>
	
	<tr>
		<th valign="top">温馨提醒：</th>
		<td>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="1" />&nbsp;请不要使用淘金币下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="2" />&nbsp;请不要催促商家返款</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="3" />&nbsp;请不要使用手机下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="4" />&nbsp;请不要使用店铺优惠券</label>
			<div class="bk3"></div>
			<label>默认快递：<input type="text" name="info[goods_tips][goods_order][kuaidi]"/></label><div class="bk3"></div>
			<label>套餐包含：<input type="text" name="info[goods_tips][goods_order][package]"/></label><div class="bk3"></div>
			<label>拍下须知：<input type="text" name="info[goods_tips][goods_order][remark]"/></label>
		</td>
	</tr>	
	
	<tr>
		<th>商品介绍：</th>
		<td>
            <textarea name="info[goods_content]" id="info[goods_content]"></textarea>
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
});

/* 下单价 */
$("#goods_price").formValidator({
	empty:false,
	onempty:'下单价不能为空',
	onshow:'请输入用户购买时的下单价',
	onfocus:"请输入用户购买时的下单价"
}).regexValidator({
	regexp:'decmal3',
	datatype:'enum',
	onerror:'下单价只能为正数'
});
/* 折扣 */
$("#goods_discount").formValidator({
	empty:false,
	onempty:'商品折扣不能为空',
	onshow:'请输入商品折扣',
	onfocus:'请输入商品折扣'
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'商品折扣只能为正数'
});
/* 返还金 */
$("#remal_price").formValidator({
	empty:false,
	onempty:'划算金不能为空',
	onshow:'请输入划算金',
	onfocus:'请输入划算金'
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'划算金只能为正数'
});

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
});

function refersh_window() {
	setcookie('refersh_time', 1);
}

$(".keyget").click(function(){
    var go_link = $("input:text[name='info[go_link]']").val();
    if(!go_link){
        art.dialog({
            lock: true,
            fixed: true,
            title: '错误提示',
            content: '请输入地址',
            ok: true
        });
        return false;
    }

    $.post('index.php?m=Product&c=Api&a=go_link', {
        go_link : go_link
    }, function(ret) {
        if(ret.status == 1) {
            $("input:text[name='info[title]']").val(ret.title);
            $("input:text[name='info[keyword]']").val(ret.keyword);
            $("input:text[name='info[goods_price]']").val(ret.goods_price);
            $("input:text[name='info[goods_url]']").val(ret.url);

            $("input[name='info[thumb]']").val(ret.img);
            $("#thumb_preview").attr("src",ret.img);

//            $("textarea[name='info[goods_content]']").html(ret.description);
        }else{
            art.dialog({
                lock: true,
                fixed: true,
                icon: 'face-sad',
                title: '错误提示',
                content: ret.info,
                ok: true
            });
        }
        return false;
    }, 'JSON');

})


//-->
</script>