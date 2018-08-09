function login_popup_click(){
$('.content #register_popup').removeAttr('class');
$('.content #login_popup').attr('class','cur');
$('.content #register_panel').addClass('Hide');
$('.content #login_panel').removeClass('Hide');
$('.content #type_id').val('login');
$('.content #login_img_l').attr('src','/public/code');
}
function register_popup_click(){
$('.content #login_popup').removeAttr('class');
$('.content #register_popup').attr('class','cur');
$('.content #login_panel').addClass('Hide');
$('.content #register_panel').removeClass('Hide');
$('.content #type_id').val('register');
$('.content #login_img_r').attr('src','/public/code');
}
function Login_register_box(exitcid){
		var type_id= $('.content #type_id').val();
		if(type_id=='login'){		
		var buy_name_l= $('.content #buy_name_l').val();
		var buy_pass_l= $('.content #buy_pass_l').val();
		var code_l= $('.content #code_l').val();
		$.ajax({
            url: '/index.php?m=login&a=buy_veri',
            type: 'POST',
            data:{buy_name:buy_name_l,buy_pass:buy_pass_l,code:code_l},
			dataType: 'html',
            success: function(html){
			   if(html=='1'){	
			   window.location.reload();
			   }else if(html=='2'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('登录失败，用户名或密码错误！');
			   }else if(html=='0'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('登录失败，请检查验证码是否正确！');
			   }else{
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('登录失败,请联系客服！');
			   }
			}
            });
		}else if(type_id=='register'){		
		var buy_name_r= $('.content #buy_name_r').val();
		var buy_pass_r= $('.content #buy_pass_r').val();
		var buy_email_l= $('.content #buy_email_l').val();
		var code_r= $('.content #code_r').val();
		$.ajax({
            url: '/index.php?m=register&a=buy_create',
            type: 'POST',
            data:{buy_name:buy_name_r,buy_pass:buy_pass_r,buy_emall:buy_email_l,code:code_r},
			dataType: 'html',
            success: function(html){
			   if(html=='1'){
			   window.location.reload();
			   }else if(html=='-3'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('请填写5-25位字符的帐号');
			   }else if(html=='-4'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('6-25个字符的密码格式');
			   }else if(html=='-2'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('邮箱格式不正确！');
			   }else if(html=='-1'){
		       $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('请检查验证码是否正确！');
			   }else if(html=='0'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('注册失败,请联系客服！');
			   }else if(html=='2'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('用户名、密码或邮箱不能为空');
			   }else if(html=='4'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('用户名已被注册！');
			   }else if(html=='3'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('邮箱已被其它用户使用！');
			   }else if(html=='5'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('请勿重复注册,请联系客服.');
			   }else{
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('用户名、密码或邮箱不能为空');
			   }			   
			}
            });			
		
		}		
}
function for_input_login_box(){
        $("#Login_register").fbmodal({
            title: "登录/注册",  //标题
           cancel: "取消",//取消按钮
             okay: "确定",//确定按钮
       okaybutton: true,//确定按钮：true 显示 false 隐藏
     cancelbutton: true,//取消按钮：true 显示 false 隐藏
          buttons: true,//全部按钮：true 显示 false 隐藏
          opacity: 0.55,//透明度
	      fadeout: false,//淡化退出或载入 ：true 开启 false 关闭
		    fixed: true,//是否固定：true 漂浮 false 固定
     overlayclose: false,//点击框外退出 ：true 开启 false 关闭
	   overbutton: true,//底部按钮 ：true 开启 false 关闭
          exitcid: "Login_register",//外部关闭标签 false 关闭 此标签为ID标签
		 exitpost: "Login_register_box",
		 modaltop: "25%",//距离顶部位置
       modalwidth: "300", //框宽度
	    modalheight: "auto" //框高度
        });  
}