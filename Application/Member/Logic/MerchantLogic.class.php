<?php
namespace Member\Logic;
use Think\Model;
class MerchantLogic extends Model{
	function __construct() {
		//初始化附件类
		$this->models = getcache('merchant_group','member');
		$this->db = D('Member');
	}
	
	/*是否参与活动*/
	public function isjoin($mod,$groupid){
		$config = unserialize($this->models[$groupid]['config']);
		$isopen = $config[$mod]['isopen'];
		if($isopen){
			return true;
		}else{
			return false;
		}
	}
	/*报名商品次数*/
	public function apply($mod,$groupid){
		$config = unserialize($this->models[$groupid]['config']);
		if($config[$mod]['apply']['radio'] == 1){
			$times = $config[$mod]['apply']['times'];
			return $times;
		}else{
			return true;
		}
	} 
	/*间隔时间*/
	public function distime($mod,$groupid,$userid){
		$config = unserialize($this->models[$groupid]['config']);
		$radio = $config[$mod]['distime']['radio'];
		if($radio == 1){
			$times =  $config[$mod]['distime']['times'];
			$type = $config[$mod]['distime']['type'];
			//取得上一次添加的时间
			$r = M('Product')->where(array('userid'=>$userid,'mod'=>$mod))->field('inputtime')->order('inputtime DESC')->find();
			if($r){
				$nowtime = NOW_TIME;
				switch ($type){
					case 1://天
						$inputtime = $r['inputtime'] + 60*60*24*$times;
						$distance =  round((60*60*24*$times - ($nowtime - $r['inputtime']))/24,2);
						if($inputtime < $nowtime){//已经操过$times天
							return 1;
						}else{
							return $distance.'天';
						}
						break;
					case 2://小时
						$inputtime = $r['inputtime'] + 60*60*$times;
						$distance = round((60*60*$times - $nowtime + $r['inputtime']) / (60*60),2);
						if($inputtime < $nowtime){//已经操过$times小时
							return 1;
						}else{
							return $distance.'小时';
						}
						break;
					case 3://分钟
						$inputtime = $r['inputtime'] + 60*$times;
						$distance = round((60*$times - ($nowtime - $r['inputtime']))/60,2);
						if($inputtime < $nowtime){//已经操过$times分
							return 1;
						}else{
							return $distance.'分钟';
						}
						break;
				}
			}
		}else{
			return false;
		}
	}
}