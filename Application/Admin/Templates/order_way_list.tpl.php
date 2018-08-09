<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form action="<?php echo __APP__;?>" method="get">
<input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
<input type="hidden" value="<?php echo CONTROLLER_NAME; ?>" name="c">
<input type="hidden" value="<?php echo ACTION_NAME; ?>" name="a">
<input type="hidden" value="<?php echo MENUID; ?>" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    
</table>
</form>

<form name="myform" action="<?php echo U('way_edit');?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
           
           
            <th align="align">ID</th>
            <th align="left"><?php echo L('下单方式')?></th>
            <th align="left"><?php echo L('下单方式图章')?></th>
            <th align="left"><?php echo "描述"?></th>
            <th align="left"><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($order_way)){
    foreach($order_way as $k=>$v) {
?>
    <tr>
        
        
        <td align="center"><?php echo $v['id']?></td>
        <td align="left"><?php echo $v['order_way']?></td>
        <td align="left"><?php echo $v['ico']?></td>
        <td align="left"><?php echo $v['description']?></td>
        <td align="left">
            <a href="<?php echo U('way_edit', array('id' => $v['id'])) ?>" onclick="javascript:edit(this, '<?php echo $v['order_way']?>');return false;">[<?php echo L('edit')?>]</a>
        </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({
        title:'<?php echo L('修改下单方式')?>【'+name+'】',
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
    $("input[name='userid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
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
//-->
</script>
</body>
</html>