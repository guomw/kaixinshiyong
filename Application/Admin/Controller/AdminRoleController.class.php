<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class AdminRoleController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->role_db = D('AdminRole');
        $this->menu_db = D('Node');
    }

    /* 角色管理 */
    public function init() {
        $infos = $this->role_db->order('listorder ASC, roleid ASC')->select();
        include $this->admin_tpl('role_list');
    }

    /* 角色添加 */
    public function add() {
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $result = $this->role_db->update($info); 
            if ($result === FALSE) {
                $this->error($this->role_db->getError());
            }
            $this->role_db->build_cache();
            $this->success('角色添加成功');
        } else {
            include $this->admin_tpl('role_add');
        }
    }

    /* 角色编辑 */
    public function edit($roleid) {
        $roleid = (int) $roleid;
        $data = $this->role_db->detail($roleid);
        if(empty($data)) $this->error('参数错误');
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['roleid'] = $roleid;
            $result = $this->role_db->update($info);
            if (!$result) {
                $this->error('角色编辑失败'); 
            } else {
                $this->role_db->build_cache();
                $this->success('角色编辑成功', HTTP_REFERER);
            }
        } else {
            extract($data);
            include $this->admin_tpl('role_edit');
        }
    }

    /* 角色删除 */
    public function delete($roleid) {
        if (empty($roleid)) {
            $this->error('参数错误');
        }
        $this->role_db->delete_by_roleid($roleid);
        $this->role_db->build_cache();
        $this->success('删除成功');
    }

    /* 禁用角色 */
    public function disabled($roleid) {
        $roleid = (int) $roleid;
        if ($roleid < 1) $this->error('参数错误');
        $data = $this->role_db->detail($roleid);
        $disabled = ($data['disabled'] == 1) ? 0 : 1;
        $result = $this->role_db->where(array('roleid' => $roleid))->setField('disabled', $disabled);
        if (!$result) {
            $this->error('操作失败');
        }
        $this->success('操作成功');
    }

    /* 更新排序 */
    public function public_listorder() {
        $listorders = (array) $_POST['listorders'];
        if (count($listorders) > 0) {
            foreach ($listorders as $key => $value) {
                $value = intval($value);
                $this->role_db->save(array('roleid' => $key, 'listorder' => $value));
            }
            $this->success('排序更新成功');
        } else {
            $this->error('参数错误');
        }
    }

    /* 角色权限 */
    public function priv_setting($roleid) {
        $rolelist = getcache('role', 'commons');
        $show_header = FALSE;
        $roleid = intval($_GET['roleid']);
        include $this->admin_tpl('role_priv_setting');
    }

    /**
     * 加载权限
     * @author   <jason>
     */
    public function role_priv($roleid) {
        $roleid = (int) $roleid;
        $roleRow = (array) $this->role_db->find($roleid);
        if(empty($roleRow)) {
            $this->error('编辑角色不存在');
        }
        if (IS_POST) {
            $menuids = (array) $_POST['menuid'];
            $data = array(
                'roleid' => $roleid, 
                'role_priv' => serialize($menuids)
            );
            $result = $this->role_db->save($data);
            if (!$result) {
                $this->error('角色权限编辑失败');
            }
            $this->role_db->build_cache();
            $this->success('角色权限编辑成功');
        } else {
            $menu = new \Common\Library\tree();
            $menu->icon = array('│ ','├ ','└─ ');
            $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
            $result = $this->menu_db->select();
            foreach ($result as $n => $t) {
                $result[$n]['cname'] = $t['name'];
                $result[$n]['checked'] = ($this->menu_db->is_checked($t['id'],$roleid))? ' checked' : '';
                $result[$n]['level'] = $this->menu_db->get_level($t['id'],$result);
                $result[$n]['parentid_node'] = ($t['parentid'])? ' class="child-of-node-'.$t['parentid'].'"' : '';
            }
            $str  = "<tr id='node-\$id' \$parentid_node>
                        <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$cname</td>
                    </tr>";        
            $menu->init($result);
            $categorys = $menu->get_tree(0, $str);
            $show_header = FALSE;
            include $this->admin_tpl('role_priv');
        }
    }

    public function company(){
        $map = array();
        $map['rolename'] = '招商专员';
        $role = model('admin_role')->where($map)->find();
        $infos = $this->admin_db->where(array('roleid'=>$role['roleid']))->select();
        include $this->admin_tpl('role_company');

    }
}