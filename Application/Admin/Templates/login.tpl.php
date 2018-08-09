<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>管理员登陆</title>
<link href="<?php echo CSS_PATH; ?>login.css" rel="stylesheet" type="text/css" id="skin"/>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.min.js"></script>
<script type="text/javascript">
$(function(){
	 $("#username").focus();
	 $("#username").keydown(function(event){
	 	if(event.keyCode==13){	 		
			login();
		}
	 })
	 $("#password").keydown(function(event){
	 	if(event.keyCode==13){
			login();
		}
	 })	
	 $("#verify").keydown(function(event){
	 	if(event.keyCode==13){
			login();
		}
	 }) 
})

function refresh_verify() {
	$("img#verify_img").attr('src', 'index.php?m=api&c=verify&a=create&_t=' + Math.random());
}

function login() {
	var errorMsg = "";
	var loginName = document.getElementById("username");
	var password = document.getElementById("password");
	var verify = document.getElementById("verify");

	if(!loginName.value){
		errorMsg += "&nbsp;&nbsp;用户名不能为空!";
	}
	if(!password.value){
		errorMsg += "&nbsp;&nbsp;密码不能为空!";
	}
	if(!verify.value){
		errorMsg += "&nbsp;&nbsp;验证码不能为空!";
	}
	if(errorMsg != ""){
		$(".login_info2").html(errorMsg);
		$(".login_info2").addClass('error');
		$(".login_info2").show();
		refresh_verify();

	}
	else{
		$(".login_info2").show();
		$(".login_info2").html("&nbsp;&nbsp;正在登录...");
		$.post("<?php echo U(ACTION_NAME) ?>",
			  {"username":loginName.value,"password":password.value,"verify":verify.value},
			  function(result){
				  if(result == null){
				  		$(".login_info2").addClass('error');
					  	$(".login_info2").html("&nbsp;&nbsp;登陆异常...");
					  	return false;
				  }
				  if(result.status== 1){
				  		$(".login_info2").removeClass('error');
				  	  	$(".login_info2").html("&nbsp;&nbsp;" + result.info);
					  	window.location = result.url;  
				  } else {
				  		$(".login_info2").addClass('error');
				  	 	$(".login_info2").html("&nbsp;&nbsp;"+result.info);
				  	 	refresh_verify();
				  }				  
			  },"json");
	}
}
</script>
<style type="text/css">	
#cursorMessageDiv {
	position: absolute;
	z-index: 99999;
	border: solid 1px #cc9933;
	background: #ffffcc;
	padding: 2px;
	margin: 0px;
	display: none;
	line-height:150%;
}
.white{
	color:#ffffff;
}
.white a{
	color:#ffffff;
	text-decoration:underline;
}
.white a:hover{
	color:#ffffff;
	text-decoration:underline;
}
.error {
	color: #AA0000;
}

.login_verify{
	padding: 28px 0 0 80px;
}

#verify{
	width: 97px;
	height: 35px;
	line-height: 35px;
	padding: 0 0 0 3px;
	font-size: 16px;
}

.login_info2{
	top: 545px;
	left: 658px;
}
</style>

</head>
<body >
	<div class="login_main">
		<div class="login_top">
		</div>
		<div class="login_middle">
			<div class="login_middleleft"></div>
			<div class="login_middlecenter">
				<form id="loginForm" action="login.do" class="login_form" method="post">
					<div class="login_user"><input type="text" id="username"></div>
					<div class="login_pass"><input type="password" id="password"></div>
					<div class="login_verify">
					<input type="text" id="verify"/>
					<img class="float_right border_color_dbe6f4 border_solid1" src="<?php echo U('Api/Verify/create');?>" alt="" title="点击切换验证码！" width="100" height="37" onclick="javascript:this.src='index.php?m=Api&c=Verify&a=create&t='+Math.random();" id="verify_img" style="vertical-align: bottom; cursor: pointer;"/>
			
		</div>
		
					<div class="clear"></div>
					<div class="login_button">
						<div class="login_button_left"><input type="button" onclick="login()"/></div>
						<div class="login_button_right"><input type="reset" value=""/></div>
						<div class="clear"></div>
					</div>
					</form>
					<div class="login_info" style="display:none;">loading...</div>
					<div class="login_info2" style="display:none;"></div>
			</div>
			<div class="login_middleright"></div>
			<div class="clear"></div>
		</div>
		<div class="login_bottom">
<!-- 			<div class="login_copyright">
			Adalways Inc<br/><br/>
			官方网址：<span class="white"><a href="http://www.dealswill.com" target="_blank">http://www.dealswill.com</a></span>
			</div> -->
		</div>
	</div>
</body>
</html>