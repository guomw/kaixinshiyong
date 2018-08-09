$(function() {
	$('.content .l-title li').click(function() {
		$('.content .l-title li a').removeClass('focus');
		$(this).children('a').addClass('focus');
		$('.box .list').addClass('d-none');
		$('.box .list:eq(' + $(this).index() + ')').removeClass('d-none');
	});
});