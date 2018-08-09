<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class LinkageController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->db = D('Linkage');
    }

    /**
     * 联动菜单列表
     * @author   <jason>
     */
    public function init() {
        $sqlmap = array('keyid' => '0');
        $infos = $this->db->where($sqlmap)->select();
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('linkage_add').'\', width:\'500\', height:\'180\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('linkage_add'));
        include $this->admin_tpl('linkage_list');
    }

    /**
     * 添加联动菜单
     * @author   <jason>
     */
    public function add() {
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['parentid'] = (int) $info['parentid'];
            $info['style'] = (int) $info['style'];
            $result = $this->db->update($info);
            if (!$result) {
                $this->error($this->db->getError());
            }
            $this->db->build_cache($result);
            $this->success('操作成功', 'javascript:close_dialog();', 1);
        } else {
            $form = new \Common\Library\form();
            $show_header = $show_validator = TRUE;
            include $this->admin_tpl('linkage_add');
        }
    }

    /**
     * 编辑联动菜单
     * @author   <jason>
     */
    public function edit() {
        $linkageid = I('linkageid', 0, 'intval');
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['name'] = isset($_POST['info']['name']) && trim($_POST['info']['name']) ? trim($_POST['info']['name']) : showmessage(L('linkage_not_empty'));
            $info['description'] = trim($_POST['info']['description']);
            $info['style'] = trim(intval($_POST['info']['style']));
            $info['setting'] = array2string(array('level'=>intval($_POST['info']['level'])));
            $info['parentid'] = (int) $info['parentid'];
            $result = $this->db->where(array('linkageid' => $linkageid))->save($info); 
            if (!$result) {
                $this->error($this->db->getError());
            }
            if(isset($_POST['keyid']) && is_numeric($_POST['keyid'])) {
                $this->db->build_cache(intval($_POST['keyid']));
            }            
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $show_header = $show_validator = array();
            $info = $this->db->where(array('linkageid'=>$linkageid))->find();
            extract($info);
            $setting = string2array($setting);
            $form = new \Common\Library\form();
            include $this->admin_tpl('linkage_edit');
        }
    }

    /**
     * 删除联动菜单
     * @author   <jason>
     */
    public function delete($linkageid) {
        $linkageid = (int) $linkageid;
        $keyid = intval($_GET['keyid']);
        $this->_get_childnode($linkageid);
        $this->childnode = array_filter(explode(",", $this->childnode));
        if(is_array($this->childnode)){
            foreach($this->childnode as $linkageid_tmp) {
                $this->db->where(array('linkageid' => $linkageid_tmp))->delete();
            }
        }
        $this->db->delete(array('keyid' => $linkageid));
        if(!$keyid) $this->db->build_cache($linkageid);
        $this->success('操作成功');
    }

    /**
     * 添加下级菜单
     * @author   <jason>
     */
    public function public_sub_add($keyid = 0) {
        $keyid = (int) $keyid;
        $parentid = I('parentid', 0, 'intval');
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['keyid'] = isset($_POST['keyid']) && trim($_POST['keyid']) ? trim(intval($_POST['keyid'])) : $this->error(L('linkage_parameter_error'));
            $name = isset($_POST['info']['name']) && trim($_POST['info']['name']) ? trim($_POST['info']['name']) : $this->error(L('linkage_parameter_error'));
            $info['description'] = trim($_POST['info']['description']);
            $info['style'] = trim($_POST['info']['style']);
            $info['parentid'] = intval($_POST['info']['parentid']);        

            $names = explode("\n", trim($name));
            foreach($names as $name) {
                $name = trim($name);
                if(!$name) continue;
                $info['name'] = $name;
                $this->db->update($info);
            }
            $this->db->build_cache($keyid);
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $show_header = $show_validator = true;
            $linkageid = (int) I('linkageid');
            $form = new \Common\Library\form();
            $list = $form::select_linkage($keyid, $parentid,'info[parentid]', 'parentid', L('cat_empty'), $linkageid);
            include $this->admin_tpl('linkage_sub_add');
        }
    }
    
    /**
     * 更新菜单排序
     * @author   <jason>
     */
    public function public_listorder() {
        $listorders = (array) $_POST['listorders'];
        if (empty($listorders)) {
            $this->error('参数错误');
        }
        foreach ($listorders as $key => $value) {
            if(!is_numeric($key) || !is_numeric($value)) continue;
            $this->db->where(array('linkageid' => $key))->save(array('listorder' => $value));
        }
        $this->success('操作成功');
    }

    /**
     * 管理子菜单
     * @param  integer $keyid [description]
     * @return [type]         [description]
     */
    public function public_manage_submenu($keyid = 0) {
        $keyid = (int) $keyid;
        if ($keyid < 1) {
            $this->error(L('linkage_parameter_error'));
        }
        $tree = new \Common\Library\tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $sum = $this->db->where(array('keyid'=>$keyid))->count();
        $sql_parentid = $_GET['parentid'] ? trim($_GET['parentid']) : 0;
        $where = $sum > 40 ? array('keyid'=>$keyid,'parentid'=>$sql_parentid) : array('keyid'=>$keyid);
        $result = $this->db->where($where)->order('listorder ASC, linkageid ASC')->select();
        foreach($result as $areaid => $area){
            $areas[$area['linkageid']] = array('id'=>$area['linkageid'],'parentid'=>$area['parentid'],'name'=>$area['name'],'listorder'=>$area['listorder'],'style'=>$area['style'],'mod'=>$mod,'file'=>$file,'keyid'=>$keyid,'description'=>$area['description']);
            $areas[$area['linkageid']]['str_manage'] = ($sum > 40 && $this->_is_last_node($area['keyid'],$area['linkageid'])) ? '<a href="'.U('public_manage_submenu', array('keyid' => $area['keyid'], 'parentid' => $area['linkageid'])).'">'.L('linkage_manage_submenu').'</a> | ' : '';
            $areas[$area['linkageid']]['str_manage'] .= '<a href="'.U('public_sub_add', array('keyid' => $keyid, 'linkageid' => $area['linkageid'])).'" onclick="add(this,\''.daddslashes($area['name']).'\'); return false;">'.L('linkage_add_submenu').'</a> | <a href="'.U('edit', array('linkageid' => $area['linkageid'], 'parentid' => $area['parentid'])).'" onclick="edit(this,\''.$area['name'].'\');return false;">'.L('edit').'</a> | <a href="javascript:confirmurl(\''.U('delete', array('linkageid' => $area['linkageid'], 'keyid' => $area['keyid'])).'\', \''.L('linkage_is_del').'\')">'.L('delete').'</a> ';
        }
        
        $str  = "<tr>
                    <td align='center' width='80'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
                    <td align='center' width='100'>\$id</td>
                    <td>\$spacer\$name</td>
                    <td >\$description</td>
                    <td align='center'>\$str_manage</td>
                </tr>";
        $tree->init($areas);
        $submenu = $tree->get_tree($sql_parentid, $str);
        $big_menu =array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('public_sub_add', array('keyid' => $keyid)).'\', title:\''.L('linkage_add').'\', width:\'500\', height:\'300\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('linkage_add'));        
        include $this->admin_tpl('linkage_submenu');
    }

    /**
     * 更新菜单缓存
     * @author   <jason>
     */
    public function public_cache($linkageid = null) {
        $linkageid = (int) $linkageid;
        $this->db->build_cache($linkageid);
        $this->success('缓存更新成功');
    }
    
    /**
     * 子菜单列表
     * @param unknown_type $keyid
     */
    private function submenulist($keyid=0) {
        $keyid = intval($keyid);
        $datas = array();
        $where = ($keyid > 0) ? array('keyid'=>$keyid) : '';
        $result = $this->db->where($where)->order('listorder ASC,linkageid ASC')->select();  
        if(is_array($result)) {
            foreach($result as $r) {
                $arrchildid = $r['arrchildid'] = $this->get_arrchildid($r['linkageid'],$result);                
                $child = $r['child'] =  is_numeric($arrchildid) ? 0 : 1;
                $this->db->update(array('linkageid'=>$r['linkageid'], 'child'=>$child,'arrchildid'=>$arrchildid));            
                $datas[$r['linkageid']] = $r;
            }
        }
        return $datas;
    }

    /**
     * 获取子菜单ID列表
     * @param $linkageid 联动菜单id
     * @param $linkageinfo
     */
    private function get_arrchildid($linkageid,$linkageinfo) {
        $arrchildid = $linkageid;
        if(is_array($linkageinfo)) {
            foreach($linkageinfo as $linkage) {
                if($linkage['parentid'] && $linkage['linkageid'] != $linkageid && $linkage['parentid']== $linkageid)    {
                    $arrchildid .= ','.$this->get_arrchildid($linkage['linkageid'],$linkageinfo);
    
                }
            }
        }
        return $arrchildid;
    }

    /**
     * 获取联动菜单子节点
     * @param int $linkageid
     */
    private function _get_childnode($linkageid) {
        $where = array('parentid'=>$linkageid);
        $this->childnode .= $linkageid.',';
        $result = $this->db->where($where)->field('linkageid')->select();
        if($result) {
            foreach($result as $r) {
                $this->_get_childnode($r['linkageid']);
            }
        }
    }

    /**
     * 返回菜单ID
     */
    public function public_get_list() {
        $infos = $this->db->where(array('keyid' => '0'))->select();
        $show_header = FALSE;
        include $this->admin_tpl('linkage_get_list');
    }

    private function _is_last_node($keyid,$linkageid) {
        $result = $this->db->where(array('keyid'=>$keyid,'parentid'=>$linkageid))->count();
        return $result ? true : false;
    }
}