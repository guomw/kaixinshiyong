<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
if (session('roleid') == 6) {
             $admininfo = model('admin')->find(session('userid'));
             $company = string2array($admininfo['company_config']);
             /*本月数据*/
             $sqlmap = array();
             $sqlmap['agent_id'] = $admininfo['userid'];
             $sqlmap['time'] = array('EGT',strtotime(date('Y-m')));
             $totals = model('company_log')->where($sqlmap)->sum('money');
             /*商家当天注册*/
             $con = array();
             $con['modelid'] = array('EQ',2);
             $con['agent_id'] = array('EQ',$admininfo['userid']);
             $con['regdate'] = array('EGT',strtotime(date('Y-m-d')));
             $seller_day = model('member')->where($con)->count();

              /*商家当月注册*/
             $con2 = array();
             $con2['modelid'] = array('EQ',2);
             $con2['agent_id'] = array('EQ',$admininfo['userid']);
             $con2['regdate'] = array('EGT',strtotime(date('Y-m')));
             $seller_month = model('member')->where($con2)->count();
             $seller_total = model('member')->where(array('modelid'=>2,'agent_id'=>$admininfo['userid']))->count();

             /*当天数据*/
             $sqlMap = array();
             $sqlMap['agent_id'] = $admininfo['userid'];
             $sqlMap['time'] = array('EGT',strtotime(date('Y-m-d')));
             $total_day = model('company_log')->where($sqlMap)->sum('money');

             $total_money = model('company_log')->where(array('agent_id'=>$admininfo['userid']))->sum('money');
             
 }
include $this->admin_tpl('header');
?>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH;?>new_index.css">
<script type="text/javascript">
	$(document).ready(function(){
		// 列表背景
		bg();
	});
</script>
<body scroll="no">
	<div id="main">
		<!-- 登陆信息 start -->
		<!-- <div class="logo_info">欢迎你，<span><?php //echo $admin_username?></span>[<?php //echo $rolename?>]，上次登录时间：<?php echo date('Y-m-d H:i:s',$logintime)?></div>-->
		<!-- 登陆信息 end -->
		<!-- 升级区域 start -->
		<?php if (session('roleid') == 6):?>

		<div class="up">
			<span><?php echo $admin_username;?>，您好，您的专属招商链接：</span>&nbsp;
			<?php if (C('DEFAULT_STYLE') == 'cloud3'): ?>
            <a target="_bank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/zs/'.session('userid'); ?>" ><?php echo 'http://'.$_SERVER['HTTP_HOST'].'/zs/'.session('userid'); ?></a>
			<?php else: ?>
			<a href="javascript:;"><?php echo U('Member/Index/register',array('modelid'=>2,'agent_id'=>session('userid')),'',TRUE,TRUE);?></a>
		    <?php endif ?>
			&nbsp;您本月目前预计可获得工资 <?php echo $admininfo['fee_money']+$totals; ?>元（含基本工资<?php echo $admininfo['fee_money'] ?>，提成<?php echo $totals; ?>）.
			以最终实际发放为准。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo U('admin/admin/company_log',array('userid'=>session('userid'))) ?>">[提成明细]</a>
		</div>
			<!-- 订单信息 start -->
		<div class="mt26">
			<dl>
				<dt class="mb20 f14 fw c3a6ea5">招商统计</dt>
				<!-- 今日统计概况 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">今日统计概况</h1>
						<li class="lh28">
							<span class="fl lh28">今日新招商家：</span>
							<span class="fr lh28">
										<?php echo $seller_day; ?>		名				
							</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">本月新招商家</span>
							<span class="fr lh28">
							<?php echo $seller_month; ?>
								 名</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">累计商家</span>
							<span class="fr lh28">
							<?php echo $seller_total; ?>
							名</span>
						</li>
						
					</ul>
				</dd>
				<!-- 今日统计概况 end -->
				<!-- 订单处理情况 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">提成统计</h1>
						<li class="lh28">
							<span class="fl lh28">今日提成金额</span>
							<span class="fr lh28">
								<?php echo sprintf("%.2f",$total_day); ?>
							元</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">本月提成金额</span>
							<span class="fr lh28">
							<?php echo sprintf("%.2f",$totals); ?>
							元</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">累计提成金额：</span>
							<span class="fr lh28">
							<?php echo sprintf("%.2f",$total_money); ?>元</span>

						</li>
						
					</ul>
				</dd>
				<!-- 订单处理情况 end -->
				<!-- 商品信息统计 start -->
				<dd class="fl" style="margin: 0;">
					<ul>
						<h1 class="title fw lh28">提成规则</h1>
						<?php if ($admininfo['fee_type'] == 1): ?>
							<li class="lh28">
							<span class="fl lh28">1.您名下商家成功发布活动，您可获得活动保证金 <?php echo $admininfo['service_fee'] ?> % 提成.
							</span>
							<span class="fr lh28">
							</span>
						</li>
						<?php endif ?>
						<?php if ($admininfo['fee_type'] == 2): ?>
						<li class="lh28">
							<span class="fl lh28">1.您名下商家每成功完成一笔订单交易，您可获得订单下单价 <?php echo $admininfo['service_fee'] ?> %提成。</span>
							<span class="fr lh28">
							</span>
						</li>
						<?php endif ?>

						<?php if ($admininfo['fee_type'] == 3): ?>
						<li class="lh28">
							<span class="fl lh28">1.您名下商家每成功完成一笔订单交易，您可获得单笔服务费 <?php echo $admininfo['service_fee'] ?> %提成。</span>
							<span class="fr lh28">
							</span>
						</li>
						<?php endif ?>

						<li class="lh28">
							<span class="fl lh28">2.您名下商家充值/续费 钻石vip，您可获得单笔 <?php echo $company['service_zuan_fee'] ?> 元提成奖励。
							</span>
							<span class="fr lh28">
							</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">3.您名下商家 充值/续费 皇冠vip，您可获得单笔<?php echo $company['service_huang_fee'] ?>元，提成奖励。
							</span>
							<span class="fr lh28">
							</span>
						</li>
						
					</ul>
				</dd>
				<!-- 商品信息统计 end -->
				<div class="clear"></div>
			</dl>
		</div>
		<!-- 订单信息 end -->
		<div class="clear"></div>



		<?php else:?>





		<div class="up">
			<span>当前版本v<?php echo C('SYSTEM_VERSION');?>，发布日期：<?php echo C('SYSTEM_RELEASE');?></span>&nbsp;
			<a href="<?php echo U('Upgrade/Index/index');?>">点击升级</a>
		</div>
		<!-- 升级区域 end -->
				<!-- 常用操作 -->
		<div class="mt26">
		<dl>
				<dt class="mb20 f14 fw c3a6ea5">常用操作</dt>
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">常用操作</h1>
						<li class="lh28">
							<span class="fl lh28">待审核活动： </span>
							<span class="fr lh28">
							<?php 
							$check_goods_count =  goods_info_count(-2);
							if($check_goods_count > 0){
							?>
							<a href="<?php echo U('Product/Product/check', array('menuid' => MENUID));?>" style="color:red;"><?php echo $check_goods_count;?></a>
							<?php }else{echo $check_goods_count;}?>
							 件</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待审核充值：</span>
							<span class="fr lh28">￥
							<?php 
							$check_pay = deposite_count(2);
							if($check_pay > 0){
							?>
							<a href="<?php echo U('Pay/PayCheck/init', array('status'=>0,'menuid' => MENUID));?>" style="color:red;"><?php echo $check_pay;?></a>
							<?php }else{
								echo $check_pay;
							}?>
							元</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待审核提现：</span>
							<span class="fr lh28">￥
							<?php 
							$check_pay_money = deposite_count(3);
							if($check_pay > 0){
							?>
							<a href="<?php echo U('Member/Deposite/init', array('status'=>0,'menuid' => MENUID));?>" style="color:red;"><?php echo $check_pay_money;?></a>
							<?php }else{
								echo $check_pay_money;
							}?>
							元</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待处理申诉订单：</span>
							<span class="fr lh28">
							<?php 
							$order_appeal_check = order_info_count(6);
							if($order_appeal_check > 0){
							?>							
							<a href="<?php echo U('Order/Appeal/init', array('appeal_status'=>1,'menuid' => MENUID));?>" style="color:red;"><?php echo $order_appeal_check;?></a>
							<?php }else{echo $order_appeal_check;}?>
							条</span>
						</li>
						<li class="bt_none lh28">
							<span class="fl lh28">待人工审核订单：</span>
							<span class="fr lh28">
							<?php 
							$member_check_order = order_info_count(3);
							if($member_check_order > 0){
							?>
							<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $member_check_order;?></a>
							<?php }else{echo $member_check_order;}?>
							条</span>
						</li>
						
						<li class="lh28">
							<span class="fl lh28">待审核品牌认证：</span>
							<span class="fr lh28">
							<?php 
							$brand_count = brand_count('brand');
							if($brand_count > 0){
							?>
							<a href="<?php echo U('Member/Check/brand', array('menuid' => MENUID));?>" style="color:red;"><?php echo $brand_count;?></a>
							<?php }else{ echo $brand_count;}?>
							个</span>
						</li>
						
						<li class="bt_none lh28">
							<span class="fl lh28">待审核实名认证：</span>
							<span class="fr lh28">
							<?php 
							$identify_count = brand_count();
							if($identify_count > 0){
							?>
							<a href="<?php echo U('Member/Check/real_name', array('menuid' => MENUID));?>" style="color:red;"><?php echo $identify_count;?></a>
							<?php }else{ echo $identify_count;}?>
							个</span>
						</li>
					</ul>
				</dd>
				<div class="clear"></div>
			</dl>
		</div>
		<div class="clear"></div>
		<!-- 常用操作 end -->
		
		<!-- 订单信息 start -->
		<div class="mt26">
			<dl>
				<dt class="mb20 f14 fw c3a6ea5">订单信息</dt>
				<!-- 今日统计概况 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">今日统计概况</h1>
						<li class="lh28">
							<span class="fl lh28">今日在线充值总额：</span>
							<span class="fr lh28">￥
								<?php
									$money_count = deposite_count(1,'1');
									if($money_count > 0){?>
									<a href="<?php echo U('Pay/Pay/pay_list', array('menuid' => MENUID));?>"  style="color:red;"><?php echo sprintf('%.2f',$money_count);?></a>
								<?php }else{?>
									<?php echo sprintf('%.2f',$money_count);?>
								<?php }?>									
							元</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">今日订单数量：</span>
							<span class="fr lh28">
							<?php 
							$order_count = order_info_count('',true);
							if($order_count > 0){?>
								<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:green;"><?php echo $order_count;?></a>
							<?php }else{?>
								<?php echo $order_count;?>
								<?php }?>
								 个</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">今日新增商家：</span>
							<span class="fr lh28">
							<?php 
								$member = member_info_count(2);
								if($member > 0){
							?> 
							<a href="<?php echo U('Member/Business/manage', array('menuid' => MENUID));?>" style="color:green;"><?php echo $member;?></a>
							<?php }else{
								echo $member;
							}?>
							人</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">今日新增会员：</span>
							<span class="fr lh28">
							<?php 
							$member_info =  member_info_count(1);
							if($member_info >0){
							?>
							<a href="<?php echo U('Member/Member/manage', array('menuid' => MENUID));?>" style="color:green;"><?php echo $member_info;?></a>
							<?php }else{
								echo $member_info;
							}?>
							 人</span>
						</li>
						<li class="bt_none lh28">
							<span class="fl lh28">今日新增提现：</span>
							<span class="fr lh28">￥ 
							<?php 
							$deosite =  deposite_count(4,'1');
							if($deosite > 0){
							?>
							<a href="<?php echo U('Member/Deposite/init', array('menuid' => MENUID));?>" style="color:green;"><?php echo $deosite;?></a>
							<?php }else{
								echo $deosite;
							}?>
							 元</span>
						</li>
					</ul>
				</dd>
				<!-- 今日统计概况 end -->
				<!-- 订单处理情况 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">订单处理情况</h1>
						<li class="lh28">
							<span class="fl lh28">待审核订单总数：</span>
							<span class="fr lh28">
							<?php 
							$order_count =  order_info_count(3);
							if($order_count > 0){
							?> 
							<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $order_count;?></a>
							<?php }else{
								echo $order_count;
							}?>
							个</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待填写订单总数：</span>
							<span class="fr lh28">
							<?php 
							$write_order_count = order_info_count(2);
							if($write_order_count > 0){
							?>
							<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $write_order_count;?></a>
							<?php }else{
								echo $write_order_count;
							}?>
							个</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">抢购订单总数：</span>
							<span class="fr lh28">
								<?php 
								$order_qcount = order_info_count(1);
								if($order_qcount > 0){
								?>
								<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $order_qcount;?></a>
								<?php }else{
									echo $order_qcount;
								}?>
							 条</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">失效订单总数：</span>
							<span class="fr lh28">
							<?php 
							$out_order_count =  order_info_count(-1);
							if($out_order_count > 0){
							?>
							<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $out_order_count;?></a>
							<?php }else{echo $out_order_count; }?>
							 条</span>
						</li>
						<li class="bt_none lh28">
							<span class="fl lh28">成功订单总数：</span>
							<span class="fr lh28">
							<?php 
							$success_order =  order_info_count(7);
							if($success_order > 0){
							?>
							<a href="<?php echo U('Order/Order/init', array('menuid' => MENUID,'act_mod'=>'trial'));?>" style="color:red;"><?php echo $success_order;?></a>
							<?php }else{echo $success_order;}?>
							 个</span>
						</li>
					</ul>
				</dd>
				<!-- 订单处理情况 end -->
				<!-- 商品信息统计 start -->
				<dd class="fl" style="margin: 0;">
					<ul>
						<h1 class="title fw lh28">商品信息统计</h1>
						<li class="lh28">
							<span class="fl lh28">待上架商品总数：</span>
							<span class="fr lh28">
							<?php 
							$wait_goods_count =  goods_info_count(-1);
							if($wait_goods_count > 0){
							?>
							<a href="<?php echo U('Product/Product/manage', array('menuid' => MENUID));?>" style="color:red;"><?php echo $wait_goods_count;?></a>
							<?php }else{echo $wait_goods_count;}?>
							 件</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待审核商品总数：</span>
							<span class="fr lh28">
							<?php 
							$check_goods_count =  goods_info_count(-2);
							if($check_goods_count > 0){
							?>
							<a href="<?php echo U('Product/Product/check', array('menuid' => MENUID));?>" style="color:red;"><?php echo $check_goods_count;?></a>
							<?php }else{echo $check_goods_count;}?>
							 件</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待交费用商品总数：</span>
							<span class="fr lh28">
							<?php 
							$unpay_goods_count =  goods_info_count(-3);
							if($unpay_goods_count > 0){
							?>
							<a href="<?php echo U('Product/Product/check', array('menuid' => MENUID));?>" style="color:red;"><?php echo $unpay_goods_count;?></a>
							<?php }else{echo $unpay_goods_count;}?>
							 件</span>
						</li>
						<li class="lh28">
							<span class="fl lh28">待结算活动商品总数：</span>
							<span class="fr lh28">
							<?php 
							$complete_goods_count =  goods_info_count(2);
							if($complete_goods_count > 0){
							?>
							<a href="<?php echo U('Product/Product/manage', array('menuid' => MENUID));?>" style="color:red;"><?php echo $complete_goods_count;?></a>
							<?php }else{echo $complete_goods_count;}?>
							 件</span>
						</li>
						<li class="bt_none lh28">
							<span class="fl lh28">成功上架商品总数：</span>
							<span class="fr lh28">
							<?php
							 $success_goods_count =  goods_info_count(1);
							 if($success_goods_count > 0){
							 ?>
							<a href="<?php echo U('Product/Product/manage', array('menuid' => MENUID));?>" style="color:red;"><?php echo $success_goods_count;?></a>
							<?php }else{echo $success_goods_count;}?>
							 件</span>
						</li>
					</ul>
				</dd>
				<!-- 商品信息统计 end -->
				<div class="clear"></div>
			</dl>
		</div>
		<!-- 订单信息 end -->
		<div class="clear"></div>
		<!-- 产品信息 start -->
		<div class="mt26">
			<dl>
				<dt class="mb20 f14 fw c3a6ea5">产品信息</dt>
				<!-- 系统信息 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">系统信息</h1>
						<li class="lh28">
							<span class="fl lh28">程序版本：</span>
							<span class="fr lh28">v<?php echo C('SYSTEM_VERSION');?>_<?php echo C('SYSTEM_RELEASE');?></span>
						</li>
						<li class="lh28">
							<span class="fl lh28">服务器系统及PHP：</span>
							<span class="fr lh28"><?php echo $sysinfo['os'];?> / PHP<?php echo $sysinfo['phpv'];?></span>
						</li>
						<li class="lh28">
							<span class="fl lh28">服务器软件：</span>
							<span class="fr lh28"><?php echo $sysinfo['web_server'];?></span>
						</li>
						<li class="lh28">
							<span class="fl lh28">服务器MySQL版本：</span>
							<span class="fr lh28"><?php echo $sysinfo['mysqlv'];?></span>
						</li>
						<li class="bt_none lh28">
							<span class="fl lh28">当前数据库尺寸：</span>
							<span class="fr lh28"><?php echo $sysinfo['mysqlsize'];?></span>
						</li>
					</ul>
				</dd>
				<!-- 系统信息 end -->
				<!-- 产品动态 start -->
				<dd class="fl">
					<ul>
						<h1 class="title fw lh28">产品动态</h1>
						<script type="text/javascript" src="http://www.dealswill.com/?a=news"></script>
						</ul>
				</dd>
				<!-- 产品动态 end -->
				<!-- adalways inc start -->
				<dd class="fl" style="margin: 0;">
					<ul>
						<h1 class="title lh28 c000000">
							<span class="team_bg lh28 c000000">adalways开发团队：</span>
						</h1>
						<li class="lh28">
							<span class="w35 fl lh28 ib">版权信息：</span>
							<span class="lh28 fl fw">adalways inc</span>
						</li>
						<li class="lh28">
							<span class="w35 fl lh28 ib">总策划兼项目经理：</span>
							<span class="fl lh28">jason</span>
						</li>
						<li class="lh28">
							<span class="w35 fl lh28 ib">产品设计与研发团队：</span>
							<span class="fl lh28 mr6">jason</span>
							<span class="fl lh28 mr6">jason</span>
						</li>
						<li class="lh28">
							<span class="w35  fl lh28 ib">帮助中心：</span>
							<span class="lh28"><a href="http://www.dealswill.com" class="fl lh28 c000000 ib">http://www.dealswill.com</a></span>
						</li>
						<li class="bt_none lh28">
							<span class="w35 fl lh28 ib">官方网站：</span>
							<span class="fl lh28 ib"><a href="http://www.dealswill.com" class="fl lh28 c000000 ib">http://www.dealswill.com</a></span>
						</li>
					</ul>
				</dd>
				<!-- adalways inc end -->
				<div class="clear"></div>
			</dl>
		</div>
			<?php endif; ?>

		<!-- 产品信息 end -->
		<div class="clear"></div>
		<!-- 版权开 start -->
		<div id="copyright">
			Powered by   v<?php echo C('SYSTEM_VERSION');?> 版权所有 &copy; 2013-2020 
		</div>
		<!-- 版权开 end -->
	</div>
</body>
</html>
<script type="text/javascript">
	// 列表背景
	function bg(){
		$("#main li:even").addClass("bg");
	}
</script>