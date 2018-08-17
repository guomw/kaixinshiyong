<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH ?>commission_product.js"></script>
<style type="text/css">
.img175{width:100px; height:75px; overflow: hidden; position:relative;}
.hids{display:none;}
.bghd{background-color:#000; position:absolute; width:100px; height:75px;}
.img175 a{position:absolute; padding-left:20px; z-index:1000; left:12px;}
.img175 a:link,.img175 a:visited{color:#fff;}
.img175 a.aa1{background:url(<?php echo IMG_PATH?>admin_img/imgicon0.png) no-repeat 0px 4px; top:16px;}
.img175 a.aa2{background:url(<?php echo IMG_PATH?>admin_img/imgicon1.png) no-repeat 0px 2px; top:40px;}
.sjt{background-color:#666; position:absolute; padding:1px 2px; right:0px; z-index:90;}
.sjt{color:#fff; background-color:rgba(0,0,0,0.4);}
.tit_ipt,.cps_ipt{display:block; border:none; background:none; width:99%;}
.tit_ipt{ margin-right:10px;font-weight: bold; color:#3A6EA5; margin-bottom:6px;}
.cps_ipt{color:#999; height:4em; line-height: 18px; padding:0px;  overflow-y:hidden;}
.iptfc{background:#fff; border: 1px solid #A7A6AA; word-wrap: break-word; word-break: normal; padding:0px; color:#666;}
</style>
<div class="pad-10">
<div class="content-menu ib-a blue line-x">
<a <?php if ($status == -2): ?>class="fb"<?php endif ?> href="<?php echo U('check', array('status' => -2)) ?>"><em>待审核（已支付）</em></a><span>|</span>
<a <?php if ($status == -3): ?>class="fb"<?php endif ?> href="<?php echo U('check', array('status' => -3)) ?>"><em>待审核（待支付）</em></a><span>|</span>
<a <?php if ($status == -1): ?>class="fb"<?php endif ?> href="<?php echo U('check', array('status' => -1)) ?>"><em>审核通过（待上线）</em></a><span>|</span>
<a <?php if ($status == 0): ?>class="fb"<?php endif ?> href="<?php echo U('check', array('status' => 0)) ?>"><em>审核失败（已退款）</em></a><span>|</span>
<!-- <a <?php if ($status == 5): ?>class="fb"<?php endif ?> href="<?php echo U('check', array('status' => 5)) ?>"><em>已暂停</em></a> -->
</div>
<div id="searchid">
<form name="searchform" action="<?php echo __APP__; ?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME ?>" name="a">
<input type="hidden" value="<?php echo $param['status'];?>" name="search[status]" />
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
    <tr>
    	<td>
    		<div class="explain-col">		
				活动时间：
				<?php echo $form::datepicker('search[start_time]',$search['start_time'], 'start_time', 0);?>-&nbsp;
				<?php echo $form::datepicker('search[end_time]',$search['end_time'], 'end_time', 0);?>
				<select name="search[type]">
					<option value='product' <?php if ($search['type'] == 'product'): ?>selected<?php endif ?>>产品关键字</option>
					<option value='company' <?php if ($search['type'] == 'company'): ?>selected<?php endif ?>>商家名称</option>
				</select>				
				<input name="search[keyword]" type="text" value="<?php echo $search['keyword'] ?>" class="input-text" />
				<input type="submit" name="searchbtn" class="button" value="<?php echo L('search');?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
</div>
<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
    <table width="100%">
		<thead>
		<tr>
			<th width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th width="40">ID</th>
			<th width="60">缩略图</th>
			<th width="220">商品标题</th>
			<th width="100">所属商家</th>
            <th width="40">保证金(美元)</th>
            <th width="70">下单价(美元)</th>
            <th width="70">试客佣金(美元)</th>
            <th width="70">平台佣金(美元)</th>
            <th width="40">数量</th>
            <th width="118">发布时间</th>
			<th width="72">管理操作</th>
		</tr>
        </thead>
		<tbody>
		<?php foreach($lists as $r) :?>
        <tr>
			<td align="left"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>
			<td align='center' ><?php echo $r['id'];?></td>
			<td>
				<div class="img175">
					<div class="hids">
						<a href="<?php echo $r['url'];?>" target="_blank" class="aa1"><?php echo L('view_video');?></a>
						<a href="javascript:void(0);" onclick="upthumb('<?php echo $r['id']?>')" class="aa2"><?php echo L('change_thumb');?></a>
						<div class="bghd"></div>
					</div>
					<div class="sjt">闪电佣金</div>
					<img src="<?php echo $r['thumb'] ? $r['thumb'] : IMG_PATH.'admin_img/bfqicon1.jpg';?>" id="thumb_images_<?php echo $r['id']?>" width="100px" />
				</div>
			</td>
			<td valign="top">
				<div class="iptd">
					<strong><?php echo $r['title']?></strong>
					<p class="cps_ipt">
						活动状态：<?php if ($r['status'] == -2) {
						echo "待审核（已支付）";
					}elseif ($r['status'] == -3) {
						echo "待审核（待支付）";
					}elseif ($r['status'] == -1) {
						echo "审核通过（待上线）";
					}elseif ($r['status'] == 0) {
						echo "审核失败（已退款）";
					} ?><br/>
						<?php if ($r['status'] == -1): ?>
						活动周期：<?php echo dgmdate($r['start_time']) ?> - <?php echo dgmdate($r['end_time'])?>
						<?php endif ?>
					</p>
				</div>
			</td>
			<td align='center'><?php echo $r['store_name']?></td>
			<td align='center'><?php if((int)$r['goods_service']){echo $r['goods_service'];}else{echo '--';};?></td>
			<td align='center'><?php echo $r['goods_price'];?></td>
			<td align='center'><?php echo $r['bonus_price'];?></td>
			<td align='center'><?php echo $r['service'];?></td>
			<td align='center'><?php echo $r['goods_number'];?></td>
			<td align='center'><?php echo dgmdate($r['inputtime'], 'Y/m/d H:i:s')?></td>
			<td align='center'>
			<?php if ($r['status'] == -2): ?>
				<a href="javascript:;" onclick="admin_product.pass(<?php echo $r['id'] ?>)">通过</a> | 
                <a href="javascript:;" onclick="admin_product.refuse(<?php echo $r['id'] ?>);">拒绝</a>
			<?php elseif($r['status'] == -3): ?>
                <a href="javascript:;" onclick="javascript:enter(<?php echo $r['id'] ?>);">确认付款</a>
			<?php elseif($r['status'] == -1): ?>
				<a href="javascript:;" onclick="admin_product.blocked(<?php echo $r['id'] ?>);">屏蔽此商品</a>
            <?php elseif($r['status'] == 5):?>
                <a href="javascript:;" onclick="admin_product.recover(<?php echo $r['id'] ?>);">恢复</a>
			<?php endif ?>
                <br/><a href="<?php echo U('edit', array('id' => $r['id'])) ?>">详情</a> |
				<a href="javascript:;" onclick="admin_product.log(<?php echo $r['id'] ?>);">日志</a>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
    <div class="btn">
		<input type="button" class="button" value="批量删除" onclick="myform.action='<?php echo U('delete', array('dosubmit' => 1)) ?>';myform.submit();"/>
	</div>
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</body>
</html>
<script type="text/javascript">
function enter(pid) {
	var _content = '';
	_content += '<p>1、您确认帮商家进行确认付款操作？</p>';
	_content += '<p>2、本操作会扣除商家账户的余额，冻结保证金。</p>';
	_content += '<p>3、如果商家账户余额不足，无法完成该操作。</p>';
	_content += '<p>4、如果商家通过线下形式转账到平台帐号，请先在后台充值给商家账户进行充值。</p>';
	_content += '<p>5、本操作会将商品状态变更为待审核(已付款)并不可逆！</p>';
  	window.top.art.dialog({
  		title:'提示', 
  		id:'edit',   
  		height:'80px',
  		content: _content,
  	}, 	function(){
  		location.href = "<?php echo __ROOT__ ?>/index.php?m=Commission&c=Commission&a=pay&product_id="+pid;
  		window.top.art.dialog({id:'edit'}).close();
  		return false;
  	}, function(){
  		window.top.art.dialog({id:'edit'}).close();
  	});

}

</script>