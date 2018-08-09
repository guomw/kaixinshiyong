<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php 
$show_header = 1;
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
var site = {
		"site_root" : '<?php echo __ROOT__;?>',
	};
</script>

<script type="text/javascript" src="<?php echo JS_PATH?>dialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>check.js"></script>
<style type="text/css">
    .explain-col{
     margin-top: 20px;
    }

</style>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<input type="hidden" value="<?php echo $info['act_mod'];?>" name="act_mod">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
        <td>
        <div class="explain-col">     
        	所属专员：
			<select name="attract">
				<option value="-99">全部</option>
				<?php foreach($attract_lists as $k=>$v){?>
				<option value="<?php echo $v['userid'];?>" <?php if(I('attract') == $v['userid']){?>selected<?php }?>><?php echo $v['username'];?></option>
				<?php }?>
			</select>
			          
            申请时间：
            <?php echo $form::date('start_time', $start_time)?>-
            <?php echo $form::date('end_time', $end_time)?>
            
            <select name="type">
                <option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>商品名称</option>
                <option value='5' <?php if(isset($_GET['type']) && $_GET['type']==5){?>selected<?php }?>>订单ID</option>
                <option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>>订单号</option>
                <option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>会员邮箱</option>
                <option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>>商家店铺</option>
                
            </select>
            <input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
            <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
            <br/><br/>
           
        </div>
        </td>
        </tr>
    </tbody>
</table>
</form>

<form name="myform" action="<?php echo U('delete');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
            <th align="left">ID</th>
            <th align="left">订单号</th>
            <th align="left" width="15%">申请人</th>
            <th align="left" width="12%">联系信息</th>
            <th align="left" width="13%">淘宝账号</th>
            <th align="left" width="4%">申请留言</th>
            <th align="left" width="10%">活动信息</th>
            <th align="left">试用价格</th>  
            <th align="left">试用商家</th>
            <th align="center" width="6%">操作</th>
        </tr>
    </thead>
<tbody>
<?php if(is_array($order_lists)){
	foreach($order_lists as $v){
?>
    <tr>
        <td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="id[]"></td>
        <td align="left"><?php echo $v['id'];?></td>
        <td align="left">
         订单编号：<?php echo $v['trade_sn'];?><br />
         活动id：<?php echo $v['goods_id'];?><br />
         申请时间：<?php echo dgmdate($v['create_time'],'Y-m-d');?>
        </td>
        <td align="left">
        头像：<img src="<?php echo $v['member_info']['avatar']?>" width="30" height="30"/><br />
        昵称：<?php echo $v['member_info']['nickname'];?><br />
        邮箱：<?php echo $v['member_info']['email'];?><br />
        ip：<?php echo $v['member_info']['lastip'];?><br />
        成功试用<?php echo $v['member_info']['trial_num'];?>次
        </td>
        <td align="left">
        姓名：<?php echo $v['member_info']['realname'];?><br />
        手机号：<?php echo $v['member_info']['phone'];?><br />
        QQ：<?php if($v['member_info']['qq']){echo $v['member_info']['qq'];}else{echo '-';}?><br />
        <?php if($v['member_info']['qq']){?>
        <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $v['member_info']['qq'];?>&amp;site=qq&amp;menu=yes">
			<img border="0" src="http://wpa.qq.com/pa?p=2:644278217:51" alt="点击这里给我发消息" title="点击这里给我发消息">
		</a>
		<?php }?>
        </td>
        <td align="left">
        淘宝账号：<?php echo $v['member_info']['taobao']['account'];?><br />
        安全等级：<?php echo $this->grade[$v['member_info']['taobao']['safe_grade']];?><br />
        买家信用：<?php echo $v['member_info']['taobao']['bscore'];?><br />
        好评率：<?php echo $v['member_info']['taobao']['favorable_rate'];?><br />
        <a href="http://cha.yunhuasuan.net/" target="_blank">手动查询</a>
        </td>
        <td align="left"><?php echo $v['talk'];?></td>
        
        <td align="left">
        商品名称：<?php echo $v['product_info']['title']?><br />
         <?php echo get_trial_by_gid($v['product_info']['id']);?>人申请<br />
        <?php echo get_trial_pass_by_gid($v['product_info']['id']);?>人已通过<br />
        <?php echo get_over_trial_by_gid($v['product_info']['id']);?> 人已完成<br />
        剩余<?php echo $v['product_info']['goods_number'] - $v['product_info']['already_num'];?>份
        </td>
        <td align="left">
        试用价： ￥<?php echo $v['product_info']['goods_price']?><br />
        赠送红包：￥<?php echo $v['product_info']['goods_bonus'];?>
        </td>
        <td align="left">
        店铺名称：<?php echo  $v['merchant_info']['store_name'];?><br />
        商家等级：<?php echo member_group_name($v['merchant_info']['userid']);?><br />
        手机：<?php echo  $v['merchant_info']['phone'];?><br />
        QQ：<?php echo  $v['merchant_info']['store_qq'];?><br />
        专员：<?php if($v['merchant_info']['role_name']){echo $v['merchant_info']['role_name'];}else{echo '-';}?>
        </td>
        <td align="center">
            <a href="javascript:;" onclick="check.pass(this)" data-msg="确定试用资格通过?该操作不可逆转" data-id="<?php echo $v['id'];?>" data-state="1" data-url="<?php echo U('Order/Check/pass');?>">[资格通过]</a> <br />
            <a href="javascript:;" onclick="check.pass(this)" data-msg="确定拒绝试用资格?该操作不可逆转" data-id="<?php echo $v['id'];?>" data-state="0" data-url="<?php echo U('Order/Check/pass');?>">[拒绝申请]</a> <br />
            <a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$v['member_info']['userid']));?>" >[查看用户]</a> <br />
            <a href="<?php echo U('Member/Member/memberinfo',array('userid'=>$v['merchant_info']['userid']));?>" >[查看商家]</a>
        </td>
    </tr>
    <?php }}?>
</tbody>
</table>
<div class="btn">
    <label for="check_box">全选/取消</label>
    <!--<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='/index.php?m=Order&c=Order&a=operate'" value="关闭"/>-->
</div>
<div id="pages"><?php echo $pages;?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function checkuid() {
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        window.top.art.dialog({content:'请勾选要删除的记录',lock:true,width:'200',height:'50',time:1.5},function(){});
        return false;
    } else {
        myform.submit();
    }
}

//-->
</script>
</body>
</html>