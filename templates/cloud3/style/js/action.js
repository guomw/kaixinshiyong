$(function() {
	var b_btn = false; // 如果正在运动取消事件
	setTimeout(function() {
		b_btn = false;
		$('#big_img').animate({
			height: 30
		},function(){
			b_btn = true;
		});
	}, 500);

	$('#big_img').hover(function() {
		if (!b_btn) return;
		b_btn = false;
		$(this).animate({
			height: 250
		}, function() {
			b_btn = true;
		});
	}, function() {
		if (!b_btn) return;
		b_btn = false;
		$(this).animate({
			height: 30
		}, function() {
			b_btn = true;
		});
	});
});


$(function() {
	// IE6 兼容
			$('a').on('focus',function(){
				$(this).blur();
			});	
	
	/*滚动*/
	$('#slide-scroll').removeClass('dn');
	$(window).on('scroll',function(){
		var t = $(this).scrollTop();
		var s = $('#slide-scroll');
		
		var h = $(this).height() / 2 - s.height() / 4 + t;
		s.css({
			'-webkit-transition' : '.1s',
			'-moz-transition' : '.1s',
			'-ms-transition' : '.1s',
			'transition' : '.1s'
		});
		s.css('top',h);
	});
	
	$('#slide-scroll .yq').on('mouseover',function(){
		$(this).siblings('.yq').find('b').addClass('dn');
		$(this).find('b').removeClass('dn');
	});
	$('#slide-scroll .yq').on('mouseout',function(){
		$(this).siblings('.yq').find('b').addClass('dn');
	});

	
	

	/* slide */
	$('#tab .t a').mouseover(function() {
		$(this).addClass('active').siblings().removeClass('active');
		$('#tab .c ul').eq($(this).index()).removeClass('dn').siblings('ul').addClass('dn');
	});

	/* banner */
	var now = 0;
	$('#bar .small a').on('click', function() {
		now = $(this).index();
		$('#bar .small a').eq(now).addClass('active').siblings('a').removeClass('active');
		$('#bar .big li').eq(now).fadeIn(300).siblings('li').fadeOut(300);
	});
	var bar_timer = null;
	$('#bar').on('mouseover', function() {
		clearInterval(bar_timer);
	}).on('mouseout', bar_auto_play);

	function bar_auto_play() {
		bar_timer = setInterval(function() {
			now++;
			if (now > $('#bar .small a').length - 1) {
				now = 0;
			}
			$('#bar .small a').eq(now).addClass('active').siblings('a').removeClass('active');
			$('#bar .big li').eq(now).fadeIn(300).siblings('li').fadeOut(300);
		}, 3000);
	}
	bar_auto_play();


});



	$(function() {
		$('.l-box .r .box a').hover(function() {
			$(this).parents('.box').addClass('active').find('.btn b').addClass('active');
		}, function() {
			$(this).parents('.box').removeClass('active').find('.btn b').removeClass('active');
		});
		/* 鼠标 */
		$('.u-tab .t a').on('click', function() {
			$(this).addClass('active').siblings('a').removeClass('active').parents('.t').next('.c').find('.c-l').eq($(this).index()).removeClass('dn').siblings('.c-l').addClass('dn');
		});
	});
	$(function() {
		// 跑马灯
		$('.tj-pmd .box').eq(0).css('margin', '0');
		var w = $('.tj-pmd .box').eq(0).width() + 1 + parseInt($('.tj-pmd .box').eq(0).css('padding-left')) + parseInt($('.tj-pmd .box').eq(0).css('padding-right'));
		var l = $('.tj-pmd .box').length;
		var h = $('.tj-pmd').html();
		var now = 0;
		var pmd_b_btn = true;
		$('.tj-pmd').html(h + h);
		$('.tj-pmd').css('width', w * l * 2);

		function move_to(ele) {
			ele.animate({
				left: -w * now
			}, function() {
				if (now === l) {
					now = 0;
					$('.tj-pmd').css('left', 0);
				}
				pmd_b_btn = true;
			});
		}
		var pmd_timer = null;
		pmd_timer = setInterval(function() {
			if (!pmd_b_btn) return;
			pmd_b_btn = false;
			now++;
			move_to($('.tj-pmd'));
		}, 1000);
		$('.tj-sy').on('mouseover', function() {
			clearInterval(pmd_timer);
		}).on('mouseout', function() {
			pmd_timer = setInterval(function() {
				if (!pmd_b_btn) return;
				pmd_b_btn = false;
				now++;
				move_to($('.tj-pmd'));
			}, 3000);
		});
		$('.tj-sy .s .prev').on('click', function() {
			if (!pmd_b_btn) return;
			pmd_b_btn = false;
			now--;
			if (now < 0) {
				now = 0;
			}
			move_to($('.tj-pmd'));
		});
		$('.tj-sy .s .next').on('click', function() {
			if (!pmd_b_btn) return;
			pmd_b_btn = false;
			now++;
			move_to($('.tj-pmd'));
		});
	});
