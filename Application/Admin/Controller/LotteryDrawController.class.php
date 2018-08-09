<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class LotteryDrawController extends InitController {
function LotteryDrawSet() {
    $where1['rank'] = 1;
    $rank1 = M('lottery_draw_set')->where($where1)->find();
    $where2['rank'] = 2;
    $rank2 = M('lottery_draw_set')->where($where2)->find();
    $where3['rank'] = 3;
    $rank3 = M('lottery_draw_set')->where($where3)->find();
    $where4['rank'] = 4;
    $rank4 = M('lottery_draw_set')->where($where4)->find();
    $where5['rank'] = 5;
    $rank5 = M('lottery_draw_set')->where($where5)->find();
    $where6['rank'] = 6;
    $rank6 = M('lottery_draw_set')->where($where6)->find();
	$lottery_draw_set2=M('lottery_draw_set2')->find();
    include $this->admin_tpl('LotteryDrawSet');
}
function LotteryDrawSetFun() {
    $data1['chance'] = $_POST['chance1'];
    $data1['class'] = $_POST['class1'];
    $data1['num'] = $_POST['num1'];
    $where1['rank'] = 1;
    M('lottery_draw_set')->where($where1)->save($data1);
    $data2['chance'] = $_POST['chance2'];
    $data2['class'] = $_POST['class2'];
    $data2['num'] = $_POST['num2'];
    $where2['rank'] = 2;
    M('lottery_draw_set')->where($where2)->save($data2);
    $data3['chance'] = $_POST['chance3'];
    $data3['class'] = $_POST['class3'];
    $data3['num'] = $_POST['num3'];
    $where3['rank'] = 3;
    M('lottery_draw_set')->where($where3)->save($data3);
    $data4['chance'] = $_POST['chance4'];
    $data4['class'] = $_POST['class4'];
    $data4['num'] = $_POST['num4'];
    $where4['rank'] = 4;
    M('lottery_draw_set')->where($where4)->save($data4);
    $data5['chance'] = $_POST['chance5'];
    $data5['class'] = $_POST['class5'];
    $data5['num'] = $_POST['num5'];
    $where5['rank'] = 5;
    M('lottery_draw_set')->where($where5)->save($data5);
    $data6['chance'] = 10000-$_POST['chance5']-$_POST['chance4']-$_POST['chance3']-$_POST['chance2']-$_POST['chance1'];
    $data6['class'] = $_POST['class6'];
    $data6['num'] = $_POST['num6'];
    $where6['rank'] = 6;
    M('lottery_draw_set')->where($where6)->save($data6);
	
	$where_lottery_draw_set2['lottery_draw_set2_id']=1;
	$data_lottery_draw_set2['rank6_max']=$_POST['rank6_max'];
	$data_lottery_draw_set2['lottery_draw_num']=$_POST['lottery_draw_num'];
	$data_lottery_draw_set2['lottery_draw_num_after_share']=$_POST['lottery_draw_num_after_share'];
	M('lottery_draw_set2')->where($where_lottery_draw_set2)->save($data_lottery_draw_set2);
	
	
    $this->success('提交成功');
}

function LotteryDrawList() {
    if ($_POST['userid']) {
        $where['userid'] = $_POST['userid'];
    }
    $start_time = strtotime($_POST['start_time']);
 	
    $end_time = strtotime($_POST['end_time']) + 60 * 60 * 24;
    if ($_POST['start_time'] and !$_POST['end_time']) {
        $where['time'] = array(
            'egt',
            $start_time
        );
    }
    if ($_POST['start_time'] and $_POST['end_time']) {
        $where['time'] = array(
            'between',
            "$start_time,$end_time"
        );
    }
    if (!$_POST['start_time'] and $_POST['end_time']) {
        $where['time'] = array(
            'elt',
            $end_time
        );
    }
    $pagecurr = max(1, I('page', 0, 'intval'));
    $pagesize = 10;
    import('ORG.Util.Page'); // 导入分页类
    $count = model('lottery_draw_list')->where($where)->count();
    $list = model('lottery_draw_list')->where($where)->page($pagecurr, $pagesize)->select();
    $pages = page($count, $pagesize);
	
	// 统计 
	$where_totalPoints['class']='points'; 
	$totalPoints=M('lottery_draw_list')->where($where_totalPoints)->sum('number');
	
	$where_totalcash['class']='cash'; 
	$totalcash=M('lottery_draw_list')->where($where_totalcash)->sum('number');	
 	
	
    include $this->admin_tpl('LotteryDrawList');
}




}