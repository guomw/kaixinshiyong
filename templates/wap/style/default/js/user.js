$(function(){
	$('.oauth-btn').click(function(){
		var aH = parseInt($('.oauth-list').css('height'));
		var aT = parseInt($('.oauth-list').css('top'));
		if(aT < 0){
			$('.oauth-list').animate({
			top:'0px'
		},200);
		}else{
			$('.oauth-list').animate({
				top:-aH+'px'
			},200);
		}
	});
});