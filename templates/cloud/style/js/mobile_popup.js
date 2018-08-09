function exitcid_mobile_box(exitcid){
		var mobile_num= $('.content #mobile_num').val();
		var verify_mobile_cood = $('.content #verify_mobile_cood').val();		
		$.ajax({
            url: '/index.php?m=user&a=form_mobile_cood',
            type: 'POST',
            data:{verify_mobile_cood:verify_mobile_cood},
			dataType: 'json',
            success: function(data){
			   if(data.out_mobile_cood=='0'){			   
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('请输入6位数字的验证码');
			   }else if(data.out_mobile_cood=='2'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('验证码错误或已超时');
			   }else if(data.out_mobile_cood=='1'){
			   alert('恭喜您,已成功验证！');			   				
  			   window.location.reload();
			   }
			}
            });
		
}
function for_input_mobile_box(){
        $("#input_mobile").fbmodal({
            title: "手机验证设置",  //标题
           cancel: "取消",//取消按钮
             okay: "提交",//确定按钮
       okaybutton: true,//确定按钮：true 显示 false 隐藏
     cancelbutton: true,//取消按钮：true 显示 false 隐藏
          buttons: true,//全部按钮：true 显示 false 隐藏
          opacity: 0.55,//透明度
	      fadeout: false,//淡化退出或载入 ：true 开启 false 关闭
		    fixed: true,//是否固定：true 漂浮 false 固定
     overlayclose: false,//点击框外退出 ：true 开启 false 关闭
	   overbutton: true,//底部按钮 ：true 开启 false 关闭
          exitcid: "input_mobile",//外部关闭标签 false 关闭 此标签为ID标签
		 exitpost: "exitcid_mobile_box",
		 modaltop: "35%",//距离顶部位置
       modalwidth: "270", //框宽度
	    modalheight: "120" //框高度
        });  
}
function list_grab_coo(){
    var mobile_num= $('.content #mobile_num').val();	 
	var mobile_url= $('.content #mobile_url').val();	
	$('.content #verify_mobile').removeAttr("onClick");
	$('.content #verify_mobile_btn').html("正在发送中..");
		$.ajax({
            url: '/index.php?m=user&a=form_buy_mobile',
            type: 'POST',
            data:{mobile_num:mobile_num},
			dataType: 'json',
            success: function(data){
			   if(data.out_mobile=='0'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('正确格式为：1380000000');			   
			   }else if(data.out_mobile=='2'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('您已经验证过了');
			   }else if(data.out_mobile=='3'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('系统出错,请联系客服解决');
			   }else if(data.out_mobile=='4'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('手机号已经被别人验证过了');
			   }else if(data.out_mobile=='1'){
			   $('#error_mas_out').attr('class','error_mas_out_on');
		       $('#error_mas_out').html('已发送成功,请查收！');
			   test_mobile.init(verify_mobile);
			   }
			   if(data.out_mobile!='1'){
			   $('.content #verify_mobile').attr("onClick","list_grab_coo();");
	           $('.content #verify_mobile_btn').html("获取验证码");	   
			   }
			}
            }); 
}
var test_mobile = {
       node:null,
       count:60,
       start:function(){
          if(this.count > 0){
			 $('.content #verify_mobile_btn').html(this.count--+" 秒重发");
             var _this = this;
             setTimeout(function(){
                 _this.start();
             },1000);
          }else{
             $('.content #verify_mobile_btn').html("重发验证码");
			 $('.content #verify_mobile').attr("onClick","list_grab_coo();");
			 this.count = 60;
          }
       },
       //初始化
       init:function(node){
          this.node = node;
		  $('.content #verify_mobile').removeAttr("onClick");
		  this.start();
       }
    };