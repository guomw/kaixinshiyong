function banner_play(ele_bar, ele_ico, ele_char, speed) {
	var iNow = 0;
	var This = this;
	small_banner_play_auto();

	var timer = null;

	function small_banner_play() {
		ele_bar && ele_bar.fadeOut(100).eq(iNow).fadeIn(300);
		ele_ico && ele_ico.removeClass('active').eq(iNow).addClass('active');
		if (ele_char) {
			ele_char.fadeOut(100).eq(iNow).fadeIn(300);
		}
	}
	function small_banner_play_auto() {
		clearInterval(ele_bar.timer);
		ele_bar.timer = setInterval(function() {
			iNow++;
			if (iNow > ele_bar.length - 1) {
				iNow = 0;
			}
			small_banner_play(iNow);
		}, (speed ? speed : 2000));
	}
	ele_ico && ele_ico.on('mouseover', function() {
		iNow = $(this).index();
		small_banner_play($(this).index());
		small_banner_play_auto();
	});
}

$(function() {
	$('[z-select]').each(function(i, n) {
		var This = $(this);
		var title = $(this).find('.title').length ? $(this).find('.title') : $(this).find('p');
		title.off('click').on('click', function(ev) {
			var titleThis = $(this);
			var opts = $(this).parents('[z-select]').find('.options').length ? $(this).parents('[z-select]').find('.options') : $(this).next();
			opts.hasClass('dn') ? opts.removeClass('dn') : opts.addClass('dn');
			opts.children().off('click').on('click', function(ev) {
				var id = $(this).attr('z-id');
				var txt = $(this).attr('z-txt');
				titleThis.attr({
					'z-id': id,
					'z-txt': txt
				});
				titleThis.attr('z-html') ? titleThis.find(titleThis.attr('z-html')).html(txt) : titleThis.html(txt);
				opts.addClass('dn');
				// 获取一个函数
				//				var fn = This.attr('z-select');
				//				var FnIn = (typeof eval(fn)).toLowerCase();
				//				if(fn && FnIn == 'function'){
				//					eval(fn).call(This,id,txt);
				//				}
				ev.stopPropagation();
			});
			ev.stopPropagation();
		});
	});
	$(window).off('click').on('click', function() {
		$('[z-select]').find('.options').length ? $('[z-select]').find('.options').addClass('dn') : $('[z-select]').find('ul').addClass('dn');;
	});
	
	// logo_bar
	banner_play($('#logo_bar .box'),false,false,3000);
	
	// menu
	$('.all_type').not('.isIndex').hover(function(){
		$(this).find('.allt_list').removeClass('dn');
	},function(){
		$(this).find('.allt_list').addClass('dn');
	});
});

$(function() {

	$(window).on('scroll', function() {
		var t = $(this).scrollTop();
		$('#slide').css({
			'top': t + ($(window).height() - $('#slide').height()) / 2,
			'margin-top': 0
		});
	});

});