<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use \Admin\Controller\InitController;
defined('MODULE_CACHE') or define('MODULE_CACHE', DATA_PATH.'caches_model/');
Class DocumentController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->document_db = D('Document');
        $this->categorys =  getcache('category', 'commons');
        $this->models = getcache('model', 'commons');
        if (!file_exists(MODULE_CACHE.'content_form.class.php')) {
            api('Cache/run', 'document_model');
        }
    }
    
    /**
     * 内容管理
     * @author   <jason>
     */
    public function manage($catid = 0) {
        $page = max(1, I('page'));        
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        if (!empty($category)) {
            $models  = getcache('model', 'commons');
            $modelid = $category['modelid'];
            $model   = $models[$modelid];
            $_table  = $model['tablename'];            
            $sqlMap          = array();
            $sqlMap['catid'] = $catid;
            if (I('search')) {
                $s = I('param.');
                if ($s['start_time']) {
                    $start_time = strtotime($s['start_time']. '00:00:00');
                    $sqlMap['updatetime'] = array("EGT", $start_time);
                }
                if ($s['end_time']) {
                    $end_time = strtotime($s['end_time']. '23:59:59');
                    $sqlMap['updatetime'] = array("ELT", $end_time);
                }
                $s['searchtype'] = (int) $s['searchtype'];
                if ($s['keyword']) {
                    switch ($s['searchtype']) {
                        case '3':
                            $username = D('Admin')->getFieldByUserid($s['searchtype']);
                            if ($username) {
                                $sqlMap['username'] = $s['keyword'];
                            }                            
                            break;
                        case '2':
                            $sqlMap['username'] = $s['keyword'];
                            break;
                        case '1':
                            $sqlMap['description'] = array("LIKE", "%".$s['keyword']."%");
                            break;
                        default:
                            $sqlMap['title'] = array("LIKE", "%".$s['keyword']."%");
                            break;
                    }
                }
                $s['posids'] = (int) $s['posids'];
                if ($s['posids'] == 1) {
                    $sqlMap['posids'] = array("NEQ", "");
                } elseif($s['posids'] == 2) {
                    $sqlMap['posids'] = array("EQ", "");
                }
            }
            $status = (int)  I('status','1') ;
            if(isset($status)){
            	$sqlMap['status'] = $status;
            }
            
            $count = M($_table)->where($sqlMap)->count();
            $datas = M($_table)->where($sqlMap)->order('id DESC')->page($page, 20)->select();
            $pages = page($count, 20);
            $tpl = 'document_list';
        } else {
            $tpl = 'document_quick';
        }
        $show_header = TRUE; 
        $form = new \Common\Library\form(); 
        $format = new \Common\Library\format(); 
        include $this->admin_tpl($tpl);
    }

    /**
     * 添加内容
     * @author   <jason>
     */
    public function add($catid = 0) {
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        if (submitcheck('dosubmit') || submitcheck('dosubmit_continue')) {
            $info = $_POST['info'];
            $category = $this->categorys[$info['catid']];
            // 内部栏目
            if($category['type'] == 0) {
                $modelid = $category['modelid'];
                $result = $this->document_db->add_content($info);
                if (!$result) {
                    $this->error($this->document_db->getError());
                }
                $forward = (isset($_POST['dosubmit'])) ? 'javascript:window.close();close_dialog();'  : HTTP_REFERER;
                $this->success('操作成功', $forward);
            } elseif($category['type'] == 1) {
                $this->page_db = D('Page');
                $result = $this->page_db->update($info);
                if (!$result) {
                    $this->error($this->page_db->getError());
                }
                $this->success('操作成功');
            }
        } else {       
            cookie('module', 'document');
            $tpl = 'document_add';
            if ($catid > 0) {                
                cookie('catid', $catid);
                if (isset($category) && !empty($category)) {
                    if($category['type']==0) {
                        $modelid = $category['modelid'];
                        require MODULE_CACHE.'content_form.class.php';
                        $content_form = new \content_form($modelid, $catid, $category);
                        $forminfos = $content_form->get();
                        $formValidator = $content_form->formValidator;
                        $tpl = 'document_add';
                    } else {
                        $data = D('Page')->where(array('catid' => $catid))->find();
                        extract($data);
                        $tpl = 'document_page';
                    }
                }               
            }
            $show_header = $show_dialog = $show_validator = 1;
            $form = new \Common\Library\form();
            include $this->admin_tpl($tpl);
        } 
    }

    /* 模型预览 */
    public function public_priview() {
        
    }

    /**
     * 编辑内容
     * @author   <jason>
     */
    public function edit($catid = 0, $id = 0) {
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $_main_table  = $this->models[$modelid]['tablename'];
        $_addon_tablename = $this->models[$modelid]['tablename'].'_data';      
        $main_row = M($_main_table)->getById($id);
        $addon_row = M($_addon_tablename)->getById($id);

        if (!is_array($main_row) || !is_array($addon_row)) {
            $this->error('该信息数据不完整，请删除后，重新添加');
        }
        $data = array_merge($main_row, $addon_row);
        $data = array_map('dhtmlspecialchars',$data);
        if (submitcheck('dosubmit') || submitcheck('dosubmit_continue')) {
            /* 内部栏目 */
            $info = $_POST['info'];
            if($category['type'] == 0) {
                $modelid = $category['modelid'];
                $this->document_db->set_model($modelid);
                $result = $this->document_db->edit_content($info, $id);
                if (!$result) {
                    $this->error($this->document_db->getError());
                }
                $forward = (isset($_POST['dosubmit'])) ? 'javascript:window.close();close_dialog();'  : U('add', array('catid' => $catid, 'fromhash' => FROMHASH));
                $this->success('操作成功', $forward );
            }            
        } else {
            require MODULE_CACHE.'content_form.class.php';
            $content_form = new \content_form($modelid, $catid, $category);
            $forminfos = $content_form->get($data);
            $formValidator = $content_form->formValidator;
            $show_header = $show_dialog = $show_validator = 1;
            include $this->admin_tpl('document_edit');
        }
    }

    /**
     * 信息删除
     * @author   <jason>
     */
    public function delete($catid = 0) {
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $model = $this->models[$modelid];
        if (submitcheck('dosubmit')) {
            $ids = (array) $_POST['ids'];
            if (empty($ids)) {
                $this->error('参数错误');
            }
            $autoindex = 0;
            $this->hits_db = D('Hits');
            foreach ($ids as $id) {
                if(!is_numeric($id)) continue;
                // 删除统计表
                $this->hits_db->where(array('hitsid'=>'c-'.$modelid.'-'.$id))->delete();
                $this->document_db->delete_content($id, $catid, $modelid);
                $autoindex = $autoindex + 1; 
            }
            $this->success('成功删除'.$autoindex.'篇信息');
        } else {
            $this->error('请勿非法提交');
        }       
    }

    /**
     * 文档排序
     * @author   <jason>
     */
    public function listorder($catid = 0) {
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $model = $this->models[$modelid];
        $tablename = $model['tablename'];
        if (submitcheck('dosubmit')) {
            $listorder = (array) $_POST['listorders'];
            if (empty($listorder)) {
                $this->error('参数错误');
            }
            foreach ($listorder as $k => $v) {
                if(!is_numeric($k) || !is_numeric($v)) continue;
                M($tablename)->where(array('id' => $k))->setField('listorder', $v);
            }
            $this->success('排序操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }

    /**
     * 批量审核
     * @author   <jason>
     */
    public function pass($catid = 0) {
        $catid = (int) $catid;
        $category = $this->categorys[$catid];
        $modelid = $category['modelid'];
        $model = $this->models[$modelid];
        $tablename = $model['tablename'];   
        if (submitcheck('dosubmit')) {
            $ids = (array) $_POST['ids'];
            if (empty($ids)) {
                $this->error('参数错误');
            }
            foreach ($ids as $id) {
                if(!is_numeric($id)) continue;
                M($tablename)->where(array('id' => $id))->setField('status', '1');
            }
            $this->success('操作成功');
        } else {
            $this->error('请勿非法提交');
        }
    }

    /**
     * 图片裁剪
     * @author   <jason>
     */
    public function public_crop() {
        if (isset($_GET['picurl']) && !empty($_GET['picurl'])) {
            $picurl = $_GET['picurl'];
            $catid = intval($_GET['catid']);
            if (isset($_GET['module']) && !empty($_GET['module'])) {
                $module = $_GET['module'];
            }
            $show_header =  '';
            include $this->admin_tpl('crop');
        }        
    }

    /**
     * 批量移动
     * @author   <jason>
     */
    public function remove() {
        $this->success('操作成功');
    }

    /**
     * 文档预览
     * @author   <jason>
     */
    public function public_preview($catid = 0, $id=0) {
        $url = new \Common\Library\url();
        $urls = $url->show($id, 1, $catid);
        redirect($urls[1]);
        echo $urls[1];
    }
    

    /**
     * 重复标题
     * @author   <jason>
     */
    public function public_check_title($data = null, $catid = 0) {
        $models = getcache('model','commons');
        if($_GET['data']=='' || (!$_GET['catid'])) return '';
        $catid = intval($_GET['catid']);
        $modelid = $this->categorys[$catid]['modelid'];
        $tablename = $models[$modelid]['tablename'];
        $title = trim($_GET['data']);     
        $r = M($tablename)->where(array('title'=>$title))->count();
        if($r) {
            exit('1');
        } else {
            exit('0');
        }
    }

    /**
     * 加载侧边栏
     * @author   <jason>
     */
    public function public_categorys() {
        $show_header = '';
        $from = isset($_GET['from']) && in_array($_GET['from'],array('block', 'document')) ? $_GET['from'] : 'document';
        $tree = new \Common\Library\tree;
        $categorys = array();
        if(!empty($this->categorys)) {
            foreach($this->categorys as $r) {
                if($r['type']==2 && $r['child']==0) continue;
                if($r['type']==1 || $from=='block') {
                    if($r['type']==0) {
                        $r['vs_show'] = "<a href='?m=block&c=block_admin&a=public_visualization&menuid=".MENUID."&catid=".$r['catid']."&type=show' target='right'>[".L('content_page')."]</a>";
                    } else {
                        $r['vs_show'] ='';
                    }
                    $r['icon_type'] = 'file';
                    $r['add_icon'] = '';
                    $r['type'] = 'add';
                } else {
                    $r['icon_type'] = $r['vs_show'] = '';
                    $r['type'] = 'manage';
                    $r['add_icon'] = "<a target='right' href='?m=document&c=document&a=manage&menuid=".MENUID."&catid=".$r['catid']."' onclick=javascript:openwinx('?m=document&c=document&a=add&menuid=".MENUID."&catid=".$r['catid']."&fromhash=".FROMHASH."','')><img src='".IMG_PATH."add_content.gif' alt='".L('add')."'></a> ";
                }
                $categorys[$r['catid']] = $r;
            }
        }
        if(!empty($categorys)) {
            $tree->init($categorys);
                switch($from) {
                    case 'block':
                        $strs = "<span class='\$icon_type'>\$add_icon<a href='?m=block&c=block_admin&a=public_visualization&menuid=".MENUID."&catid=\$catid&type=list' target='right'>\$catname</a> \$vs_show</span>";
                        $strs2 = "<img src='".IMG_PATH."folder.gif'> <a href='?m=block&c=block_admin&a=public_visualization&menuid=".MENUID."&catid=\$catid&type=category' target='right'>\$catname</a>";
                    break;

                    default:
                        $strs = "<span class='\$icon_type'>\$add_icon<a href='?m=document&c=document&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid' target='right' onclick='open_list(this)'>\$catname</a></span>";
                        $strs2 = "<span class='folder'>\$catname</span>";
                        break;
                }
            $categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2,$ajax_show);
        } else {
            $categorys = L('please_add_category');
        }
        include $this->admin_tpl('category_tree');
    }

    /**
     * 相关文章处理
     * @author   <jason>
     * @return [type] [description]
     */
    public function public_relationlist($modelid = 0) {
        $model = $this->models[$modelid];
        $tablename = $model['tablename'];
        $pagesize = 20;
        $pagecurr = max(1, I('page', '0'));
        /* 限定条件 */
        $map = array();
        $map['modelid'] = $modelid;

        $catid = 0;
        $param = I('param.');
        if ($param['keywords'] && $param['field']) {
            switch ($param['field']) {
                // 根据标题搜索
                case 'id': 
                    $map['id'] = (int) $param['keywords'];
                    break;
                default:
                    $map[$param['field']] = array("LIKE", "%".$param['keywords']."%"); 
                    break;
            }
        }
        if (isset($param['catid']) && is_numeric($param['catid']) && $this->categorys[$param['catid']]) {
            $category = $this->categorys[$param['catid']];
            if ($category['arrchildid'] && $category['child']) {
                $map['catid'] = array("IN", explode(",", $category['arrchildid']));
            } else {
                $map['catid'] = $param['catid'];
            }
            $catid = (int) $param['catid'];
        }
        $count = M($tablename)->where(array('modelid'=>1))->count();
        $lists = M($tablename)->where($map)->limit($pagesize)->page($pagecurr)->select();
        foreach ($lists as $key => $list) {
            $lists[$key]['category'] = $this->categorys[$list[catid]];
        }
        $page = page($count, $pagesize);

        $form = new \Common\Library\form();
        $catoption = $form::select_category($catid, 'name=catid', '不限栏目', $modelid);

        /* 模板赋值并渲染 */
        include $this->admin_tpl('relationlist');
    }

    // 获取图片列表
    public function public_get_piclist($picfile, $ismake = 1){
        echo getpicfile($picfile, $ismake);
    }

    /**
     * 查询相关文档
     * @author   <jason>
     */
    public function public_getjson_ids($modelid = 0, $id = 0) {
        $modelid = (int) $modelid;
        $id = (int) $id;
        $result = array();
        if ($modelid > 0 && $id > 0 && isset($this->models[$modelid])) {
            $model = $this->models[$modelid];
            $tablename = $model['tablename'];
            $data_tablename = $model['tablename'].'_data';
            $relation = M($data_tablename)->getFieldById($id, 'relation');
            if ($relation) {
                $relations = explode("|", $relation);
                $ids = M($tablename)->field('id, title')->where(array('id' => array('IN', $relations)))->select();
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $id['sid'] = 'v'.$id['id'];
                        $result[] = $id;
                    }
                }
            }
        }
        $this->ajaxReturn($result);  
    }


}