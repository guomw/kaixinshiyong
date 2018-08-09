<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Api;
use Common\Api\Api;

class PositionApi extends Api{

    /* 构造方法 */
    protected function init(){
        $this->db = D('PositionData');
        $this->positions = getcache('position', 'commons');
    }

	/**
	 * 推荐位推送修改接口
	 * 适合在文章发布、修改时调用
	 * @param int $id 推荐文章ID
	 * @param int $modelid 模型ID
	 * @param array $posid 推送到的推荐位ID
	 * @param array $data 推送数据
	 * @param int $expiration 过期时间设置
	 * @param int $undel 是否判断推荐位去除情况
	 * @param string $model 调取的数据模型
	 * 调用方式
	 * $push = pc_base::load_app_class('push_api','admin');
	 * $push->position_update(323, 25, 45, array(20,21), array('title'=>'文章标题','thumb'=>'缩略图路径','inputtime'='时间戳'));
	 */
    public function position_update($id, $modelid, $catid, $posid, $data, $expiration = 0, $undel = 0) {
		$arr = $param = array();
		$id = intval($id);
		$posid = intval($posid);
		$posinfo = $this->positions[$posid];
		if($id == '0' || !$posid || !$posinfo) return false;
		$modelid = intval($modelid);
		$data['inputtime'] = $data['inputtime'] ? $data['inputtime'] : SYS_TIME;
		if (($posinfo['modelid'] > 0 && $posinfo['modelid'] != $modelid) || ($posinfo['catid'] > 0 && $posinfo['catid'] != $catid)) return false;

		/* 判断最大值 */
		if ($posinfo['maxnum'] > 0) {
			$count = $this->db->where(array('posid' => $posid))->count();
			if ($count >= $posinfo['maxnum']){
				$this->db->where(array('posid' => $posid))->limit(1)->order("listorder DESC, id DESC")->delete();
			}
		}
		//组装属性参数
		$param['id'] = $id;
		$param['posid'] = $posid;
		$arr['modelid'] = $modelid;
		$arr['catid']   =  $catid;
		$arr['listorder'] = $id;
		$arr['thumb']   = $data['thumb'] ? 1 : 0;
		$arr['data']    = array2string($data);

		$rs = $this->db->where($param)->find();
		if (!$rs) {
			$arr['id'] =  $id;
			$arr['posid'] =  $posid;
			$this->db->add($arr);
		} elseif($rs['synedit'] == 0) {
			$this->db->where($param)->save($arr);
		}
    }
}