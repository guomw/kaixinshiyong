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
	.cps_ipt{color:#999; height:4em; line-height: 16px; padding:0px;  overflow-y:hidden;}
	.iptfc{background:#fff; border: 1px solid #A7A6AA; word-wrap: break-word; word-break: normal; padding:0px; color:#666;}
</style>
<div class="pad-10">
<div class="content-menu ib-a blue line-x">
	<a class="add fb" href="<?php echo U('add') ?>"><em>发布佣金商品</em></a>
</div>
<div id="searchid">
<form name="searchform" action="<?php echo __APP__; ?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME ?>" name="a">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
    <tr>
    	<td>
    		<div class="explain-col">		
				<select name="search[status]">
					<option value="-99">活动状态</option>
					<?php foreach ($this->status as $key => $value): ?>
					<option value="<?php echo $key ?>" <?php if ($key == $status): ?>selected<?php endif ?>><?php echo $value; ?></option>	
					<?php endforeach ?>
				</select>
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
			<th width="16"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th width="40">ID</th>
			<th width="60">缩略图</th>
			<th width="250">商品标题</th>
			<th width="100">库存</th>
			<th width="80">试客佣金</th>
			<th width="80">平台佣金</th>
			<th width="80">所属商家</th>
            <th width="40">浏览量</th>
            <th width="118">更新时间</th>
			<th width="72">管理操作</th>
		</tr>
        </thead>
		<tbody>

		<?php foreach($lists as $r) : ; ?>
        <tr>
			<td align="center"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>
			<td align='center' ><?php echo $r['id'];?></td>
			<td>
				<div class="img175">
					<div class="hids">
						<a href="<?php echo $r['url'];?>" target="_blank" class="aa1"><?php echo L('view_video');?></a>
						<a href="javascript:void(0);" onclick="upthumb('<?php echo $r['id']?>')" class="aa2"><?php echo L('change_thumb');?></a>
						<div class="bghd"></div>
					</div>
					<div class="sjt"闪电佣金</div>
					<img src="<?php echo $r['thumb'] ? $r['thumb'] : IMG_PATH.'admin_img/bfqicon1.jpg';?>" id="thumb_images_<?php echo $r['id']?>" width="100px" />
				</div>
			</td>
			<td valign="top">
				<div class="iptd">
					<strong><a href="<?php echo $r['url']?>" rel="nofollow" target="_blank"><?php echo $r['title']?></a></strong>
					<p class="cps_ipt">
						<span style="color:red;">活动状态：<?php echo $this->status[$r['status']]; ?></span><br/>
						<span>开始时间：<?php echo dgmdate($r['start_time']) ?></span><br/>
						结束时间：<?php echo dgmdate($r['end_time'])?>

					</p>
				</div>
			</td>

			
			<td align='center'><?php echo $r['goods_number'] ?></td>
			<td align='center'><?php echo $r['bonus_price'] ?></td>
			<td align='center'><?php echo $r['service'] ?></td>
			<td align='center'><?php echo $r['store_name'] ?></td>
			<td align='center'><?php echo $r['hits'];?></td>
			<td align='center'><?php echo dgmdate($r['updatetime'], 'Y/m/d H:i:s')?></td>
			<td align='center'>
				<a href="<?php echo U('edit', array('id' => $r['id']))?>">编辑</a> | 
				<a href="<?php echo U('delete', array('dosubmit' => 1, 'ids[]' => $r['id'])); ?>" onclick="return confirm('您确定要删除本商品？');">删除</a><br/>
				<a href="<?php echo U('Order/Commission/init', array('goods_id' => $r['id'])) ?>">订单</a> | 
				<a href="javascript:;" onclick="admin_product.log(<?php echo $r['id'] ?>);">日志</a>
				<?php if ($r['status'] == 1): ?>
					<!-- <a href="javascript:;" onclick="admin_product.blocked(<?php echo $r['id'] ?>);">屏蔽此商品</a> -->
				<?php elseif ($r['status'] == 2 && ($r['mod']=='rebate'|| $r['mod']=='trial')): ?>
					<a href="<?php echo U('activity_over', array('id' => $r['id'])) ?>" onclick="javascript:activity_over(this, '<?php echo $r['title']?>');return false;">[活动结算]</a>
				<?php endif ?>
			</td>
		</tr>
		</tbody>
		<?php endforeach ?>
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