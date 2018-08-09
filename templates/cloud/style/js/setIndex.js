//悬浮菜单
function sidebar(){
	var side = getClass(document,'sidebar1')[0];
	var sideT = getClass(document,'sidebar2')[0];
	var aLi = side.getElementsByTagName('li');
	var wrap = getClass(document,'wrap')[0];
	var shop = getClass(document,'shop_one');
	function scroll(){
		var scrollTop = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
		return scrollTop;
	}	
	function scrollTop(){
		onscrollT = scroll()+parseInt((document.documentElement.clientHeight-side.offsetHeight)/2);
		return onscrollT;
	}
	window.onscroll = function(){
		$('.shop_one').each(function(i,n){
			if($('.shop_one:eq('+i+')').offset().top-100 <= scroll()){
				$('.sidebar1 li').removeClass('active');
				$('.sidebar1 li a').removeClass('a_link');
				$('.sidebar1 li:eq('+i+')').addClass('active');
				$('.sidebar1 li a:eq('+i+')').addClass('a_link');
			}
		});	
		if(scrollTop() >= 1000){ move(sideT,{'opacity':100},10); }else{ move(sideT,{'opacity':0},10); }
		if(scrollTop() >= 1000){ move(side,{'opacity':100,'top': scrollTop() },5); }else{ move(side,{'opacity':0},10); }
	}
}
//悬浮菜单点击效果
function sClick(obj,sw)
{
	var now = 0;
	function setW() {
		var oSidebar = $(obj).parent();
		var d = $(window).width();
		var w = $('.wrap').width();
		var o = oSidebar.width();
		oSidebar.css('right', (d - w) / 2 - (o + 20) + 'px');
	}
	
	setW();
	
	$(obj).click(function(){
		now = 1;
		function str(){
			if(sw||now==1)return;
			$(obj).children('a').removeClass('active');
			$(this).children('a').addClass('active');		
		}
		str();
		var scroll = $('.shop_one:eq('+$(this).index()+')').offset().top-100;
		if(sw){ scroll = 0; }
		$('html,body').animate({
			'scrollTop':scroll
		},300);
	});
	
	$(window).resize(function(){
		setW();
	});
}
	//首页顶部移入效果	
function setTopMouseover(){
	$('.header_right li').mouseover(function(){
		$(this).children('.top_drop_down').css('display','block');
	});	
	
	$('.header_right li').mouseout(function(){
		$(this).children('.top_drop_down').css('display','none');
	});	
}	
//商品移入效果
function shopsMouse(obj)
{
	$(obj).mouseover(function(){
		var h = $(this).height()-$(this).children('.order_size').height();
		var w = $(this).width()-$(this).children('.order_size').width();	
		$(obj).children('.order_size').css({ 'top': h/2,'left':w/2});
		$(this).children('.small_shade').css('display','block');
		$(this).children('.order_size').css('display','block');
	});
	$(obj).mouseout(function(){
		$(this).children('.small_shade').css('display','none');
		$(this).children('.order_size').css('display','none');
	});
}		


function sMouBox(Main,setBox)
{
	$(Main).mouseover(function(){
		$(setBox).css('display','block');
	});
	$(Main).mouseout(function(){
		$(setBox).css('display','none');
	});
	
	$(setBox+'>li').each(function(i,n){
		var mTxt = $(Main+'>a:eq(0)').text();
		var oTxt = $(this).children('a').text();
		$(this).click(function(){
			if($(this).children().text() == oTxt){
				$(this).children().text(mTxt);
				$(Main+'>a:eq(0)').text(oTxt);
			}else{
				$(this).children().text(oTxt);
				$(Main+'>a:eq(0)').text(mTxt);
			}
			$(setBox).css('display','none');
		});
	});
}


//首页中间展示大图制作
function bannerPlay(btn,obj){
	var now = 0;
	$(btn+':eq(0)').addClass('btnHover');
	/*
	function play(){
		$(obj+':eq('+now+')').css('opacity',0).animate({ 'opacity' : '1', },300);
		$(btn).removeClass('btnHover'); $(btn+':eq('+now+')').addClass('btnHover');
		$(obj).css('z-index','0'); $(obj+':eq('+now+')').css('z-index','1');
	}*/
	function play(){
		$(obj).animate({ 'top' : -now*$(obj).height() },300);
		$(btn).removeClass('btnHover'); $(btn+':eq('+now+')').addClass('btnHover');
	}
	function autoPlay(){ now++; if(now == $(btn).length){ now = 0; } play(); }
		$(btn).each(function(i,n){
		$(this).click(function(){ if(i == now)return; now = i; play($(this)); });
	});
	timer = setInterval(autoPlay,3000);
	$(obj).parent().parent().mouseover(function(){ clearInterval(timer); });
	$(obj).parent().parent().mouseout(function(){ timer = setInterval(autoPlay,3000); });
}

//商城公告部分
function showSign(obj,sClass){
	$(obj).parent().next().children('ul:eq(0)').addClass('display_block');
	$(obj).mouseover(function(){
		$(obj).removeClass(sClass);$(this).addClass(sClass);
		$(this).parent().next().children('ul').removeClass('display_block');
		$(this).parent().next().children('ul:eq('+$(this).index()+')').addClass('display_block');
	});
}

//左侧	
function topMove(){
	$('.menu_wrap:eq(0)').css({'padding-top':1+'px','margin-top':-1+'px'});
	
	$('.all_menu > font').mouseover(function(){
		$('.z_index:eq(0)').addClass('display_block menu_list_bg');
		$('.banner_menu').addClass('display_block');
	});
	$('.all_menu > font').mouseout(function(){ 
		$('.banner_menu').removeClass('display_block');
	});
	$('.banner_menu').mouseout(function(){
		$(this).removeClass('display_block');
	});
	$('.menu_content').mouseover(function(){
		$('.menu_list:eq(0)').children('p').removeClass('right_icon');
		$('.banner_menu').addClass('display_block');$(this).addClass('display_block'); });
	$('.menu_content').mouseout(function(){
		$('.banner_menu').removeClass('display_block');
		$(this).removeClass('display_block');
	});
	$('.menu_w_none').mouseover(function(){
		$('.banner_menu').addClass('display_block');		
	});	
	$('.menu_w_none').mouseout(function(){
		$('.banner_menu').removeClass('display_block');	
	});
	$('.menu_wrap').mouseover(function(){
		$('.menu_list:eq(0)').children('p').removeClass('right_icon');
		$('.banner_menu').addClass('display_block');		
		$('.menu_content').addClass('display_block');
	});
	$('.menu_wrap').mouseout(function(){
		$('.menu_list:eq(0)').children('p').addClass('right_icon');
		$('.menu_content').removeClass('display_block');
	});	
}			
//热门商品信息显示
function setHorComShow()
{
	$('.hot_show_wrap:eq(0)').css('display','block');
	$('.hot_com_menu span a').click(function(){
		$('.hot_com_menu span a').removeClass('hot_com_menu_active');
		$(this).addClass('hot_com_menu_active');
		$('.hot_show_wrap').css('display','none');
		$('.hot_show_wrap:eq('+$(this).parent().index()+')').css('display','block');
	});
}	
//导航点击样式
function setShopsStyle(obj,sClass){
	$(obj+' li a:eq(0)').addClass(sClass);
	$(obj).parent().next().children('.shop_one_list:eq(0)').addClass('display_block');
	$(obj+' li a').click(function(){
		$(obj+' li a').each(function(i,n){ $(this).attr('index',i); });		
		$(obj+' li a').removeClass(sClass); $(this).addClass(sClass);
		$(obj).parent().next().children('.shop_one_list').removeClass('display_block');
		$(obj).parent().next().children('.shop_one_list:eq('+$(this).attr('index')+')').addClass('display_block');
	});
}