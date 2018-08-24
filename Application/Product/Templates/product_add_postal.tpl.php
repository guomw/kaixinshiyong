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
        		<!-- <h6>允许参与用户组</h6>
        		<?php $member_group = getcache('member_group', 'member');?>
        		<?php
        		$member_groups = array();
				foreach ($member_group as $groupid => $group) {
					$member_groups[$groupid] = $group['name'];
				}
        		?>
        		<?php echo $form::checkbox('info[allow_groupid]', '', $member_groups);?> -->
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
		<th>亚马逊佣金：</th>
		<td>
			<!-- <label><input type="radio" name="info[taobaoke]" value="1"/>&nbsp;是</label>
			<label><input type="radio" name="info[taobaoke]" value="0"/>&nbsp;否</label> -->
			<input type="text" name="info[taobao_charge]" class="input-text" style="width:50px" id="taobao" />
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
		<th>下单地址：</th>
		<td><input type="text" name="info[goods_url]" id="goods_url" value="<?php echo $rs['goods_url']?>"/></td>
	</tr>		
	
	<tr>
      <th>活动时间：</th>
      <td><input type="text" name="info[activity_days]" id="activity_days" /></td>
    </tr>
    
	<tr>
		<th valign="top">温馨提醒：</th>
		<td>
			<!-- <label><input type="checkbox" name="info[goods_tips][order_tip][]" value="1" />&nbsp;请不要使用信用卡下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="info[goods_tips][order_tip][]" value="2" />&nbsp;请不要催促商家返款</label>
			<div class="bk3"></div> -->
			<label>默认快递：<input type="text" name="info[goods_tips][goods_order][kuaidi]" style="width:250px;"/></label>
			<div class="bk3"></div>
			<label>拍下须知：<input type="text" name="info[goods_tips][goods_order][remark]" style="width:250px;"/></label>
			<div class="bk3"></div>
			<label>原价为：<input type="text" name="info[goods_tips][goods_order][price][cost]" style="width:50px;">美元</label>，<label>拍下后价格为：<input type="text" name="info[goods_tips][goods_order][price][after]" style="width:50px;">美元
		</td>
	</tr>	
	
	<tr>
		<th>商品介绍：</th>
		<td>
			<textarea name="info[goods_content]" id="info[goods_content]" ></textarea>
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
    <!-- <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X" onclick="refersh_window()"></div> -->
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

$("#taobao").formValidator({
	empty:false,
	onempty:'亚马逊佣金不能为空',
	onshow:'亚马逊佣金不能为空',
	onfocus:'请输入亚马逊佣金'
}).regexValidator({
	regexp:'decmal1',
	datatype:'enum',
	onerror:'亚马逊佣金输入错误'
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