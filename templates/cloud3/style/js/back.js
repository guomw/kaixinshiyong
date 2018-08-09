$(function() {
			$('.yz').validate({
				change: true,
				focus: function(data) {
					$(this).siblings('p').remove();
					$(this).after('<p class="hint suc">' + data + '<p>');
				},
				error: function(data) {
					$(this).siblings('p').remove();
					$(this).after('<p class="hint err">' + data + '<p>');
				},
				success: function(data) {
					$(this).siblings('p').remove();
					$(this).after('<p class="hint suc">' + data + '<p>');
				},
				null: function(data) {
					$(this).siblings('p').remove();
					$(this).after('<p class="hint err">' + data + '<p>');
				},
				status: function(status) {
					st = status;
					if($(this).find('.checkbox').attr('type')){
						var c = $(this).find('.checkbox').attr('checked') ? true : false;
					}else{
						var c = true;
					}
					if(status && c && t_st){
						$(this).find('[type=submit]').removeClass('dasied').attr('disabled',false);;
					}else{
						$(this).find('[type=submit]').addClass('dasied').attr('disabled',true);;
					}
				},
			});
			
			var st = false;  // 协议状态
			
				// .kCode 验证码输入域
				// .checkbox 协议点击位置 
				
			var t_st = false;  // 验证码状态
			
			
			$.ajax({
				url : '/WEB/js/ajax.php',
				success : function(data){
					$('.yz').find('.kCode').on('input propertychange',function(){
						if( $('.yz').find('.kCode').val() == data){
							t_st = true;
						}else{
							t_st = false;
						}
						var c = $(this).parents('.yz').find('.checkbox').attr('checked') ? true : false;
						var b = st && c && t_st;
						if(b){
							$(this).parents('form').find('[type=submit]').removeClass('dasied').attr('disabled',false);
						}else{
							$(this).parents('form').find('[type=submit]').addClass('dasied').attr('disabled',true);
						}
					});
				},
			});
			
			$('.yz [type="submit"]').attr('disabled',true);
			$('.yz').find('.checkbox').on('click',function(){
				var c = $(this).attr('checked') ? true : false;
				var b = st && c && t_st;
				if(b){
					$(this).parents('form').find('[type=submit]').removeClass('dasied').attr('disabled',false);
				}else{
					$(this).parents('form').find('[type=submit]').addClass('dasied').attr('disabled',true);
				}
			});
			
});			