<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>style_set.css" /> 
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>site_set.css" /> 
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.js"></script>
</head>
<body>
    <form action="<?php echo U('Setting/contact') ?>" method="post">
   <div class="wrap marginTop2 marginTop6">
        <h2 class="floatLeft wiHe3 borderBottom_ddd wrap_title fontSize15 color3">发布文章</h2>         
  </div>
        <div class="wiHe3 floatLeft borderBottom_f2f2f2 paddingBottom1">
            <div class="wrap marginTop2 setList">   
            <ul class="floatLeft wiHe3">
                        <h2 class="floatLeft wiHe3 wrap_title fontSize15 color3 marginTop4">
                            <span class="fontSize14 floatLeft">文章分类</span>
                            <div class="floatLeft onMouse positionRelative">
                                <a href="#" class="displayNone"><img class="floatLeft" src="/static/images/query_icon.jpg" alt=""></a>
                                <div class="hint_message fontWeight2">
                                    <div class="hint_over border_4c9bff">
                                    <img src="/static/images/hint_message.jpg" class="hint_message_bg" alt="">
                                    弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框
                                    <p><a href="#" class="color1">查看详情</a></p>
                                    </div>
                                </div>  
                            </div>
                        </h2>
                        <li class="floatLeft wiHe4">
                           <select name="parentid">
                               <option value="">请选择</option>
                               option
                           </select>

                        </li>
            </ul>
             </div>
        </div>

        <div class="wiHe3 floatLeft borderBottom_f2f2f2 paddingBottom1">
              <div class="wrap marginTop2 setList">   
                 <ul class="floatLeft wiHe3">
                    <h2 class="floatLeft wiHe3 wrap_title fontSize15 color3 marginTop4">
                        <span class="fontSize14 floatLeft">文章标题</span>
                        <div class="floatLeft onMouse positionRelative">
                            <a href="#" class="displayNone" ><img class="floatLeft" src="/static/images/query_icon.jpg" alt=""></a>
                            <div class="hint_message fontWeight2">
                                <div class="hint_over border_4c9bff">
                                <img src="/static/images//hint_message.jpg" class="hint_message_bg" alt="">
                                弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框
                                <p><a href="#" class="color1">查看详情</a></p>
                                </div>
                            </div>  
                        </div>
                    </h2>
                    <li class="floatLeft wiHe4"><input type="text"  name="setting[contact_qq]" value="<?php echo $setting['contact_qq'] ?>" class="color3 lineHeight textWidth marginRight1 textIndent1"></li>
                    <li class="floatLeft wiHe4 color3 lineHeight">请输入文章标题</li>
                </ul>
            </div>
     </div>


    <div class="wiHe3 floatLeft borderBottom_f2f2f2 paddingBottom1">
        <div class="wrap marginTop2 setList">   
             <ul class="floatLeft wiHe3">
                <h2 class="floatLeft wiHe3 wrap_title fontSize15 color3 marginTop4">
                    <span class="fontSize14 floatLeft">文章内容</span>
                    <div class="floatLeft onMouse positionRelative">
                        <a href="#" class="displayNone" ><img class="floatLeft" src="/static/images/query_icon.jpg" alt=""></a>
                        <div class="hint_message fontWeight2">
                            <div class="hint_over border_4c9bff">
                            <img src="/static/images//hint_message.jpg" class="hint_message_bg" alt="">
                            弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框
                            <p><a href="#" class="color1">查看详情</a></p>
                            </div>
                        </div>  
                    </div>
                </h2>
                <li class="floatLeft wiHe4">
                   <textarea id="" cols="30" rows="10" name="setting[keyword]" class="color3 lineHeight textWidth setHeight1 marginRight1 textIndent1">素材下载cfdfastrgs</textarea>

                </li>
                <li class="floatLeft wiHe4 color3 lineHeight">请输入文章内容</li>
             </ul>
        </div>
    </div>



    <div class="wiHe3 floatLeft borderBottom_f2f2f2 paddingBottom1">
        <div class="wrap marginTop2 setList">   
             <ul class="floatLeft wiHe3">
                <h2 class="floatLeft wiHe3 wrap_title fontSize15 color3 marginTop4">
                    <span class="fontSize14 floatLeft">是否显示</span>
                    <div class="floatLeft onMouse positionRelative">
                        <a href="#" class="displayNone" ><img class="floatLeft" src="/static/images/query_icon.jpg" alt=""></a>
                        <div class="hint_message fontWeight2">
                            <div class="hint_over border_4c9bff">
                            <img src="/static/images//hint_message.jpg" class="hint_message_bg" alt="">
                            弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框
                            <p><a href="#" class="color1">查看详情</a></p>
                            </div>
                        </div>  
                    </div>
                </h2>
                <li class="floatLeft wiHe4">
                <input name="" type="radio" value="1" checked>&nbsp;是&nbsp;&nbsp;
                <input name="" type="radio" value="0">&nbsp;否

                </li>
             </ul>
        </div>
    </div>


       <div class="wiHe3 floatLeft borderBottom_f2f2f2 paddingBottom1">
        <div class="wrap marginTop2 setList">   
             <ul class="floatLeft wiHe3">
                <h2 class="floatLeft wiHe3 wrap_title fontSize15 color3 marginTop4">
                    <span class="fontSize14 floatLeft">是否推荐</span>
                    <div class="floatLeft onMouse positionRelative">
                        <a href="#" class="displayNone" ><img class="floatLeft" src="/static/images/query_icon.jpg" alt=""></a>
                        <div class="hint_message fontWeight2">
                            <div class="hint_over border_4c9bff">
                            <img src="/static/images//hint_message.jpg" class="hint_message_bg" alt="">
                            弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框弹出框
                            <p><a href="#" class="color1">查看详情</a></p>
                            </div>
                        </div>  
                    </div>
                </h2>
                <li class="floatLeft wiHe4">
                <input name="" type="radio" value="1" checked>&nbsp;是&nbsp;&nbsp;
                <input name="" type="radio" value="0">&nbsp;否

                </li>
             </ul>
        </div>
    </div>
    <li><label>&nbsp;</label><input type="submit" name="dosubmit" class="borderR1 BtnSubWiHe1 background1 color2 bordernove marginLeft1 cursorPointer" value="确认保存"/></li>
    </ul>
</form>
<script type="text/javascript">
		$(".tab_p").hide();
	$(".tab_tp").hover(function(){
		$(".tab_p").toggle();
	})
</script>
</body>
</html>