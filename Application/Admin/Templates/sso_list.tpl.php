<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<!-- <div class="pad-lr-10">
    <div class="explain-col">
温馨提示：自定义任务需人工审核
</div> -->
<div class="pad_10">
<form name="myform"  method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
         
            <th align="left">应用名</th>
            <th align="left">appid</th>
            <th align="left">当前服务器ip</th>
            <th align="left">整合平台ip</th>
            <th align="left">通信地址</th>
            <th align="left">通信状态</th>
            <th align="left">是否启用</th>
            <th align="left">操作</th>
        </tr>
    </thead>
<tbody>

    <tr style="height:60px;">
        <td align="left"><?php echo C('webname');?></td>
        <td align="left"><?php echo C('appid');?></td>
        <td align="left"><?php echo gethostbyname($_SERVER["SERVER_NAME"]);?></td>
        <td align="left">
        <?php 
         preg_match("/^(http:\/\/)?([^\/]+)/i", C('sso_address'), $matches);
         $url = str_replace('http://','',$matches[0]);
         echo gethostbyname($url);
         ?></td>
        <td align="left"><?php echo C('sso_address');?></td>
        <td align="left" ><span id='tongxin'> </span></td>
        <td align="left"><?php if(C('sso_is_open') == 1){ echo "启用";}else{
            echo "禁用";
        }?></td>
       
        <td align="left">
            <a href="<?php echo U('add') ?>" >[<?php echo L('修改')?>]</a>
           
        </td>
    </tr>

</tbody>
</table>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">

function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({
        title:'<?php echo L('修改任务设置')?>【'+name+'】',
        id:'edit',
        iframe:obj.href,
        width:'700',
        height:'500'
        }, 
        function(){
            var d = window.top.art.dialog({id:'edit'}).data.iframe;
            d.document.getElementById('dosubmit').click();
            return false;
        }, 
        function(){
            window.top.art.dialog({id:'edit'}).close()}
        );
}

function checkuid() {
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        window.top.art.dialog({content:'<?php echo L('请选择要删除的记录');?>',lock:true,width:'200',height:'50',time:1.5},function(){});
        return false;
    } else {
        myform.submit();
    }
}

/*function member_infomation(obj, name) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}*/
function member_infomation(userid) {
    window.top.art.dialog({id:'modelinfo'}).close();
    window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=Member&c=Member&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

function aword(obj ,name){
    window.top.art.dialog({id:'aword'}).close();
    window.top.art.dialog({title:'<?php echo L('aword').L('member')?>《'+name+'》',id:'aword',iframe:obj.href,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}

  /* 与支付平台进行通信*/

function tongxin() {

$('#tongxin').html('<font color="red">正在进行通信....</font>')

}

$.getJSON('/index.php?m=admin&c=sso&a=tongxin',function(s){

    if(s['status'] == 1){
        $('#tongxin').html('<font color="green">'+s.info+'</font>');
    }else{
        $('#tongxin').html('<font color="red">'+s.info+'</font>');
    }
})

tongxin();

</script>
</body>
</html>