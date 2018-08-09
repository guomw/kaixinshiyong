$(document).ready(function(){
	//标注已读
	$("#onread").click(function(){  
		//输出选中的值
		var str="";  
		$("input[name='announceid[]']:checked").each(function(){  
			str+=$(this).val()+",";  
		}); 
		if(str.length <=0){alert('您没有选择任何站内信！');return false;}
		$.ajax({
			url:'index.php?m=Member&c=Announce&a=read',
			type:'post',
			dataType:'json',
			data:{'ids':str,'type':type},
			success:function(data){
				if(data == 1){
					alert('标记已读成功');
					location.reload();
				}else{
					alert('系统错误，请稍后再试');
				}
			}
		});
	});	  
	//删除所选
	$("#deletecheck").click(function(){
		//输出选中的值
		var ids = [];
		$("input[name='announceid[]']:checked").each(function(){
			ids.push(this.value);
		});	
		if(ids.length<=0){
			alert('您没有选择任何站内信！');
			return false;
		}
		this.remove(ids.join(","),"您确定要删除所选的站内信吗?");
	});
});