<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'Admin');?>
<form name="myform" id="myform" action="<?php echo U('delete'); ?>" method="post">
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
            <th align="left">兑换人</th>
            <th align="left">兑换商品名称</th>
            <th align="left">兑换商品属性</th>
            <th width="100">消耗积分</th>
            <th width="100">状态</th>
            <th width="100">备注（审核原因）</th>
            <th width="120">兑换时间</th>    
            <th width="120">审核时间</th>            
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
            <td align="left"><?php echo nickname($r['userid'])?></td>
            <td align="left"><?php echo $r['title']?></td>
            <td align="left"><?php echo $r['spec']?></td>
            <td align="center"><?php echo $r['point']?></td>
            <td align="center">
                <?php if ($r['status'] == -1): ?>
                    <font class="gray4">审核失败</font>
                <?php elseif($r['status'] == 1): ?>
                    <font class="green">审核通过</font>
                <?php else: ?>
                    <font class="red">等待审核</font>
                <?php endif ?>
            </td>
            <td align="center"><?php echo  $r['remark'];?></td>
            <td align="center"><?php echo dgmdate($r['apply_time'],"Y/m/d H:i:s")?></td>

            <td align="center"><?php echo  $r['complete_time'] ? dgmdate($r['complete_time'],"Y/m/d H:i:s"):"-"?></td>
                
            <td align="center">
                <?php if ($r['status'] == 0): ?>
                <a href="<?php echo U('check', array('id' => $r['id']));?>" onclick="javascript:check(this, '<?php echo $r['title']?>');return false;" >审核</a>
                <?php else: ?>
                <font class="gray4">审核</font>
                <?php endif ?>
                 | <a href="<?php echo U('delete', array('dosubmit' => true, 'ids[]' => $r['id']));?>"
                >删除</a> 
            </td>
        </tr>
        <?php
    }
?>
</tbody>
 </table>

<div class="btn">
    <label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> 
    <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('批量删除')"/>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<script language="JavaScript">
<!--
function check(obj ,name){
    window.top.art.dialog({id:'aword'}).close();
    window.top.art.dialog({title:'<?php echo "审核";?>《'+name+'》',id:'aword',iframe:obj.href,width:'500',height:'350'}, function(){var d = window.top.art.dialog({id:'aword'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'aword'}).close()});
}
//-->
</script>
</body>
</html>