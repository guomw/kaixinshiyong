<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<div class="pad-10">
<div class="common-form">
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#title").formValidator({
		onshow:"请输入1到30个字符",
		onfocus:"请输入1到30个字符"
	}).inputValidator({
		min:1,
		max:30,
		onerror:"  标题为1到30个字符"
	}).defaultPassed();

	$("#price").formValidator({
		empty:false,
		onempty:'积分商品价格不能为空',
		onshow:'请输入积分商品价格' ,
		onfocus:"请输入积分商品价格" 
	}).regexValidator({
		regexp:'decmal1',
		datatype:'enum',
		onerror:'积分商品价格只能为正数'
	}).defaultPassed();

	$("#total_num").formValidator({
		empty:false,
		onempty:'积分商品份数不能为空',
		onshow:'请输入积分商品份数' ,
		onfocus:"请输入积分商品份数" 
	}).regexValidator({
		regexp:'intege1',
		datatype:'enum',
		onerror:'请输入积分商品份数'
	}).defaultPassed();

	$("#point").formValidator({
		empty:false,
		onempty:'所需积分不能为空',
		onshow:'请输入商品兑换所需积分' ,
		onfocus:"请输入商品兑换所需积分" 
	}).regexValidator({
		regexp:'intege1',
		datatype:'enum',
		onerror:'积分只能为正数'
	}).defaultPassed();

	$("#buy_url").formValidator({
		empty:true,
		onempty:'不开放外部购买',
		onshow:'请输入商品外部购买链接' ,
		onfocus:"请输入商品外部购买链接" 		
	}).regexValidator({
		regexp:'url',
		datatype:'enum',
		onerror:'网址格式输入错误'
	}).defaultPassed();

	$("#spec").formValidator({
		empty:true,
		onempty:'无需区分属性',
		onshow:'请输入商品属性，多个请用半角“,”分割' ,
		onfocus:"请输入商品属性，多个请用半角“,”分割" 		
	}).defaultPassed();

	$("#end_time").formValidator({
		empty:false,
		onempty:'请选择结束时间',
		onshow:'请选择结束时间' ,
		onfocus:"请选择结束时间" 
	}).functionValidator({
		fun:function(val,elem){
			if (val == '') {
				return '请选择结束时间';
			};
			return true;
		}
	}).defaultPassed();

	$("#images").formValidator({
		empty:false,
		onempty:'商品缩略图不能为空',
		onshow:'请上传商品缩略图' ,
		onfocus:"请上传商品缩略图" 
	}).functionValidator({
		fun:function(val,elem){
			if (val == '') {
				return '请上传商品缩略图';
			};
			return true;
		}
	}).defaultPassed();

	$("#desc").formValidator({
		empty:false,
		onempty:'积分商品描述不能为空',
		onshow:'请输入积分商品描述' ,
		onfocus:"请输入积分商品描述" 
	}).functionValidator({
		fun:function(val,elem){
			if (val == '') {
				return '请输入积分商品描述';
			};
			return true;
		}
	}).defaultPassed();
});
</script>
<form name="myform" action="<?php echo U(ACTION_NAME);?>" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<fieldset>
	<legend>编辑商品</legend>
	<table width="100%" class="table_form">
		<tr>
			<td width="80">商品名称</td> 
			<td><input type="text" name="title" id="title" value="<?php echo $rs['title'] ?>" class="input-text"/>
				
			</td>
		</tr>
		<tr>
			<td>商品价值</td>
			<td><input type="text" name="price" id="price" value="<?php echo $rs['price'] ?>" class="input-text"/>美元
			</td>
		</tr>
		<tr>
			<td>商品份数</td>
			<td><input type="text" name="total_num" id="total_num"  value="<?php echo $rs['total_num'] ?>" class="input-text"/>
			</td>
		</tr>

		<tr>
			<td>商品属性</td>
			<td><input type="text" name="spec" id="spec" value="<?php echo $rs['spec'] ?>"  class="input-text"/>
			</td>
		</tr>

		<tr>
			<td>购买地址</td>
			<td><input type="text" name="buy_url" id="buy_url" value="<?php echo $rs['buy_url'] ?>"  class="input-text"/>
			</td>
		</tr>

		<tr>
			<td>所需积分</td>
			<td><input type="text" name="point" id="point" value="<?php echo $rs['point'] ?>"  class="input-text"/>
			</td>
		</tr>

		<tr>
			<td><?php echo L('截止日期')?></td> 
			<td> <?php echo $form::date('end_time',dgmdate($rs['end_time']), 1)?> </td>
		</tr>
		<tr>
			<td><?php echo L('商品缩略图')?></td> 
			<td><?php echo $form::images('images', 'images', $rs['images'], 'member');?></td>			
		</tr>

		<tr>
			<td><?php echo L('商品描述')?></td> 
			<td><textarea name="desc" id="desc"><?php echo $rs['spec'] ?></textarea>
			<?php echo $form::editor("desc", "full");?></td>
		</tr>
	</table>
</fieldset>

    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
</html>