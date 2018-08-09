function bannerMove() {
	var sLen = $('.big li').length;
	var wW = $(window).width();
	var now = 0;
	var timer = null;
	$('.big li a img').css('width', wW + 'px');
	$('.big').css('width', wW * sLen + 'px');

	function move() {
		$('.small li').removeClass('b-s-focus');
		$('.small li:eq(' + now + ')').addClass('b-s-focus');
		$('.big').animate({
			'left': -now * wW + 'px',
		}, 300);
	}
	move();
	$('#banner').on("swipeleft",function(ev){
		var ev = ev || event;

  		now++;
  		move();
		clearInterval(timer);
  		timer = setInterval(autoPlay, 3000);

  		return false;
	});

	$('#banner').on("swiperight",function(){
  		var ev = ev || event;
  		ev.preventDefault();

  		now--;
  		move();


		clearInterval(timer);
  		timer = setInterval(autoPlay, 3000);
  		return false;
	});

	function autoPlay(){
		if (now > sLen-1) {
			now = 0;
		}
		if( now < 0 ){
			now = sLen-1;
		}
		move();
		now++;
	}

	clearInterval(timer);
	timer = setInterval(autoPlay, 3000);
}