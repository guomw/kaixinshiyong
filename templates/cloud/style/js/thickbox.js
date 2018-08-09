ResumeError=function (){return true;}
window.onerror = ResumeError;
(function($){
$.fn.fbmodal = function(options, callback){  
  var defaults = {  
        title: "FB Modal",  //标题
       cancel: "取消",//取消按钮
         okay: "确定",//确定按钮
   okaybutton: true,//确定按钮：true 显示 false 隐藏
 cancelbutton: true,//取消按钮：true 显示 false 隐藏
      buttons: true,//全部按钮：true 显示 false 隐藏
      opacity: 0.0,//透明度
	  fadeout: true,//淡化退出或载入 ：true 开启 false 关闭
	    fixed: true,//是否固定：true 漂浮 false 固定
 overlayclose: true,//点击框外退出 ：true 开启 false 关闭
   overbutton: true,//底部按钮 ：true 开启 false 关闭
   cancelexit: false,//外部进行关闭
   hwoverflow: true,//滚动条：true 显示 false 隐藏
      exitcid: "0",//外部关闭标签 0 关闭 此标签为ID标签
	  exitpost: "exitcid_box",//外部提交标签 false 关闭 此标签为ID标签
     modaltop: "30%",//距离顶部位置
   modalwidth: "",//框宽度
   modalheight:""//框高度
  };  
var options = $.extend(defaults, options);
if(options.cancelexit == true){ 
    if(options.fadeout == true){
    $("#fbmodal").fadeOut( function(){
    $("#fbmodal").remove();
    $("#fbmodal_overlay").removeClass("fbmodal_overlay");
    });
    }else{
    $("#fbmodal").remove();
    $("#fbmodal_overlay").removeClass("fbmodal_overlay");
    }
}else{
   if(options.exitcid == "0"){
	 htmlcid = 'id="ok"';
   }else{
     if(options.exitpost != "exitcid_box"){		
	    if(options.title == "积分试用"){
		htmlcid = 'onClick="return '+options.exitpost+'(\''+options.exitcid+'\')"';
		}else{
		htmlcid = 'onClick="'+options.exitpost+'(\''+options.exitcid+'\')"';	
		}
	 }else{
		htmlcid = 'onClick="exitcid_box(\''+options.exitcid+'\')"'; 	 
	 }
   }

if(options.title == "积分试用"){
var htmlaid = '<div class="button_outside_border_blue_jifen">\
<a class="button_inside_border_blue_jifen" id="okay" href="/item/item_course/'+options.exitcid+'" target="_blank" '+htmlcid+'>\
</a>\
</div>';	
}else{
var htmlaid = '<div class="button_outside_border_blue" '+htmlcid+'>\
<div class="button_inside_border_blue" id="okay">\
</div>\
</div>';
}
if(options.overbutton == true){
var htmlbutton=' \
<div class="footer"> \
<span class="error_mas_out_off" id="error_mas_out">订单号有误!</span> \
<div class="right">'+htmlaid+'<div class="button_outside_border_grey" id="close">\
<div class="button_inside_border_grey" id="cancel">\
</div>\
</div>\
</div>';
}else{
var htmlbutton='';
	}
var fbmodalHtml=' \
<div id="fbmodal" > \
<div class="popup"> \
<div title="关闭" class="fancybox-item fancybox-close" id="close"></div> \
<table> \
<tbody> \
<tr> \
<td class="tl"/><td class="b"/><td class="tr"/> \
</tr> \
<tr> \
<td class="b"/> \
<td class="body"> \
<div class="title">\
</div> \
<div class="container">\
<div class="content">\
</div>'+htmlbutton+'\
<div class="clear">\
</div>\
</div> \
</div>\
</td> \
<td class="b"/> \
</tr> \
<tr> \
<td class="bl"/><td class="b"/><td class="br"/> \
</tr> \
</tbody> \
</table> \
</div> \
</div>';
   
   var preload = [ new Image(), new Image() ]
    $("#fbmodal").find('.b:first, .bl, .br, .tl, .tr').each(function() {
      preload.push(new Image())
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1')
    })	
    var dat=this.html();
	$("body").append(fbmodalHtml);
	$("#fbmodal .content").append('<div class="loading"><img src="images/loading.gif"/></div>');
    $("#fbmodal").css("top",options.modaltop);	
	if(options.okaybutton == false || options.buttons == false){
	$("#fbmodal #ok").hide();
	}
    if(options.cancelbutton == false || options.buttons == false){
	$("#fbmodal #close").hide();
	}
	$("#fbmodal .title").append(options.title);
    $("#fbmodal #okay").append(options.okay);
    $("#fbmodal #cancel").append(options.cancel);	
	if(options.hwoverflow == true){
	$("#fbmodal .content").append(dat).css({height:options.modalheight,width:options.modalwidth,overflow:"auto"});
	}else{
	$("#fbmodal .content").append(dat).css({height:options.modalheight,width:options.modalwidth});	
	}
	$("#fbmodal .loading").remove();
	$("body").append('<div id="fbmodal_overlay" class="fbmodal_hide"></div>');
	$("#fbmodal_overlay").addClass("fbmodal_overlay").fadeTo(0,options.opacity);    
	var windowWidth=$(window).width();	
	var fbmodalWidth=$("#fbmodal").width();
	var fbWidth=windowWidth / 2 - fbmodalWidth / 2;
    $("#fbmodal").css("left",fbWidth);
    $(window).bind("resize", function(){  
    var windowWidth=$(window).width();	
	var fbmodalWidth=$("#fbmodal").width();
	var fbWidth=windowWidth / 2 - fbmodalWidth / 2;
    $("#fbmodal").css("left",fbWidth);
    });     
	if(options.close == true){ 
      if(options.fadeout == true){
      $("#fbmodal").fadeOut( function(){
      $("#fbmodal").remove();
      $("#fbmodal_overlay").removeClass("fbmodal_overlay");
      });
      }else{
      $("#fbmodal").remove();
      $("#fbmodal_overlay").removeClass("fbmodal_overlay");
      }
    }
    if(options.fixed == true){
    $("#fbmodal").css("position","fixed");
    }else{
    $("#fbmodal").css("position","absolute");
    }
	if(options.overlayclose == true){
    var overlay="ok, #close, .fbmodal_hide"
    }else{
    var overlay="ok, #close"
    }
	  $("#"+overlay).click( function(){
	  if(options.fadeout == true){
      $("#fbmodal").fadeOut( function(){
      $("#fbmodal").remove();
      $("#fbmodal_overlay").removeClass("fbmodal_overlay");
      });
      }else{
      $("#fbmodal").remove();
      $("#fbmodal_overlay").removeClass("fbmodal_overlay");
      }
      });
	$("#fbmodal #okay").click(function() {
	var okay=1;
    callback(okay);
	});
	$("#fbmodal #cancel").click(function() {
	
	var cancel=2;
    callback(cancel);
	});
 }
}
})(jQuery); 