<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'Admin');?>
<form name="myform" id="myform" action="<?php echo U('delete'); ?>" method="post" onsubmit="check();return false;">
<div class="pad-lr-10">
<div class="table-list">
<style type="text/css">
    .img175{width:100px; height:75px; overflow: hidden; position:relative;}
    .hids{display:none;}
    .bghd{background-color:#000; position:absolute; width:100px; height:75px;}
    .img175 a{position:absolute; padding-left:20px; z-index:1000; left:12px;}
    .img175 a:link,.img175 a:visited{color:#fff;}
    .img175 a.aa1{background:url(/cn. .pro.cloud/static/images/admin_img/imgicon0.png) no-repeat 0px 4px; top:16px;}
    .img175 a.aa2{background:url(/cn. .pro.cloud/static/images/admin_img/imgicon1.png) no-repeat 0px 2px; top:40px;}
    .sjt{background-color:#666; position:absolute; padding:1px 2px; right:0px; z-index:90;}
    .sjt{color:#fff; background-color:rgba(0,0,0,0.4);}
    .tit_ipt,.cps_ipt{display:block; border:none; background:none; width:99%;}
    .tit_ipt{ margin-right:10px;font-weight: bold; color:#3A6EA5; margin-bottom:6px;}
    .cps_ipt{color:#999; height:4em; line-height: 18px; padding:0px;  overflow-y:hidden;}
    .iptfc{background:#fff; border: 1px solid #A7A6AA; word-wrap: break-word; word-break: normal; padding:0px; color:#666;}
</style>

<table width="100%" cellspacing="0" class="">
    <thead>
        <tr>
            <th align="left" width="16"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th align="left">ID</th>
            <th align="left">商品名称</th>
            <th width="100" align="left">商品价值（￥）</th>
            <th width="80">商品份数</th>
            <th width="80">当前剩余</th>
            <th width="100">所需积分</th>
            <th width="120">截止时间</th>           
            <th width="120">操作时间</th>
            <th width="120">管理操作</th>
        </tr>
    </thead>
<tbody>
<?php
    foreach($lists as $k => $r) {
?>
         <tr>
            <td align="left"><input type="checkbox" value="<?php echo $r['id']?>" name="ids[]"></td>
            <td align="left"><?php echo $r['id']?></td>
            <td><a href="<?php echo U('Index/show', array('id' => $r['id'])) ?>" target="_blank"><?php echo $r['title'] ?></a></td>
            <td align="left"><?php echo $r['price']?></td>
            <td align="center"><?php echo $r['total_num']?></td>
            <td align="center"><?php echo $r['total_num'] - $r['sale_num']?></td>
            <td align="center"><?php echo $r['point']?></td>
            <td align="center"><?php echo date("Y/m/d H:i:s",$r['end_time'])?></td>
            <td align="center"><?php echo date("Y/m/d H:i:s",$r['dateline'])?></td>         
            <td align="center">
                <a href="<?php echo U('Shop/Shop/log', array('id' => $r['id'])) ?>" title="查看兑换记录">[记录]</a>
                <a href="<?php echo U('Shop/Shop/edit', array('id' => $r['id']));?>" onclick="edit(this, '<?php echo $r['title']?>'); return false;">[修改]</a>
                <a
                href="javascript:confirmurl('<?php echo U('Shop/Shop/delete', array('ids[]' => $r['id']));?>','<?php echo L('sure_delete')?>');"
                >[删除]</a> 
            </td>
        </tr>
        <?php
    }
?>
</tbody>
 </table>

<div class="btn">
    <label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<script language="JavaScript">
<!--
function edit(obj, name) {
    window.top.art.dialog({id:'edit'}).close();
    window.top.art.dialog({
        title:'<?php echo L('edit')?>《'+name+'》',
        id:'edit',
        iframe:obj.href,
        width:'700',
        height:'500'
    }, function(){
        var d = window.top.art.dialog({id:'edit'}).data.iframe;
        d.document.getElementById('dosubmit').click();
        return false;
    }, function(){
        window.top.art.dialog({id:'edit'}).close()
    });
    return false;
}

function check() {
    if(myform.action == "<?php echo U('delete'); ?>") {
        var ids='';
        $("input[name='ids[]']:checked").each(function(i, n){
            ids += $(n).val() + ',';
        });
        if(ids=='') {
            window.top.art.dialog({content:'<?php echo L('plsease_select').L('member_group')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
            return false;
        }
    }
    myform.submit();
}
//-->
</script>
</body>
</html>