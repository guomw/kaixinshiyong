<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH ?>admin_product.js"></script>
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
<?php if ($_GET['mod'] == 'rebate'): ?>
		<a class="add fb" href="<?php echo U('add') ?>"><em>发布购物返利商品</em></a>

<?php elseif ($_GET['mod'] == 'trial'): ?>
<a class="add fb" href="<?php echo U('add', array('mod' => 'trial')) ?>"><em>发布免费试用商品</em></a>
<?php elseif ($_GET['mod'] == 'postal'): ?>
<a class="add fb" href="<?php echo U('add', array('mod' => 'postal')) ?>"><em>发布九块九商品</em></a>
<?php elseif ($_GET['mod'] == 'commission'): ?>
<a class="add fb" href="<?php echo U('add', array('mod' => 'commission')) ?>"><em>发布闪电试用</em></a>
<?php endif ?>	


</div>
<div id="searchid">
<form name="searchform" action="<?php echo __APP__; ?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME ?>" name="a">
<input type="hidden" value="<?php echo $_GET['mod']; ?>" name="mod">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
    <tr>
    	<td>
    		<div class="explain-col">	
    		    所属专员：
    		  <select name="search[attract]">
    		      <option value="-99">全部</option>
    		      <?php foreach ($attract_lists as $l){?>
    		      <option value="<?php echo $l['userid'];?>" <?php if( $attract == $l['userid'] ){?>selected<?php }?>><?php echo $l['username'];?></option>
    		      <?php }?>
    		  </select>  
    		  	
				<select name="search[status]">
					<option value="-99">活动状态</option>
					<?php foreach ($this->activity_status as $key => $value): ?>
					<option value="<?php echo $key ?>" <?php if ($key == $status): ?>selected<?php endif ?>><?php echo $value; ?></option>	
					<?php endforeach ?>
				</select>
				活动时间：
				<?php echo $form::datepicker('search[start_time]',$search['start_time'], 'start_time', 0);?>-&nbsp;
				<?php echo $form::datepicker('search[end_time]',$search['end_time'], 'end_time', 0);?>
				<select name="search[type]">
					<option value='product' <?php if ($search['type'] == 'product'): ?>selected<?php endif ?>>产品关键字</option>
					<option value='company' <?php if ($search['type'] == 'company'): ?>selected<?php endif ?>>商家名称</option>
					<option value='goods_id' <?php if ($search['type'] == 'goods_id'): ?>selected<?php endif ?>>活动id</option>
					<option value='shop' <?php if ($search['type'] == 'shop'): ?>selected<?php endif ?>>绑定店铺</option>
					<option value='phone' <?php if ($search['type'] == 'phone'): ?>selected<?php endif ?>>商家手机</option>
					<option value='email' <?php if ($search['type'] == 'email'): ?>selected<?php endif ?>>商家邮箱</option>
					<option value='userid' <?php if ($search['type'] == 'userid'): ?>selected<?php endif ?>>商家id</option>
				</select>	
				是否推荐：
				<select name="search[recommend]">
						<option value='99' selected>请选择</option>
					<option value='1' <?php if ($search['recommend'] == 1): ?>selected<?php endif ?>>是</option>
					<option value='0' <?php if ($search['recommend'] == '0'): ?>selected<?php endif ?>>否</option>
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
            <th width="30">ID</th>
			<th width="60">缩略图</th>
			<th width="220">商品标题</th>
			<th width="100">数量</th>
			<?php if ($_GET['mod'] == 'rebate'): ?>
			<th width="80">划算价</th>
			<th width="100">保证金</th>
			<?php elseif ($_GET['mod'] == 'trial'): ?>
			<th width="90">试用价</th>
			<th width="80">参与人数</th>
			<th width="100">保证金</th>
			<?php endif ?>
			
			<th width="100">所属商家</th>
			<th width="60">所属专员</th>
            <th width="40">浏览量</th>
            <th width="118">更新时间</th>
            <th width="50">推荐</th>
			<th width="72">管理操作</th>
		</tr>
        </thead>
		<tbody>

		<?php foreach($lists as $r) : ; ?>
        <tr>
			<td align="center"><input class="inputcheckbox " name="ids[]" value="<?php echo $r['id'];?>" type="checkbox"></td>
			<!-- <td align='center'><input name='listorders[<?php echo $r['id'];?>]' type='text' size='3' value='<?php echo $r['listorder'];?>' class='input-text-c'></td> -->
			<td align='center' ><?php echo $r['id'];?></td>
			<td>
				<div class="img175">
					<div class="hids">
						<a href="<?php echo $r['url'];?>" target="_blank" class="aa1"><?php echo L('view_video');?></a>
						<a href="javascript:void(0);" onclick="upthumb('<?php echo $r['id']?>')" class="aa2"><?php echo L('change_thumb');?></a>
						<div class="bghd"></div>
					</div>
					<div class="sjt"><?php echo $this->activity_lists[$r['mod']];?></div>
					<img src="<?php echo $r['thumb'] ? $r['thumb'] : IMG_PATH.'admin_img/bfqicon1.jpg';?>" id="thumb_images_<?php echo $r['id']?>" width="100px" />
				</div>
			</td>
			<td valign="top">
				<div class="iptd">
					<strong><a href="<?php echo $r['url']?>" rel="nofollow" target="_blank"><?php echo $r['title']?></a></strong>
					<p class="cps_ipt">
						<span style="color:red;">活动状态：<?php echo $this->activity_status[$r['status']]; ?></span><br/>

						<span>开始时间：<?php echo dgmdate($r['start_time']) ?></span><br/>

						结束时间：<?php echo dgmdate($r['end_time'])?>

					</p>
				</div>
			</td>

			<td valign="top">
				<div class="iptd">
					<strong>总数 <?php echo $r['goods_number'] ?></strong>
					<p class="cps_ipt">
						<span style="color:red;">剩余份数 <?php echo $r['goods_number']-$r['already_num']; ?></span><br/>
						已完成份数：<?php echo get_over_trial_by_gid($r['id']); ?>
					</p>
				</div>
			</td>
			<?php if ($_GET['mod'] == 'trial'): ?>
				<td valign="top">
					<div class="iptd">
					<strong><?php echo $r['goods_price'] ?></strong>
					<p class="cps_ipt">
					<?php if ($r['goods_bonus'] >0): ?>
						<span style="color:red;">
						<?php echo $r['goods_bonus']; ?> 元红包</span><br/>
					<?php endif ?>
				
					</p>

					<a class="button"  href="javascript:window.top.art.dialog({id:'add',iframe:'<?php echo U('Member/Unreal/dialog',array('goods_id'=>$r['id'])); ?>', title:'虚拟人数', width:'700', height:'500', lock:true}, function(){var d = window.top.art.dialog({id:'add'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'add'}).close()});void(0);"><em>增加虚拟申请人数</em></a>

					<!-- <input type="button" class="button" onclick="javascript:omnipotent('goods_id', '<?php echo U('Member/Unreal/dialog', array('goods_id' => $r['id']));?>', '虚拟订单', 1);" value="虚拟订单"> -->
						<!-- <strong><a>虚拟订单</a></strong> -->
					</div>
				</td>
			<?php endif ?>

			<?php 
			/*实际缴纳保证金商品id=966 and 订单id=0 and 金额包含- */
            $conditions = array();
            $conditions['goods_id'] = $r['id'];
            $conditions['userid'] = $r['company_id'];
            $conditions['order_id'] = 0;
            $conditions['num'] = array('LT',0);
            $conditions['type'] = 'money';
           	$total = model('member_finance_log')->where($conditions)->SUM('num');
           	$total = explode('-', $total);
           	$total = $total[1];
			 ?>	

			<?php if ($_GET['mod'] == 'rebate'): ?>

				<td valign="top">
				<div class="iptd">

						<strong>原 <?php echo $r['goods_price'] ?></strong><br>
						<p class="cps_ipt">
						 折扣：<?php echo $r['goods_discount'] ?>
						<span style="color:red;">
						<?php echo sprintf('%.2f', $r['goods_price'] * $r['goods_discount'] / 10) ?>
					</span>
						</p>
				</div>
			</td>

			<?php endif ?>

			<?php if ($_GET['mod'] == 'rebate'): ?>

				<td valign="top">
				<div class="iptd">
                         缴纳方式：<?php if($r['service_type'] == 2)  echo "部分缴纳";  ?><?php if($r['service_type'] == 1)  echo "全额缴纳";  ?><br>
						<strong>总保证金 ：<?php echo $r['goods_deposit'] ?></strong><br>
						<p class="cps_ipt">
							 <span style="color:red;">实际缴纳: <?php echo $total;?> </span><br/>

						<!-- <span style="color:red;">已返还 <?php echo sprintf('%.2f',($r['goods_price'] +$r['goods_bonus'])*get_over_trial_by_gid($r['id']));   ?> </span><br/> -->
					<!-- 	剩余 <?php echo sprintf('%.2f', $r['goods_deposit'] -($r['goods_price'] +$r['goods_bonus'])*get_over_trial_by_gid($r['id']));  ?> -->
						<?php
							if ($total - $r['goods_deposit'] == 0) {
								echo "<div id='titleTip' class='onCorrect'>正常</div>";
							}else{
								echo "<div id='priceTip' class='onError'>有异常</div>";
							}

						 ?>
						
						
						
					</p>
					
						
				</div>
			</td>

			<?php endif ?>

			<?php if ($_GET['mod'] == 'trial'): ?>
				<td valign="top">
				<div class="iptd">
					<strong>已申请 <?php echo get_trial_by_gid($r['id']); ?></strong>
					<p class="cps_ipt">
						<span style="color:red;">已完成 <?php echo get_over_trial_by_gid($r['id']); ?></span><br/>
						
					</p>
				</div>
			</td>
			<?php endif ?>
			<?php if ($_GET['mod'] == 'trial'): ?>
			<td valign="top">
				<div class="iptd">
					<strong>总保证金 
						<?php echo $r['goods_deposit']; ?><br>
						(实际缴纳：<?php echo $total;?>)
					</strong>
					<?php if ($r['goods_deposit'] > 0): ?>
					<p class="cps_ipt">
						<span style="color:red;">已返还 <?php echo sprintf('%.2f',($r['goods_price'] +$r['goods_bonus'])*get_over_trial_by_gid($r['id']));   ?> </span><br/>
						剩余 <?php echo sprintf('%.2f', $r['goods_deposit'] -($r['goods_price'] +$r['goods_bonus'])*get_over_trial_by_gid($r['id']));  ?>
						<?php
							if ($total - $r['goods_deposit'] == 0) {
								echo "<div id='titleTip' class='onCorrect'>正常</div>";
							}else{
								echo "<div id='priceTip' class='onError'>有异常</div>";
							}

						 ?>
						
						
						
					</p>
					<?php endif ?>
				</div>
			</td>

			<?php endif ?>
			<td align='center'>商家id: <?php echo $r['company_id'];?> <br/>商家qq: <?php echo $r['goods_qq'];?><br/>旺旺: <?php echo $r['goods_ww'] ?><br/><a target="_blank"  class="button" href="/index.php?m=Member&c=Member&a=memberinfo&userid=<?php echo $r['company_id'];?>">查看商家资料</a> </td>
			<td align="center"><?php if($r['attract']){echo $r['attract'];}else{echo '-';}?></td><!-- 所属专员 -->
			<td align='center'><?php echo $r['hits'];?></td>
			<td align='center'><?php echo dgmdate($r['updatetime'], 'Y/m/d H:i:s')?></td>
			<td align='center'><?php if($r['isrecommend'] == 1){?>是<?php }else{?>否<?php }?></td>
			<td align='center'>
				<P><a href="<?php echo U('edit', array('id' => $r['id']))?>">编辑活动</a> </P>
				<P><a href="<?php echo U('delete', array('dosubmit' => 1, 'ids[]' => $r['id'])); ?>" onclick="return confirm('您确定要删除本商品？非特殊情况请不要使用本功能。删除不可恢复。');">删除活动</a><br/></P>
				<P> <a href="<?php echo U('Order/Order/init', array('goods_id' => $r['id'],'act_mod'=>$_GET['mod'])) ?>">活动订单</a> </P>
				<P> <a href="javascript:;" onclick="admin_product.log(<?php echo $r['id'] ?>);">活动日志</a></P>
				<?php if ($r['status'] == 1): ?>
					<P><a href="javascript:;" onclick="admin_product.blocked(<?php echo $r['id'] ?>);">屏蔽此商品</a></P>
				<?php elseif ($r['status'] == 2 && ($r['mod']=='rebate'|| $r['mod']=='trial'|| $r['mod'] == 'commission')): ?>
					<a href="<?php echo U('activity_over', array('id' => $r['id'])) ?>" onclick="javascript:activity_over(this, '<?php echo $r['title']?>');return false;">[活动结算]</a><br>
					<a href="<?php echo U('add_time', array('id' => $r['id'])) ?>" onclick="javascript:add_time(this, '<?php echo $r['title']?>');return false;">[延期时间]</a>

				<?php elseif ($r['status'] == 5):  ?>
				<P><a href="<?php echo U('cancel', array('dosubmit' => 1, 'ids[]' => $r['id'])); ?>" onclick="return confirm('您确定要取消屏蔽此商品？非特殊情况请不要使用本功能');">取消屏蔽</a><br/></P>
				<?php endif ?>
			</td>
		</tr>
		</tbody>
		<?php endforeach ?>
	</table>
    <div class="btn">
    	<!-- <input type="button" class="button" value="更新排序" onclick="myform.action='<?php //echo U('public_listorders', array('dosubmit' => 1)) ?>';myform.submit();"/> -->
		<input type="button" class="button" value="批量删除" onclick="myform.action='<?php echo U('delete', array('dosubmit' => 1)) ?>';myform.submit();"/>
	</div>
	</div>
    <div id="pages"><?php echo $pages;?></div>
</div>
</form>
</body>
</html>
<script type="text/javascript"> 
function activity_over(obj, name) {
	window.top.art.dialog({id:'activity_over'}).close();
	window.top.art.dialog({
		title:'【活动结算】'+name,id:'activity_over',
		iframe:obj.href,
		width:'700',
		height:'450',
		okVal:'点此确认结算'
	}, function(){
		var d = window.top.art.dialog({id:'activity_over'}).data.iframe;
		d.document.getElementById('dosubmit').click();
		return false;
	}, function(){
		window.top.art.dialog({id:'activity_over'}).close()
	});
}

function add_time(obj, name) {
	window.top.art.dialog({id:'add_time'}).close();
	window.top.art.dialog({
		title:'【延期时间】'+name,id:'add_time',
		iframe:obj.href,
		width:'700',
		height:'450',
		okVal:'点此确认'
	}, function(){
		var d = window.top.art.dialog({id:'add_time'}).data.iframe;
		d.document.getElementById('dosubmit').click();
		return false;
	}, function(){
		window.top.art.dialog({id:'add_time'}).close()
	});
}
</script>