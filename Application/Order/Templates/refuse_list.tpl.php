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
<script type="text/javascript" src="<?php echo JS_PATH?>order.js"></script>
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
<input type="hidden" value="<?php echo $info['act_mod']; ?>" name="act_mod">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
        <tr>
        <td>
        <div class="explain-col">               
            下单时间：
            <?php echo $form::date('start_time', $start_time)?>-
            <?php echo $form::date('end_time', $end_time)?>
            <select name="type">
                <option value='5' <?php if(isset($_GET['type']) && $_GET['type']==5){?>selected<?php }?>>订单ID</option>
                <option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>订单号</option>
                <option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>>会员昵称</option>
                <option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>>商家昵称</option>
                <option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>>商品标题</option>
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
            <th align="left">订单ID</th>
            <th align="left">会员</th>
            <th align="left">QQ</th>
            <th align="left">手机</th>
            <th align="left">姓名</th>
            <th align="left">亚马逊账号</th>
            <th align="left">订单号</th>
            <th align="left">商家</th>
            <th align="left">商品</th>
            <th align="left">活动类型</th>
            <th align="left">订单状态</th>
            <th align="left">失败原因</th>
            <th align="left">审核时间</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($orders)){
    foreach($orders as $k=>$v) {
?>
    <tr>
        <td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="id[]"></td>
        <td align="left"><?php echo $v['id']?></td>
        <td align="left"><?php echo $v['buyer']['nickname']?></td>
        <td align="left"><?php echo $v['buyer']['qq']?></td>
        <td align="left"><?php echo $v['buyer']['phone']?></td>
        <td align="left"><?php echo $v['realname']?></td>
        <td align="left"><?php echo $v['taobao']?></td>
        
        <td align="left"><?php if ($v['order_sn']){echo $v['order_sn'];}else{echo '--';}?></td>
        <td align="left"><?php echo $v['store_name']?></td>
        <td align="left"><a href='<?php echo $v['product']['url'];?>' rel="nofollow" target="_blank"><?php echo $v['product']['title']?></a></td>
        <td align="left">
            <?php if ($v['act_mod'] == 'commission'){echo '闪电试用';}
            elseif($v['act_mod']=='trial'){echo '免费试用';}
            ?>
        </td>
        <td align="left"><?php echo $state[$v['status']]; ?></td>
        <td align="left"><font color="red"><?php echo $v['close_cause']; ?></font></td>
        <td align="left"><?php if($v['check_time']){ echo date("Y-m-d H:i",$v['check_time']);}else{echo '-';}?></td>
        <td align="left">
        	
        	
            <a href="<?php echo U('read', array('id' => $v['id'])) ?>" onclick="javascript:read(this);return false;">[查看详情]</a> 
            
          
               
            <a href="javascript:;" onclick="javascript:log(<?php echo $v['id'] ?>);return false;">[查看日志]</a>
        </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>
<div class="btn">
    <label for="check_box">全选/取消</label>
    <!-- <input type="submit" class="button" name="dosubmit" onclick="document.myform.action='/index.php?m=Order&c=Order&a=delete'" value="删除"/> -->
    <input type="submit" class="button" name="dosubmit" onclick="document.myform.action='/index.php?m=Order&c=Order&a=operate'" value="关闭"/>
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
// 查看详情
function read(obj) {
    window.top.art.dialog({id:'read'}).close();
    window.top.art.dialog({title:'查看订单详情',id:'read',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'read'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'read'}).close()});
}
function log(oid) {
   $.getJSON('index.php?m=order&c=order&a=log', {order_id : oid}, function(ret){
        var _content = '';
        if(ret.status == 1) {
            _content += '<div class="table-list"><table width="500"><thead><tr>';
            _content += '<th>ID</th>';
            _content += '<th>操作理由</th>';
            _content += '<th>操作人</th>';
            _content += '<th>操作时间</th>';
            _content += '<th>IP</th>';
            _content += '</tr></thead>';
            $.each(ret.data, function(i, n) {
                var is_sys = (n.is_sys == 1) ? '<font color="red">(管理员)</font>' : '';
                _content += '<tbody><tr>';
                _content += '<td>'+n.id+'</td>';
                _content += '<td>'+n.cause+'</td>';
                _content += '<td>'+n.nickname +'</td>';
                _content += '<td>'+n.inputtime+'</td>';
                _content += '<td>'+n.ip+'</td>';
                _content += '</tr></tbody>';
            })
            _content += '</table></div>';
            window.top.art.dialog({
                id:oid+'_log',
                lock:true,
                title:'订单日志',
                padding:'1px',
                content:_content,
                ok:function() {
                    return true;
                }
            })
        } else {
            alert('无任何日志');
            return false;
        }
    });
}
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
function member_infomation(userid) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}
//-->
</script>
</body>
</html>