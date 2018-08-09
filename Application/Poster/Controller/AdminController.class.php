<?php
namespace Poster\Controller;
class AdminController extends \Admin\Controller\InitController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('poster/poster');
        $this->types = array(
            'text'  => '文字广告',
            'image' => '图片广告',
            'code'  => '代码广告'
        );
    }
    
    /* 管理广告 */
    public function manage() {
        $sqlmap = array();
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 12)->order("dateline DESC, id DESC")->select();
        $pages = page($count, 12);
        $types = $this->types;
        include $this->admin_tpl('poster_list');
    }
    
    /* 添加广告 */
    public function add() {
        $types = $this->types;
        if(submitcheck('dosubmit')) {
            $poster = $_POST['poster'];
            $poster['setting'] = array2string($_POST['setting']);
            if(isset($poster['start_time']) && !empty($poster['start_time'])) $poster['start_time'] = strtotime ($poster['start_time']);
            if(isset($poster['end_time']) && !empty($poster['end_time'])) $poster['end_time'] = strtotime ($poster['end_time']);
            $result = $this->db->update($poster);
            if(!$result) {
                $this->error($this->db->getError());
            }
            $this->success('广告位添加成功', U('manage'));
        } else {
            $form = new \Common\Library\form();
            include $this->admin_tpl('poster_add');
        }
    }
    
    /* 编辑广告 */
    public function edit($id = 0) {
        $id = (int) $id;
        $rs = $this->db->detail($id);
        if(!$rs) $this->error('参数错误');
        $types = $this->types;
        if(submitcheck('dosubmit')) {
            $poster = $_POST['poster'];
            $poster['setting'] = array2string($_POST['setting']);
            if(isset($poster['start_time']) && !empty($poster['start_time'])) $poster['start_time'] = strtotime ($poster['start_time']);
            if(isset($poster['end_time']) && !empty($poster['end_time'])) $poster['end_time'] = strtotime ($poster['end_time']);
            $poster['id'] = $id;
            $result = $this->db->update($poster);
            if(!$result) {
                $this->error($this->db->getError());
            }
            $this->success('广告位修改成功', U('manage'));
        } else {
            $rs['setting'] = string2array($rs['setting']);
            $form = new \Common\Library\form();
            include $this->admin_tpl('poster_edit');
        }
    }
    
    /* 删除广告 */
    public function delete() {
        if(submitcheck('dosubmit', 'GP')) {
            $ids = (array) I('ids');
            if(empty($ids)) $this->error ('请指定要删除的广告');
            $sqlmap = array();
            $sqlmap['id'] = array("IN", $ids);
            $result = $this->db->where($sqlmap)->delete();
            if(!$result) {
                $this->error('广告位删除失败');
            }
            $this->success('广告位删除成功');
        } else {
            $this->error('请勿非法提交');
        }
    }
    
    /* 更新排序 */
    public function public_listorder() {
        if(submitcheck('dosubmit')) {
            $listorders = (array) $_POST['listorders'];
            if(empty($listorders)) $this->error ('参数错误');
            foreach ($listorders as $id => $listorder) {
                $this->db->where(array('id' => $id))->setField('listorder', $listorder);
            }
            $this->success('广告排序更新成功');
        } else {
            $this->error('请勿非法提交');
        }
    }

    /* 调用代码 [kza] */
    public function public_call(){
        $id = (int) I('id');
        if (!$id) {$this->error('参数错误');};
        $r = $this->db->where(array('id'=> $id))->find();
        extract($r);
        include $this->admin_tpl('space_call');
    }
}
