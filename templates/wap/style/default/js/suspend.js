$(function() {
		$('.details .deta-title li').click(function() {
			$('.details .deta-box').removeClass('d-block').addClass('d-none');
			$('.details .deta-box').eq($(this).index()).addClass('d-block');
			/*  焦点  */
			$('.details .deta-title li').removeClass('deta-focus');
			$(this).addClass('deta-focus');
		});
	
	
	
	
	/* 设置单独样式控制 */
	var susW = $('.suspend').width();
	$('.suspend').attr('data-boolean', true);
	/* 点击 */
//	$('.suspend ul .icon').click(function() {
//		var susRightValue = susW - $(this).width();
//		if ($(this).parent('ul').parent('.suspend').attr('data-boolean') == 'true') {
//			$(this).removeClass('undefined').addClass('colse');
//			$(this).parent('ul').parent('.suspend').animate({
//				'right': -susRightValue + 'px'
//			}, 100);
//			$(this).parent('ul').parent('.suspend').attr('data-boolean', false);
//		} else {
//			$(this).removeClass('colse').addClass('undefined');
//			$(this).parent('ul').parent('.suspend').animate({
//				'right': 0 + 'px'
//			}, 100);
//			$(this).parent('ul').parent('.suspend').attr('data-boolean', true)
//		}
//	});
	$('.suspend .set-top').click(function() {
		var timer = null;
		timer = setInterval(function() {
			var top = $(window).scrollTop();
			top -= top / 2;
			$(window).scrollTop(top);
			if ($(window).scrollTop() <= 1) {
				$(window).scrollTop(0);
				clearInterval(timer);
			}
		}, 30);
	});
});