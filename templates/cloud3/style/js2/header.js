	$(function () {
		//商品列表触发事件
		var $menuLi = $("#goods_list li");
        $menuLi.hover(function () {
		  var $this = $(this);
          $menuLi.removeClass("on");
          $this.addClass("on");  
         },function(){
		 $menuLi.removeClass("on");
		 } );	
		//搜索框触发事件
		var $search_form = $("#search_form_on");
		var $search_span = document.getElementById("hdlist_on");
		$search_form.hover(function () {		  
          var $this = $(this);
		  $search_span.style.display="block";
		  $this.addClass("mod_search_hd_unfold");
         },function(){
		 var $this = $(this);
		 $search_span.style.display="none";
		 $this.removeClass("mod_search_hd_unfold");
		 } );
		 $('#search_text').live("focus",function(){//搜索框获得焦点
			  var $islistshow_new = $("#islistshow_new");	  
			  $islistshow_new.addClass("mod_search_hot");
         });
		 $("#search_text").live("blur",function(){//搜索框失去焦点
			  var $islistshow_new = $("#islistshow_new");
			  $islistshow_new.removeClass("mod_search_hot");			  
         }); 
		 
		 $('#welcome').fadeTo(2000, 1).delay(2000).animate({
		opacity: 0,
		marginTop: '-=200'
	},
	1000,
	function(){
		$('#welcome').hide();
	});
	$(window).scroll(function(){
		if($(window).scrollTop() > 50) {
			$('#jump li:eq(0)').fadeIn(800);
		}else{
			$('#jump li:eq(0)').fadeOut(800);
		}
	});
	$("#top").click(function(){
		$('body,html').animate({
			scrollTop: 0
		},0);
		return false;
	});
		 
		 
    });
	function showEWM(){
	document.getElementById("EWM").style.display = 'block';
}
function hideEWM(){
	document.getElementById("EWM").style.display = 'none';
}
	function SetCookie(name,value){
     var Days = 30; 
     var exp = new Date(); 
     exp.setTime(exp.getTime() + Days*24*60*60*1000);
     document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
	}

    function getCookie(name){
     var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;
	}
	
	function search_form($id,$name){
		$.ajax({
            url: '/index.php?m=public&a=search_form',
            type: 'POST',
            data:{form_id:$id},
			dataType: 'html',
            success: function(html){
			var $search_input = document.getElementById("search_input");
			var $search_name = document.getElementById("search_name");
			var $search_form_on = document.getElementById("search_form_on");
			var $search_span = document.getElementById("hdlist_on");
			$search_input.innerHTML=html;
			$search_name.innerHTML=$name;
			$search_span.style.display="none";		
			}
            });  
	}
function search_Go() {
        var search_text = $('#search_text').val();
		var search_type = $('#search_type').val();
		if(search_type=='0'){
			if(search_text==''){	  
				  $("#islistshow_new").addClass("mod_search_hot");
				  $('#search_text').val('请输入您要搜索的商品名称');
			}else if(search_text=='请输入您要搜索的商品名称'){	  
				  $("#islistshow_new").addClass("mod_search_hot");
			}else{
			window.location.href="/search/0_c_"+decodeURIComponent(search_text);				
			}
		}else if(search_type=='1'){
		    if(search_text==''){	  
				  $("#islistshow_new").addClass("mod_search_hot");
				  $('#search_text').val('请输入商家名称');
			}else if(search_text=='请输入商家名称'){	  
				  $("#islistshow_new").addClass("mod_search_hot");
			}else{
			window.location.href="/search/1_c_"+decodeURIComponent(search_text);				
			}
		}
		//if(search_type=='0'){
//		window.location.href="/search/c_"+search_text;
//		}else if(search_type=='1'){
//		//可搜索商家
//		}
					
}