<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="off">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo L('admin_site_title')?></title>
<link href="<?php echo CSS_PATH?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH.LANG_SET;?>-system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>table_form.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles1.css" title="styles1" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles2.css" title="styles2" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles3.css" title="styles3" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles4.css" title="styles4" media="screen" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.sgallery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.sgallery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog/iframeTools.js"></script>
<script language="javascript" type="text/javascript" src="/static/js/toastmessage/jquery.toastmessage.js"></script>
<link href="/static/js/toastmessage/css/jquery.toastmessage.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
var fromhash = '<?php echo session("FROMHASH");?>';
</script>
<style type="text/css">
.objbody{overflow:hidden}
.btns{background-color:#666;}
.btns{position: absolute; top:116px; right:30px; z-index:1000; opacity:0.6;}
.btns2{background-color:rgba(0,0,0,0.5); color:#fff; padding:2px; border-radius:3px; box-shadow:0px 0px 2px #333; padding:0px 6px; border:1px solid #ddd;}
.btns:hover{opacity:1;}
.btns h6{padding:4px; border-bottom:1px solid #666; text-shadow: 0px 0px 2px #000;}
.btns .pd4{ padding-top:4px; border-top:1px solid #999;}
.pd4 li{border-radius:0px 6spx 0px 6px; margin-top:2px; margin-bottom:3px; padding:2px 0px;}
.btns .pd4 li span{padding:0px 6px;}
.pd{padding:4px;}
.ac{background-color:#333; color:#fff;}
.hvs{background-color:#555; cursor: pointer;}
.bg_btn{background: url(<?php echo IMG_PATH?>admin_img/icon2.jpg) no-repeat; width:32px; height:32px;}
.header_case {position: absolute;right: 10px;top: 10px;color: #fff;width: 420px;}
.header_case a {color: #fff;}

</style>
</head>
<body scroll="no" class="objbody">
<div id="dvLockScreen" class="ScreenLock" style="display:<?php if(session('lock_screen') == 0) echo 'none';?>">
    <div id="dvLockScreenWin" class="inputpwd">
    <h5><b class="ico ico-info"></b><span id="lock_tips"><?php echo L('lockscreen_status');?></span></h5>
    <div class="input">
    	<label class="lb"><?php echo L('password')?>：</label><input type="password" id="lock_password" class="input-text" size="20">
        <input type="submit" class="submit" value="&nbsp;" name="dosubmit" onclick="check_screenlock();return false;">
    </div></div>
</div>
<div class="header">
	<div class="logo lf"><a href="<?php echo $currentsite['domain']?>" target="_blank"><span class="invisible"><?php echo L('phpcms_title')?></span></a>

	</div>

    <div class="rt-col">
    	<div class="tab_style white cut_line text-r"><a href="javascript:;" onclick="lock_screen()"><img src="<?php echo IMG_PATH.'icon/lockscreen.png'?>"> <?php echo L('lockscreen')?></a>
  
       </div> 
     

     <div style="width: 310px;" class="header_case"><?php echo L('hello'),$admin_username?>  [<?php echo $rolename?>]<span>|</span>
		<a href="<?php echo U('public_logout') ?>">[<?php echo L('exit')?>]</a><span>|</span>
		<a href="<?php echo HOME_PAGE; ?>" target="_blank" id="site_homepage"><?php echo L('site_homepage')?></a><span>|</span>
		<a href="?m=Member" target="_blank"><?php echo L('member_center')?></a><span>|</span>
<!--		<a href="http://help. .cn/?/explore/" target="_blank">帮助中心</a><span>|</span>-->
<!--		<a href="http://www.dealswill.com/" target="_blank">官方网站</a>-->
	</div>

    </div>
    <div class="col-auto">

    	<div class="log white cut_line">
    	</div>


        <ul class="nav white" id="top_menu">
        <?php foreach ($top_menu as $_v): ?>
        	<li id="_M<?php echo $_v['id'] ?>" class="top_menu<?php if ($_v['id'] == 1): ?> on<?php endif;?>"><a href="javascript:_M('<?php echo $_v['id'] ?>','<?php echo U($_v['m'].'/'.$_v['c'].'/'.$_v['a'].$_v['data']); ?>')" hidefocus="true" style="outline:none;"><?php echo $_v['name']; ?></a></li>
        <?php endforeach; ?>
        </ul>
    </div>


</div>
<div id="content">
	<div class="col-left left_menu">
    	<div id="Scroll"><div id="leftMain"></div></div>
        <a href="javascript:;" id="openClose" style="outline-style: none; outline-color: invert; outline-width: medium;" hideFocus="hidefocus" class="open" title="<?php echo L('spread_or_closed')?>"><span class="hidden"><?php echo L('expand')?></span></a>
    </div>
	<div class="col-1 lf cat-menu" id="display_center_id" style="display:none" height="100%">
	<div class="content">
        	<iframe name="center_frame" id="center_frame" src="" frameborder="false" scrolling="auto" style="border:none" width="100%" height="auto" allowtransparency="true"></iframe>
            </div>
        </div>
    <div class="col-auto mr8">
    <div class="crumbs">
    <div class="shortcut cu-span">
    <!-- <a href="?m=content&c=create_html&a=public_index&fromhash=<?php echo $_SESSION['fromhash'];?>" target="right"><span><?php echo L('create_index')?></span></a> -->
    <a href="<?php echo U('Syscache/clear', array('fromhash' => FROMHASH)) ?>" target="right"><span><?php echo L('update_backup')?></span></a>
    <!-- 
    <a href="javascript:art.dialog({id:'map',iframe:'?m=admin&c=index&a=public_map', title:'<?php echo L('background_map')?>', width:'700', height:'500', lock:true});void(0);"><span><?php echo L('background_map')?></span></a><?php echo runhook('admin_top_left_menu')?>
     -->
    </div>
    <?php echo L('current_position')?><span id="current_pos"></span></div>
    	<div class="col-1">
        	<div class="content" style="position:relative; overflow:hidden">
                <iframe name="right" id="rightMain" src="?m=admin&c=index&a=public_main" frameborder="false" scrolling="auto" style="border:none; margin-bottom:30px" width="100%" height="auto" allowtransparency="true"></iframe>
                <div class="fav-nav">
					<div id="panellist">
						<?php foreach($adminpanel as $v) {?>
                        <span id='quickmenu_<?php echo $v['menuid']?>'><a onclick="paneladdclass(this);" target="right" href="<?php echo $v['url'].'&menuid='.$v['menuid'].'&fromhash='.session('FROMHASH');?>"><?php echo L($v['name'])?></a><a class="panel-delete" href="javascript:delete_panel(<?php echo $v['menuid']?>, this);"></a></span>
						<?php }?>
					</div>
					<div id="paneladd"></div>
					<input type="hidden" id="menuid" value="">
					<input type="hidden" id="bigid" value="" />
                    <div id="help" class="fav-help"></div>
				</div>
        	</div>
        </div>
    </div>
</div>
<div class="scroll"><a href="javascript:;" class="per" title="使用鼠标滚轴滚动侧栏" onclick="menuScroll(1);"></a><a href="javascript:;" class="next" title="使用鼠标滚轴滚动侧栏" onclick="menuScroll(2);"></a></div>
<div id="alert_sound" style="display:none;"></div>

<script type="text/javascript">
if(!Array.prototype.map)
Array.prototype.map = function(fn,scope) {
  var result = [],ri = 0;
  for (var i = 0,n = this.length; i < n; i++){
	if(i in this){
	  result[ri++]  = fn.call(scope ,this[i],i,this);
	}
  }
return result;
};

var getWindowSize = function(){
return ["Height","Width"].map(function(name){
  return window["inner"+name] ||
	document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
});
}
window.onload = function (){
	if(!+"\v1" && !document.querySelector) { // for IE6 IE7
	  document.body.onresize = resize;
	} else { 
	  window.onresize = resize;
	}
	function resize() {
		wSize();
		return false;
	}
}
function wSize(){
	//这是一字符串
	var str=getWindowSize();
	var strs= new Array(); //定义一数组
	strs=str.toString().split(","); //字符分割
	var heights = strs[0]-150,Body = $('body');$('#rightMain').height(heights);   
	//iframe.height = strs[0]-46;
	if(strs[1]<980){
		$('.header').css('width',980+'px');
		$('#content').css('width',980+'px');
		Body.attr('scroll','');
		Body.removeClass('objbody');
	}else{
		$('.header').css('width','auto');
		$('#content').css('width','auto');
		Body.attr('scroll','no');
		Body.addClass('objbody');
	}
	
	var openClose = $("#rightMain").height()+39;
	$('#center_frame').height(openClose+9);
	$("#openClose").height(openClose+30);	
	$("#Scroll").height(openClose-20);
	windowW();
}
wSize();
function windowW(){
	if($('#Scroll').height()<$("#leftMain").height()){
		$(".scroll").show();
	}else{
		$(".scroll").hide();
	}
}
windowW();
//站点下拉菜单
$(function(){
	//默认载入左侧菜单
	$("#leftMain").load("<?php echo U('public_menu_left', array('menuid' => 1)); ?>");
})
//隐藏站点下拉。
var s = 0;
var h;
function hidden_site_list() {
	s++;
	if(s>=3) {
		$('.tab-web-panel').hide();
		clearInterval(h);
		s = 0;
	}
}
function clearh(){
	if(h)clearInterval(h);
}
function hidden_site_list_1() {
	h = setInterval("hidden_site_list()", 1);
}

//左侧开关
$("#openClose").click(function(){
	if($(this).data('clicknum')==1) {
		$("html").removeClass("on");
		$(".left_menu").removeClass("left_menu_on");
		$(this).removeClass("close");
		$(this).data('clicknum', 0);
		$(".scroll").show();
	} else {
		$(".left_menu").addClass("left_menu_on");
		$(this).addClass("close");
		$("html").addClass("on");
		$(this).data('clicknum', 1);
		$(".scroll").hide();
	}
	return false;
});

function _M(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#bigid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
	if(menuid != 8) {
		$("#leftMain").load("<?php echo U('public_menu_left'); ?>", {menuid: menuid, limit: 25}, function(){
		   windowW();
		 });
	} else {
		$("#leftMain").load("<?php echo U('public_menu_left'); ?>", {menuid: menuid, limit: 25}, function(){
		   windowW();
		 });
	}
	//$("#rightMain").attr('src', targetUrl);
	$('.top_menu').removeClass("on");
	$('#_M'+menuid).addClass("on");
	$.get("<?php echo U('public_current_pos');?>", {menuid:menuid}, function(data){
		$("#current_pos").html(data);
	});
	//当点击顶部菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
	//显示左侧菜单，当点击顶部时，展开左侧
	$(".left_menu").removeClass("left_menu_on");
	$("#openClose").removeClass("close");
	$("html").removeClass("on");
	$("#openClose").data('clicknum', 0);
	$("#current_pos").data('clicknum', 1);
}
function _MP(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
	$("#rightMain").attr('src', targetUrl+'&menuid='+menuid+'&fromhash='+fromhash);
	$('.sub_menu').removeClass("on fb blue");
	$('#_MP'+menuid).addClass("on fb blue");
	$.get("<?php echo U('public_current_pos');?>", {menuid: menuid}, function(data){
		$("#current_pos").html(data+'<span id="current_pos_attr"></span>');
	});
	$("#current_pos").data('clicknum', 1);
}
function add_panel() {
	var menuid = $("#menuid").val();
	$.ajax({
		type: "POST",
		url: "<?php echo U('AdminPanel/add'); ?>",
		dataType : 'json',
		data: "menuid=" + menuid,
		success: function(ret){
            var _html = '';
			if (ret.status == 0) {
				alert(ret.info);
				return false;
			} else {
                _html += "<span id='quickmenu_"+ ret.info.menuid +"'><a onclick='paneladdclass(this);' target='right' href='"+ ret.info.url +"'>"+ ret.info.name +"</a><a class='panel-delete' href='javascript:delete_panel("+ ret.info.menuid +", this);'></a></span>";
				$("#panellist").append(_html);
			}
		}
	});
}
function delete_panel(menuid, id) {
	$.ajax({
		type: "POST",
		url: "<?php echo U('AdminPanel/delete'); ?>",
		data: "menuid[]=" + menuid + '&dosubmit=true&fromhash=' + fromhash,
		dataType : 'json',
		success: function(data) {
			if (data.status == 0) {
				alert(data.info);
				return false;
			} else {
				$("#panellist > #quickmenu_" + menuid).remove();
				return true;
			}
		}
	});
}

function paneladdclass(id) {
	$("#panellist span a[class='on']").removeClass();
	$(id).addClass('on')
}
setInterval("session_life()", 160000);
function session_life() {
	$.get("<?php echo U('public_session_life'); ?>");
}
function lock_screen() {
	$.get("<?php echo U('public_lock_screen');?>");
	$('#dvLockScreen').css('display','');
}
function check_screenlock() {
	var lock_password = $('#lock_password').val();
	if(lock_password=='') {
		$('#lock_tips').html('<font color="red"><?php echo L('password_can_not_be_empty');?></font>');
		return false;
	}
	$.getJSON("<?php echo U('public_login_screenlock');?>", { lock_password: lock_password},function(data){
		if (data.status == 1) {
			$('#dvLockScreen').css('display','none');
			$('#lock_password').val('');
			$('#lock_tips').html('锁屏状态，请输入密码解锁');
		} else {
			$('#lock_tips').html('<font color="red">' + data.info + '</font>');
		}
	});
}
$(document).bind('keydown', 'return', function(evt){check_screenlock();return false;});
(function(){
    var addEvent = (function(){
             if (window.addEventListener) {
                return function(el, sType, fn, capture) {
                    el.addEventListener(sType, fn, (capture));
                };
            } else if (window.attachEvent) {
                return function(el, sType, fn, capture) {
                    el.attachEvent("on" + sType, fn);
                };
            } else {
                return function(){};
            }
        })(),
    Scroll = document.getElementById('Scroll');
    // IE6/IE7/IE8/Opera 10+/Safari5+
    addEvent(Scroll, 'mousewheel', function(event){
        event = window.event || event ;  
		if(event.wheelDelta <= 0 || event.detail > 0) {
				Scroll.scrollTop = Scroll.scrollTop + 29;
			} else {
				Scroll.scrollTop = Scroll.scrollTop - 29;
		}
    }, false);

    // Firefox 3.5+
    addEvent(Scroll, 'DOMMouseScroll',  function(event){
        event = window.event || event ;
		if(event.wheelDelta <= 0 || event.detail > 0) {
				Scroll.scrollTop = Scroll.scrollTop + 29;
			} else {
				Scroll.scrollTop = Scroll.scrollTop - 29;
		}
    }, false);
	
})();
function menuScroll(num){
	var Scroll = document.getElementById('Scroll');
	if(num==1){
		Scroll.scrollTop = Scroll.scrollTop - 60;
	}else{
		Scroll.scrollTop = Scroll.scrollTop + 60;
	}
}

<?php
            $site_web_notice = model('setting')->getFieldByKey('site_web_notice', 'value');
?>

<?php if($site_web_notice == 1){ ?>
function playmp3(type){
    var ua = navigator.userAgent.toLowerCase();
    if(ua.match(/msie ([\d.]+)/)){
        jQuery('#alert_sound').html('<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95"><param name="AutoStart" value="1" /><param name="Src" value="static/play.mp3" /></object>');
    }
    else if(ua.match(/firefox\/([\d.]+)/)){
        jQuery('#alert_sound').html('<embed src="static/mp3/' + type + '.mp3"  type="audio/mp3" hidden="true" loop="false" mastersound></embed>');
    }
    else if(ua.match(/chrome\/([\d.]+)/)){
        jQuery('#alert_sound').html('<audio src="static/mp3/' + type + '.mp3" type="audio/mp3" autoplay="autoplay" hidden="true"></audio>');
    }
    else if(ua.match(/opera.([\d.]+)/)){
        jQuery('#alert_sound').html('<embed src="static/mp3/' + type + '.mp3" hidden="true" loop="false"><noembed><bgsounds src="static/play.mp3"></noembed>');
    }
    else if(ua.match(/version\/([\d.]+).*safari/)){
        jQuery('#alert_sound').html('<audio src="static/mp3/' + type + '.mp3" type="audio/mp3" autoplay="autoplay" hidden="true"></audio>');
    }
    else {
        jQuery('#alert_sound').html('<embed src="static/mp3/' + type + '.mp3" type="audio/mp3" hidden="true" loop="false" mastersound></embed>');
    }
}

    function callback() {
	$.getJSON('<?php echo U('Product/api/callback');?>', function(data){
		   if(!data) return false;
		    //console.log(data);
		 //    if(!data) return false;
		   showStickyNoticeToast(data);
	    }
	).error(function() {
	});
    	// body...
    }

    function showStickyNoticeToast(data) {
        $().toastmessage('showToast', {
             text     : data.body,
             sticky   : true,
             position : 'top-right',
             type     : 'notice',
             closeText: '',
             close    : function () {
             	console.log("toast is closed ...");
             }
        });
        playmp3(data.type);
    }

  setInterval('callback()',12000);

<?php }?>


</script>
</body>
</html>