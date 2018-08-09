<?php 
namespace Member\Controller;
use \Admin\Controller\InitController;
class MemberTypeController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model("member_group");
		
	}

	public function init(){
		$pagecurr = max(1, I('page', 0, 'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$count = $this->db->where($sqlMap)->count();
		$level = $this->db->where($sqlMap)->page($pagecurr, $pagesize)->select();
		$pages = page($count, $pagesize);
		$show_dialog = TRUE;
		include $this->admin_tpl('level_list'); 
	}

	public function add(){
		if (IS_POST) {
		$info = $_POST['info'];
		$result = $this->db->update($info);
		if (!$result) {
			$this->error($this->db->getError());
		}
		$this->db->build_cache();
		$this->success('操作成功',U("MemberType/init"));
		}else {
		$form = new \Common\Library\form();
		$show_dialog = TRUE;
		include $this->admin_tpl('level_add');  
		}
	}


	public function edit(){

		if (IS_POST) {
			$info = $_POST['info'];
//			$info['allowpost'] = (int) $info['allowpost'];
//			$info['allowpostverify'] = (int) $info['allowpostverify'];
//			$info['allowupgrade'] = (int) $info['allowupgrade'];
//			$info['allowsendmessage'] = (int) $info['allowsendmessage'];
//			$info['allowattachment'] = (int) $info['allowattachment'];
//			$info['allowsearch'] = (int) $info['allowsearch'];

            $info['pricetype'] = implode(',',$info['pricetype']);
			$result = $this->db->where('groupid='.$info['groupid'])->save($info);

			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->db->build_cache();
			 $this->success('操作成功');
		} else {
		$form = new \Common\Library\form();
		$show_dialog = false;
		$id = $_REQUEST['groupid'];
		$info = $this->db->where(array('groupid'=>$id))->find();
		if (empty($info)) $this->error('参数错误');
        extract($info);
        $pricetype = explode(',',$pricetype);//收费标准

		include $this->admin_tpl('level_edit'); 
		}
	}


	/**
	 * 删除多个
	 * @author   <jason>
	 */
	public function delete() {
		$ids = (array) I('groupid');
		if (empty($ids)) {
			$this->error('参数错误');
		}
		foreach ($ids as $id) {
			$id = (int) $id;
			$systemid = $this->db->where(array('issystem'=>1))->find();
			if ($systemid) {
				$this->db->where(array('groupid' => $id))->delete();
			}
		}
		$this->success('操作成功');
	}

	/**
	 * 数组排序
	 * @author   <jason>
	 */
	public function public_sort() {
		$listorders = (array) $_POST['sort'];
		if (submitcheck('dosubmit')) {
			if (empty($listorders)) {
				$this->error('参数错误');
			}
			foreach ($listorders as $id => $sort) {
				if(!is_numeric($id) || !is_numeric($sort)) continue;
				$this->db->where(array('id' => $id))->setField('sort', $sort);
			}
			
			$this->success('操作成功');
		} else {
			$this->error('请勿非法访问'.ACTION_NAME);
		}
	}

	public function public_checkename_ajax() {
		 $name = $_GET['name'];
        if(!$name) {
           $this->error('请输入该会员等级名称');
        }
        $sqlmap = array();
        $sqlmap['name'] = $name;
        $id = $_GET['id'];
       
        if ($id) {
        	  $sqlmap['groupid'] = array('NEQ',$id);
        }
        if(model('member_group')->where($sqlmap)->count() > 0) {
            $this->error('该会员等级已存在,请重新输入');
        }else{
             $this->success('输入正确');
        }    
	}

}
?>