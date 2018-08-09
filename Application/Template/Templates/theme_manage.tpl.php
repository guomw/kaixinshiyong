<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">


<div class="design_page">
    <ul class="cc">
    	<?php foreach ($lists as $k => $v): ?>
    	<li>
    		<div class="img"><a title="点击进行模板主题切换" href="<?php echo U('setting', array('skin' => $k)) ?>"><img src="<?php echo $v ?>" width="210" height="140" alt="<?php echo $k ?>"></a></div>
    		<div class="title" title="default"><?php echo $k ?></div>
    		<div class="ft">
    			<span><?php if (C('DEFAULT_STYLE') == $k): ?><em class="red">已使用</em><?php else: ?><em>未使用</em><?php endif ?></span>
    		</div>
    	</li>
    	<?php endforeach ?>
    </ul>
</div>
<style type="text/css">
.design_page{
	padding-bottom:10px;
	width:800px;
}
.design_page li{
	float:left;
	margin-right:23px;
	display:inline;
	box-shadow:0 0 1px rgba(0,0,0,0.1);
	background:#fff;
	margin-bottom:20px;
	border:1px solid;
	border-color:#ecebeb #e1e0e0 #d5d5d5 #e1e0e0;
	width:230px;
	height:218px;
	position:relative;
}
.design_page .img{
	display:block;
	padding:10px;
}
.design_page li img{
	display:block;
}
.design_page li .ft{
	position:absolute;
	left:0;
	right:0;
	bottom:0;
	width:100%;
	background:#f8f8f8;
	border-top:1px solid #eeeeee;
	padding:5px 0;
	border-bottom:1px solid #fff;
}
.design_page li .ft span{
	padding:0 0 0 10px;
}
.design_page li .ft a{
	color:#666;
	margin:0 0 0 10px;
}
.design_page li .title{
	padding:0 10px 0;
	font-size:14px;
	line-height:18px;
	height:18px;
	overflow:hidden;
	margin-bottom:3px;
	white-space:nowrap;
	text-overflow:ellipsis;
	-ms-text-overflow:ellipsis;
	word-wrap:normal;
}
.design_page li .descrip{
	padding:0 10px 3px;
	color:#999;
	line-height:18px;
	height:18px;
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
	-ms-text-overflow:ellipsis;
	word-wrap:normal;
} 
.design_page li .type{
	padding:0 10px 8px;
	color:#999;
}
.design_page li .type span{
	margin-right:10px;
}
</style>
</div>
</body>
</html>