var member = (function() {
		return {
			/* 获取登录状态 */
			init:function() {
				if (typeof(userinfo) != 'object') {
					return false;
				}		
				if(userinfo.userid > 0) {
					var str = '';
					var url = '';
					
					if (userinfo.nickname != '') {
							url = site.site_root + '/index.php?m=member&c=profile&a=index';
					}else{
						if (userinfo.modelid == 1) {
							url = site.site_root + '/index.php?m=member&c=profile&a=infomation';
						}else{
							url = site.site_root + '/index.php?m=member&c=merchant&a=complete';
						};
					}

					if(userinfo.nickname =='') userinfo.nickname = "新会员 " + userinfo.userid;

					str += '<span class="fl">'+'<font style="float:left;">您好，</font>'+'<a style="color:#ff6c00;margin-right:10px;" class="name_hover" href="'+url+'">' + userinfo.nickname + '</a>欢迎来到'+site.webname+'！</span>';
					str += '<span><font style="float:left;">您有未读提醒</font>( <a style="color:#ff6c00;" href="'+ site.site_root + '/index.php?m=member&c=announce&a=announce&type=1" style="margin-left:5px;">'+site.message+'</a> )条</span>';
					str += '<span><a href="javascript:;" id="logout" style="cursor:pointer;"> [退出] </a></span>';	
					$("#uBar").html(str);
				}
			},
			/* 登录操作 */
			login:function() {
				var div_form = [];
				div_form.push('<form class="login-form"><div style="display:none;" class="login-tipMsg"></div>');
				div_form.push('<div class="login-uName"><p>账号：</p><input type="text" name="account" value=""></div>');
				div_form.push('<div class="login-pwd"><p>密码：</p><input type="password" name="password" value=""></div>');
				div_form.push('<div class="login-other clearfix"><span class="login-otherL"><a class="login-QQlogin" href="'+qq_url+'"><img alt="QQ登录" src="'+qq_ico+'">QQ登录</a><a href="'+ site.site_root + '/index.php?m=member&c=index&a=userregister" target="_blank">立即注册</a></span><a target="_blank" class="login-otherR" href="#">忘记密码？</a></div>');
				div_form.push('<input class="login-btn" type="submit" value="登 录">');
				div_form.push('</form>');
				art.dialog({
					id : 'login',
					title:'用户登录',
					fixed:true,
					lock:true,
					content: div_form.join(''),
					init:function() {
						var win = this;
						var form = $('.login-form');
						var tipMsg = form.find(".login-tipMsg");
						var iAccount = form.find(':text');
						var iPassword = form.find(':password');
						var iSubmit = form.find("input[type=submit]");
						form.submit(function() {
							var account = $.trim(iAccount.val());
							var password = $.trim(iPassword.val());
							if(!account) {
								tipMsg.html('请输入/邮箱/手机').show();
								iAccount.focus();
								return false;
							}
							if(!password) {
								tipMsg.html("请输入登录密码").show();
								iPassword.focus();
								return false;
							}
							$.post(site.site_root + '/index.php?m=member&c=index&a=login', {
								username:account,
								password:password,
								needverify:1,
								ajax:1
							}, function(ret) {
								if(ret.status == 0) {
									tipMsg.html(ret.info).show();
									iSubmit.attr("value", "登 录").removeClass("login-btnDisabled");
									return false;
								} else {
									location.reload();
								}
							}, 'JSON');
							iSubmit.attr("value", "登录中...").addClass("login-btnDisabled");
							return false;
						});
						iAccount.focus();
					}
				});
				
			},
			message:function(){
	               //调用信息发送方法
	               if (window.Notification){
	               	   if(window.Notification.permission == 'granted'){
	               	       $.getJSON('/index.php?m=Order&c=api&a=order_message',function(s){    

	               	         if(s.status == 1){
	                          var notification = new Notification(s.info.subject,{
	                              body : s.info.content,
	                              icon : '/uploadfile/avatar/avatar.jpg',
	                              tag : s.info.messageid // 可以加一个tag
	                          });

	               	         }else{

	               	         	return false;
	               	         }            

	               	       })
	               	   }else {
	               	   	    //请求获取通知权限
	               	     Notification.requestPermission(function(result) {
	               	       if (result === 'denied') {
	               	         return;
	               	       } else if (result === 'default') {
	               	         return;
	               	       }
	               	       // Do something with the granted permission.
	               	     });

	               	     Notification.requestPermission();
	               	   };
	               }else{
	               	return false;
	               }

	         	}

		}}
	)();

$(document).ready(function(){
	//执行异步请求会员信息
  $.getJSON("/index.php?m=Member&c=Usercp&a=userinfo", function(result){

  	if(result.status == 0){
  		return false;
  	}
    userinfo = result;
     $('#login_sta').attr('href','/user/');
     if(userinfo.modelid >0){
      
       setInterval('member.message()',30000);
     	//执行定时任务请求获取数据	
     }
     member.init();
  	  
  });

})