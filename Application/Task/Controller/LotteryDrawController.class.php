<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Task\Controller;
Class LotteryDrawController extends \Common\Controller\BaseController {
public function LotteryDrawIndex() {
	// $_POST['userid']=2;
     $userinfo = is_login();

     if (!$userinfo) {
       $this->error('请先登录');
    }
	
    //判断抽奖的次数
    $where['userid'] = $_POST['userid'];
    $time_min = date("Y-m-d");
    $time_min = $time_min . " 0:0:0";
    $time_min = strtotime($time_min);
    $time_max = $time_min + 60 * 60 * 24;
    $where['time'] = array(
        'between',
        "$time_min,$time_max"
    );
	
	
  //判断是否已经分享	
  $where_Share['userid']=$_POST['userid'];
  $where_Share['time'] = array(
        'between',
        "$time_min,$time_max"
    );
  $IsShare=M('lottery_draw_share')->where($where_Share)->count();
		 //-end --判断是否已经分享	
	
    $lotteryDrawSet2Data = M('lottery_draw_set2')->find();
    if ($IsShare) {
        $lotteryDrawNum = $lotteryDrawSet2Data['lottery_draw_num_after_share'];
    } else {
        $lotteryDrawNum = $lotteryDrawSet2Data['lottery_draw_num'];
    }
 	
	$lotteryDrawNumAlso= $lotteryDrawNum- M('lottery_draw_list')->where($where)->count(); //还剩余的抽奖次数
    //-end 判断抽奖的次数
	
	$where=array();
	
	$where['rank']=1;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank1=$data['num'];
	if( $data['class']=='points'){
	 $rank1=$rank1.'积分';
	}
	else{
	$rank1=$rank1.'元';
	}
	
	
   $where['rank']=2;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank2=$data['num'];
	if( $data['class']=='points'){
	 $rank2=$rank2.'积分';
	}
	else{
	$rank2=$rank2.'元';
	}

   $where['rank']=3;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank3=$data['num'];
	if( $data['class']=='points'){
	 $rank3=$rank3.'积分';
	}
	else{
	$rank3=$rank3.'元';
	}
	
	
   $where['rank']=4;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank4=$data['num'];
	if( $data['class']=='points'){
	 $rank4=$rank4.'积分';
	}
	else{
	$rank4=$rank4.'元';
	}
	
		
   $where['rank']=5;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank5=$data['num'];
	if( $data['class']=='points'){
	 $rank5=$rank5.'积分';
	}
	else{
	$rank5=$rank5.'元';
	}
	
	
		
   $where['rank']=6;
	$data=M('lottery_draw_set')->where($where)->find(); 
	$rank6=$data['num'].'至';
	
	$where2['lottery_draw_set2_id']=1;
	$data2=M('lottery_draw_set2')->where($where2)->find(); 
	$rank6=$data['num'].'至'.$data2['rank6_max'];
	
	if( $data['class']=='points'){
	 $rank6=$rank6.'积分';
	}
	else{
	$rank6=$rank6.'元';
	}
	
	$data2=M('lottery_draw_list')->limit('10')->select();
     include template('LotteryDrawIndex');
}

public function LotteryDrawIndexFunAjax($userid = '') {
    $ajax_data = array();
	$_POST['userid']=I('userid')?I('userid'):$userid;
    if (!$_POST['userid']) {
         return;
    }
    //判断抽奖的次数
    $where['userid'] = $_POST['userid'];
    $time_min = date("Y-m-d");
    $time_min = $time_min . " 0:0:0";
    $time_min = strtotime($time_min);
    $time_max = $time_min + 60 * 60 * 24;
    $where['time'] = array(
        'between',
        "$time_min,$time_max"
    );
    $IsShare = $_POST['IsShare']; //判断是否已经分享
    $lotteryDrawSet2Data = M('lottery_draw_set2')->find();
    if ($IsShare) {
        $lotteryDrawNum = $lotteryDrawSet2Data['lottery_draw_num_after_share'];
    } else {
        $lotteryDrawNum = $lotteryDrawSet2Data['lottery_draw_num'];
    }
    if (M('lottery_draw_list')->where($where)->count() >= $lotteryDrawNum):
	     echo '抽奖次数已经用完';
          return;
    endif;
     //-end 判断抽奖的次数
 	
    $rangNum = rand(1, 10000);
    $where_chance['rank'] = 1;
    $chance1 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    $where_chance['rank'] = 2;
    $chance2 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    $where_chance['rank'] = 3;
    $chance3 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    $where_chance['rank'] = 4;
    $chance4 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    $where_chance['rank'] = 5;
    $chance5 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    $where_chance['rank'] = 6;
    $chance6 = M('lottery_draw_set')->where($where_chance)->getField('chance');
    if ($rangNum <= $chance1 and $rangNum >= 0):
        $ajax_data['rank']=1;;
        $where_result['rank'] = 1;
    elseif ($rangNum <= $chance1 + $chance2 and $rangNum > $chance1):
/*        $ajax_data=$ajax_data."ajax_data['rank']=2;";
*/                $ajax_data['rank']=2;;

    $where_result['rank'] = 2;
    elseif ($rangNum <= $chance1 + $chance2 + $chance3 and $rangNum > $chance1 + $chance2):
        $ajax_data['rank']=3;;
        //$ajax_data=$ajax_data."ajax_data['rank']=3;";
        $where_result['rank'] = 3;
    elseif ($rangNum <= $chance1 + $chance2 + $chance3 + $chance4 and $rangNum > $chance1 + $chance2 + $chance3):
        $ajax_data['rank']=4;;
        //$ajax_data=$ajax_data."ajax_data['rank']=4;";
        $where_result['rank'] = 4;
    elseif ($rangNum <= $chance1 + $chance2 + $chance3 + $chance4 + $chance5 and $rangNum > $chance1 + $chance2 + $chance3 + $chance4):
        $ajax_data['rank']=5;;
       // $ajax_data=$ajax_data."ajax_data['rank']=5;";
        $where_result['rank'] = 5;
    elseif ($rangNum <= $chance1 + $chance2 + $chance3 + $chance4 + $chance5 + $chance6 and $rangNum > $chance1 + $chance2 + $chance3 + $chance4 + $chance5):
        $ajax_data['rank']=6;;
       // $ajax_data=$ajax_data."ajax_data['rank']=6;";
        //当6等奖 奖品为 现金时。 金额为随机数
        $where_result['rank'] = 6;
        $data = M('lottery_draw_set')->where($where_result)->find();
        if ($data['class'] == 'cash') {
            $result=$this->LotteryDrawIndexFunAjax_part_rank6Save($_POST['userid']);  //当6等奖 奖品为 现金时，数据提交
			$ajax_data['result']=$result;
			
               return $ajax_data;

           
        }
		
	 if ($data['class'] == 'points') {
            $result=$this->LotteryDrawIndexFunAjax_part_rank6Save2($_POST['userid']);  //当6等奖 奖品为 现金时，数据提交
			//$ajax_data=$ajax_data."ajax_data['result']=\"".$result."\";";
            $ajax_data['result']=$result;
		      return  $ajax_data;
        }
		
    endif;
	    $result= $this->LotteryDrawIndexFunAjax_part_otherSave($where_result, $_POST['userid']);  //当其他情况，数据提交
			//$ajax_data=$ajax_data."ajax_data['result']=\"".$result."\";";
         $ajax_data['result']=$result;
 	        return  $ajax_data;

	
}
public function LotteryDrawIndexFunAjax_part_rank6Save($userid) {        //当6等奖 奖品为 现金时，数据提交
    $where_lotteryDrawSet2['lottery_draw_set2_id'] = 1;
    $data_lotteryDrawSet2 = M('lottery_draw_set2')->where($where_lotteryDrawSet2)->find();
    $rangNum_rank6 = rand($data['num'] * 10, $data_lotteryDrawSet2['rank6_max'] * 10);
    $rangNum_rank6 = $rangNum_rank6 / 10;
    $data2['class'] = 'cash';
    $data2['number'] =$rangNum_rank6;
    $data2['userid'] = $userid;
    $data2['time'] = time();
	$data2['rank']=6;
    M('lottery_draw_list')->add($data2);
    $where_user['userid'] = $userid;
    $data_user = M('member')->where($where_user)->find();

   // $data_user2['money'] = $data_user['money'] + $rangNum_rank6;
   // M('member')->where($where_user)->save($data_user2);

     $result='现金'.$rangNum_rank6.'元';
    // 8-reward-userid-当前时间-1
    $msg = '六等奖，抽取'.$result;
    $sign = '8-reward-'.$userid.'-'.NOW_TIME.'-1';
    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    if(!$rs){
        action_finance_log($userid,$rangNum_rank6,'money',$msg,$sign);
    }
     return($result);
}

public function LotteryDrawIndexFunAjax_part_rank6Save2($userid) {        //当6等奖 奖品为 积分时，数据提交
    $where_lotteryDrawSet2['lottery_draw_set2_id'] = 1;
    $data_lotteryDrawSet2 = M('lottery_draw_set2')->where($where_lotteryDrawSet2)->find();
    $rangNum_rank6 = rand($data['num'] , $data_lotteryDrawSet2['rank6_max']);
    $data2['class'] = 'points';
    $data2['number'] = $rangNum_rank6;
    $data2['userid'] = $userid;
    $data2['time'] = time();
	$data2['rank']=6;
    M('lottery_draw_list')->add($data2);
    $where_user['userid'] = $userid;
    $data_user = M('member')->where($where_user)->find();
    //$data_user2['money'] = $data_user['money'] + $rangNum_rank6 ;
   // M('member')->where($where_user)->save($data_user2);

    $result='积分'.$rangNum_rank6.'分';
    $msg = '六等奖，抽取'.$result;

    $sign = '8-reward-'.$userid.'-'.NOW_TIME.'-1';
    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    if(!$rs){
        action_finance_log($userid,$rangNum_rank6,'point',$msg,$sign);
    }
     return($result);
}




public function LotteryDrawIndexFunAjax_part_otherSave($where_result, $userid) {        //当其他情况，数据提交
    $data = M('lottery_draw_set')->where($where_result)->find();
    $data2['class'] = $data['class'];
    $data2['number'] = $data['num'];
    $data2['userid'] = $userid;
    $data2['time'] = time();
	$data2['rank'] = $where_result['rank'];
	
    M('lottery_draw_list')->add($data2);
    if ($data['class'] == 'points') {
        $where_user['userid'] = $userid;
        $data_user = M('member')->where($where_user)->find();
      /*  $data_user2['point'] = $data_user['point'] + $data['num'];
        M('member')->where($where_user)->save($data_user2);*/
        $result='积分'.$data['num'].'分';
        $msg = $where_result['rank'].'等奖，抽取'.$result;
        $sign = '8-reward-'.$userid.'-'.NOW_TIME.'-1';
        $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
        if(!$rs){
            action_finance_log($userid,$data['num'],'point',$msg,$sign);
        }
    }
    if ($data['class'] == 'cash') {
        $where_user['userid'] = $userid;
        $data_user = M('member')->where($where_user)->find();
       // $data_user2['money'] = $data_user['money'] + $data['num'];
       // M('member')->where($where_user)->save($data_user2);
        $result='现金'.$data['num'].'元';
        $msg = $where_result['rank'].'等奖，抽取'.$result;

        $sign = '8-reward-'.$userid.'-'.NOW_TIME.'-1';
        $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
        if(!$rs){
            action_finance_log($userid,$data['num'],'money',$msg,$sign);
        }
    }


      return($result);
}





//      $s = model('admin')->select();	
//		echo $_GET['asdasd'];
// 		var_dump($s);	
//     	$asdasd = 'asdasdssssss';
//    	include $this->admin_tpl('aaa'); 
//     include template('LotteryDrawSet'); 




}