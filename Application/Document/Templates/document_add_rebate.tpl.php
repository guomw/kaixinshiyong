<?php
defined('IN_ADMIN') or exit('No permission resources.');$addbg=1;
include $this->admin_tpl('header','admin');?>
<style>.addContent{ width: auto;}</style>
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
<script type="text/javascript">var catid=<?php echo $catid;?></script>
<form name="myform" id="myform" action="<?php echo U(ACTION_NAME) ?>" method="post" enctype="multipart/form-data">
<div class="addContent">
<div class="col-right">
    	<div class="col-1">
        	<div class="content pad-6">        		
        		<h6>活动状态</h6>
        		<select name="info[status]">
        			<option value="">待审核 （待付款）</option>
        			<option value="">待审核 （已付款）</option>
        			<option value="">审核通过 （待上线）</option>
        			<option value="">审核失败 （已退款）</option>
        			<option value="" style="background-color: #006000;color: #FFFFFF;">活动进行中</option>
        			<option value="">活动结束（结算中）</option>
        			<option value="">活动结束（已结算）</option>
        			<option value="">已撤销</option>
        			<option value="">已屏蔽</option>
        		</select>
        		<h6>允许参与用户组</h6>
        		<?php $member_group = getcache('member_group', 'member');?>
        		<?php
        		$member_groups = array();
				foreach ($member_group as $groupid => $group) {
					$member_groups[$groupid] = $group['name'];
				}
        		?>
        		<?php echo $form::checkbox($member_groups);?>
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
      <td><input type="text" name="rebate[title]" class="input-text"/></td>
    </tr>
    
	<tr>
      <th>商品关键字：</th>
      <td><input type="text" name="rebate[keyword]" class="input-text"/></td>
    </tr>    

	<tr>
      <th>产品分类：</th>
      <td><?php echo $form::select_category(0, "name=rebate[catid]", "请选择");?></td>
    </tr> 

	<tr>
      <th>商品来源：</th>
      <td>
			<label><input type="radio" name="" id="1" />&nbsp;亚马逊</label>
			<label><input type="radio" name="" id="2" />&nbsp;天猫</label>
			<label><input type="radio" name="" id="3" />&nbsp;京东</label>
			<label><input type="radio" name="" id="4" />&nbsp;苏宁</label>
      </td>
    </tr>
    
	<tr>
		<th>下单方式：</th>
		<td>
			<label><input type="radio" name="rebate[type]" value="general" />&nbsp;普通下单</label>
			<label><input type="radio" name="rebate[type]" value="search" />&nbsp;搜索下单</label>
			<label><input type="radio" name="rebate[type]" value="answer" />&nbsp;答案下单</label>
			<label><input type="radio" name="rebate[type]" value="qrcode" />&nbsp;二维码下单</label>
		</td>
	</tr>
		
	<tr>
		<th>下单地址：</th>
		<td><input type="text" name="rebate[shop_url]"/></td>
	</tr>
	
	<tr>
		<th>所属商家：</th>
		<td>
			<input type="text" name="rebate[user_id]" value=""/>
			<input type="button" class="button" onclick="javascript:flashupload('image2_images', '附件上传','image2',submit_images,'1,jpg|jpeg|gif|bmp|png,1,,,0','admin','','4e3375496a1d25ca5ba38e40aa4be0db')" value="选择商家">
		</td>
	</tr>

	<tr>
		<th>商品多图：</th>
		<td>
			<input type="text" name="rebate[user_id]" value=""/>
			<input type="button" class="button" onclick="javascript:flashupload('image2_images', '附件上传','image2',submit_images,'1,jpg|jpeg|gif|bmp|png,1,,,0','admin','','4e3375496a1d25ca5ba38e40aa4be0db')" value="选择商家">
		</td>
	</tr>	

	<tr>
		<th>下单价格：</th>
		<td><input type="text" name="rebate[shop_url]"/></td>
	</tr>		

	<tr>
		<th>价格折扣：</th>
		<td><input type="text" name="rebate[shop_url]"/></td>
	</tr>
	
	<tr>
		<th>返还金：</th>
		<td><input type="text" name="rebate[shop_url]"/></td>
	</tr>
	
	<tr>
		<th>活动时间：</th>
		<td><input type="text" name="rebate[shop_url]"/></td>
	</tr>
	
	<tr>
		<th valign="top">温馨提醒：</th>
		<td>
			<label><input type="checkbox" name="rebate[tips]" />&nbsp;请不要使用淘金币下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="rebate[tips]" />&nbsp;请不要催促商家返款</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="rebate[tips]" />&nbsp;请不要使用手机下单</label>
			<div class="bk3"></div>
			<label><input type="checkbox" name="rebate[tips]" />&nbsp;请不要使用店铺优惠券</label>
			<div class="bk3"></div>
			<label>默认快递：<input type="text" name="" id="" /></label><div class="bk3"></div>
			<label>套餐包含：<input type="text" name="" id="" /></label><div class="bk3"></div>
			<label>拍下须知：<input type="text" name="" id="" /></label>
		</td>
	</tr>	
	
	<tr>
		<th>商品介绍：</th>
		<td>
			<textarea name="rebate[content]" id="rebate[content]"></textarea>
			<?php echo $form::editor("rebate[content]", "full");?>
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
    <div class="button"><input value="<?php echo L('save_continue');?>" type="submit" name="dosubmit_continue" class="cu" style="width:130px;" title="Alt+X" onclick="refersh_window()"></div>
    <div class="button"><input value="<?php echo L('c_close');?>" type="button" name="close" onclick="refersh_window();close_window();" class="cu" style="width:70px;"></div>
      </div>
</div>
</form>

</body>
</html>
<script type="text/javascript"> 
<!--
//只能放到最下面
var openClose = $("#RopenClose"), rh = $(".addContent .col-auto").height(),colRight = $(".addContent .col-right"),valClose = getcookie('openClose');
$(function(){
	if(valClose==1){
		colRight.hide();
		openClose.addClass("r-open");
		openClose.removeClass("r-close");
	}else{
		colRight.show();
	}
	openClose.height(rh);
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({id:'check_content_id',content:msg,lock:true,width:'200',height:'50'}, 	function(){$(obj).focus();
	boxid = $(obj).attr('id');
	if($('#'+boxid).attr('boxid')!=undefined) {
		check_content(boxid);
	}
	})}});
	<?php echo $formValidator;?>
	
/*
 * 加载禁用外边链接
 */

	$('#linkurl').attr('disabled',true);
	$('#islink').attr('checked',false);
	$('.edit_content').hide();
	jQuery(document).bind('keydown', 'Alt+x', function (){close_window();});
})
document.title='<?php echo L('add_content');?>';
self.moveTo(-4, -4);
function refersh_window() {
	setcookie('refersh_time', 1);
}
openClose.click(
	  function (){
		if(colRight.css("display")=="none"){
			setcookie('openClose',0,1);
			openClose.addClass("r-close");
			openClose.removeClass("r-open");
			colRight.show();
		}else{
			openClose.addClass("r-open");
			openClose.removeClass("r-close");
			colRight.hide();
			setcookie('openClose',1,1);
		}
	}
)
//-->
</script>