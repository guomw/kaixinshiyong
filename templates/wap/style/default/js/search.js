/* 搜索切换 */
$(function() {

    var vercalW = $(window).width();
	$('.open-s-list').click(function(){
		$('.search-content').removeClass('d-none').addClass('d-block').animate({
			left: '0px'
		}, 300);
		document.ontouchmove = function() {
			return false;
		}
	});
	$('.close-search').click(function() {
		document.ontouchmove = function() {
			return true;
		}
		$('.search-content').animate({
			left: vercalW + 'px'
		}, 300,function(){
			$('.search-content').removeClass('d-block').addClass('d-none');
		});
	});

    //a标签加class
    $('.s-list-c > li a').click(function(){
        if($(this).attr('class') == 'c-f60'){
            $(this).removeClass('c-f60');
        }else{
            $(this).parents(".s-list-c").children("li").each(function(i){
                $(this).children("a").removeClass('c-f60');
            })
            $(this).addClass('c-f60');
        }
    })

    //一级分类 出现相应的二级分类
    $('.get-txt-02 > li a').click(function(){
        //二级所有选择全部去掉class
        $('.get-txt-03').children("li").each(function(i){
            $(this).children("a").removeClass("c-f60");
        })
        var catid = $(this).attr('data-catid')
        $("#cat_"+catid+"").parent(".b-r-style").children("ul").each(function(){
            $(this).hide();
        })
        $("#cat_"+catid+"").siblings("a").attr("data-onOff","on")

        $("#cat_"+catid+"").show();
    })

    //出现和隐藏
    $(".b-r-style >a").click(function(){
        var iskai = $(this).attr('data-onOff');
        if(iskai == 'on'){
            $(this).siblings('.s-list-c').hide(200);
            $(this).attr('data-onOff','off');
        }else{
            var catid = '';
            $(".get-txt-02").children("li").each(function(i){
                var is_select = $(this).children("a").attr("class");
                if(is_select == "c-f60"){
                    catid = $(this).children("a").attr("data-catid");
                    return false;
                }
            })
            if(catid){
                $("#cat_"+catid+"").show();
            }else{
                $(this).next('.s-list-c').show(200);
            }
            $(this).attr('data-onOff','on');
        }
    })
    //
    $('.clear-txt').click(function() {
        $('.s-list-c> li a').removeClass('c-f60');
    });

    $(".search_btn").click(function(){localsearch();})

});

function localsearch() {
    var stype = 'product';

    //获取类型
    var is_select_mod = '';
    var data_mod = '';
    $(".get-txt-01").children("li").each(function(){
        is_select_mod = $(this).children("a").attr("class")
        if(is_select_mod == 'c-f60'){
            data_mod = $(this).children("a").attr('data-mod');
            return false
        }
    })
    //获取一级分类
    var is_select_catid = '';
    var data_catid = '';
    $(".get-txt-02").children("li").each(function(){
        is_select_catid = $(this).children("a").attr("class")
        if(is_select_catid == 'c-f60'){
            data_catid = $(this).children("a").attr('data-catid');
            return false
        }
    })
    //获取二级分类
    var is_select_catid2 = '';
    var data_catid2 = '';
    $(".get-txt-03").children("li").each(function(){
        is_select_catid2 = $(this).children("a").attr("class")
        if(is_select_catid2 == 'c-f60'){
            data_catid2 = $(this).children("a").attr('data-catid');
            return false
        }
    })
    if(data_catid2 == ""){
        data_catid2 = data_catid;
    }

    var keyword = $("input[search-keyword]").val();
//    if($.trim(keyword).length == 0) {
//        alert('关键字不能为空');
//        return false;
//    }
    window.location.href = site.site_root + "/index.php?m=search&type=" + stype + '&keyword=' + keyword + '&mod=' + data_mod+ '&catid=' + data_catid2;
}


/* 搜索菜单   */
function menu() {
	var oMenuOnOff = true;
	var MenuH = $('.menu').height();
	$('.menu').css('height', '0px');

	function toMove(obj) {
		if (oMenuOnOff) {
			obj.next('ul').animate({
				'height': MenuH + 'px'
			}, 100);
			oMenuOnOff = false;
		} else {
			obj.next('ul').animate({
				'height': '0px'
			}, 100);
			oMenuOnOff = true;
		}
	}
	$('.menu-t > p').click(function() {
		toMove($(this));
	});
	$('.menu li a').click(function() {
		var text = $(this).text();
		var t = $('.menu-t > p').text();
		$(this).text(t);
		$('.menu-t > p').text(text);
		toMove($('.menu-t > p'));
	});
}

/* 关键字 */
function TxtShowHide() {
	var now = true;
	$('.s-list-c').hide();

	$('.b-r-style > a').attr('data-onOff',true);
	$('.b-r-style > a').click(function(){
			if($(this).attr('data-onOff') == 'true'){
				$('.s-list-c').hide(200);
				$(this).next('.s-list-c').show(200);
				$('.b-r-style > a').attr('data-onOff',false);
			}else{
				$('.b-r-style > a').attr('data-onOff',true);
			}
		});
}

//function getTxt(obj, value) {
//		$('.s-list-c > li a').attr('data-text', true); //为每一个点击对象赋一个独立属性控制
//		var arr = [value]; //添加默认关键字
//		function arrTxt() { //恢复默认关键字
//				for (var i = 0; i < arr.length; i++) {
//					$(obj).prev().children('strong').html('<b class="default">' + arr[i] + '</b>');
//				}
//			}
//			//初始化
//		arrTxt();
//
//		$(obj + ' > li').click(function() {
//			var oTxt = $(this).children().text(); //获取当前文本内容
//			var tInt = $(obj).prev().children('strong').children('b'); //获取节点
//			var intTxt = $(obj).prev().children('strong').html(); //目标点内容
//			if ($(this).children().attr('data-text') == 'true') { //
//				$(this).children().addClass('c-f60');
//				$(obj).prev().children('strong').html('<b>' + oTxt + '</b>' + intTxt);
//				$(this).children().attr('data-text', false);
//			} else {
//				$(this).children().removeClass('c-f60');
//				tInt.each(function(i, n) {
//					if ($(this).html() == oTxt) {
//						$(this).remove();
//					}
//				});
//
//				$(this).children().attr('data-text', true);
//			}
//
//        /*  默认关键字恢复条件  */
//			if ($(obj).prev().children('strong').children('b').length == 0) {
//				arrTxt();
//				$(obj).prev().children('strong').children('.default').css('color', '#b3b3b3');
//			} else {
//				$(obj).prev().children('strong').children('.default').remove();
//			}
//			$(obj).prev().children('strong').children().addClass('c-f60');
//		});
//		/* 恢复操作 */
//		$('.clear-txt').click(function() {
//			arrTxt();
//			$(obj + ' > li a').removeClass('c-f60');
//			$('.s-list-c > li a').attr('data-text', true);
//		});
//	}
//	/*
//		执行对象
//	*/
//$(function() {
//	TxtShowHide(); //目标的显示隐藏    clear-Txt  按钮
//	getTxt('.get-txt-01', '默认关键字1,默认关键字11,默认关键字111');
//	getTxt('.get-txt-02', '默认关键字2,默认关键字22,默认关键字222');
//	getTxt('.get-txt-03', '默认关键字3,默认关键字33,默认关键字333');
//});


/* 排序 */
function serSort() {
	/* 初始化布尔值 */
	var arr = [];
	$('.sort li a span').each(function(i, n) {
		var inNow = $(this).attr('class');
		inNow.split(' ');
		arr.push(inNow);
	});
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] == 'up') {
			$('.sort li a').attr('data-boole', true);
		} else {
			$('.sort li a').attr('data-boole', false);
		}
	}
	/* 点击执行 */
	$('.sort li a').click(function() {
		/* 获取焦点 */
		$('.sort li a').removeClass('now');
		$(this).addClass('now');
		$('.sort li a').children('span').removeClass('focus').addClass('blur');
		$(this).children('span').removeClass('blur').addClass('focus');
		/* 设置 */
		if ($(this).attr('data-boole') == 'true') {
			$(this).children('span').removeClass('down').addClass('up');
			$(this).attr('data-boole', false);
		} else {
			$(this).children('span').removeClass('up').addClass('down');
			$(this).attr('data-boole', true);
		}
        getContent(1,1);
	});


}