<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
use Common\Library\tree;
/* 节点管理 */
class NodeController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->db = D('Node');
    }

    /* 菜单管理 */
    public function init() {
        $result = $this->db->field('id, name, parentid, listorder')->order('listorder ASC, id ASC')->select();        
        $tree = new tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['name'];
            $r['listorder'] = (int) $r['listorder'];
            $r['str_manage'] = '<a href="?m=admin&c=node&a=add&parentid='.$r['id'].'&menuid='.$_GET['menuid'].'">添加子菜单</a> | <a href="?m=admin&c=node&a=edit&id='.$r['id'].'&menuid='.$_GET['menuid'].'&pos=menu_'.$r['id'].'">修改</a> | <a href="javascript:confirmurl(\'?m=admin&c=node&a=delete&id='.$r['id'].'&menuid='.$_GET['menuid'].'\',\'确认要删除该菜单吗？\')">删除</a> ';
            $array[] = $r;
        }
        $str  = "<tr>
                    <td align='center'><a name='menu_\$id'></a><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
                    <td align='center'>\$id</td>
                    <td >\$spacer\$cname</td>
                    <td align='center'>\$str_manage</td>
                </tr>";
        $tree->init($array);
        $menus = $tree->get_tree(0, $str);
        include $this->admin_tpl('node_manage');
    }

    public function add() {
    	if (IS_POST) {
            $info = $_POST['info'];
            $info['parentid'] = (int) $info['parentid'];
    		$result = $this->db->update($info);
    		if ($result === FALSE) {
    			$this->error($this->db->getError());
    		}
    		$this->success('菜单添加成功', U('init#menu_'.$result));
    	} else {
            $show_validator = '';
            $tree = new tree();
            $result = $this->db->select();
            $array = array();
            foreach($result as $r) {
                $r['cname'] = L($r['name']);
                $r['selected'] = $r['id'] == $_GET['parentid'] ? 'selected' : '';
                $array[] = $r;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
            $tree->init($array);
            $select_categorys = $tree->get_tree(0, $str);
            include $this->admin_tpl('node_add');
    	}
    }

    public function edit($id) {
        $id = (int) $id;
        $data = $this->db->detail($id);
        if (IS_POST) {
            $info = $_POST['info'];
            $info['id'] = $id;
            $result = $this->db->update($info);
            if ($result === FALSE) {
                $this->error($this->db->getError());
            }
            $this->success('菜单修改成功', U('init#menu_'.$id));
        } else {
            $show_validator = $array = $r = '';
            $tree = new tree();
            $r = $this->db->where(array('id'=>$id))->find();
            if($r) extract($r);
            $result = $this->db->select();
            foreach($result as $r) {
                $r['cname'] = L($r['name']);
                $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
                $array[] = $r;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
            $tree->init($array);
            $select_categorys = $tree->get_tree(0, $str);
            include $this->admin_tpl('node_edit');
        }
    }

    public function public_listorder() {
        if (submitcheck('dosubmit')) {
            $info = I('post.');
            $listorders = (array) $info['listorders'];
            if (count($listorders) > 0) {
                foreach ($listorders as $key => $value) {
                    $value = intval($value);
                    $this->db->save(array('id' => $key, 'listorder' => $value));
                }
                $this->success('排序更新成功');
            } else {
                $this->error('参数错误');
            }   
        }
    }

    /**
     * 删除节点菜单
     * @author   <jason>
     */
    public function delete($id = 0) {
        $id = (int) $id;
        if ($id < 1) $this->error('参数错误');
        $arrchild = $this->db->getmenu($id);
        if (is_array($arrchild) && !empty($arrchild)) {
            $this->error('该菜单有下级菜单，不能直接删除');
        }
        $result = $this->db->delete($id);
        if (!$result) {
            $this->error('菜单删除失败');
        }
        $this->success('操作成功');
    }
}