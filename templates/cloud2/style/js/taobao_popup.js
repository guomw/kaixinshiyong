function exitcid_taobao_box(exitcid){
		var buy_tb_user = $('.content #buy_tb_user').val();
		var buy_tb_num = $('.content #buy_tb_num').val();
		var buy_tb_num_2 = $('.content #buy_tb_num_2').val();
		$.ajax({
            url: '/index.php?m=user&a=attestation_tb',
            type: 'POST',
            data:{buy_tb_user:buy_tb_user,buy_tb_num:buy_tb_num,buy_tb_num_2:buy_tb_num_2},
			dataType: 'json',
            success: function(data){
				if(data.success=='0'){
				$('#error_mas_out').attr('class','error_mas_out_on');
		        $('#error_mas_out').html('抱歉,请检查淘宝帐号或订单编号！');	
				}else if(data.success=='2'){
				$('#error_mas_out').attr('class','error_mas_out_on');
		        $('#error_mas_out').html('抱歉！此淘宝帐号已被认证过了！');
				}else if(data.success=='3'){
				$('#error_mas_out').attr('class','error_mas_out_on');
		        $('#error_mas_out').html('您已认证了5个淘宝帐号了！');
				}else if(data.success=='4'){
				$('#error_mas_out').attr('class','error_mas_out_on');
		        $('#error_mas_out').html('淘宝帐号或订单编号不能为空！');
				}else if(data.success=='1'){
				alert('恭喜您,已成功绑定淘宝帐号！');
				window.location.reload();
				}
				}
            });  
		
}
function for_input_taobao_box(){
        $("#input_taobao").fbmodal({
            title: "淘宝帐号绑定设置",  //标题
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
          exitcid: "input_taobao",//外部关闭标签 false 关闭 此标签为ID标签
		 exitpost: "exitcid_taobao_box",
		 modaltop: "35%",//距离顶部位置
       modalwidth: "300", //框宽度
	    modalheight: "120" //框高度
        });  
}