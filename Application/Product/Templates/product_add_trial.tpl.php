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
      <td><?php echo $form::select_product_category('catid', 0);?></td>
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
		<th>试用类型：</th>
		<td>
			<label><input type="radio" name="info[trial_type]" value="1" checked/>&nbsp;拍A发A </label> 
			<label><input type="radio" name="info[trial_type]" value="2" />&nbsp;拍A发B</label>
			<label><input type="radio" name="info[trial_type]" value="3" />&nbsp;红包试用</label>
		</td>
	</tr>
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
	<tr id="type_search" class="tr_type_box" <?php if ($rs['type'] != 'search'): ?>style="display: none;"<?php endif ?>>
		<th>排序方式：</th>
		<td>
			<input type="radio" name="info[goods_rule][sort]" value="综合" checked />&nbsp;综合</label>
			<input type="radio" name="info[goods_rule][sort]" value="人气" />&nbsp;人气</label>
			<input type="radio" name="info[goods_rule][sort]" value="销量" />&nbsp;销量</label>
	        <input type="radio" name="info[goods_rule][sort]" value="信用" />&nbsp;信用</label>
	        <input type="radio" name="info[goods_rule][sort]" value="最新" />&nbsp;最新</label>
	        <input type="radio" name="info[goods_rule][sort]" value="价格" />&nbsp;价格</label>
        </td>
	</tr>

	<tr id="type_search" class="tr_type_box" <?php if ($rs['type'] != 'search'): ?>style="display: none;"<?php endif ?>>
		<th>商品位置：</th>
		<td><input type="text" name="info[goods_rule][address]" value=""/>&nbsp;商品所在位置</td>
	</tr>
	 <tr id="type_search" class="tr_type_box" <?php if ($rs['type'] != 'search'): ?>style="display: none;"<?php endif ?>>
		<th>是否需要收藏：</th>
		<td>
			<label><input type="radio" name="info[goods_rule][collect]" value="1" />&nbsp;是</label>
			<label><input type="radio" name="info[goods_rule][collect]" value="0" checked />&nbsp;否</label>
	    </td>
	</tr>

     <tr class="tr_type_box" >
    	<th>是否包邮：</th>
    	<td>
    		<label><input type="radio" name="info[goods_postage]" value="0"  checked/>&nbsp;是</label>
    		<label><input type="radio" name="info[goods_postage]" value="1"  />&nbsp;否</label>
        </td>
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
			 <th>仿ip重复:</th>
			  <td>
				<select name="info[goods_tips][goods_order][is_ip]">
				  <option value="1" >是</option>
				  <option value="0" selected >否</option>
				</select>
			   <td>	
	    </tr>

	    <tr>
			<th>防止重复申请:</th>
	            <td>
		    		<select name="info[goods_tips][goods_order][is_join]">
		    		  <option value="1" >是</option>
		    		  <option value="0" selected>否</option>
		    		</select>	
		        <td>			
	    </tr>


	<tr>
		<th>下单地址：</th>
		<td><input type="text" name="info[goods_url]" id="goods_url" style="width:300px;" /></td>
	</tr>
	<?php if (C_READ('seller_pat_isopen') == 1){?>
	<tr id="trial_type2" style="background-color:#ccc" >
		<th>最终试用商品：</th>
		<td>
			<label><input type="radio" name="info[goods_tryproduct]" value="0" checked>&nbsp;活动商品</label>
			<label><input type="radio" name="info[goods_tryproduct]" value="1" id="tryproduct" >&nbsp;其他商品</label>
			<label for="field_goods_tryproduct" style="display:none;">
			<input type="text" name="info[field_goods_tryproduct]" id="field_goods_tryproduct" style="width:80px;" /></label>
		</td>
	</tr>
	<?php }?>
	<?php if(C_READ('seller_bonus_isopen') == 1){?>
	<tr id="trial_type3" style="background-color:#ccc">
		<th>每单赠送用户红包：</th>
		<td><input type="text" name="info[goods_bonus]" id="goods_bonus"/>/美元&nbsp;</td>
	</tr>
	<?php }?>	
		<script type="text/javascript">
		/*默认隐藏拍A发B 红包试用*/
		$("#trial_type2").hide();
		$("#trial_type3").hide();
			$("input[name='info[goods_tryproduct]']").click(function(){
				if($(this).val() == 1) {
					$("label[for=field_goods_tryproduct]").show();
				} else {
					$("label[for=field_goods_tryproduct]").hide();
				}
			})

			$("input[name='info[trial_type]']").click(function(){
				if($(this).val() == 1) {
					$("#trial_type2").hide();
					$("#trial_type3").hide();
				}else if($(this).val() == 2) {
					$("#trial_type2").show();
					$("#trial_type3").hide();
				}else if($(this).val() ==3){
					$("#trial_type3").show();
	                $("#trial_type2").hide();
				}
			})

			$("#field_goods_tryproduct").blur(function(){
			  $("#tryproduct").val($(this).val());
			});

		</script>
	<tr>
		<th>所属商家：</th>
		<td>
			<input name="info[company_id]" type="hidden" id="company_id"/>
			<input name="info[goods_ww]" type="hidden" value="<?php echo $rs['goods_ww'] ?>" id="goods_ww"/>

			<input type="text" id="company_id_text" value="" disabled/>
			<input type="button" class="button" onclick="javascript:omnipotent('field_company_id', '<?php echo U('Member/Member/dialog', array('result_id' => 'company_id','goods_ww'=>'goods_ww'));?>', '选择商家', 1);" value="选择商家">
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

	<!-- <tr>
		<th>预存邮费：</th>
		<td>
		<label><input type="radio" name="info[goods_postage]" value="0" checked>&nbsp;不预存</label>
		<label><input type="radio" name="info[goods_postage]" value="1">&nbsp;预存</label>
		<label for="field_goods_postage" style="display:none;"><input type="text" name="info[field_goods_postage]" id="field_goods_postage" style="width:80px;" />美元/份</label>
	</tr> -->

	<script type="text/javascript">
	$("input[name='info[goods_postage]']").click(function(){
		if($(this).val() == 1) {
			$("label[for=field_goods_postage]").show();
		} else {
			$("label[for=field_goods_postage]").hide();
		}
	})
	</script>

	<tr>
		<th>活动时间：</th>
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
	
	<tr>
		<th valign="top">温馨提醒：</th>
		<td>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="1" />&nbsp;请不要使用信用卡下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="2" />&nbsp;请不要催促商家返款</label>
			<div class="bk3"></div>
			<label>默认快递：<input type="text" name="info[goods_tips][goods_order][kuaidi]" style="width:250px;"/></label>
			<div class="bk3"></div>
			<label>拍下须知：<input type="text" name="info[goods_tips][goods_order][remark]" style="width:250px;"/></label>
			<div class="bk3"></div>
			<label>原价为：<input type="text" name="info[goods_tips][goods_order][price][cost]" style="width:50px;">美元，拍下后价格为：<input type="text" name="info[goods_tips][goods_order][price][after]" style="width:50px;">美元</label>
			<div class="bk3"></div>
			请用V1-V3价格<input type="text" name="info[goods_tips][goods_order][price][lv]" style="width:50px;">美元下单
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
});
/* 预存邮费 */
$("#field_goods_postage").formValidator({
	empty:true,
	onshow:'请输入商品折扣',
	onfocus:'请输入商品折扣'
}).functionValidator({
	fun:function(val,elem){
		var re = new   RegExp("^[1-9]|[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$");
		if(re.test(val) === false && $("input[name='info[goods_postage]']:checked").val() == 1) {
			return '邮费必须为正数';
		}
	}
});
/*最终试用商品*/
$("#field_goods_tryproduct").formValidator({
	empty:true,
	onshow:'请输入最终试用商品',
	onfocus:'请输入最终试用商品'
}).functionValidator({
	fun:function(val,elem){
		if(typeof (val) === 'null' && $("input[name='info[goods_tryproduct]']:checked").val() == 1) {
			return '请输入最终试用商品';
		}
	}
}).regexValidator({
	regexp:'notempty',
	datatype:'enum',
	onerror:'请输入最终试用商品'
});


/*每单赠送用户红包*/
$("#goods_bonus").formValidator({
	empty:true,
	onempty:'不参与红包任务？',
	onshow:'请输入赠送用户的红包',
	onfocus:'请输入赠送用户的红包'
}).regexValidator({
	regexp:'num',
	datatype:'enum',
	onerror:'用户红包只能为正数'
});
/* 返还金 */
$("#remal_price").formValidator({
	empty:false,
	onempty:'返还金不能为空',
	onshow:'请输入返还金',
	onfocus:'请输入返还金'
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'返还金只能为正数'
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
})

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
            if(!ret.title || ret.title =='' ){

                alert('一键获取失败，请手动发布商品！一键获取只支持已加入亚马逊的亚马逊.天猫商品')

                 return false;
            }

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