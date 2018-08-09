var m1 = angular.module('starter.filter', []);

/**
 * @name [我的信息 未读状态]
 */
m1.filter('announce_state', function() {

  return function(r) {

    if (r == "0") {
      return "[未读] ";

    } else if (r == "1") {

      return "";

    }


  }

})

/**
 * @name [实名认证]
 */
m1.filter('f_name_status', function() {

  return function(r) {

    if (r == 0) {
      return "审核中";

    } else if (r == 1) {

      return "已认证";

    } else if (r == -1) {

      return "审核未通过";
    } else {
      return "待认证";
    }

  }

})

/**
 * @name [QQ 状态]
 */
m1.filter('f_user_qq', function() {

  return function(r) {

    if (r == 0) {
      return "未绑定";

    } else {

      return r;

    }

  }

})



/**
 * @name [显示默认淘宝帐号]
 */
m1.filter('is_default_taobao', function($sce) {

    return function(r) {

      if (r == 1) {
        return $sce.trustAsHtml(" <span style ='color:red;font-size:14px;'>默认使用该帐号</span> ");

      } else {

        return "";

      }

    }

  })
  /**
   * @name [支付宝认证]
   */
m1.filter('f_alipay_status', function() {

  return function(r) {

    if (r == 0) {
      return "未绑定";

    } else if (r == 1) {

      return "已绑定";

    }

  }

})


/**
 * @name [银行卡认证]
 */
m1.filter('f_bank_status', function() {

  return function(r) {

    if (r == 0) {
      return "未绑定";

    } else if (r == 1) {

      return "已绑定";

    }

  }

})


/**
 * @name [拍A发B 红包试用状态]
 */
m1.filter('f_protype', function() {

  return function(r) {
    if (r == 1) {
      return " ";

    } else if (r == 2) { //拍a发b

      return "hd_2";

    } else {

      return "hd_2 ";

    }

  }

})


/**
 * @name [商品来源 1.淘宝 2.天猫 3.京东 4.苏宁 5.1号店]
 */
m1.filter('f_source', function() {

  return function(r) {
    if (r == 1) {
      return "淘宝 ";

    } else if (r == 2) { //拍a发b

      return "天猫";

    } else if (r == 3) {

      return "京东 ";

    }else{

      return "";
    }

  }

})



/**
 * @name [商品排序 1.综合 2.人气 3.销量 4.信用 5.最新 6.价格]
 */
m1.filter('f_sort', function() {

  return function(r) {
    if (r == 1) {
      return "综合";

    } else if (r == 2) { //拍a发b

      return "人气";

    } else if (r == 3) {

      return "销量 ";

    }else if (r == 4) {

      return "信用 ";

    }else if (r == 5) {

      return "最新 ";

    }else if (r == 6) {

      return "价格";

    }else{

      return r;
    }


  }

})



m1.filter('f_img_source', function() {

  return function(r) {
    if (r == 1) {
      return "img/taobao.jpg";

    } else if (r == 2) { //拍a发b

      return "img/tianmao.jpg";

    } else if (r == 3) {

      return "img/jd.png";

    }else{

      return "";
    }

  }

})


/**
 * @name [倒计时效果]
 */
m1.filter('f_fomtime', ['$rootScope','$interval','$q',function($rootScope,$interval,$q) {

  return function (r) {
    if(r - Math.round(new Date().getTime() / 1000) < 0){
       return "已结束!";

    }

    var date;

     $interval(function() {
     if (r - Math.round(new Date().getTime() / 1000) < 0) {
       $interval.cancel();
       return "已结束!";
       return false;
     };

    getRTime();
    }, 1000);

     function getRTime() {
      //   var EndTime= new Date('2015/10/2 10:00:00'); 
      var NowTime = new Date();
      var t = r - Math.round(new Date().getTime() / 1000);
      d = Math.floor(t / 60 / 60 / 24);
      h = Math.floor(t / 60 / 60 % 24);
      m = Math.floor(t / 60 % 60);
      s = Math.floor(t % 60);
      d = d ==0 ? "" : d + "天";
      h = h ==0 ? "" : h + "小时";
     date = ( d + h + m + "分" + s + "秒");

      $('#'+r).html(date);

      return date;

    };

     getRTime();

      return date;

  };
}])



/**
 * @name [提现处理状态]
 */
m1.filter('f_tixian_status', function() {

  return function(r) {
    if (r == 1) {
      return " 已完成    ";

    } else if (r == 0) {

      return "处理中";

    } else if (r == -1) {

      return "提现失败";

    } else {


      return "";
    }

  }

})



/**
 * @name [手机认证]
 */
m1.filter('f_phone_status', function() {

  return function(r) {

    if (r == 0) {
      return "待认证";

    } else if (r == "") {

      return "待认证";
    } else if (r == 1) {

      return r;

    }

  }

})


/**
 * @name [邮箱认证]
 */
m1.filter('user_email_state', ['$sce',function($sce) {

  return function(r) {

    if (r == "0") {
      return $sce.trustAsHtml(" <span style ='color: red;'>未认证</span> ");

    } else if (r == "1") {

      return $sce.trustAsHtml(" <span style ='color: green;'>已认证</span> ");

    }

  }

}])


/**
 * @name [活动状态]
 */

m1.filter('goods_state', function() {

  // 这是自定义替换和过滤

  return function(n1) {
    //    //console.log(n1);
    //       return n1.replace(/1/,'12');
    if (n1 == '1') {
      return '活动进行中';
    } else if (n1 == '2') {
      return '活动未上架';
    } else if (n1 == '3') {
      return '活动结束';
    } else {
      return '参数不正确';
    }
  }
});



/**
 * @name [试用订单状态]
 */

m1.filter('f_t_status', function() {

  // 这是自定义替换和过滤

  return function(n1) {
    //   1 已申请，等待商家审核  2.已获得试用资格 去下单  3.填写订单号待审核 4.订单号审核失败 6.申诉中 7.已完成  8.待填写试用报告                            
    if (n1 == '1') {
      return '试用申请审核中';
    } else if (n1 == '2') {
      return '恭喜您获得试用资格,快去下单!';
    } else if (n1 == '3') {
      return '订单号审核中';
    } else if (n1 == '4') {
      return '订单号有误,请修改!';
    } else if (n1 == '6') {
      return '申述处理中';
    } else if (n1 == '7') {
      return '已完成';
    } else if (n1 == '8') {
      return '填写试用报告';
    } else if (n1 == '0') {
      return '已关闭';
    } else {
      return n1 + '订单状态不对';
    }
  }
});

// 重新定义免费试用返回的4个订单状态 只针对免费试用

//           3.已完成  返回数据库存在的状态7
//           2.未通过  返回数据库状态为 已关闭 
//           1.已通过   数据库状态为 2  3 4  6 8
//           0.待审核 返回数据库订单状态  1


/**
 * @name [替换图片地址]
 */

m1.filter('imgUrl',['ENV',function(ENV) {

  // 这是自定义替换和过滤

  return function(url) {
    if(!url) return '';
    url = url.substr(0,4).toLowerCase() == "http" ? url : ENV.imgUrl + url;
    return url;
  }


}])


/**
 * @name [替换昵称]
 */

m1.filter('f_nickname', function() {

  // 这是自定义替换和过滤

  return function(r) {

    if(r == "新会员，请完善资料"){
         return "试客***";
    }
      
      return r;

  }



})


/**
 * @name [已绑定淘宝默认帐号]
 */


m1.filter('taobao_is_default', ['$sce',function($sce) {


  return function(r) {
    if (r == "0") {
      return r;

    } else if (r == "1") {

      return $sce.trustAsHtml(" (<span style ='color: green;'>默认</span>) ");

    }



  }


}])


/**
 * 为相对地址的图片补全url地址
 */


m1.filter('f_img_url', ['ENV',function(ENV) {


  return function(r) {

      if(!r) return "";
      
      var regexp = /<img([^<]*)src=\"(?!(http))(.*?)\"([^<]*)>/gim;
      r = r.replace(regexp,'<img $1 src="' + ENV.imgUrl +'$2$3" />');
      return  r;

  }


}])

/**
 * 坑爹 为试用报告反斜杠补全地址
 */

 m1.filter('f_report_img_url', ['ENV',function(ENV) {


   return function(r) {

       if(!r) return false;
       r = r.replace(/<img.*\/>/ig, ""); 
       return  r;

   }


 }])

 /**
  * 坑爹 恢复转义之后的文章内容
  */


  m1.filter('f_img_html', ['$sce','ENV',function($sce,ENV) {


    return function(r) {

      if(!r) return false;
        r = r.replace(/\\/ig, ""); 
        return $sce.trustAsHtml(r);

    }


  }])


// 给第一商品添加高亮
  m1.filter('f_shop_liang', ['$sce','ENV',function($sce,ENV) {


    return function(r) {

      $(".i_PointsDetail .s_part6 .R span:eq(0)").attr('class','sel')
        return r;

    }


  }])

// 判断是现金还是积分
  m1.filter('f_m_type', ['$sce','ENV',function($sce,ENV) {


    return function(r) {
              
    if(r=='money' || r=='cash'   ) {

       return "现金RMB：";
    }

    if(r=='point' || r=="points" ) {

       return "积分：";
    }


    }


  }])



  // 将小写1转换成大写一
    m1.filter('f_jiang_1', ['$sce','ENV',function($sce,ENV) {

      return function(r) {
                
      if(r==1) {

         return "一";
      }
      if(r==2) {

         return "二";
      }

      if(r==3) {

         return "三";
      }

      if(r==4) {

         return "四";
      }

      if(r==5) {

         return "五";
      }

      if(r==6) {

         return "六";
      }


      }


    }])

  // 将小写1转换成大写一
    m1.filter('f_jiang_time', ['$sce','ENV',function($sce,ENV) {

      return function(r) {      
        //JavaScript函数：
        var minute = 60;
        var hour = minute * 60;
        var day = hour * 24;
        var halfamonth = day * 15;
        var month = day * 30;

        var now = Math.round(new Date().getTime() / 1000);

        var diffValue = now - r;
        var monthC =diffValue/month;
        var weekC =diffValue/(7*day);
        var dayC =diffValue/day;
        var hourC =diffValue/hour;
        var minC =diffValue/minute;
        if(monthC>=1){
          return  parseInt(monthC) + "个月前";
         }
         else if(weekC>=1){
            return parseInt(weekC) + "周前";
         }
         else if(dayC>=1){
          return  parseInt(dayC) +"天前";
         }
         else if(hourC >= 1){
           return parseInt(hourC) +"小时前";
         }
         else if(minC >= 1){
          return  parseInt(minC) +"分钟前";
         }else{
          return "刚刚";
         }
         

      }


    }])



    // 积分兑换商品状态
      m1.filter('f_jifen_status',['$sce','ENV',function($sce,ENV) {

        return function(r) {      
          //JavaScript函数：

          if(r==1){
            return   "已完成兑换 请注意查收";
           }
           else if(r==0){
              return "请等待审核";
           }

           

        }


      }])


 //判断是收入还是支出
 
 m1.filter('f_mobey', ['$sce',function($sce) {

   return function(r) {      

     if(r > 0){
      return $sce.trustAsHtml("<span style='color:green'> 收入 +" + r + "</span>");
      }else{
        return $sce.trustAsHtml("<span style='color:red'> 支出 -" + r + "</span>");
      }

  
   }


 }])


 //保留小数点后2位
 
 m1.filter('f_xiaoshu', function() {

   return function(r) {      
      
      return r.toFixed(2);
  
   }


 })


 //平台补贴
 
 m1.filter('f_bu', function() {

   return function(r) {   

       if(r==1)   return '积分';
       if(r==2)   return '现金';
  
   }


 })


 //替换消息详情样式
 
 m1.filter('announce_show', ['$sce',function($sce) {

   return function(r) {   

    if(!r) return "";

     r = r.replace(/您好/ig, "</p>您好"); 
     r = r.replace(/！/ig, "！</p>"); 
     r = r.replace(/资金/ig, "</p>资金");
     r = r.replace(/商品名称/ig, "</p>商品名称");
     r = r.replace(/请及时/ig, "</p>请及时");
     r = r.replace(/请尽快/ig, "</p>请及时");
     return  $sce.trustAsHtml(r);
  
   }


 }])



 //统一图片大小尺寸
 
 m1.filter('imgzheng', ['$sce',function($sce) {

   return function(r) {   

      console.log(p);

    $('.hang1').each(function(){
      if($(this).find('img').height() < $(this).find('img').width()){
        $(this).find('img').css('height',$(this).find('img').width());
      }
    });

  
   }


 }])

;