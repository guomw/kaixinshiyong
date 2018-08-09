<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
tr{
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}
td, th {
    display: table-cell;
    vertical-align: inherit;
    height: 30px;
}

.table-list thead th {
    height: 30px;
    background: #eef3f7;
    border-bottom: 1px solid #d5dfe8;
    font-weight: normal;
    color:#444;
}

.table-list  tr td {
    border:1px solid #eee;
}


.table-list tbody td, .table-list .btn {
    border-bottom: #eee 1px solid;
    padding-top: 5px;
    padding-bottom: 5px;
    color:#444;
}
.table-list tr:hover, .table-list table tbody tr:hover {
    background: #fbffe4;
}

.table_form tbody td, .table-list .btn {
    border-bottom: #eee 1px solid;
    padding-top: 8px;
    padding-bottom: 8px;
    color:#444;
}
.table_form tr:hover, .table-list table tbody tr:hover {
    background: #fbffe4;
}

.table-list input .input-text-c .input-text{
    border-top: 1px solid #989898;
    border-left: 1px solid #989898;
    border-bottom: 1px solid #e6e6e6;
    border-right: 1px solid #e6e6e6;
    font-size: 14px;
    padding: 3px;
    width: 25px;
    text-align: center;
}

input, label, img, th {
    vertical-align: middle;
}
 

</style>
<form name="myform" action="<?php echo U('listorder'); ?>" method="post">
<div class="pad_10">
<div class="explain-col">
温馨提示：请在添加、修改分类全部完成后，<a href="<?php echo U('public_cache')?>" style='color:red'>【<?php echo L('更新产品分类缓存');?>】</a>
</div>
<div class="bk10"></div>
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th width="90"><?php echo L('listorder');?></th>
            <th width="60">catid</th>
            <th >栏目名称</th>
            <th align='center' width="70"><?php echo L('items');?></th>
            <th align='center' width="70"><?php echo L('vistor');?></th>
            <th align='center' width="70"><?php echo '是否推荐';?></th>
			<th ><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php echo $categorys;?>
    </tbody>
    </table>

    <div class="btn">
	<input type="hidden" name="pc_hash" value="<?php echo $_SESSION['pc_hash'];?>" />
	<input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
</div>
</div>
</form>
<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
//-->
</script>
</body>
</html>
