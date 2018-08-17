<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style type="text/css">
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
<script type="text/javascript" src="/static/js/jquery-1.11.2.js"></script>
</head>
<body>
<div class="i_LotteryDrawSet">
  <form action="/index.php?m=Admin&c=lotteryDraw&a=LotteryDrawSetFun" method="post" name="form">
    <div  class="h1">
      <div class="a1">一等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3">
        <input type="text"  name="chance1"  value="<?php echo $rank1['chance']?>"/>
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class1">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num1"  value="<?php echo $rank1['num']?>"  />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div  class="h1">
      <div class="a1">二等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3">
        <input type="text"  name="chance2"  value="<?php echo $rank2['chance']?>" />
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class2">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num2"  value="<?php echo $rank2['num']?>"  />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div  class="h1">
      <div class="a1">三等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3">
        <input type="text"  name="chance3" value="<?php echo $rank3['chance']?>"/>
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class3">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num3"  value="<?php echo $rank3['num']?>"   />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div  class="h1">
      <div class="a1">四等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3">
        <input type="text"  name="chance4" value="<?php echo $rank4['chance']?>"/>
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class4">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num4"  value="<?php echo $rank4['num']?>"  />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div  class="h1">
      <div class="a1">五等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3">
        <input type="text"  name="chance5" value="<?php echo $rank5['chance']?>"/>
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class5">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num5"  value="<?php echo $rank5['num']?>"  />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div  class="h1">
      <div class="a1">六等奖</div>
      <div class="a2">概率-万分之</div>
      <div class="a3"> <?php echo  10000-$rank5['chance']- $rank4['chance']-$rank3['chance']-$rank2['chance']-$rank1['chance'] ?>
      </div>
      <div class="a2">奖品类型</div>
      <div class="a4">
        <select name="class6">
          <option value="points">积分</option>
          <option value="cash">现金</option>
        </select>
      </div>
      <div class="a2">奖品数量</div>
      <div class="a3">
        <input type="text" name="num6"  value="<?php echo $rank6['num']?>"  />
      </div>
      <div class="a3"> 至
        <input type="text" name="rank6_max"  value="<?php echo $lottery_draw_set2['rank6_max']?>"  />
      </div>
      <div class="clear"></div>
    </div>
    <!--h1-->
    <div class="h3">备注：现金的单位 美元</div>
    <div class="h2"> 
       <div class="a1">抽奖次数</div><div class="a2"><input type="text" name="lottery_draw_num"  value="<?php echo $lottery_draw_set2['lottery_draw_num']?>"  /></div><div class="a1">分享后可以抽奖的次数</div><div class="a2"><input type="text" name="lottery_draw_num_after_share"  value="<?php echo $lottery_draw_set2['lottery_draw_num_after_share']?>"  /></div>
        <div class="clear"></div>
      </div>
    <!--h2-->
  </form>
  <div class="btn" onclick="form_submit()">提交</div>
  <div class="clear"></div>
  <script type="text/javascript">
       $("select[name='class1'] ").find("option[value='<?php echo $rank1['class']?>'] ").get(0).selected = true;
       $("select[name='class2'] ").find("option[value='<?php echo $rank2['class']?>'] ").get(0).selected = true;
       $("select[name='class3'] ").find("option[value='<?php echo $rank3['class']?>'] ").get(0).selected = true;
       $("select[name='class4'] ").find("option[value='<?php echo $rank4['class']?>'] ").get(0).selected = true;
       $("select[name='class5'] ").find("option[value='<?php echo $rank5['class']?>'] ").get(0).selected = true;
       $("select[name='class6'] ").find("option[value='<?php echo $rank6['class']?>'] ").get(0).selected = true;
	   
	   
	    function form_submit(){  
			var regx=/^\d+(\.\d+)?$/;
			for( i=1;i<6;i++){
				if(  ! regx.test(   $("input[name='chance"+i+"'] ").val()  ) ){
					alert('概率只能为数字,并且不小于0')
					return
				}
				   <!--end if-->	
  			}
			<!--end for-->
			
			total=0
			for( i=1;i<6;i++){
				total=total+ Number( $("input[name='chance"+i+"'] ").val() )
  			}
			<!--end for-->
			if( total>10000){
				alert('概率的总和不能大于10000')
				return   
			}			  
			  
			  form.submit() 
 		}
	   <!--end fun-->
	   
	   
	   
	   
	   
	   
	   
	   
	   
	   
    </script> 
</div>
<!--i_LotteryDrawSet-->

</body>
</html>
