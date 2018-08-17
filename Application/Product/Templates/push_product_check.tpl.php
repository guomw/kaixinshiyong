<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH ?>admin_product.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>dialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>check.js"></script>
<div class="subnav">
	    <div class="content-menu ib-a blue line-x">
        <a href="<?php echo U('push_product') ?>" class="on"><em>追加历史记录</em></a> </div>
</div>
<style type="text/css">
.img175{width:100px; height:75px; overflow: hidden; position:relative;}
.hids{display:none;}
.bghd{background-color:#000; position:absolute; width:100px; height:75px;}
.img175 a{position:absolute; width:100px; height:75px;}
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

	    		     所属专员：
			      <select name="search[attract]">
			          <option value="-99">全部</option>
			          <?php foreach ($attract_lists as $l){?>
			          <option value="<?php echo $l['userid'];?>" <?php if($attract == $l['userid']){?>selected<?php }?>><?php echo $l['username'];?></option>
			          <?php }?>
			      </select>
    		     
				<select name="search[mod]">
					<option value="">活动类型</option>
					<option value="rebate" <?php if ($info['mod'] == 'rebate'): ?>selected<?php endif ?>>购物返利</option>
					<option value="trial" <?php if ($info['mod'] == 'trial'): ?>selected<?php endif ?>>免费试用</option>
					<option value="postal" <?php if ($info['mod'] == 'postal'): ?>selected<?php endif ?>>九块九包邮</option>
				</select>
				活动时间：
				<?php echo $form::datepicker('search[start_time]',$_GET['search']['start_time'], 'start_time', 0);?>-&nbsp;
				<?php echo $form::datepicker('search[end_time]',$_GET['search']['end_time'], 'end_time', 0);?>
				<select name="search[type]">
					<option value='1' <?php if ($info['type'] == '1'): ?>selected<?php endif ?>>产品关键字</option>
					<option value='2' <?php if ($info['type'] == '2'): ?>selected<?php endif ?>>商家名称</option>
				</select>				
				<input name="search[keyword]" type="text" value="<?php echo $info['keyword'] ?>" class="input-text" />
				<input type="submit" name="searchbtn" class="button" value="<?php echo L('search');?>" />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
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
		<!-- 	<th width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th> -->
            <th width="40">ID</th>
			<th width="60">缩略图</th>
			<th width="220">商品标题</th>
			<th width="100">所属商家</th>
            <th width="100">追加保证金(美元)</th>
            <th width="70">下单价(美元)</th>
            <th width="40">追加份数</th>
            <th width="70">追加天数</th>
            <th width="70">状态</th>
            <th width="118">追加时间</th>
			<th width="72">管理操作</th>
		</tr>
        </thead>
		<tbody>
		<?php foreach($lists as $r) :?>
        <tr>
			<!-- <td align="left"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td> -->
			<td align='center' ><?php echo $r['id'];?></td>
			<td>
				<div class="img175">
					
						<img src="<?php echo $r['thumb'] ? $r['thumb'] : IMG_PATH.'admin_img/bfqicon1.jpg';?>" id="thumb_images_<?php echo $r['goods_id
						']?>" width="100px" />
				</div>
			</td>
			<td align='center'>
				<a href="<?php echo $r['url'] ?>" rel="nofollow" target="_blank"><?php echo $r['title']?></a>
				
			</td>
			<td align='center'>
			<?php echo '商家id:'.$r['company_id'] ?><br/>
			<a target="_blank"  class="button" href="/index.php?m=Member&c=Member&a=memberinfo&userid=<?php echo $r['company_id'];?>">查看商家资料</a>
			</td>
			<td align='center'><?php if((int)$r['com_total_fee']){echo $r['com_total_fee'];}else{echo '--';};?>
				<br>
				含平台服务费
				<?php echo $r['goods_service'] ?>

			</td>
			
			<td align='center'><?php echo $r['goods_price'];?></td>
			<td align='center'><?php echo $r['com_number'];?></td>
			<td align='center'><?php echo $r['com_day'];?></td>
			<td align='center'>
				<?php
					if ($r['status'] == 0) {
						echo "未付款";
					}elseif($r['status'] == 1){
						echo "审核通过（已付款）";
					}elseif($r['status'] == 2){
						echo "待审核（已付款）";
					}

				 ?>
			</td>

			<td align='center'><?php echo dgmdate($r['dateline'], 'Y/m/d H:i:s')?></td>
			<td align='center'>
				<?php if ($r['status'] == 2) { ?>
					<a href="javascript:;" onclick="check_pass('<?php echo $r['id'] ?>');">[通过]</a>  
			    <?php 
					
				} ?>
<!--                 <a href="javascript:;" onclick="refuse('<?php echo $r['id']; ?>'');">拒绝</a>
 -->			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
    <div class="btn">
	</div>
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</body>
</html>
<script type="text/javascript">

	function check_pass(id){
		var id = parseInt(id);
		var url = "<?php echo __ROOT__ ?>/index.php?m=Product&c=Product&a=push_product_check&id="+id;
        var msg = '您确认该商家已进行付款操作?';
		art.dialog.confirm(msg, function() {
			$.get(url, {id:id}, function(ret) {
				if(ret.status == 1){
					check.show_message('提示信息',ret.info,'S');
				    location.href= ret.url;
				}else{
					check.show_message('提示信息',ret.info,'E');
				    location.href= ret.url;
				}
			});
			},function(){return true;}
		); 
	}

	function product_refuse(id){
		var id = parseInt(id);
		var url = "<?php echo __ROOT__ ?>/index.php?m=Product&c=Product&a=push_refuse&id="+id;
        var msg = '您确认进行此操作，该操作不可逆转?';
		art.dialog.confirm(msg, function() {
			$.get(url, {id:id}, function(ret) {
				if(ret.status == 1){
					check.show_message('提示信息',ret.info,'S');
				    location.href= ret.url;
				}else{
					check.show_message('提示信息',ret.info,'E');
				    location.href= ret.url;
				}
			});
			},function(){return true;}
		); 
	}


</script>