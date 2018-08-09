<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = true;
$show_scroll = true;
include $this->admin_tpl('header');
?>
<body scroll="no">
<div style="padding:6px 3px">
    <div class="col-2 col-left mr6" style="width:140px">
      <h6><img src="<?php echo IMG_PATH?>icon/sitemap-application-blue.png" width="16" height="16" />角色选择</h6>
       <div id="site_list">
          <ul class="content role-memu" >
          <?php foreach($rolelist as $n => $r) {?>
            <li <?php if ($r['roleid'] == $roleid): ?>class="on"<?php endif; ?>><a href="<?php echo U('role_priv', array('roleid' => $r['roleid'])) ?>" target="role"><span><img src="<?php echo IMG_PATH?>icon/gear_disable_green.png" width="16" height="16" />设置</span><em><?php echo $r['rolename']; ?></em></a></li>
           <?php } ?>
      </ul>
      </div>
    </div>
    <div class="col-2 col-auto">
        <div class="content" style="padding:1px">
        <iframe name="role" id="role" src="<?php echo U('role_priv', array('roleid' => $roleid)) ?>" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="483" allowtransparency="true"></iframe>
        </div>
    </div>
</div>
</body>
</html>
<script type="text/javascript">
$("#site_list li").click(
	function(){$(this).addClass("on").siblings().removeClass('on')}
);
$(function(){
	var site_list=$("#site_list"),col_left=$(".col-left");
	if(site_list.height()>458){
		col_left.attr("style","width:160px");
		site_list.attr("style","overflow-y:auto;height:458px");
	}
})
</script>