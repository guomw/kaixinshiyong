<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-10">
    <form name="search" action="<?php echo U('dialog'); ?>" method="get" >
        <input type="hidden" value="<?php echo MODULE_NAME; ?>" name="m">
        <input type="hidden" value="<?php echo CONTROLLER_NAME ?>" name="c">
        <input type="hidden" value="<?php echo ACTION_NAME ?>" name="a">
        <input type="hidden" value="<?php echo $modelid;?>" name="modelid">
        <table width="100%" cellspacing="0" class="search-form">
            <tbody>
            <tr>
                <td align="center">
                    <div class="explain-col">
                        <select name="field">
                            <option value='1' <?php if($_GET['field']=='username') echo 'selected';?>>店铺名称</option>
                            <option value='2' <?php if($_GET['field']=='userid') echo 'selected';?>>商家ID</option>
                            <option value='3' <?php if($_GET['field']=='userid') echo 'selected';?>>商家邮箱</option>
                        </select>
                        <?php $form::select_category($catid,'name="catid"', L('no_limit_category'), $modelid,0,1);?>
                        <input name="keywords" type="text" value="<?php echo stripslashes($_GET['keywords'])?>" style="width:330px;" class="input-text" />
                        <input type="submit" name="search" class="button" value="<?php echo L('search');?>" />
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    <div class="table-list">
     <form action="<?php echo U('Member/RecommendMerchant/recommend');?>" method="post" name="myform" id="myform">
        <table width="100%" cellspacing="0">
            <thead>
            <tr>
                <th align="left">ID</th>
                <th align="left">店铺名称</th>
                <th align="left">店铺图标</th>
                <th align="left">所在地址</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(is_array($infos)){
                foreach($infos as $k=>$v) {
                    ?>
                    <tr onclick="select_list(this, '<?php echo $v['store_name'] ?>', '<?php echo $v['userid'] ?>');" class="item" style="cursor: pointer;">
                        <td align="left"><?php echo $v['userid'] ?></td>
                        <td align="left"><?php echo $v['store_name'] ?></td>
                        <td align="left"><img src="<?php echo $v['store_logo']?>" alt="<?php echo $v['store_name'] ?>" width="100" height="100"/></td>
                        <td align="left"><?php echo $v['store_address'];?></td>
                    </tr>
                <?php
                }
            }
            ?>
            
            <input type="hidden" value="" id="company_id_text" name="company_id_text"/>
        	 <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">
            </tbody>
        </table>
        </form>
        <div id="pages"><?php echo $pages;?></div>
    </div>
</div>
<style type="text/css">
    .line_ff9966,.line_ff9966:hover td{
        background-color:#FF9966;
    }
    .line_fbffe4,.line_fbffe4:hover td {
        background-color:#fbffe4;
    }
</style>
<SCRIPT type="text/javascript">
function select_list(obj,title,id) {
    $(".item").removeClass('line_ff9966');
    $(obj).addClass("line_ff9966");
    $("#company_id_text").attr("value",id);
}
</SCRIPT>
</body>
</html>