<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/static/css/admin_LotteryDraw.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/static/js/jquery-1.11.2.js"></script>
</head>
<body>
  <style type="text/css" media="screen">
  @charset "utf-8";
*{ margin:0px; padding:0px;} 
body{font-size:12px; color: #333; line-height:18px; font-family:"宋体"; -webkit-text-size-adjust:none;}
a{TEXT-DECORATION: none; color: #333;}
i{ font-style:normal}
b { font-weight:normal}
ul{width:100%}
ul ,li{list-style:none; }
.clear{ font: 0px/0px sans-serif;clear:both;display: block; height:0px; overflow:hidden}
img { border:none; vertical-align:middle}
.hidden{display:none;}
a:hover {text-decoration:underline;}


.i_LotteryDrawSet{ border:1px solid #dce3ed; margin:10px; padding:10px;}
.i_LotteryDrawSet .h1{ height:36px; line-height:24px; border-bottom:1px solid #dce3ed ; padding-top:10px;}
.i_LotteryDrawSet .h1 .a1{ float: left;width:100px; text-align:right; padding-right:20px; font-weight:bolder; color:#D0272A}
.i_LotteryDrawSet .h1 .a2{ float: left; margin-right:10px; }
.i_LotteryDrawSet .h1 .a3{ float: left; width:135px;}
.i_LotteryDrawSet .h1 .a3 input{ border:1px solid #dce3ed; height:22px; line-height:22px; padding-left:5px; width:100px;}
.i_LotteryDrawSet .h1 .a4{ float:left; margin-top:3px;margin-right:30px; }
.i_LotteryDrawSet .btn{ float:left; padding:0px 10px; background:#ddd; border-right:1px solid #666;border-bottom:1px solid #666; height:24px; line-height:24px; margin-top:10px; cursor:pointer}


 
.i_LotteryDrawSet .h2{ height:36px; line-height:24px; border-bottom:1px solid #dce3ed ; padding-top:10px; padding-left:65px ;}
.i_LotteryDrawSet .h2 .a1{ float: left;  padding-right:20px; }
.i_LotteryDrawSet .h2 .a2{ float: left; margin-right:10px; }
.i_LotteryDrawSet .h2 .a2 input{ border:1px solid #dce3ed; height:22px; line-height:22px; padding-left:5px; width:100px;}

.i_LotteryDrawSet .h3{ height:36px; line-height:24px; border-bottom:1px solid #dce3ed ; padding-top:10px; padding-left:65px ; color:#FF0004}


.i_LotteryDrawList{ margin:10px;}
.i_LotteryDrawList .search{ background:#fffced; border:1px solid #ffbe7a; padding:5px 10px; margin-bottom:10px;}
.i_LotteryDrawList .search .a1{ float: left; line-height:30px; margin-right:10px;}
.i_LotteryDrawList .search .a2{ float: left}
.i_LotteryDrawList .search .a2  input{border:1px solid #d0d0d0; height:24px; line-height:24px; width:100px; margin-right:20px; padding-left:10px;}
.i_LotteryDrawList .search .a3{ float: left}
.i_LotteryDrawList .search .btn{ float:left; padding:0px 10px; background:#ddd; border-right:1px solid #666;border-bottom:1px solid #666; height:24px; line-height:24px; cursor:pointer; margin-right:10px;}
.i_LotteryDrawList .search .a3  input{background: #fff url(../images/admin_img/input_date.png) no-repeat right 3px; padding-right:18px;font-size:12px; border:1px solid #d0d0d0; height:24px; line-height:24px; width:100px; margin-right:10px; padding-left:10px; display:block}
 .i_LotteryDrawList .search .a4{ float:left; line-height:30px; padding-left:10px;}


.i_LotteryDrawList .list .h1{ height:36px; line-height:24px; border-bottom:1px solid #dce3ed ;  background:#eef3f7; height:30px; line-height:30px;}
.i_LotteryDrawList .list .h1 .part{ width:20%; float:left; text-align:center}


.i_LotteryDrawList .list .h2{}
.i_LotteryDrawList .list .h2  table{border-collapse: collapse;}
.i_LotteryDrawList .list .h2 td{ width:20%; text-align:center; border:1px solid #dce3ed;line-height:40px}



 

/*分页*/
#pages { padding:14px 0 10px;font-family:宋体; text-align:right}
#pages a { display:inline-block; height:22px; line-height:22px; background:#fff; border:1px solid #e3e3e3; text-align:center; color:#333; padding:0 10px}
#pages a.a1 { background:url(../images/admin_img/pages.png) no-repeat 0 5px; width:56px; padding:0 }
#pages a:hover { background:#f1f1f1; color:#000; text-decoration:none; }
#pages span { display:inline-block; height:22px;padding:0 10px; line-height:22px; background:#5a85b2; border:1px solid #5a85b2; color:#fff; text-align:center; }
.page .noPage { display:inline-block; height:22px; line-height:22px; background:url(../img/icu/titleBg.png) repeat-x 0 -55px ; border:1px solid #e3e3e3; text-align:center; color:#a4a4a4; }
   
  </style>
<div class="i_LotteryDrawList" >
  <form action="/index.php?m=Admin&c=lotteryDraw&a=LotteryDrawList" method="post" name="form">
  <div class="search"> 
      <div class="a1">用户id:</div>
      <div class="a2"><input type="text"  name="userid"  value="<?php echo $_POST['userid']?>"/></div>
      <div class="a1">查询时间:</div>
    <link rel="stylesheet" type="text/css" href="/static/js/calendar/jscal2.css">
    <link rel="stylesheet" type="text/css" href="/static/js/calendar/border-radius.css">
    <link rel="stylesheet" type="text/css" href="/static/js/calendar/win2k.css">
    <script type="text/javascript" src="/static/js/calendar/calendar.js"></script> 
    <script type="text/javascript" src="/static/js/calendar/lang/en.js"></script>
    <div class="a3"><input type="text" name="start_time" id="start_time" value="<?php echo $_POST['start_time']?>" size="10" class="date input-text" readonly="" ></div>
    <div class="a1">到</div>
    <script type="text/javascript">
			Calendar.setup({
			weekNumbers: true,
		    inputField : "start_time",
		    trigger    : "start_time",
		    dateFormat: "%Y-%m-%d",
		    showTime: false,
		    minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
        </script>
    <div class="a3"><input type="text" name="end_time" id="end_time" value="<?php echo $_POST['end_time']?>" size="10" class="date input-text" readonly=""></div>
   <script type="text/javascript">
			Calendar.setup({
			weekNumbers: true,
		    inputField : "end_time",
		    trigger    : "end_time",
		    dateFormat: "%Y-%m-%d",
		    showTime: false,
		    minuteStep: 1,
		    onSelect   : function() {this.hide();}
			});
        </script>
        <div class="btn" onclick="form.submit()">搜索</div><a class="btn" href="/index.php?m=Admin&c=LotteryDraw&a=LotteryDrawList">重置</a>
        <div class="a4">合计：发放总积分为：<?php echo $totalPoints ?> 总现金为：<?php echo $totalcash ?>美元 </div>
        <div class="clear"></div>
  </div>
  <!--search-->
  </form>
  <div class="list">
    <div class="h1">
      <div class="part">用户id</div>
      <div class="part">抽奖时间</div>
      <div class="part">奖品等奖</div>
      <div class="part">奖品类型</div>
      <div class="part">奖品数量（现金的单位 美元）</div>
      <div class="clear"></div>
    </div>
    <div class="h2">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <?php 
    foreach($list as $value){
      ?>
          <tr>
            <td><?php  echo  $value['userid']?></td>
            <td><?php  echo  date('Y-m-d h:i:sa',$value['time']) ?></td>
            <td><?php  echo  $value['rank'] ?></td>
            <td><?php 
	     if(  $value['class']=='points'){
			 echo '积分';
		  }
		 if(  $value['class']=='cash'){
			 echo '现金';
		  }	
	   ?></td>
            <td><?php  echo  $value['number']?></td>
          </tr>
          <?php 	
}
?>
        </tbody>
      </table>
    </div>
    <!--h2-->
    <div id="pages"><?php echo $pages ?></div>
  </div>
  <!--list--> 
  
</div>
<!--i_LotteryDrawList-->

</body>
</html>
