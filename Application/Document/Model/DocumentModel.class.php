<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Model;
use Think\Model;
if(!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
Class DocumentModel extends Model {
	/*自动验证*/
	public $tableName = ''; 
	public function __construct() {
		$this->categorys = getcache('category', 'commons');
		$this->url = new \Common\Library\url;
	}

	public function set_model($modelid = 0) {
		if ($modelid < 1) {
			$this->error = '模型数据错误';
			return FALSE;
		}
		$this->model = getcache('model', 'commons');
		$this->modelid = $modelid;
		$this->tableName = $this->model[$modelid]['tablename'];
		$this->model_fields = getcache('model_field_'.$modelid,'model');
		return TRUE;
	}

	/**
	 * 添加数据
	 * @param  array  $data     待入库的数组
	 * @param  boolean $isimport 是否为外部接口导入
	 * @author   <jason>
	 */
	public function add_content($data, $isimport = FALSE) {
		if($isimport) $data = daddslashes($data);
		$modelid = $this->categorys[$data['catid']]['modelid'];
		$this->set_model($modelid);
		require_once MODULE_CACHE.'content_input.class.php';
        require_once MODULE_CACHE.'content_update.class.php';
		$content_input = new \content_input($this->modelid);
		$inputinfo = $content_input->get($data,$isimport);

		if ($content_input->getError()) {
			$this->error = $content_input->getError();
			return FALSE;
		}
		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];

		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = NOW_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}
		
		//读取模型字段配置中，关于日期配置格式，来组合日期数据
		$this->fields = getcache('model_field_'.$modelid,'model');
		$setting = string2array($this->fields['inputtime']['setting']);
		extract($setting);
		if($fieldtype=='date') {
			$systeminfo['inputtime'] = date('Y-m-d');
		}elseif($fieldtype=='datetime'){
 			$systeminfo['inputtime'] = date('Y-m-d H:i:s');
		}

		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		$systeminfo['username'] = $data['username'] ? $data['username'] : cookie('admin_username');
		$systeminfo['sysadd'] = defined('IN_ADMIN') ? 1 : 0;
		if ($systeminfo['sysadd'] == 0) {
			$systeminfo['username'] = cookie('_userid');
		}
		
		//自动提取摘要
		if(isset($_POST['add_introduce']) && $systeminfo['description'] == '' && isset($modelinfo['content'])) {
			$content = stripslashes($modelinfo['content']);
			$introcude_length = intval($_POST['introcude_length']);
			$systeminfo['description'] = str_cut(str_replace(array("'","\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;'), '', strip_tags($content)),$introcude_length);
			$inputinfo['system']['description'] = $systeminfo['description'] = addslashes($systeminfo['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $systeminfo['thumb'] == '' && isset($modelinfo['content'])) {
			$content = $content ? $content : stripslashes($modelinfo['content']);
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['description']);
		$systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);	
		//主表
		$id = $modelinfo['id'] = M($this->tableName)->add($systeminfo);
		//更新URL地址
		if($data['islink']==1) {
			$urls[0] = trim_script($_POST['linkurl']);
			$urls[0] = remove_xss($urls[0]);
			
			$urls[0] = str_replace(array('select ',')','\\','#',"'"),' ',$urls[0]);
		} else {
			$urls = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'], $inputinfo, 'add');
		}
		M($this->tableName)->where(array('id'=>$id))->save(array('url'=>$urls[0]));
		
		//附属表
		M($this->tableName.'_data')->add($modelinfo);
		//添加统计
		$this->hits_db = D('Hits');
		$hitsid = 'c-'.$modelid.'-'.$id;
		$this->hits_db->add(array('hitsid'=>$hitsid,'catid'=>$systeminfo['catid'],'updatetime'=>SYS_TIME));

		$content_update = new \content_update($this->modelid,$id);
		//合并后，调用update
		$merge_data = array_merge($systeminfo,$modelinfo);
		$merge_data['posids'] = $data['posids'];
		$content_update->update($merge_data);
		/* 其它的数据暂时不做更新，下个版本增加 */
		return TRUE;
	}

	/**
	 * 编辑内容
	 * @author   <jason>
	 */
	public function edit_content($data, $id) {
		if($isimport === TRUE) $data = daddslashes($data);	
		if (empty($data)) {
			$this->error = '数据参数不正确';
			return false;
		}
		$modelid = $this->categorys[$data['catid']]['modelid'];
		$this->set_model($modelid);
		
		require_once MODULE_CACHE.'content_input.class.php';
        require_once MODULE_CACHE.'content_update.class.php';
        $content_input = new \content_input($this->modelid);
        $inputinfo = $content_input->get($data);

		if (empty($inputinfo)) {
			$this->error = $content_input->getError();
			return FALSE;
		}
        /*格式化入库*/
		$systeminfo = $inputinfo['system'];
		$modelinfo = $inputinfo['model'];
		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = NOW_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}
		
		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}

		//自动提取摘要
		if(isset($_POST['add_introduce']) && $systeminfo['description'] == '' && isset($modelinfo['content'])) {
			$content = stripslashes($modelinfo['content']);
			$introcude_length = intval($_POST['introcude_length']);
			$systeminfo['description'] = str_cut(str_replace(array("\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;'), '', strip_tags($content)),$introcude_length);
			$inputinfo['system']['description'] = $systeminfo['description'] = addslashes($systeminfo['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $systeminfo['thumb'] == '' && isset($modelinfo['content'])) {
			$content = $content ? $content : stripslashes($modelinfo['content']);
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		/* 更新URL地址 */
		if ($data['islink'] == 1) {
			$_url = trim_script($_POST['linkurl']);
		} else {
			$_urls = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'], $inputinfo,'edit');
			$_url = $_urls['0'];
		}	
		M($this->tableName)->save(array('id' => $id, 'url' => $_url));
		
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['description']);
		$systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);
		// 更新主表内容
		M($this->tableName)->where(array('id'=>$id))->save($systeminfo);
		// 更新附属表内容
		M($this->tableName.'_data')->where(array('id'=>$id))->save($modelinfo);
		//调用 update		
		$content_update = new \content_update($this->modelid,$id);
		$content_update->update($data);
		return TRUE;
	}

	public function delete_content($id = 0, $catid = 0, $modelid = 0) {
		if (!$this->set_model($modelid)) {
			return FALSE;
		}
		M($this->tableName)->where(array('id' => $id))->delete();
		$this->tableName = $this->tableName.'_data';
		M($this->tableName)->where(array('id' => $id))->delete();
	}

	/* 读取单条记录 */
	public function detail($id, $field = TRUE) {
		$data = $this->field($field)->find($id);
		if (!is_array($data)) {
			$this->error = '您查看的文章不存在';
			return false;
		}
		return $data;
	}

	public function remove($fromid, $tocatid) {
		$fromid = (array) $fromid;
		$tocatid = (int) $tocatid;
		if (empty($fromid)) {
			$this->error = '来源栏目不正确';
			return FALSE;
		}
		if ($tocatid < 1) {
			$this->error = '目标栏目不正确';
			return FALSE;
		}
		return M($this->tableName)->where(array('catid' => array("IN", $fromid)))->save(array('catid' => $tocatid));
	}

	/**
	 * 更新栏目统计
	 * @author   <jason>
	 */
	private function update_category_items($catid,$action = 'add',$cache = 0) {
		$this->category_db = D('Category');
		if($action=='add') {
			$this->category_db->update(array('items'=>'+=1'),array('catid'=>$catid));
		}  else {
			$this->category_db->update(array('items'=>'-=1'),array('catid'=>$catid));
		}
		if($cache) $this->cache_items();
	}

	public function cache_items() {
		$datas = $this->category_db->select(array('modelid'=>$this->modelid),'catid,type,items',10000);
		$array = array();
		foreach ($datas as $r) {
			if($r['type']==0) $array[$r['catid']] = $r['items'];
		}
		setcache('category_items_'.$this->modelid, $array,'commons');
	}

}